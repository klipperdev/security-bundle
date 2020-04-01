<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class NodeUtils
{
    /**
     * Create an array node.
     *
     * @param string           $name    The name of the root node
     * @param null|NodeBuilder $builder The node builder
     */
    public static function createArrayNode(string $name, NodeBuilder $builder = null): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder($name, 'array', $builder);
        /* @var ArrayNodeDefinition $node */
        return $treeBuilder->getRootNode();
    }
}
