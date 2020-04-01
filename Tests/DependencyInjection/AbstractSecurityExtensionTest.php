<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\DependencyInjection;

use Klipper\Bundle\SecurityBundle\DependencyInjection\KlipperSecurityExtension;
use Klipper\Bundle\SecurityBundle\KlipperSecurityBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Base for security extension tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
abstract class AbstractSecurityExtensionTest extends TestCase
{
    /**
     * Create container.
     *
     * @param array $configs    The configs
     * @param array $parameters The container parameters
     * @param array $services   The service definitions
     *
     * @throws
     */
    protected function createContainer(array $configs = [], array $parameters = [], array $services = []): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.bundles' => [
                'FrameworkBundle' => FrameworkBundle::class,
                'SecurityBundle' => SecurityBundle::class,
                'KlipperSecurityBundle' => KlipperSecurityBundle::class,
            ],
            'kernel.bundles_metadata' => [],
            'kernel.cache_dir' => sys_get_temp_dir().'/klipper_security_bundle',
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => sys_get_temp_dir().'/klipper_security_bundle',
            'kernel.project_dir' => sys_get_temp_dir().'/klipper_security_bundle',
            'kernel.charset' => 'UTF-8',
        ]));

        $container->setParameter('doctrine.default_entity_manager', 'test');
        $container->setDefinition('doctrine.orm.test_metadata_driver', new Definition(\stdClass::class));

        $sfExt = new FrameworkExtension();
        $sfSecurityExt = new SecurityExtension();
        $extension = new KlipperSecurityExtension();

        $container->registerExtension($sfExt);
        $container->registerExtension($sfSecurityExt);
        $container->registerExtension($extension);

        foreach ($parameters as $name => $value) {
            $container->setParameter($name, $value);
        }

        foreach ($services as $id => $definition) {
            $container->setDefinition($id, $definition);
        }

        $sfExt->load([['form' => true]], $container);
        $extension->load($configs, $container);

        $bundle = new KlipperSecurityBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
