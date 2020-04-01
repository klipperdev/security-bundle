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

use Doctrine\Common\Annotations\Reader;
use Klipper\Bundle\SecurityBundle\DependencyInjection\KlipperSecurityExtension;
use Klipper\Component\Security\Permission\Loader\AnnotationLoader as PermissionAnnotationLoader;
use Klipper\Component\Security\Sharing\Loader\IdentityAnnotationLoader as SharingIdentityAnnotationLoader;
use Klipper\Component\Security\Sharing\Loader\SubjectAnnotationLoader as SharingSubjectAnnotationLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class AnnotationBuilder implements ExtensionBuilderInterface
{
    /**
     * @var KlipperSecurityExtension
     */
    private $ext;

    /**
     * Constructor.
     *
     * @param KlipperSecurityExtension $extension The security extension
     */
    public function __construct(KlipperSecurityExtension $extension)
    {
        $this->ext = $extension;
    }

    /**
     * {@inheritdoc}
     *
     * @throws
     */
    public function build(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        if (interface_exists(Reader::class) && class_exists(Finder::class)) {
            $resourcesDef = $container->getDefinition('klipper_security.permission.array_resource');

            foreach ($config['annotations']['include_paths'] as $path) {
                $resourcesDef->addMethodCall('add', [$path, 'annotation']);
            }

            if ($config['annotations']['permissions']) {
                $loader->load('annotation_permission.xml');

                $this->ext->addAnnotatedClassesToCompile([
                    PermissionAnnotationLoader::class,
                ]);
            }

            if ($config['annotations']['sharing']) {
                $loader->load('annotation_sharing.xml');

                $this->ext->addAnnotatedClassesToCompile([
                    SharingIdentityAnnotationLoader::class,
                    SharingSubjectAnnotationLoader::class,
                ]);
            }
        }
    }
}
