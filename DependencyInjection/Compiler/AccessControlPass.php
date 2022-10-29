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
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class AccessControlPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private static array $availableExpressionNames = [
        'token', 'user', 'object', 'roles', 'request', 'trust_resolver',
    ];

    /**
     * @var Reference[]
     */
    private array $requestMatchers = [];

    /**
     * @var Reference[]
     */
    private array $expressions = [];

    private ?ExpressionLanguage $expressionLanguage = null;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('klipper_security.access_control')) {
            return;
        }

        $accesses = $container->getParameter('klipper_security.access_control');
        $this->createAuthorization($container, $accesses);
        $container->getParameterBag()->remove('klipper_security.access_control');
    }

    /**
     * Create the authorization.
     *
     * @param ContainerBuilder $container The container
     * @param array            $accesses  The control accesses
     */
    private function createAuthorization(ContainerBuilder $container, array $accesses): void
    {
        foreach ($accesses as $access) {
            $matcher = $this->createRequestMatcher(
                $container,
                $access['path'],
                $access['host'],
                $access['methods'],
                $access['ips']
            );

            $attributes = $access['roles'];

            if ($access['allow_if']) {
                $attributes[] = $this->createExpression($container, $access['allow_if']);
            }

            $container->getDefinition('security.access_map')
                ->addMethodCall('add', [$matcher, $attributes, $access['requires_channel']])
            ;
        }
    }

    /**
     * Create the request matcher.
     *
     * @param ContainerBuilder     $container  The container
     * @param null|string          $path       Tha path
     * @param null|string          $host       The host
     * @param array                $methods    The request methods
     * @param null|string|string[] $ips        The client ip
     * @param array                $attributes The attributes
     */
    private function createRequestMatcher(
        ContainerBuilder $container,
        ?string $path = null,
        ?string $host = null,
        array $methods = [],
        $ips = null,
        array $attributes = []
    ): Reference {
        if (!empty($methods)) {
            $methods = array_map('strtoupper', $methods);
        }

        $serialized = serialize([$path, $host, $methods, $ips, $attributes]);
        $id = 'security.request_matcher.'.md5($serialized).sha1($serialized);

        if (isset($this->requestMatchers[$id])) {
            return $this->requestMatchers[$id];
        }

        // only add arguments that are necessary
        $arguments = [$path, $host, $methods, $ips, $attributes];
        while (\count($arguments) > 0 && !end($arguments)) {
            array_pop($arguments);
        }

        $container
            ->register($id, RequestMatcher::class)
            ->setPublic(false)
            ->setArguments($arguments)
        ;

        return $this->requestMatchers[$id] = new Reference($id);
    }

    /**
     * Create the expression.
     *
     * @param ContainerBuilder $container  The container
     * @param string           $expression The expression
     */
    private function createExpression(ContainerBuilder $container, string $expression): Reference
    {
        if (isset($this->expressions[$id = 'security.expression.'.sha1($expression)])) {
            return $this->expressions[$id];
        }

        $container
            ->register($id, SerializedParsedExpression::class)
            ->setPublic(false)
            ->addArgument($expression)
            ->addArgument(serialize(
                $this->getExpressionLanguage($container)->parse(
                    $expression,
                    self::$availableExpressionNames
                )->getNodes()
            ))
        ;

        return $this->expressions[$id] = new Reference($id);
    }

    /**
     * Get the expression language.
     *
     * @param ContainerBuilder $container The container
     */
    private function getExpressionLanguage(ContainerBuilder $container): ExpressionLanguage
    {
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage(
                null,
                $this->getExpressionFunctions($container)
            );
        }

        return $this->expressionLanguage;
    }

    /**
     * Get the expression function providers of expression language.
     *
     * @param ContainerBuilder $container The container
     *
     * @return ExpressionFunctionProviderInterface[]
     *
     * @throws
     */
    private function getExpressionFunctions(ContainerBuilder $container): array
    {
        $providers = [];
        $services = $container->findTaggedServiceIds('security.expression_language_provider');

        foreach ($services as $id => $attributes) {
            $def = $container->getDefinition($id);
            $ref = new \ReflectionClass($def->getClass());
            $providers[] = $ref->newInstance();
        }

        return $providers;
    }
}
