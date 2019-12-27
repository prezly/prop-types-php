<?php
namespace Prezly\PropTypes\Checkers;

use InvalidArgumentException;
use Prezly\PropTypes\Exceptions\PropTypeException;

final class UnionTypeChecker implements TypeChecker
{
    /** @var \Prezly\PropTypes\Checkers\TypeChecker[] */
    private $checkers;

    /**
     * @param \Prezly\PropTypes\Checkers\TypeChecker[] $checkers
     */
    public function __construct(array $checkers)
    {
        foreach ($checkers as $key => $checker) {
            if (! $checker instanceof TypeChecker) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid argument supplied to oneOfType(). Expected an array of %s instances, but received %s at key `%s`.',
                    TypeChecker::class,
                    is_object($checker) ? 'instance of ' . get_class($checker) : gettype($checker),
                    $key
                ));
            }
        }
        $this->checkers = $checkers;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return \Prezly\PropTypes\Exceptions\PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        foreach ($this->checkers as $checker) {
            $checker_result = $checker->validate($props, $prop_name, $prop_full_name);
            if ($checker_result === null) {
                return null;
            }
        }

        return new PropTypeException(
            $prop_name,
            'invalid',
            "Invalid `{$prop_full_name}` supplied, none of types matched."
        );
    }
}
