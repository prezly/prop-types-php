<?php
namespace Prezly\PropTypes\Checkers;

use Prezly\PropTypes\Exceptions\PropTypeException;

interface TypeChecker
{
    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException;
}
