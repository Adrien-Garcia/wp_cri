var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	rename = require('gulp-rename'),
	spritesmith = require('gulp.spritesmith'),
	imagemin = require('gulp-imagemin'),
	browserSync = require('browser-sync'),
	reload      = browserSync.reload,
	uglify = require('gulp-uglify'),
	sourcemaps = require('gulp-sourcemaps'),
	minimist = require('minimist'),
    plumber = require('gulp-plumber'),
    concat = require('gulp-concat'),
	themePath = '../wp-content/themes/maestro',
	libPath = '../wp-content/themes/maestro/library',
	knownOptions = {
	  string: 'env',
	  string: 'nav'
	};
	options = minimist(process.argv.slice(2), knownOptions);


gulp.task('sass', function() {

	/* SASS task */
	gulp.src(libPath+'/scss/*.scss')
		.pipe(sourcemaps.init())
        .pipe(plumber())
    	.pipe(sass({ style: 'compressed' }))
    	.pipe(autoprefixer('last 2 version'))
        .pipe(plumber.stop())
    	.pipe(gulp.dest(libPath+'/css'))
	    .pipe(sourcemaps.write())
	    .pipe(gulp.dest(libPath+'/css'))
	    .pipe(reload({stream: true}))

});
 											// PROD VERSION
											gulp.task('sass-prod', function() {
												
												gulp.src(libPath+'/scss/*.scss')
													
											        .pipe(plumber())
											    	.pipe(sass({ style: 'compressed' }))
											    	.pipe(autoprefixer('last 2 version'))
											        .pipe(plumber.stop())
											    	.pipe(gulp.dest(libPath+'/css'))
												    //.pipe(minifycss())
											
											});

gulp.task('uglify', function() {

	/* JS task */
	gulp.src(libPath+'/js/*.js')
    	.pipe(uglify())
    	.pipe(rename({suffix: '.min'}))
    	.pipe(gulp.dest(libPath+'/js/min/'))
    	//.pipe(minifycss())
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

	browserSync({
        proxy: options.env,
        host:  options.env,
        open: "external",
        browser: options.nav
    });

});

gulp.task('watch', function() {

	/* WATCH task */
     gulp.watch(libPath+'/images/origin/*.*', ['sprite']).on('change', browserSync.reload);
	 gulp.watch(libPath+'/scss/**/*.scss', ['sass']);
	 gulp.watch(libPath+'/js/*.js', ['uglify', browserSync.reload]);
	 gulp.watch(themePath+'/**/*.php').on('change', browserSync.reload);

});

gulp.task('default', ['sass', 'sprite', 'uglify','browser-sync', 'watch'], function() {});
gulp.task('prod', ['sass-prod', 'sprite', 'uglify'], function() {});


