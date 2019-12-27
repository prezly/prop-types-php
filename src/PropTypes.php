<?php
namespace Prezly\PropTypes;

use InvalidArgumentException;
use Prezly\PropTypes\Checkers\AnyTypeChecker;
use Prezly\PropTypes\Checkers\ArrayOfTypeChecker;
use Prezly\PropTypes\Checkers\CallbackTypeChecker;
use Prezly\PropTypes\Checkers\ChainableTypeChecker;
use Prezly\PropTypes\Checkers\InstanceTypeChecker;
use Prezly\PropTypes\Checkers\PrimitiveTypeChecker;
use Prezly\PropTypes\Checkers\ShapeTypeChecker;
use Prezly\PropTypes\Checkers\StrictShapeTypeChecker;
use Prezly\PropTypes\Checkers\TypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;

final class PropTypes
{
    /**
     * @param \Prezly\PropTypes\Checkers\TypeChecker[] $specs
     * @param array $values
     * @throws \Prezly\PropTypes\Exceptions\PropTypeException When a prop-type validation fails.
     * @throws \InvalidArgumentException When invalid specs configuration was given.
     */
    public static function checkPropTypes(array $specs, array $values): void
    {
        foreach ($specs as $key => $checker) {
            if (! $checker instanceof TypeChecker) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid argument supplied to %s(). Expected an associative array of `%s` instances, but received `%s` at key `%s`.',
                    __FUNCTION__,
                    TypeChecker::class,
                    is_object($checker) ? get_class($checker) : gettype($checker),
                    $key
                ));
            }
        }

        foreach (array_keys($values) as $prop_name) {
            if (! isset($specs[$prop_name])) {
                throw new PropTypeException(
                    $prop_name,
                    'unexpected_extra_property',
                    "Unexpected extra property `{$prop_name}` supplied."
                );
            }
        }

        foreach ($specs as $prop_name => $checker) {
            $error = $checker->validate($values, $prop_name, $prop_name);
            if ($error !== null) {
                throw $error;
            }
        }
    }

    public static function any(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new AnyTypeChecker());
    }

    public static function array(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('array'));
    }

    public static function arrayOf(TypeChecker $checker): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new ArrayOfTypeChecker($checker));
    }

    public static function bool(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('boolean'));
    }

    public static function callback(callable $callback): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new CallbackTypeChecker($callback));
    }
  
    public static function exact(array $shape): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new StrictShapeTypeChecker($shape));
    }

    public static function instanceOf(string $expected_class): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new InstanceTypeChecker($expected_class));
    }

    public static function int(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('integer'));
    }

    public static function float(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('double'));
    }

    public static function null(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('NULL'));
    }

    public static function object(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('object'));
    }

    public static function shape(array $shape): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new ShapeTypeChecker($shape));
    }

    public static function string(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('string'));
    }
}
