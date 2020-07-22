<?php

namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\EqualityTypeChecker;

final class EqualityTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param mixed $expected_value
     * @param mixed $value
     */
    public function it_should_pass_valid_values($expected_value, $value = null)
    {
        $value = $value ?? $expected_value;

        $error = (new EqualityTypeChecker($expected_value))->validate(['value' => $value], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param  mixed  $expected_value
     * @param  mixed  $value
     * @param  string  $expected_error
     */
    public function it_should_return_error_for_invalid_values($expected_value, $value, string $expected_error)
    {
        $error = (new EqualityTypeChecker($expected_value))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertSame('value', $error->getPropName());
        $this->assertSame($expected_error, $error->getMessage());
    }

    public function valid_examples(): iterable
    {
        yield 'a null' => [null];
        yield 'a string' => ['c'];
        yield 'a number' => [10];
        yield 'an instance' => [$this];
        yield 'a bool' => [true];
        yield 'an array' => [[1, 2]];
        yield 'an object' => [(object) ['name' => 'Elvis'], (object) ['name' => 'Elvis']];
    }

    public function invalid_examples(): iterable
    {
        yield 'a string' => [
            'a',
            'd',
            'Invalid property `test.value` of value `"d"` supplied, expected: "a".',
        ];
        yield 'a number' => [
            1,
            5,
            'Invalid property `test.value` of value `5` supplied, expected: 1.',
        ];
        yield 'an instance' => [
            (object) ['name' => 'Elvis'],
            (object) ['name' => 'Melvis'],
            'Invalid property `test.value` of value `object {name: "Melvis"}` supplied, expected: object {name: "Elvis"}.',
        ];
        yield 'a bool' => [
            true,
            false,
            'Invalid property `test.value` of value `false` supplied, expected: true.',
        ];
        yield 'an array' => [
            [1, 2, 3],
            [1, 2, 4],
            'Invalid property `test.value` of value `[1, 2, 4]` supplied, expected: [1, 2, 3].',
        ];
        yield 'a resource' => [
            fopen('php://temp', 'r'),
            fopen('php://stdin', 'r'),
            'Invalid property `test.value` of value `resource` supplied, expected: resource.'
        ];
    }
}
