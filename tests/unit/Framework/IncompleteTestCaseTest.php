<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function array_shift;
use function sprintf;
use PHPUnit\Event\Facade;

final class IncompleteTestCaseTest extends TestCase
{
    public function testDefaults(): void
    {
        $testCase = new IncompleteTestCase(
            'Foo',
            'testThatBars'
        );

        $this->assertSame('', $testCase->getMessage());
    }

    public function testGetNameReturnsClassAndMethodName(): void
    {
        $className  = 'Foo';
        $methodName = 'testThatBars';

        $testCase = new IncompleteTestCase(
            $className,
            $methodName
        );

        $name = sprintf(
            '%s::%s',
            $className,
            $methodName
        );

        $this->assertSame($name, $testCase->getName());
    }

    public function testGetMessageReturnsMessage(): void
    {
        $message = 'Somehow incomplete, right?';

        $testCase = new IncompleteTestCase(
            'Foo',
            'testThatBars',
            $message
        );

        $this->assertSame($message, $testCase->getMessage());
    }

    public function testRunMarksTestAsIncomplete(): void
    {
        $className  = 'Foo';
        $methodName = 'testThatBars';
        $message    = 'Somehow incomplete, right?';

        $testCase = new IncompleteTestCase(
            $className,
            $methodName,
            $message
        );

        $result = new TestResult;

        Facade::suspend();
        $testCase->run($result);
        Facade::resume();

        $this->assertTrue($testCase->status()->isIncomplete());
        $this->assertSame(1, $result->notImplementedCount());

        $failures = $result->notImplemented();

        $failure = array_shift($failures);

        $name = sprintf(
            '%s::%s',
            $className,
            $methodName
        );

        $this->assertSame($name, $failure->getTestName());
        $this->assertSame($message, $failure->exceptionMessage());
    }
}
