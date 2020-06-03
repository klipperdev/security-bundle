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
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ExpressionVariableStoragePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('klipper_security.expression.variable_storage')) {
            return;
        }

        $variables = [];
        foreach ($container->findTaggedServiceIds('klipper_security.expression.variables') as $id => $tags) {
            foreach ($tags as $attributes) {
                foreach ($attributes as $name => $value) {
                    $value = $this->buildValue($container, $id, $value);

                    if (null !== $value) {
                        $variables[$name] = $value;
                    }
                }
            }
        }

        $container->getDefinition('klipper_security.expression.variable_storage')->replaceArgument(0, $variables);
    }

    /**
     * Build the value of the expression variables.
     *
     * @param ContainerBuilder $container The container
     * @param string           $serviceId The service id
     * @param mixed            $value     The value of expression variables
     *
     * @return mixed
     */
    private function buildValue(ContainerBuilder $container, string $serviceId, $value)
    {
        if (\is_string($value) && 0 === strpos($value, '@')) {
            $value = ltrim($value, '@');
            $optional = 0 === strpos($value, '?');
            $value = ltrim($value, '?');
            $hasDef = $container->hasDefinition($value) || $container->hasAlias($value);

            if (!$hasDef && !$optional) {
                throw new ServiceNotFoundException($value, $serviceId);
            }

            $value = $hasDef
                ? new Reference($value)
                : null;
        }

        return $value;
    }
}
