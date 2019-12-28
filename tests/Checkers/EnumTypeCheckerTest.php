<?php

namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\EnumTypeChecker;
use stdClass;

class EnumTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param array $expected_values
     * @param mixed $value
     */
    public function it_should_pass_valid_values(array $expected_values, $value)
    {
        $error = (new EnumTypeChecker($expected_values))->validate(['value' => $value], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param array $expected_values
     * @param mixed $value
     */
    public function it_should_return_error_for_invalid_values(array $expected_values, $value, string $expected_error)
    {
        $error = (new EnumTypeChecker($expected_values))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertSame('value', $error->getPropName());
        $this->assertSame($expected_error, $error->getMessage());
    }

    public function valid_examples(): iterable
    {
        yield 'one of strings' => [
            ['a', 'b', 'c'],
            'c',
        ];
        yield 'one of numbers' => [
            [1, 10, 100],
            10,
        ];
        yield 'one of instances' => [
            [(object) [], $this],
            $this,
        ];
        yield 'one of scalars' => [
            [true, false, null],
            false,
        ];
        yield 'one of arrays' => [
            [[1], [1, 2], [1, 2, 3]],
            [1, 2],
        ];
    }

    public function invalid_examples(): iterable
    {
        yield 'none of strings' => [
            ['a', 'b', 'c'],
            'd',
            'Invalid property `test.value` of value `"d"` supplied, expected one of: ["a", "b", "c"].',
        ];
        yield 'none of numbers' => [
            [1, 10, 100],
            5,
            'Invalid property `test.value` of value `5` supplied, expected one of: [1, 10, 100].',
        ];
        yield 'none of instances' => [
            [(object) [], $this],
            new stdClass(),
            'Invalid property `test.value` of value `object {}` supplied, expected one of: [object {}, instance of Prezly\PropTypes\Tests\Checkers\EnumTypeCheckerTest].',
        ];
        yield 'none of scalars' => [
            [true, false, null],
            0,
            'Invalid property `test.value` of value `0` supplied, expected one of: [true, false, null].',
        ];
        yield 'none of arrays' => [
            [[1], [1, 2], [1, 2, 3]],
            [1, 2, 4],
            'Invalid property `test.value` of value `[1, 2, 4]` supplied, expected one of: [[1], [1, 2], [1, 2, 3]].',
        ];
        yield 'none of resources' => [
            [fopen('php://temp', 'r')],
            $this,
            'Invalid property `test.value` of value `instance of Prezly\PropTypes\Tests\Checkers\EnumTypeCheckerTest` supplied, expected one of: [resource].'
        ];
    }
}
