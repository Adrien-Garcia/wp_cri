var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	minifycss = require('gulp-minify-css'),
	rename = require('gulp-rename'),
	concat = require('gulp-concat'),
	spritesmith = require('gulp.spritesmith'),
	imagemin = require('gulp-imagemin'),
	browserSync = require('browser-sync'),
	reload      = browserSync.reload,
	uglify = require('gulp-uglify'),
	sourcemaps = require('gulp-sourcemaps'),
	minimist = require('minimist'),
    plumber = require('gulp-plumber'),
	themePath = '../wp-content/themes/maestro',
	libPath = '../wp-content/themes/maestro/library',
	iconfont = require('gulp-iconfont'),
	consolidate = require('gulp-consolidate'),
	runTimestamp = Math.round(Date.now()/1000),
	knownOptions = {
	  string: 'env'
	};
	options = minimist(process.argv.slice(2), knownOptions);


gulp.task('sass', function() {

	/* SASS task */
	gulp.src(libPath+'/scss/*.scss')
		 .pipe(plumber())
			.pipe(sourcemaps.init())
    			.pipe(sass({outputStyle: 'nested', includePaths: ['./breakpoints']}))
    			//.pipe(autoprefixer('last 2 version'))
       //  		.pipe(minifycss())
	    	.pipe(sourcemaps.write())
    	.pipe(plumber.stop())
    .pipe(gulp.dest(libPath+'/css'))
    .pipe(browserSync.stream({match: "**/*.css"}));
	    
});
gulp.task('sass-build', function() {

	/* SASS task */
	gulp.src(libPath+'/scss/*.scss')
		 .pipe(plumber())
			.pipe(sass({outputStyle: 'compressed'}))
			.pipe(autoprefixer('last 2 version'))
       		// .pipe(minifycss())
    	 .pipe(plumber.stop())
    .pipe(gulp.dest(libPath+'/css'));
	    
});

gulp.task('iconfont', function () {
  return gulp.src([libPath+'/images/svgicons/*.svg'])
	.pipe(plumber())
    .pipe(iconfont({
      fontName: 'aux-font',
      normalize: true,
      fontHeight: 1001,
      prependUnicode: true,
      formats: ['ttf', 'eot', 'woff', 'svg', 'woff2'],
      timestamp: runTimestamp
    }))
    .pipe(plumber.stop())
    .on('glyphs', function (glyphs, options) {
        gulp.src(libPath+'/scss/templates/_icons.scss')
        .pipe(plumber())
        .pipe(consolidate('lodash', {
          glyphs: glyphs,
          fontName: 'aux-font',
          fontPath: '../fonts/svgfont/',
          className: 'icon'
        }))
        .pipe(plumber.stop())
        .pipe(gulp.dest(libPath+'/scss/modules'));
    })
    .pipe(gulp.dest(libPath+'/fonts/svgfont'));
});

gulp.task('uglify', function() {
	
	/* JS task */
	gulp.src(libPath+'/js/!(app).js')
        .pipe(plumber())
    	.pipe(uglify())
    	.pipe(rename({suffix: '.min'}))
        .pipe(plumber.stop())
    	.pipe(gulp.dest(libPath+'/js/min/'));
    	
    /* JS task */
	gulp.src([libPath+'/js/app.js', libPath+'/js/application/*.js'])
        .pipe(plumber())
        .pipe(concat('app.concat.js'))
    	//.pipe(uglify())
    	.pipe(rename("app.min.js"))
        .pipe(plumber.stop())
    	.pipe(gulp.dest(libPath+'/js/min/'));
});

gulp.task('sprite', function() {
    
	/* SPRITE task */
	var spriteData = gulp.src(libPath+'/images/origin/*.{png,jpg,gif}')
		.pipe(spritesmith({
			imgName: 'spritesheet.png',
			imgPath: '../images/sprites/spritesheet.png',
			cssName: '_spritesheet.scss'
		}));
	
	  	spriteData.img
	  		.pipe(imagemin())
	  		.pipe(gulp.dest(libPath+'/images/sprites/'));
	  				
	  	spriteData.css
	  		.pipe(gulp.dest(libPath+'/scss/modules/'));

});


gulp.task('browser-sync', function() {

	browserSync.init({
        proxy: options.env,
        browser: [],
        host: options.env,
        injectChanges: true,
        open: false
    });

});

gulp.task('copy', function() {
   gulp.src(libPath+'/bower_components/*/dist/*.min.js')
       .pipe(plumber())
       .pipe(gulp.dest(libPath+'/js/'));
});

gulp.task('watch', function() {
	 
	/* WATCH task */
     gulp.watch(libPath+'/images/origin/*.*', ['sprite']).on('change', browserSync.reload);
     gulp.watch(libPath+'/images/svgicons/*.*', ['iconfont']).on('change', browserSync.reload);
	 gulp.watch(libPath+'/scss/**/*.scss', ['sass']);
	 gulp.watch(libPath+'/js/**/*.js', ['uglify', browserSync.reload]);
	 gulp.watch(themePath+'/**/*.php').on('change', browserSync.reload);

});

gulp.task('default', ['copy','sprite','iconfont', 'sass', 'uglify','browser-sync', 'watch'], function() {});
gulp.task('build', ['copy','sprite', 'iconfont','sass-build', 'uglify',], function() {});


