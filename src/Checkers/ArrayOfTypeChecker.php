<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

class ArrayOfTypeChecker implements TypeChecker
{
    /** @var \Prezly\PropTypes\Checkers\TypeChecker */
    private $checker;

    public function __construct(TypeChecker $checker)
    {
        $this->checker = $checker;
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
                "Invalid property `{$prop_full_name}` of type `{$prop_type}` supplied, expected an array."
            );
        }

        foreach (array_keys($prop_value) as $index) {
            $error = $this->checker->validate($prop_value, (string) $index, "{$prop_full_name}[{$index}]");
            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }
}
