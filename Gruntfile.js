// inspired by https://gist.github.com/jshawl/6225945
// Thanks @jshawl!

// now using grunt-sass to avoid Ruby dependency

module.exports = function( grunt ) {
	const sass = require( 'node-sass' );
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		sass: {
			// sass tasks
			dist: {
				files: {
					'dist/css/main.min.css': 'css/main.scss',
					'dist/css/admin.min.css': 'css/admin.scss',
				},
			},
			options: {
				implementation: sass,
				compass: true,
				style: 'expanded',
				sourceMap: true,
			},
		},
		postcss: {
			options: {
				map: true, // inline sourcemaps
				processors: [
					require( 'pixrem' )(), // add fallbacks for rem units
					require( 'autoprefixer' )( { grid: 'autoreplace' } ), // add vendor prefixes
					require( 'cssnano' )(), // minify the result
				],
			},
			dist: {
				src: 'dist/css/*.css',
			},
		},
		terser: {
			options: {
				sourceMap: true,
			},
			dist: {
				files: [
					{
						expand: true,
						src: '*.js',
						dest: 'dist/js',
						cwd: 'js',
						ext: '.min.js',
					},
				],
			},
		},
		phpcs: {
			application: {
				src: [ '*.php', 'inc/*.php', 'post-types/*.php', 'taxonomies/*.php', 'templates/*.php' ],
			},
			options: {
				bin: './vendor/squizlabs/php_codesniffer/bin/phpcs --runtime-set ignore_warnings_on_exit true',
				standard: 'phpcs.xml',
			},
		},
		stylelint: {
			src: [ 'css/*.scss', 'css/**/*.scss', 'css/*.css' ],
		},
		run: {
			stylelintfix: {
				cmd: 'npx',
				args: [ 'stylelint', 'css/*.scss', '--fix' ],
			},
			eslintfix: {
				cmd: 'eslint',
				args: [ 'js/*.js', '--fix' ],
			},
		},
		eslint: {
			gruntfile: {
				src: [ 'Gruntfile.js' ],
			},
			src: {
				src: [ 'js/*.js' ],
			},
		},
		watch: {
			css: {
				files: [ 'css/*.scss' ],
				tasks: [ 'sass', 'postcss', 'run:stylelintfix' ],
			},
			js: {
				files: [ 'js/*.js' ],
				tasks: [ 'terser', 'run:eslintfix' ],
			},
		},
	} );

	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-terser' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-stylelint' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( '@lodder/grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-run' );
	grunt.registerTask( 'init', [ 'string-replace', 'sass', 'postcss', 'terser' ] );
	grunt.registerTask( 'default', [ 'sass', 'postcss', 'terser', 'watch' ] );
	grunt.registerTask( 'preflight', [ 'sass', 'postcss', 'terser', 'phpcs', 'stylelint', 'eslint' ] );
};
