<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

final class PrimitiveTypeChecker implements TypeChecker
{
    /** @var string */
    private $expected_type;

    public function __construct(string $expected_type)
    {
        $this->expected_type = $expected_type;
    }

    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        $prop_value = $props[$prop_name];
        $prop_type = gettype($prop_value);

        if ($prop_type !== $this->expected_type) {
            $actual_type = is_object($prop_value) ? get_class($prop_value) : gettype($prop_value);

            return new PropTypeException(
                $prop_name,
                'invalid',
                "Invalid property `{$prop_full_name}` of type `{$actual_type}` supplied, expected `{$this->expected_type}`."
            );
        }
        return null;
    }
}
