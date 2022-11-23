<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;
use stdClass;

final class EnumTypeChecker implements TypeChecker
{
    /** @var array */
    private $expected_values;

    /**
     * @param mixed[] $expected_values
     */
    public function __construct(array $expected_values)
    {
        $this->expected_values = $expected_values;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        $prop_value = $props[$prop_name];

        foreach ($this->expected_values as $i => $expected_value) {
            if ($prop_value === $expected_value) {
                return null;
            }
        }

        $expected_values_string = self::stringifyValue($this->expected_values);
        $value_string = self::stringifyValue($prop_value);

        return new PropTypeException(
            $prop_name,
            "Invalid property `{$prop_full_name}` of value `{$value_string}` supplied, expected one of: {$expected_values_string}."
        );
    }

    /**
     * @param mixed $value
     * @return string
     */
    private static function stringifyValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_resource($value)) {
            return 'resource';
        }

        if (is_string($value)) {
            if (mb_strlen($value) > 100) {
                return sprintf('"%s..." (%s characters)', mb_substr($value, 0, 80), mb_strlen($value));
            }
            return sprintf('"%s"', $value);
        }

        if (is_array($value)) {
            return self::stringifyArray($value);
        }

        if (is_object($value)) {
            return self::stringifyInstance($value);
        }

        return (string) $value;
    }

    private static function stringifyObject(stdClass $value): string
    {
        $struct = array_map(function ($value) {
            return self::stringifyValue($value);
        }, (array) $value);

        $pairs = [];
        foreach ($struct as $property => $value) {
            $pairs[] = "{$property}: {$value}";
        }
        return sprintf('{%s}', implode(', ', $pairs));
    }

    /**
     * @param object $value
     * @return string
     */
    private static function stringifyInstance($value): string
    {
        if ($value instanceof stdClass) {
            return sprintf('object %s', self::stringifyObject($value));
        }

        $class = get_class($value);
        if (method_exists($value, '__toString')) {
            return "instance of {$class} ({$value->__toString()})";
        }
        return "instance of {$class}";
    }

    private static function stringifyArray(array $value): string
    {
        $values = array_map(function ($value) {
            return self::stringifyValue($value);
        }, $value);

        return sprintf('[%s]', implode(', ', $values));
    }
}
