<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

final class InstanceTypeChecker implements TypeChecker
{
    /** @var string */
    private $expected_class;

    public function __construct(string $expected_class)
    {
        $this->expected_class = $expected_class;
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

        if (! $prop_value instanceof $this->expected_class) {
            $actual_prop_type = is_object($prop_value) ? get_class($prop_value) : gettype($prop_value);
            return new PropTypeException(
                $prop_name,
                'invalid',
                "Invalid property `{$prop_full_name}` of type `{$actual_prop_type}` supplied, expected instance of `{$this->expected_class}`."
            );
        }

        return null;
    }
}
