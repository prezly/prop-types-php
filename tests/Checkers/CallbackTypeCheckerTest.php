<?php
namespace Prezly\PropTypes\Tests\Checkers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\CallbackTypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;
use RuntimeException;

class CallbackTypeCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_invoke_callback_passing_arguments()
    {
        $checker = (new CallbackTypeChecker(function (...$arguments) {
            $this->assertCount(3, $arguments);
            [$props, $prop_name, $full_prop_name] = $arguments;
            $this->assertSame(['name' => 'Elvis Presley'], $props);
            $this->assertSame('name', $prop_name);
            $this->assertSame('test.name', $full_prop_name);
        }));

        $error = $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');

        $this->assertTrue(is_null($error));
    }

    /**
     * @test
     */
    public function it_should_pass_if_callback_returns_null()
    {
        $checker = (new CallbackTypeChecker(function () {
            return null;
        }));

        $error = $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');

        $this->assertTrue(is_null($error));
    }

    /**
     * @test
     */
    public function it_should_fail_if_callback_returns_an_error()
    {
        $checker = (new CallbackTypeChecker(function () {
            return new PropTypeException('prop', 'whatever', 'Prop is not valid');
        }));

        $error = $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');

        $this->assertInstanceOf(PropTypeException::class, $error);
        $this->assertSame('prop', $error->getPropName());
        $this->assertSame('whatever', $error->getCode());
        $this->assertSame('Prop is not valid', $error->getMessage());
    }

    /**
     * @test
     */
    public function it_should_fail_if_callback_throws_an_error()
    {
        $checker = (new CallbackTypeChecker(function () {
            throw new PropTypeException('prop', 'whatever', 'Prop is not valid');
        }));

        $error = $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');

        $this->assertInstanceOf(PropTypeException::class, $error);
        $this->assertSame('prop', $error->getPropName());
        $this->assertSame('whatever', $error->getCode());
        $this->assertSame('Prop is not valid', $error->getMessage());
    }

    /**
     * @test
     */
    public function it_should_fail_if_callback_throws_invalid_argument_exception()
    {
        $checker = (new CallbackTypeChecker(function () {
            throw new InvalidArgumentException('Prop is not valid');
        }));

        $error = $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');

        $this->assertInstanceOf(PropTypeException::class, $error);
        $this->assertSame('name', $error->getPropName());
        $this->assertSame('invalid', $error->getCode());
        $this->assertSame('Prop is not valid', $error->getMessage());
    }

    /**
     * @test
     */
    public function it_should_not_catch_random_exceptions_thrown_from_callback()
    {
        $checker = (new CallbackTypeChecker(function () {
            throw new RuntimeException('Something went wrong');
        }));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong');

        $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');
    }

    /**
     * @test
     */
    public function it_should_throw_if_callback_returns_neither_null_nor_prop_exception()
    {
        $checker = (new CallbackTypeChecker(function () {
            return 1;
        }));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A callback() checker callback is allowed to return either `null` or `PropTypeException`, but `integer` returned instead.'
        );

        $checker->validate(['name' => 'Elvis Presley'], 'name', 'test.name');
    }
}
