module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),
    
    dirs: {
    	
    	library: '../wp-content/themes/maestro/library'
    	
    },

    uglify: { // javascript minification
      
      build: {

        files: {
        	'<%= dirs.library %>/js/min/scripts.min.js': ['<%= dirs.library %>/js/scripts.js']
        }

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
            cwd: '<%= dirs.library %>/scss',
            src: ['*.scss','!_*'],
            dest: '<%= dirs.library %>/css',
            ext: '.css'
          }]

        }

    },

    image: {

      dynamic: {

        options: {
          pngquant: true,
          optipng: true,
          advpng: true,
          zopflipng: true,
          pngcrush: true,
          pngout: true,
          mozjpeg: true,
          jpegRecompress: true,
          jpegoptim: true,
          gifsicle: true,
          svgo: true
        },

        files: [{
          expand: true,
          cwd: '<%= dirs.library %>/images/origin/', 
          src: ['<%= dirs.library %>/**/*.{png,jpg,gif,svg}'],
          dest: '<%= dirs.library %>/images/origin/'
        }]

      }

    },

    datauri: {

      myicons: {

        files: {

         '<%= dirs.library %>/scss/modules/_datauri_variables.scss' : '<%= dirs.library %>/images/datauris/*.{png,jpg,gif,svg}'

        }

      }

    },
    
    sprite:{
    	
    	all: {
    		
    		src: '<%= dirs.library %>/images/origin/*.png',
    		dest: '<%= dirs.library %>/images/sprites/spritesheet.png',
    		imgPath: '../images/sprites/spritesheet.png',
    		destCss: '<%= dirs.library %>/scss/modules/_spritesheet.scss'
    			
	    }
    
    },

    watch: { // Watch task

      scripts: {

        files: ['<%= dirs.library %>/js/scripts.js'],
        tasks: ['uglify']

      },

      css: {

        files: ['<%= dirs.library %>/scss/**/*.scss'],
        tasks: ['sass']

      },

      icon: {

        files: ['<%= dirs.library %>/images/datauris/*.{png,jpg,gif,svg}'],
        tasks: ['datauri']

      },
      
      sprite: {

	      files: ['<%= dirs.library %>/images/origin/*.{png,jpg,gif,svg}'],
	      tasks: ['sprite']
	
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
  //grunt.loadNpmTasks('grunt-sass'); // voir https://github.com/sindresorhus/grunt-sass

  // Load image optims task
  grunt.loadNpmTasks('grunt-image');

  // Watch task
  grunt.loadNpmTasks('grunt-contrib-watch');

  // datauri
  grunt.loadNpmTasks('grunt-datauri-variables');
  
  // grunt-spritesmith
  grunt.loadNpmTasks('grunt-spritesmith');

  // Default task(s).
  grunt.registerTask('default', ['image', 'datauri', 'sprite', 'sass', 'uglify','watch']);

};