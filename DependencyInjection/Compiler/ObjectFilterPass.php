<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "klipper_security.object_filter.voter" as arguments
 * of the "klipper_security.object_filter.extension" service.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ObjectFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('klipper_security.object_filter.extension')) {
            return;
        }

        $voters = [];
        foreach ($container->findTaggedServiceIds('klipper_security.object_filter.voter') as $id => $attributes) {
            $priority = $attributes[0]['priority'] ?? 0;
            $voters[$priority][] = new Reference($id);
        }

        // sort by priority and flatten
        if (\count($voters) > 0) {
            krsort($voters);
            $voters = array_merge(...$voters);
        }

        $container->getDefinition('klipper_security.object_filter.extension')->replaceArgument(0, $voters);
    }
}
