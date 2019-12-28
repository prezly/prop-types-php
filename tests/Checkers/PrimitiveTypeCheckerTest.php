<?php

namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\PrimitiveTypeChecker;

class PrimitiveTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param string $type
     * @param mixed $value
     */
    public function it_should_pass_value_of_matching_primitive_type(string $type, $value)
    {
        $error = (new PrimitiveTypeChecker($type))->validate(['value' => $value], 'value', 'value');
        $this->assertNull($error);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param string $expected_type
     * @param mixed $value
     * @param string $actual_type
     */
    public function it_should_return_error_for_value_of_mismatching_primitive_type(
        string $expected_type,
        $value,
        string $actual_type
    ) {
        $error = (new PrimitiveTypeChecker($expected_type))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        $this->assertEquals('value', $error->getPropName());
        $this->assertEquals(
            "Invalid property `test.value` of type `{$actual_type}` supplied, expected `{$expected_type}`.",
            $error->getMessage()
        );
    }

    public function valid_examples(): iterable
    {
        return [
            'valid array'             => ['array', []],
            'valid boolean'           => ['boolean', false],
            'valid int (0)'           => ['integer', 0],
            'valid int (PHP_INT_MIN)' => ['integer', PHP_INT_MIN],
            'valid float (0.0)'       => ['double', 0.0],
            'valid float (INF)'       => ['double', INF],
            'valid object'            => ['object', $this],
            'valid string'            => ['string', 'Elvis'],
            'valid null'              => ['NULL', null],
        ];
    }

    public function invalid_examples(): iterable
    {
        return [
            'invalid array'               => ['array', 1, 'integer'],
            'invalid boolean'             => ['boolean', [], 'array'],
            'invalid int (0.5)'           => ['integer', 0.5, 'double'],
            'invalid int (INF)'           => ['integer', INF, 'double'],
            'invalid float (5)'           => ['double', 5, 'integer'],
            'invalid float (PHP_INT_MIN)' => ['double', true, 'boolean'],
            'invalid object'              => ['object', self::class, 'string'],
            'invalid string'              => ['string', $this, self::class],
        ];
    }
}
