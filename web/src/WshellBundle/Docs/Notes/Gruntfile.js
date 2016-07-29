module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // copy only non-modified files
        bowercopy: {
            options: {
                srcPrefix: 'bower_components',
                destPrefix: 'web/assets'
            },
            scripts: {
                files: {
                    'js/jquery.js':     'jquery/dist/jquery.min.js',
                    'js/bootstrap.js':  'bootstrap/dist/js/bootstrap.min.js',
                    'js/autobahn.js':   'autobahn/autobahn.min.js',
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css':    'bootstrap/dist/css/bootstrap.min.css',
                    'css/font-awesome.css': 'font-awesome/css/font-awesome.min.css',
                }
            },
            fonts: {
                files: {
                    'fonts': 'font-awesome/fonts'
                }
            }
        },

        cssmin : {
            all: {
                files: [{
                    expand: true,
                    cwd: 'src/Wshell/MainBundle/Resources/public/css',
                    src: '**/*.css',
                    dest: 'web/assets/WshellMainBundle/css'
                }]
            }
        },
        uglify : {
            all: {
                files: [{
                    expand: true,
                    cwd: 'src/Wshell/MainBundle/Resources/public/js',
                    src: '**/*.js',
                    dest: 'web/assets/WshellMainBundle/js'
                }]
            }
        },
        imagemin: {
            all: {
                files: [{
                    expand: true,
                    cwd: 'src/Wshell/MainBundle/Resources/public/img',
                    src: ['**/*.{png,jpg,gif,svg}'],
                    dest: 'web/assets/WshellMainBundle/img'
                }]
            }
        },

        watch: {
            options: { spawn: false },
            scripts: {
                files: ['src/Wshell/MainBundle/Resources/public/js/**/*.js'],
                tasks: ['uglify']
            },
            css: {
                files: ['src/Wshell/MainBundle/Resources/public/css/**/*.css'],
                tasks: ['cssmin']
            },
        }
    });

    // minified only changed file
    // grunt.event.on('watch', function(action, filepath, target) {
    //     if(target === 'scripts'){
    //         var path = 'src/Wshell/MainBundle/Resources/public/js/';
    //         var dest = grunt.config('uglify.all.dest') + '/' + filepath.replace(path, '');

    //         grunt.log.write(grunt.config('uglify.all.src'));
    //         grunt.log.write(grunt.config('uglify.all.dest'));

    //         grunt.config('uglify.all.src', filepath);
    //         grunt.config('uglify.all.dest', dest);
    //     }
    //     if(target === 'css'){
    //         var path = 'src/Wshell/MainBundle/Resources/public/css/';
    //         var dest = grunt.config.get('cssmin.all.dest') + filepath.replace(path, '');
    //         grunt.config('uglify.all.src', filepath);
    //         grunt.config('uglify.all.dest', dest);
    //     }
    // });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['bowercopy', 'cssmin', 'uglify', 'imagemin', 'watch']);
};
