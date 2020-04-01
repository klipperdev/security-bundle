<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\SecurityBundle\Tests\Fixtures;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class TestProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('identity', static function ($input) {
                return $input;
            }, static function (array $values, $input) {
                return $input;
            }),

            ExpressionFunction::fromPhp('strtoupper'),

            ExpressionFunction::fromPhp('\strtolower'),

            ExpressionFunction::fromPhp('Klipper\Bundle\SecurityBundle\Tests\Fixtures\fn_namespaced', 'fn_namespaced'),
        ];
    }
}

function fn_namespaced()
{
    return true;
}

