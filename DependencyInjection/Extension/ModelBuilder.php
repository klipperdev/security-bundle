<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\DependencyInjection\Extension;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ModelBuilder implements ExtensionBuilderInterface
{
    /**
     * @var string
     */
    private $alias;

    /**
     * Constructor.
     *
     * @param string $alias The security extension alias
     */
    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if ('custom' !== $config['db_driver']) {
            $container->setParameter($this->alias.'.backend_type_'.$config['db_driver'], true);
        }
    }
}
