<?php
namespace Prezly\PropTypes\Checkers;

use Closure;
use InvalidArgumentException;
use Prezly\PropTypes\Exceptions\PropTypeException;

class CallbackTypeChecker implements TypeChecker
{
    /** @var \Closure */
    private $callback;

    /**
     * @param \Closure $callback (array $props, string $prop_name, ?string $component_name = null): ?PropTypeException()
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param array $props
     * @param string $prop_name
     * @param string $prop_full_name
     * @return \Prezly\PropTypes\Exceptions\PropTypeException|null Exception is returned if prop type is invalid
     */
    public function validate(array $props, string $prop_name, string $prop_full_name): ?PropTypeException
    {
        try {
            $error = ($this->callback)($props, $prop_name, $prop_full_name);
        } catch (PropTypeException $exception) {
            $error = $exception;
        } catch (InvalidArgumentException $exception) {
            $error = new PropTypeException($prop_name, 'invalid', $exception->getMessage(), $exception);
        }

        if ($error === null) {
            return null;
        }

        if ($error instanceof PropTypeException) {
            return $error;
        }

        throw new InvalidArgumentException(sprintf(
            'A callback() checker callback is allowed to return either `null` or `PropTypeException`, but `%s` returned instead.',
            is_object($error) ? 'instance of ' . get_class($error) : gettype($error)
        ));
    }
}
