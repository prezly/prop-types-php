<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

final class ChainableTypeChecker implements TypeChecker
{
    /** @var TypeChecker */
    private $checker;

    /** @var bool */
    private $is_optional;

    /** @var bool */
    private $is_nullable;

    public function __construct(TypeChecker $checker, bool $is_optional = false, bool $is_nullable = false)
    {
        $this->checker = $checker;
        $this->is_optional = $is_optional;
        $this->is_nullable = $is_nullable;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        if (! array_key_exists($prop_name, $props)) {
            if (! $this->is_optional) {
                return new PropTypeException(
                    $prop_name,
                    "The property `{$prop_full_name}` is marked as required, but it's not defined."
                );
            }
            return null;
        }

        if ($props[$prop_name] === null) {
            if (! $this->is_nullable) {
                // If nested checker allows NULL, we allow it too.
                if ($this->checker->validate($props, $prop_name, $prop_full_name) === null) {
                    return null;
                }

                return new PropTypeException(
                    $prop_name,
                    "The property `{$prop_full_name}` is marked as not-null, but its value is `null`."
                );
            }
            return null;
        }

        return $this->checker->validate($props, $prop_name, $prop_full_name);
    }

    public function isOptional(): self
    {
        return new self($this->checker, true, $this->is_nullable);
    }

    public function isNullable(): self
    {
        return new self($this->checker, $this->is_optional, true);
    }
}
