var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	minifycss = require('gulp-minify-css'),
	rename = require('gulp-rename'),
	spritesmith = require('gulp.spritesmith'),
	imagemin = require('gulp-imagemin'),
	browserSync = require('browser-sync'),
	reload      = browserSync.reload,
	uglify = require('gulp-uglify'),
	sourcemaps = require('gulp-sourcemaps'),
	minimist = require('minimist'),
	themePath = '../wp-content/themes/maestro',
	libPath = '../wp-content/themes/maestro/library',
	knownOptions = {
	  string: 'env'
	};
	options = minimist(process.argv.slice(2), knownOptions);


gulp.task('sass', function() {

	/* SASS task */
	gulp.src(libPath+'/scss/*.scss')
		.pipe(sourcemaps.init())
    	.pipe(sass({ style: 'expanded' }))
    	.pipe(autoprefixer('last 2 version'))
    	.pipe(gulp.dest(libPath+'/css'))
	    .pipe(minifycss())
	    .pipe(sourcemaps.write())
	    .pipe(gulp.dest(libPath+'/css'))
	    .pipe(reload({stream: true}))
	    
});

gulp.task('uglify', function() {
	
	/* JS task */
	gulp.src(libPath+'/js/*.js')
    	.pipe(uglify())
    	.pipe(rename({suffix: '.min'}))
    	.pipe(gulp.dest(libPath+'/js/min/'))
    	
});

gulp.task('sprite', function() {
    
	/* SPRITE task */
	var spriteData = gulp.src(libPath+'/images/origin/*.*')
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

	browserSync({
        proxy: options.env,
        browser: ["google chrome", "firefox"]
    });

});

gulp.task('watch', function() {
	 
	/* WATCH task */
	 gulp.watch(libPath+'/scss/**/*.scss', ['sass']);
	 gulp.watch(libPath+'/js/*.js', ['uglify', browserSync.reload]);
	 gulp.watch(libPath+'/images/origin/*.*', ['sprite']).on('change', browserSync.reload);
	 gulp.watch(themePath+'/**/*.php').on('change', browserSync.reload);

});

gulp.task('default', ['sass', 'uglify', 'sprite', 'browser-sync', 'watch'], function() {

});


