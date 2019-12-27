<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

class ShapeTypeChecker implements TypeChecker
{
    /** @var \Prezly\PropTypes\Checkers\TypeChecker[] */
    private $shape;

    public function __construct(array $shape)
    {
        $this->shape = $shape;
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

        foreach (array_keys($this->shape) as $shape_prop_name) {
            $shape_prop_checker = $this->shape[$shape_prop_name];

            $error = $shape_prop_checker->validate($prop_value, (string) $shape_prop_name, "{$prop_full_name}.{$shape_prop_name}");

            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }
}
