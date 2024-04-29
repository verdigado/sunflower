<?php
/**
 * ESC configuration file
 *
 * @package sunflower
 */

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
	->withPaths(
		array(
			__DIR__ . '/functions',
			__DIR__ . '/inc',
			__DIR__ . '/template-parts',
			__DIR__ . '/',
		)
	)

	->withRules(
		array(
			NoUnusedImportsFixer::class,
			ArraySyntaxFixer::class,
			ArrayListItemNewlineFixer::class,
		)
	)
	->withPreparedSets(
		spaces: false,
		arrays: true,
		namespaces: true,
		comments: true,
		docblocks: true,
		psr12: true,
	);
