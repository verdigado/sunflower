<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/functions',
        __DIR__ . '/inc',
        __DIR__ . '/template-parts',
        __DIR__ . '/*.php',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(php82: true)
    ->withPreparedSets(codeQuality: true, codingStyle: true, naming: true, earlyReturn: true, privatization: true)
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
