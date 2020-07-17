<?php

namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\CallableTypeChecker;
use Prezly\PropTypes\Checkers\PrimitiveTypeChecker;
use stdClass;

class CallableTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param  callable  $callable
     */
    public function it_should_pass_valid_callables(callable $callable)
    {
        $error = (new CallableTypeChecker())->validate(['value' => $callable], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param  mixed  $value
     * @param  string  $actual_type
     */
    public function it_should_return_error_for_any_non_callable_value(
        $value,
        string $actual_type
    ) {
        $error = (new CallableTypeChecker())->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertEquals('value', $error->getPropName());
        $this->assertEquals(
            "Invalid property `test.value` of type `{$actual_type}` supplied, expected callable.",
            $error->getMessage()
        );
    }

    public function valid_examples(): iterable
    {
        $closure = function () {
            return true;
        };

        $invokable = new class {
            public function __invoke()
            {
                return true;
            }
        };

        return [
            'valid function name'           => ['strtolower'],
            'closure'                       => [$closure],
            'instance method array'         => [[$this, __FUNCTION__]],
            'static method array'           => [[TestCase::class, 'assertEquals']],
            'static method string'          => [TestCase::class.'::assertEquals'],
            'instance with __invoke method' => [$invokable],
        ];
    }

    public function invalid_examples(): iterable
    {
        return [
            'null'                    => [null, 'NULL'],
            'number'                  => [2, 'integer'],
            'bool'                    => [true, 'boolean'],
            'empty array'             => [[], 'array'],
            'array of numbers'        => [[1, 2, 3], 'array'],
            'single item array'       => [[$this], 'array'],
            '3+ items array'          => [[$this, __FUNCTION__, __FUNCTION__], 'array'],
            'unknown function name'   => ['we_have_to_make_sure_that_there_is_no_such_function_declared', 'string'],
            'unknown instance method' => [[$this, 'there_is_no_such_method_declared'], 'array'],
            'unknown static method'   => [[self::class, 'there_is_no_such_method_declared'], 'array'],
            'non-callable instance'   => [$this, self::class],
        ];
    }
}
