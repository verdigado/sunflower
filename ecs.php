<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/functions',
        __DIR__ . '/inc',
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
