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
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Abstract loader compiler pass.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class AbstractLoaderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private string $serviceId;

    private string $tagName;

    private int $argumentPosition;

    /**
     * @param string $serviceId        The service id
     * @param string $tagName          The tag name
     * @param int    $argumentPosition The argument position
     */
    public function __construct(string $serviceId, string $tagName, int $argumentPosition = 0)
    {
        $this->serviceId = $serviceId;
        $this->tagName = $tagName;
        $this->argumentPosition = $argumentPosition;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->serviceId)) {
            return;
        }

        $loaders = [];

        foreach ($this->findAndSortTaggedServices($this->tagName, $container) as $service) {
            $loaders[] = $service;
        }

        $container->getDefinition($this->serviceId)->replaceArgument($this->argumentPosition, $loaders);
    }
}
