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

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\ObjectFilterPass;
use Klipper\Component\Security\ObjectFilter\MixedValue;
use Klipper\Component\Security\ObjectFilter\ObjectFilterExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Object Filter Pass tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class ObjectFilterPassTest extends TestCase
{
    public function testProcessWithoutExtension(): void
    {
        $container = new ContainerBuilder();
        $compiler = new ObjectFilterPass();

        static::assertCount(1, $container->getDefinitions());
        $compiler->process($container);
        static::assertCount(1, $container->getDefinitions());
    }

    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $compiler = new ObjectFilterPass();

        $def = new Definition(ObjectFilterExtension::class);
        $def->setArguments([
            [],
        ]);
        $def->setProperty('container', $container);
        $container->setDefinition('klipper_security.object_filter.extension', $def);

        $defVoter = new Definition(MixedValue::class);
        $defVoter->addTag('klipper_security.object_filter.voter');
        $container->setDefinition('klipper_security.object_filter.voter.mixed', $defVoter);

        $compiler->process($container);
        static::assertCount(3, $container->getDefinitions());
        static::assertCount(1, $def->getArgument(0));
    }
}
