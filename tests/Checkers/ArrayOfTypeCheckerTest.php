<?php

namespace Prezly\PropTypes\Tests\Checkers;

use Closure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\AnyTypeChecker;
use Prezly\PropTypes\Checkers\ArrayOfTypeChecker;
use Prezly\PropTypes\Checkers\TypeChecker;

final class ArrayOfTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param mixed $value
     */
    public function it_should_pass_valid_values($value)
    {
        $error = (new ArrayOfTypeChecker(new AnyTypeChecker()))->validate(['value' => $value], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param mixed $value
     * @param string $actual_type
     */
    public function it_should_return_error_for_non_array_values($value, string $actual_type)
    {
        $error = (new ArrayOfTypeChecker(new AnyTypeChecker()))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertEquals('value', $error->getPropName());
        $this->assertEquals(
            "Invalid property `test.value` of type `{$actual_type}` supplied, expected an array.",
            $error->getMessage()
        );
    }

    /**
     * @test
     */
    public function it_should_delegate_array_individual_items_type_checks_to_nested_type_checker()
    {
        $checker = new ArrayOfTypeChecker($this->mockChecker((function (MockObject $mock) {
            $mock->expects(self::never())->method('validate');
        })));

        $error = $checker->validate(['value' => false], 'value', 'value');
        $this->assertNotNull($error);

        $checker = new ArrayOfTypeChecker($this->mockChecker((function (MockObject $mock) {
            $mock->expects(self::exactly(3))->method('validate')->withConsecutive(
                [['a', 'b', 'c'], '0', 'value[0]'],
                [['a', 'b', 'c'], '1', 'value[1]'],
                [['a', 'b', 'c'], '2', 'value[2]']
            )->willReturn(null);
        })));

        $error = $checker->validate(['value' => ['a', 'b', 'c']], 'value', 'value');
        $this->assertNull($error);
    }

    public function valid_examples(): iterable
    {
        return [
            'empty array'           => [[]],
            'array of numbers'      => [[1, 2, 3]],
            'array of strings'      => [['a', 'b', 'c']],
            'array of mixed values' => [[1, false, 'c', $this]],
        ];
    }

    public function invalid_examples(): iterable
    {
        return [
            'boolean instead of an array' => [false, 'boolean'],
            'string instead of an array'  => ['array', 'string'],
            'null instead of an array'    => [null, 'NULL'],
            'object instead of an array'  => [$this, 'object'],
        ];
    }

    private function mockChecker(Closure $configure = null): TypeChecker
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|TypeChecker $mock */
        $mock = $this->createMock(TypeChecker::class);

        if ($configure) {
            $configure($mock);
        }

        return $mock;
    }
}
