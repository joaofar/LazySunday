module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // UGLIFY
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      build: {
        src: 'app/webroot/js/lazyfoot.js',
        dest: 'app/webroot/js/build/lazyfoot.min.js'
      }
    },

    // COMPASS
    compass: {
        dist: {
            options: {
                config: 'config.rb'
            }
        }
    },

    //WATCH
    watch: {
        app: {
          options: { livereload: true },
          files: ['app/Model/**', 'app/View/**', 'app/Controller/**']
        },
        compass: {
          files: ['app/webroot/sass/*.scss'],
          tasks: ['compass']
        },
        css: {
          options: { livereload: true },
          files: ['app/webroot/css/*.css']
        },
        js: {
          options: { livereload: true },
          files: ['app/webroot/js/*.js']
        }
    },


  });

  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['watch']);

};