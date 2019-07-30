<?php
namespace Prezly\PropTypes\Tests;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\TypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;
use Prezly\PropTypes\PropTypes;

class PropTypesTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_provide_type_checkers()
    {
        $this->assertInstanceOf(TypeChecker::class, PropTypes::any());
    }

    /**
     * @test
     * @dataProvider valid_data_examples
     * @param array $specs
     * @param array $values
     */
    public function it_should_silently_pass_valid_data(array $specs, array $values)
    {
        PropTypes::checkPropTypes($specs, $values);
        $this->assertTrue(true, "PropTypes::checkPropTypes didn't throw an exception");
    }

    /**
     * @test
     * @dataProvider invalid_data_examples
     * @param array $specs
     * @param array $values
     * @param string $expected_err_prop
     * @param string $expected_err_code
     * @param string $expected_err_message
     */
    public function it_should_throw_on_invalid_data(
        array $specs,
        array $values,
        string $expected_err_prop,
        string $expected_err_code,
        string $expected_err_message
    ) {
        try {
            PropTypes::checkPropTypes($specs, $values);
        } catch (PropTypeException $error) {
            $this->assertInstanceOf(PropTypeException::class, $error);
            $this->assertEquals($expected_err_prop, $error->getPropName());
            $this->assertEquals($expected_err_code, $error->getErrorCode());
            $this->assertEquals($expected_err_message, $error->getMessage());
            return;
        }

        $this->expectException(PropTypeException::class);
    }

    public function valid_data_examples(): iterable
    {
        yield 'any property: string' => [
            ['name' => PropTypes::any()],
            ['name' => 'Elvis Presley'],
        ];
        yield 'any property: null' => [
            ['name' => PropTypes::any()],
            ['name' => null],
        ];
        yield 'any property: missing' => [
            ['name' => PropTypes::any()],
            [],
        ];
    }

    public function invalid_data_examples(): iterable
    {
        yield 'extra property "occupation"' => [
            ['name' => PropTypes::any()],
            ['name' => 'Elvis Presley', 'occupation' => 'The King'],
            'occupation',
            'unexpected_extra_property',
            'Unexpected extra property `occupation` supplied.',
        ];
    }
}
