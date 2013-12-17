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
        compass: {
          files: ['app/webroot/sass/*.scss'],
          tasks: ['compass']
        },
        livereload: {
          options: { livereload: true },
          files: ['app/webroot/css/*.css', 'app/View/**']
        }
    },


  });

  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

};