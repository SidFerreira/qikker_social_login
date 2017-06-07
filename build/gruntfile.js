// TODO: add autoprefixer and experiment with support settings (https://github.com/nDmitry/grunt-autoprefixer)
// TODO: create seperate watch task for both images and sprites
// TODO: create seperate watch task for single HTML files
// TODO: split up gruntfile into more managable chunks
// TODO: make filenames and task names more clear (needed after refactor)
// TODO: add sublime-workspace files to gitignore

module.exports = function(grunt) {

    // Used to generate timestamp
    var customhash = ((new Date()).valueOf().toString()) + (Math.floor((Math.random()*1000000)+1).toString());

    // Project paths
    var assetPath = '../assets';
    var projectPath = '../';

    // Needed to seperate and combine custom and JS files
    var vendor_js_files = require(assetPath + '/js/vendor.json');
    var custom_js_files = require(assetPath + '/js/custom.json');
    var production_js_files = vendor_js_files.concat(custom_js_files);

    // 1. All configuration goes here
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),
        assetPath: assetPath,
        projectPath: projectPath,
        customhash: customhash,

        // We have a funcion to clean caches and public folders
        clean: {

            icons: {

                src: ["<%= projectPath %>/public/icons/*/**"]

            },

            development: {

                src: [

                    "<%= projectPath %>/caches/*/**",
                    "<%= projectPath %>/public/css/**/*",
                    "!<%= projectPath %>/public/css/**/*.min.css",
                    "<%= projectPath %>/public/js/custom*",
                    "<%= projectPath %>/public/js/vendor*"

                ]

            },

            development_js: {

                src: ["<%= projectPath %>/caches/js/*/**", "<%= projectPath %>/public/js/custom*"]

            },

            development_js_vendor: {

                src: ["<%= projectPath %>/caches/js/*/**", "<%= projectPath %>/public/js/vendor*"]

            },

            development_css: {

                src: ["<%= projectPath %>/caches/css/*/**", "<%= projectPath %>/public/css/**/*", "!<%= projectPath %>/public/css/**/*.min.css"]

            },

            caches: {

                src: [

                    "<%= projectPath %>/caches/*/**"

                ]

            },

            production: {

                src: [

                    "<%= projectPath %>/caches/*/**",
                    "<%= projectPath %>/public/css/**/*.min.css",
                    "<%= projectPath %>/public/js/scripts*"

                ]

            },

            options: {

                force: true

            }

        },

        // Our SASS related tasks
        sass: {

            development: {

                options: {

                    sourceMap: true, // on development, we have sourcemaps enabled for easy debugging
                    sourceComments: false, // on development, we have extra code comments disabled for easy debugging
                    outputStyle: 'nested', // on development, we don't compress our code for easy debugging,
                    precision: 2,

                },

                files: {

                    '<%= projectPath %>/public/css/styles-<%= customhash %>.css': '<%= assetPath %>/sass/styles.scss',
                    '<%= projectPath %>/public/css/wp-login-<%= customhash %>.css': '<%= assetPath %>/sass/wp-login.scss',
                    '<%= projectPath %>/public/css/wp-admin-<%= customhash %>.css': '<%= assetPath %>/sass/wp-admin.scss',
                    '<%= projectPath %>/public/css/tinymce-<%= customhash %>.css': '<%= assetPath %>/sass/tinymce.scss'

                }

            },

            production: {

                options: {

                    sourceMap: false, // on production, we don't have sourcemaps because no need
                    sourceComments: true, // on development, we have extra code comments disabled because no need
                    outputStyle: 'compressed', // on production, we compress our code for speed
                    precision: 2

                },

                files: {

                    '<%= projectPath %>/public/css/styles-<%= customhash %>.min.css': '<%= assetPath %>/sass/styles.scss',
                    '<%= projectPath %>/public/css/wp-login-<%= customhash %>.min.css': '<%= assetPath %>/sass/wp-login.scss',
                    '<%= projectPath %>/public/css/wp-admin-<%= customhash %>.min.css': '<%= assetPath %>/sass/wp-admin.scss',
                    '<%= projectPath %>/public/css/tinymce-<%= customhash %>.min.css': '<%= assetPath %>/sass/tinymce.scss'

                }

            }

        },

        // We minify our (generated) JS and move them to the public folder
        uglify: {

            production: {

                expand: true,
                // Enable dynamic expansion.
                cwd: '<%= projectPath %>/caches/js/',
                // Src matches are relative to this path.
                src: ['*.js'],
                // Actual pattern(s) to match.
                dest: '<%= projectPath %>/public/js/',
                // Destination path prefix.
                ext: '.min.js',
                // Dest filepaths will have this extension.

            }

        },

        jshint: {

            both: {

                files: {

                    src: ['<%= assetPath %>/js/custom/**/*.js']

                },

                options: {

                    globals: {

                        jQuery: true,
                        console: true,
                        module: true

                    }

                }

            }

        },

        // translation
        pot: {

            options:{

                text_domain: 'qikker_theme',
                dest: '<%= projectPath %>/languages/',
                keywords: ['_e', '__'],

            },

            files:{

                src:  [ '<%= projectPath %>/**/*.php', '!<%= projectPath %>/build/**/*.php' ],
                expand: true,

            }

        },

        replace: {

            js_custom: {

                options: {

                    patterns: [

                        {

                            match: 'replace_js',
                            replacement: '<%= customhash %>'

                        }

                    ]

                },

                files: [

                    {expand: true, flatten: true, src: ['<%= assetPath %>/replace/assets-js.php'], dest: '<%= projectPath %>/includes/assets/'}

                ]

            },

            js_vendor: {

                options: {

                    patterns: [

                        {

                            match: 'replace_vendor',
                            replacement: '<%= customhash %>'

                        }

                    ]

                },

                files: [

                    {expand: true, flatten: true, src: ['<%= assetPath %>/replace/assets-js_vendor.php'], dest: '<%= projectPath %>/includes/assets/'}

                ]

            },

            js_both: {

                options: {

                    patterns: [

                        {

                            match: 'replace_js',
                            replacement: '<%= customhash %>'

                        }

                    ]

                },

                files: [

                    {expand: true, flatten: true, src: ['<%= assetPath %>/replace/assets-js_both.php'], dest: '<%= projectPath %>/includes/assets/'}

                ]

            },

            css_development: {

                options: {

                    patterns: [

                        {

                            match: 'replace_css',
                            replacement: '<%= customhash %>'

                        }

                    ]

                },

                files: [

                    {expand: true, flatten: true, src: ['<%= assetPath %>/replace/assets-css_development.php'], dest: '<%= projectPath %>/includes/assets/'}

                ]

            },

            css_production: {

                options: {

                    patterns: [

                        {

                            match: 'replace_css',
                            replacement: '<%= customhash %>'

                        }

                    ]

                },

                files: [

                    {expand: true, flatten: true, src: ['<%= assetPath %>/replace/assets-css_production.php'], dest: '<%= projectPath %>/functions/generated/'}

                ]

            }

        },

        // We watch our folders and if change happens the LESS/JS/IMG files get recompiled on the fly (also includes grunt-watch - install the browser plugin!)
        watch: {

            icons: {

                files: ['<%= assetPath %>/icons/**/*'],
                tasks: ['svg_sprite'],
                options: {

                    livereload: true,

                }

            },

            js_custom: {

                files: ['<%= assetPath %>/js/custom/**/*.js'],
                tasks: ['development-js'],
                options: {

                    livereload: true,

                }

            },

            js_vendor: {

                files: ['<%= assetPath %>/js/vendor/**/*.js'],
                tasks: ['development-js_vendor'],
                options: {

                    livereload: true,

                }

            },

            css: {

                files: ['<%= assetPath %>/sass/**/*.scss', '!<%= assetPath %>/sass/variables/sprites.scss'],
                tasks: ['development-css'],
                options: {

                    livereload: true,

                }

            },

            php: {

                files: ['<%= projectPath %>/**/**/*.php', '!<%= projectPath %>/functions/generated/*'],

                options: {

                    livereload: true,

                }

            },

        },

        concat : {

            development : {

                options : {

                    sourceMap :true

                },

                src  : custom_js_files,
                dest : '<%= projectPath %>/public/js/custom-<%= customhash %>.js'

            },

            development_vendor : {

                options : {

                    sourceMap :true

                },

                src  : vendor_js_files,
                dest : '<%= projectPath %>/public/js/vendor-<%= customhash %>.js'

            },

            production : {

                options : {

                    sourceMap : false

                },

                src  : production_js_files,
                dest : '<%= projectPath %>/caches/js/scripts-<%= customhash %>.js'

            }

        },

        modernizr: {

            dist: {

                "cache" : false,

                // Path to save out the built file
                "dest" : "<%= assetPath %>/js/vendor/modernizr/_modernizr.js",

                "files" : {

                    "src": [

                        "<%= assetPath %>/js/custom/**/*.js",
                        "<%= assetPath %>/sass/**/*.scss",
                        //'!<%= assetPath %>/sass/mixins/**/*',
                        '!<%= assetPath %>/sass/vendor/**/*'

                    ]

                },

                "options" : [

                    "setClasses",
                    "html5shiv"

                ],

            }

        },

        svg_sprite : {

            complex: {

                // Target basics
                expand : true,
                src : ['<%= assetPath %>/icons/*.svg'],
                dest : '<%= projectPath %>/public/icons/',

                // Target options
                options : {

                    shape : {

                        id : {

                            separator : '--',
                            generator : function(name) {

                                path = require('path');
                                name = path.basename(name, '.svg');
                                icon = "icon--" + name;

                                return icon;

                            },
                            pseudo : '~',
                            whitespace : '_'

                        },

                        dimension : {

                            // Set maximum dimensions
                            maxWidth : 32,
                            maxHeight : 32

                        },

                        spacing : {

                            // Add padding
                            padding : 0

                        }

                    },

                    mode : {

                        symbol : {

                            // Activate the «symbol» mode
                            bust : false,
                            sprite : '../icons.svg',
                            render      : {
                                scss    : false      // Activate Sass output (with default options)
                            },
                            svg : {

                                xmlDeclaration : false,
                                rootAttributes: {

                                    style : 'display: none;'

                                }

                            }

                        },

                    },

                }

            }

        }

    });

    // 2. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-replace');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-pot');
    grunt.loadNpmTasks("grunt-modernizr");
    grunt.loadNpmTasks('grunt-svg-sprite');

    // 3. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('development', [

        'clean:development',
        'svg_sprite',
        'sass:development',
        'modernizr',
        'jshint:both',
        'concat:development_vendor',
        'concat:development',
        'replace:js_custom',
        'replace:js_vendor',
        'replace:css_development',
        'clean:caches',
        'pot'

    ]);

    grunt.registerTask('development-js', [

        'clean:development_js',
        'jshint:both',
        'concat:development',
        'replace:js_custom',
        'clean:caches'

    ]);

    grunt.registerTask('development-js_vendor', [

        'clean:development_js_vendor',
        'jshint:both',
        'concat:development_vendor',
        'replace:js_vendor',
        'clean:caches'

    ]);

    grunt.registerTask('vendor', [

        'clean:development_js_vendor',
        'jshint:both',
        'concat:development_vendor',
        'replace:js_vendor',
        'clean:caches'

    ]);

    grunt.registerTask('development-css', [

        'clean:development_css',
        'sass:development',
        'replace:css_development',
        'clean:caches'

    ]);

    grunt.registerTask('production', [

        'clean:production',
        'copy:production',
        'svg_sprite',
        'sass:production',
        'modernizr',
        'jshint:both',
        'concat:production',
        'uglify:production',
        'replace:js_both',
        'replace:css_production',
        'clean:caches',
        'pot'

    ]);

    grunt.registerTask('default', 'watch');

};