<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/functions',
        __DIR__ . '/inc',
        __DIR__ . '/template-parts',
        __DIR__ . '/',
    ])

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
        ArraySyntaxFixer::class,
        ArrayListItemNewlineFixer::class,
    ])
    ->withPreparedSets(
        spaces: true,
        arrays: true,
        namespaces: true,
        comments: true,
        docblocks: true,
        psr12: true,
        // common: true
    )
;
