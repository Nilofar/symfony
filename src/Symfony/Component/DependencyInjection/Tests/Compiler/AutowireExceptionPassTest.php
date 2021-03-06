<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\AutowireExceptionPass;
use Symfony\Component\DependencyInjection\Compiler\AutowirePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\AutowiringFailedException;

class AutowireExceptionPassTest extends TestCase
{
    public function testThrowsException()
    {
        $autowirePass = $this->getMockBuilder(AutowirePass::class)
            ->getMock();

        $autowireException = new AutowiringFailedException('foo_service_id', 'An autowiring exception message');
        $autowirePass->expects($this->any())
            ->method('getAutowiringExceptions')
            ->will($this->returnValue(array($autowireException)));

        $container = new ContainerBuilder();
        $container->register('foo_service_id');

        $pass = new AutowireExceptionPass($autowirePass);

        try {
            $pass->process($container);
            $this->fail('->process() should throw the exception if the service id exists');
        } catch (\Exception $e) {
            $this->assertSame($autowireException, $e);
        }
    }

    public function testNoExceptionIfServiceRemoved()
    {
        $autowirePass = $this->getMockBuilder(AutowirePass::class)
            ->getMock();

        $autowireException = new AutowiringFailedException('non_existent_service');
        $autowirePass->expects($this->any())
            ->method('getAutowiringExceptions')
            ->will($this->returnValue(array($autowireException)));

        $container = new ContainerBuilder();

        $pass = new AutowireExceptionPass($autowirePass);

        $pass->process($container);
        // mark the test as passed
        $this->assertTrue(true);
    }
}
