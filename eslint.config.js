const wpPlugin = require( '@wordpress/eslint-plugin' );
const globals = require( 'globals' );

module.exports = [
	{
		ignores: [ 'js/**.min.js', 'dist/**/*.js' ],
	},
	{
		plugins: {
			'@wordpress': wpPlugin,
		},
		linterOptions: {
			reportUnusedDisableDirectives: false,
		},
		languageOptions: {
			globals: {
				...globals.browser,
				...globals.node,
				jQuery: 'readonly',
				$: 'readonly',
			},
		},
		rules: {
			...wpPlugin.configs.recommended.rules,
			'max-len': [ 'error', { code: 140 } ],
			'space-in-parens': [ 'error', 'always' ],
			'array-bracket-spacing': [ 'error', 'always' ],
			'computed-property-spacing': [ 'error', 'always' ],
			'space-before-function-paren': [ 'error', 'never' ],
			'space-unary-ops': [ 'error', { words: true, nonwords: false, overrides: { '!': true } } ],
			'newline-per-chained-call': [ 'error', { ignoreChainWithDepth: 2 } ],
			'dot-location': [ 'error', 'property' ],
			'prettier/prettier': 'off',
			'no-console': 'warn',
			'camelcase': 'warn',
			'indent': [ 'warn', 'tab', { SwitchCase: 1 } ],
			'no-mixed-spaces-and-tabs': 'warn',
		},
	},
];
