<?php
namespace Checkers;

use Closure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\ChainableTypeChecker;
use Prezly\PropTypes\Checkers\TypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;

final class ChainableTypeCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_not_allow_prop_missing_by_default()
    {
        $checker = new ChainableTypeChecker($this->mockChecker(function (MockObject $mock) {
            $mock->expects(self::never())->method('validate');
        }));
        $error = $checker->validate(['name' => 'Elvis'], 'job', 'job');
        $this->assertNotNull($error);
        $this->assertEquals('job', $error->getPropName());
        $this->assertEquals("The property `job` is marked as required, but it's not defined.", $error->getMessage());
    }

    /**
     * @test
     */
    public function it_should_allow_prop_missing_if_configured_so()
    {
        $checker = (new ChainableTypeChecker(
            $this->mockChecker(function (MockObject $mock) {
                $mock->expects(self::never())->method('validate');
            })
        ))->isOptional();
        $error = $checker->validate(['name' => 'Elvis'], 'job', 'job');
        $this->assertNull($error);
    }

    /**
     * @test
     */
    public function it_should_not_allow_nulls_by_default()
    {
        $checker = new ChainableTypeChecker($this->mockChecker(function (MockObject $mock) {
            $mock->expects(self::any())
                ->method('validate')
                ->willReturn(new PropTypeException('name', 'Invalid'));
        }));
        $error = $checker->validate(['name' => 'Elvis', 'job' => null], 'job', 'job');
        $this->assertNotNull($error);
        $this->assertEquals('job', $error->getPropName());
        $this->assertEquals("The property `job` is marked as not-null, but its value is `null`.", $error->getMessage());
    }

    /**
     * @test
     */
    public function it_should_allow_nulls_if_configured_so()
    {
        $checker = (new ChainableTypeChecker($this->mockChecker(function (MockObject $mock) {
            $mock->expects(self::never())->method('validate');
        })))->isNullable();
        $error = $checker->validate(['name' => 'Elvis', 'job' => null], 'job', 'job');
        $this->assertNull($error);
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
