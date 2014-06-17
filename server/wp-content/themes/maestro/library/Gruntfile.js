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

    /*grunticon: {

      myIcons: {

          files: [{
              expand: true,
              cwd: 'images/origin',
              src: ['*.svg', '*.png'],
              dest: "images/sprite"
          }],

          options: {
            
          }
      }

    },*/

    datauri: {

      options: {

        colors: {      // a color mapping object that will map
                     // files named with the following scheme
                     // `truck.colors-red-green.svg` into separate datauri vars.
          red: '#00ffff',
          green: '#ff00ff'

        }

      },

      myicons: {

        files: {

         'scss/modules/_datauri_variables.scss' : 'images/origin/*.{png,jpg,gif,svg}'

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

      icon: {

        files: ['images/origin/*.{png,jpg,gif,svg}'],
        tasks: ['datauri']

      },

      options: {
        spawn: true,
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

  // datauri
  grunt.loadNpmTasks('grunt-datauri-variables');

  // Grunticon
  //grunt.loadNpmTasks('grunt-grunticon');

  // Default task(s).
  grunt.registerTask('default', ['uglify','sass','datauri','watch']);

};