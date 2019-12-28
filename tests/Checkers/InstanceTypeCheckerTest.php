<?php

namespace Prezly\PropTypes\Tests\Checkers;

use DateTime;
use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\AnyTypeChecker;
use Prezly\PropTypes\Checkers\ChainableTypeChecker;
use Prezly\PropTypes\Checkers\InstanceTypeChecker;
use Prezly\PropTypes\Checkers\TypeChecker;

class InstanceTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param string $class
     * @param mixed $value
     */
    public function it_should_pass_value_of_matching_class(string $class, $value)
    {
        $error = (new InstanceTypeChecker($class))->validate(['value' => $value], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param string $expected_class
     * @param mixed $value
     * @param string $actual_type
     */
    public function it_should_return_error_for_value_of_mismatching_class(
        string $expected_class,
        $value,
        string $actual_type
    ) {
        $error = (new InstanceTypeChecker($expected_class))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertEquals('value', $error->getPropName());
        $this->assertEquals(
            "Invalid property `test.value` of type `{$actual_type}` supplied, expected instance of `{$expected_class}`.",
            $error->getMessage()
        );
    }

    public function valid_examples(): iterable
    {
        return [
            self::class     => [self::class, $this],
            TestCase::class => [TestCase::class, $this],
            DateTime::class => [DateTime::class, new DateTime('now')],
        ];
    }

    public function invalid_examples(): iterable
    {
        return [
            DateTime::class    => [DateTime::class, $this, self::class],
            TypeChecker::class => [ChainableTypeChecker::class, new AnyTypeChecker(), AnyTypeChecker::class],
            'string'           => [self::class, 'Elvis', 'string'],
        ];
    }
}
