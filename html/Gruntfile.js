module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		sass: {
			options: {
				style: 'compressed',
				compass: true
			},
			all_files: {
				files: [{
					expand: true,
					cwd: 'scss',
					src: ['*.scss'],
					dest: 'css',
					ext: '.css'
				}]
			},
			one_file: {
				src: 'scss/' + grunt.option('filename') + '.scss',
				dest: 'css/' + grunt.option('filename') + '.css'
			}
		},
		autoprefixer: {
			all_files: {
				files: [{
					expand: true,
					cwd: 'css',
					src: ['*.css'],
					dest: 'css'
				}]
			},
			one_file: {
				src: 'css/' + grunt.option('filename') + '.css',
				dest: 'css/' + grunt.option('filename') + '.css'
			}
		},
		closureCompiler: {
			options: {
				compilerFile: 'c:\\Program Files (x86)\\Google Closure compiler\\compiler.jar'
			},
			root_files: {
				files: [{
					expand: true,
					cwd: 'js_source',
					src: '*.js',
					dest: 'js',
					ext: '.min.js'
				}]
			},
			contact: {
				src: 'js/contact.min.js',
				dest: 'js/contact.min.js'
			}
		},
		concat: {
			options: {
				separator: grunt.util.linefeed + ';'
			},
			contact: {
				src: ['js_source/contact/*.js'],
				dest: 'js/contact.min.js'
			}
		},
		imagemin: {
			all_files: {
				files: [{
					expand: true,
					cwd: 'images_source',
					src: ['**/*.{png,gif,jpg,jpeg}'],
					dest: 'images'
				}]
			}
		},
		copy: {
			non_optim_images: {
				files: [{
					expand: true,
					cwd: 'images_source',
					src: ['**/*.*', '!**/*.{png,gif,jpg,jpeg}'],
					dest: 'images'
				}]
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-closure-tools');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-copy');

	//helper tasks
	grunt.registerTask('js_contact', ['concat:contact', 'closureCompiler:contact']);
	grunt.registerTask('images', ['imagemin:all_files', 'copy:non_optim_images']);

	// Default task(s).
	grunt.registerTask('default', ['sass:all_files', 'autoprefixer:all_files', 'closureCompiler:root_files', 'js_contact', 'images']);
	grunt.registerTask('scss', ['sass:one_file', 'autoprefixer:one_file']);


};