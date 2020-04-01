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

use Klipper\Bundle\SecurityBundle\DependencyInjection\AccessControlConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Access Control Tests.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class AccessControlConfigurationTest extends TestCase
{
    public function testNoConfig(): void
    {
        $config = [];

        $processor = new Processor();
        $configuration = new AccessControlConfiguration();
        static::assertCount(1, $processor->processConfiguration($configuration, [$config]));
    }

    public function testConfig(): void
    {
        $config = [
            'access_control' => [
                [
                    'ips' => '127.0.0.1',
                    'methods' => 'GET,POST',
                    'roles' => 'ROLE_USER,ROLE_ADMIN',
                ],
            ],
        ];
        $validConfig = [
            'access_control' => [
                [
                    'methods' => ['GET', 'POST'],
                    'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                    'ips' => ['127.0.0.1'],
                    'requires_channel' => null,
                    'path' => null,
                    'host' => null,
                    'allow_if' => null,
                ],
            ],
        ];

        $processor = new Processor();
        $configuration = new AccessControlConfiguration();
        $fConfig = $processor->processConfiguration($configuration, [$config]);

        static::assertEquals($validConfig, $fConfig);
    }
}
