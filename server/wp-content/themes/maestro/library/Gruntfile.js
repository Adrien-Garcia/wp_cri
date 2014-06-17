module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    uglify: { // javascript minification
      
      build: {
        src: 'js/scripts.js', //input
        dest: 'js/min/scripts.min.js' //output
      }

    },

    sass: { // SASS compilation

        dist: {

          options: {
            trace: true,
            style: 'compressed'
          },

          files: [{
            expand: true,
            cwd: 'scss/',
            src: ['*.scss','!_*'],
            dest: 'css/',
            ext: '.css'
          }]
        }

    },

    grunticon: {

      myIcons: {

          files: [{
              expand: true,
              cwd: 'images/origin',
              src: ['*.svg', '*.png'],
              dest: "images/sprite"
          }],

          options: {
            loadersnippet: "grunticon.loader.js"
          }
      }

    },

    watch: { // Watch task

      scripts: {

        files: ['js/scripts.js'],
        tasks: ['uglify']

      },

      css: {

        files: ['scss/**/*.scss'],
        tasks: ['sass']

      },

      another: {

        files: ['images/origin/*.*'],
        tasks: ['grunticon']

      },

      options: {
        spawn: false,
        event: ['changed','added','deleted'],
        livereload: 35729
      }

    }

  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Load SASS task
  grunt.loadNpmTasks('grunt-contrib-sass');

  // Watch task
  grunt.loadNpmTasks('grunt-contrib-watch');

  // GruntIcon
  grunt.loadNpmTasks('grunt-grunticon');

  // Default task(s).
  grunt.registerTask('default', ['uglify','sass','watch','grunticon:myIcons']);

};