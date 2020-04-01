<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\DependencyInjection\Compiler;

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ExpressionVariableStoragePass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Expression Variable Storage Pass Test.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class ExpressionVariableStoragePassTest extends TestCase
{
    public function testProcessWithoutExtension(): void
    {
        $container = new ContainerBuilder();
        $compiler = new ExpressionVariableStoragePass();

        static::assertCount(1, $container->getDefinitions());
        $compiler->process($container);
        static::assertCount(1, $container->getDefinitions());
    }
}
