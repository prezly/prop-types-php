<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

final class ChainableTypeChecker implements TypeChecker
{
    /** @var \Prezly\PropTypes\Checkers\TypeChecker */
    private $checker;

    /** @var bool */
    private $is_required;

    /** @var bool */
    private $is_nullable;

    public function __construct(TypeChecker $checker, bool $is_required = false, bool $is_nullable = false)
    {
        $this->checker = $checker;
        $this->is_required = $is_required;
        $this->is_nullable = $is_nullable;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return \Prezly\PropTypes\Exceptions\PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        if (! array_key_exists($prop_name, $props)) {
            if ($this->is_required) {
                return new PropTypeException(
                    $prop_name,
                    'required',
                    "The property `{$prop_full_name}` is marked as required, but it's not defined."
                );
            }
            return null;
        }

        if ($props[$prop_name] === null) {
            if (! $this->is_nullable) {
                return new PropTypeException(
                    $prop_name,
                    'not_null',
                    "The property `{$prop_full_name}` is marked as not-null, but its value is `null`."
                );
            }
            return null;
        }

        return $this->checker->validate($props, $prop_name, $prop_full_name);
    }

    public function isRequired(): self
    {
        return new self($this->checker, true, $this->is_nullable);
    }

    public function isNullable(): self
    {
        return new self($this->checker, $this->is_required, true);
    }
}
