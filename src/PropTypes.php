<?php
namespace Prezly\PropTypes;

use InvalidArgumentException;
use Prezly\PropTypes\Checkers\AnyTypeChecker;
use Prezly\PropTypes\Checkers\ArrayOfTypeChecker;
use Prezly\PropTypes\Checkers\CallableTypeChecker;
use Prezly\PropTypes\Checkers\CallbackTypeChecker;
use Prezly\PropTypes\Checkers\ChainableTypeChecker;
use Prezly\PropTypes\Checkers\EnumTypeChecker;
use Prezly\PropTypes\Checkers\EqualityTypeChecker;
use Prezly\PropTypes\Checkers\InstanceTypeChecker;
use Prezly\PropTypes\Checkers\PrimitiveTypeChecker;
use Prezly\PropTypes\Checkers\ShapeTypeChecker;
use Prezly\PropTypes\Checkers\StrictShapeTypeChecker;
use Prezly\PropTypes\Checkers\TypeChecker;
use Prezly\PropTypes\Checkers\UnionTypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;

class PropTypes
{
    private const DEFAULT_OPTIONS = [
        'allow_extra_properties' => false,
    ];

    /**
     * @param \Prezly\PropTypes\Checkers\TypeChecker[] $specs
     * @param array $props
     * @param array $options
     *        - bool "allow_extra_properties" (default: false)
     * @throws \Prezly\PropTypes\Exceptions\PropTypeException When a prop-type validation fails.
     * @throws \InvalidArgumentException When invalid specs configuration was given.
     */
    public static function check(array $specs, array $props, array $options = []): void
    {
        $options = array_merge(self::DEFAULT_OPTIONS, $options);

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

        if (! $options['allow_extra_properties']) {
            foreach (array_keys($props) as $prop_name) {
                if (! isset($specs[$prop_name])) {
                    throw new PropTypeException(
                        $prop_name,
                        "Unexpected extra property `{$prop_name}` supplied."
                    );
                }
            }
        }

        foreach ($specs as $prop_name => $checker) {
            $error = $checker->validate($props, $prop_name, $prop_name);
            if ($error !== null) {
                throw $error;
            }
        }
    }

    public static function equals($value): ChainableTypeChecker
    {
        $checker = new ChainableTypeChecker(new EqualityTypeChecker($value));

        // No need to initially forbid null,
        // as equality check will reject null value,
        // unless it is expected.
        return $checker->isNullable();
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

    public static function callable(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new CallableTypeChecker());
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

    public static function object(): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new PrimitiveTypeChecker('object'));
    }

    public static function oneOfType(array $checkers): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new UnionTypeChecker($checkers));
    }

    public static function oneOf(array $expected_values): ChainableTypeChecker
    {
        return new ChainableTypeChecker(new EnumTypeChecker($expected_values));
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
