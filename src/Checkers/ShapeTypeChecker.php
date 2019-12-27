<?php
namespace Prezly\PropTypes\Checkers;

use InvalidArgumentException;
use Prezly\PropTypes\Exceptions\PropTypeException;

class ShapeTypeChecker implements TypeChecker
{
    /** @var \Prezly\PropTypes\Checkers\TypeChecker[] */
    private $shape_types;

    /**
     * @param \Prezly\PropTypes\Checkers\TypeChecker[] $shape_types
     */
    public function __construct(array $shape_types)
    {
        foreach ($shape_types as $key => $checker) {
            if (! $checker instanceof TypeChecker) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid argument supplied to shape(). Expected an associative array of %s instances, but received %s at key "%s".',
                    TypeChecker::class,
                    is_object($checker) ? 'instance of ' . get_class($checker) : gettype($checker),
                    $key
                ));
            }
        }

        $this->shape_types = $shape_types;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return \Prezly\PropTypes\Exceptions\PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        $prop_value = $props[$prop_name];

        if (! is_array($prop_value)) {
            $prop_type = gettype($prop_value);

            return new PropTypeException(
                $prop_name,
                'invalid',
                "Invalid property `{$prop_full_name}` of type `{$prop_type}` supplied, expected an associative array."
            );
        }

        foreach ($this->shape_types as $key => $checker) {
            $error = $checker->validate($prop_value, (string) $key, "{$prop_full_name}.{$key}");

            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }
}
