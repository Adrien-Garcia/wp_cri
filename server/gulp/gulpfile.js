var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	minifycss = require('gulp-minify-css'),
	rename = require('gulp-rename'),
	spritesmith = require('gulp.spritesmith'),
	imagemin = require('gulp-imagemin'),
	uglify = require('gulp-uglify');

var sourcemaps = require('gulp-sourcemaps');

var libPath = '../wp-content/themes/maestro/library';

gulp.task('sass', function() {
	
	/* SASS task */
	gulp.src(libPath+'/scss/*.scss')
		.pipe(sourcemaps.init())
    	.pipe(sass({ style: 'expanded' }))
    	.pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
    	.pipe(gulp.dest(libPath+'/css'))
	    .pipe(minifycss())
	    .pipe(sourcemaps.write())
	    .pipe(gulp.dest(libPath+'/css'))
	    
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

gulp.task('watch', function() {
	 
	/* WATCH task */
	 gulp.watch(libPath+'/scss/**/*.scss', ['sass']);
	 gulp.watch(libPath+'/js/*.js', ['uglify']);
	 gulp.watch(libPath+'/images/origin/*.*', ['sprite']);

});

gulp.task('default', ['sass', 'uglify', 'sprite', 'watch'], function() {

});