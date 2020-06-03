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

use Klipper\Bundle\SecurityBundle\DependencyInjection\Compiler\TranslatorPass;
use Klipper\Component\Security\PermissionContexts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Translator Pass tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class TranslatorPassTest extends TestCase
{
    /**
     * @var ContainerBuilder|MockObject
     */
    protected $container;

    protected ?TranslatorPass $compiler = null;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $this->compiler = new TranslatorPass();
    }

    public function testProcessWithoutTranslator(): void
    {
        /** @var ContainerBuilder|MockObject $container */
        $container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $container->expects(static::once())
            ->method('hasDefinition')
            ->with('translator.default')
            ->willReturn(false)
        ;

        $this->compiler->process($container);
    }

    public function testProcess(): void
    {
        $reflection = new \ReflectionClass(PermissionContexts::class);
        $dirname = \dirname($reflection->getFileName());
        $file = realpath($dirname.'/Resources/config/translations/validators.en.xlf');

        static::assertFileExists($file);

        $translator = $this->getMockBuilder(Definition::class)->disableOriginalConstructor()->getMock();

        $this->container->expects(static::once())
            ->method('hasDefinition')
            ->with('translator.default')
            ->willReturn(true)
        ;

        $this->container->expects(static::once())
            ->method('getDefinition')
            ->with('translator.default')
            ->willReturn($translator)
        ;

        $translator->expects(static::once())
            ->method('getArguments')
            ->willReturn([null, null, [], []])
        ;

        $translator->expects(static::once())
            ->method('getArgument')
            ->with(3)
            ->willReturn([])
        ;

        $translator->expects(static::once())
            ->method('replaceArgument')
            ->with(3, [
                'resource_files' => [
                    'en' => [
                        $file,
                    ],
                ],
            ])
        ;

        $this->compiler->process($this->container);
    }
}
