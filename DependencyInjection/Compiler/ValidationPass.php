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

use Klipper\Component\Security\PermissionContexts;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Configure the validation service.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('validator.builder')) {
            return;
        }

        $xmlMappings = $this->getValidatorMappingFiles($container);

        if (\count($xmlMappings) > 0) {
            $container->getDefinition('validator.builder')
                ->addMethodCall('addXmlMappings', [$xmlMappings])
            ;
        }
    }

    /**
     * Get the validator mapping files.
     *
     * @param ContainerBuilder $container The container
     *
     * @throws
     *
     * @return string[]
     */
    private function getValidatorMappingFiles(ContainerBuilder $container): array
    {
        $files = [];

        $reflection = new \ReflectionClass(PermissionContexts::class);
        $dirname = \dirname($reflection->getFileName());

        if (is_dir($dir = $dirname.'/Resources/config/validation')) {
            $foundFiles = Finder::create()->files()->in($dir)->name('*.xml')->getIterator();

            foreach ($foundFiles as $file) {
                $files[] = realpath($file->getPathname());
            }

            $container->addResource(new DirectoryResource($dir));
        }

        return $files;
    }
}
