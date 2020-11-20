<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude('bootstrap/')
    ->exclude('laradock/')
    ->exclude('nova/')
    ->exclude('public/')
    ->exclude('resources/')
    ->exclude('storage/')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PhpCsFixer'  => true,
            '@PSR2'        => true,
            'array_syntax' => ['syntax' => 'short'],
        ]
    )
    ->setFinder($finder);
