/**
 * ESLint flat config (replaces .eslintrc.js / .eslintignore).
 *
 * `wp-scripts lint-js` only picks up a project config when it is a flat
 * config file; with an eslintrc file it silently falls back to the default
 * config shipped with @wordpress/scripts.
 */
const globals = require( 'globals' );
const wpPlugin = require( '@wordpress/eslint-plugin' );
const commentsPlugin = require( '@eslint-community/eslint-plugin-eslint-comments' );

module.exports = [
	{
		ignores: [ '**/build/**', '**/node_modules/**', '**/vendor/**' ],
	},

	...wpPlugin.configs.recommended,

	{
		plugins: {
			'@eslint-community/eslint-comments': commentsPlugin,
		},
		rules: {
			...commentsPlugin.configs.recommended.rules,
		},
	},

	{
		languageOptions: {
			globals: {
				...globals.browser,
				// Use `window.wp` instead of the bare global.
				wp: 'off',
			},
			// No Babel config in the project: parse JSX/ESNext with the
			// WordPress preset, same as the @wordpress/scripts default config.
			parserOptions: {
				requireConfigFile: false,
				babelOptions: {
					presets: [
						require.resolve( '@wordpress/babel-preset-default' ),
					],
				},
			},
		},
		settings: {
			jsdoc: {
				mode: 'typescript',
			},
		},
		rules: {
			'react/jsx-boolean-value': 'error',
			'@wordpress/wp-global-usage': 'error',
			'@wordpress/react-no-unsafe-timeout': 'error',
			'@wordpress/no-unsafe-wp-apis': 'off',
			'@wordpress/data-no-store-string-literals': 'error',
			'import/default': 'error',
			'import/named': 'error',
		},
	},

	{
		// The @typescript-eslint plugin is only registered for TS files.
		files: [ '**/*.ts', '**/*.tsx', '**/*.mts', '**/*.cts' ],
		rules: {
			'@typescript-eslint/no-restricted-imports': [
				'error',
				{
					paths: [
						{
							name: 'react',
							message:
								'Please use React API through `@wordpress/element` instead.',
							allowTypeImports: true,
						},
					],
				},
			],
			'@typescript-eslint/consistent-type-imports': [
				'error',
				{
					prefer: 'type-imports',
					disallowTypeAnnotations: false,
				},
			],
		},
	},
];
