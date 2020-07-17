<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

final class CallableTypeChecker implements TypeChecker
{
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        $prop_value = $props[$prop_name];

        if (! is_callable($prop_value)) {
            $actual_type = is_object($prop_value) ? get_class($prop_value) : gettype($prop_value);

            return new PropTypeException(
                $prop_name,
                "Invalid property `{$prop_full_name}` of type `{$actual_type}` supplied, expected callable."
            );
        }
        return null;
    }
}
