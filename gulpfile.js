/* ------------------------------------------------------------------------ *
 * Gulp Packages
 * ------------------------------------------------------------------------ */

var gulp            = require('gulp'); 

var autoprefixer    = require('gulp-autoprefixer');
var del             = require('del');
var runSequence     = require('run-sequence');
var sourcemaps      = require('gulp-sourcemaps');
var uglify          = require('gulp-uglify');

/*
Browser List for Autoprefixer https://github.com/ai/browserslist
https://github.com/sindresorhus/del
https://www.npmjs.com/package/del
https://github.com/OverZealous/run-sequence
https://www.npmjs.com/package/run-sequence
https://github.com/floridoo/gulp-sourcemaps
https://www.npmjs.com/package/gulp-sourcemaps
https://github.com/terinjokes/gulp-uglify
https://www.npmjs.com/package/gulp-uglify
https://github.com/terinjokes/gulp-uglify/issues/56
*/





/* ------------------------------------------------------------------------ *
 * Local
 * ------------------------------------------------------------------------ */





/* ------------------------------------------------------------------------ *
 * Dev
 * 
 * gulp dev
 *
 * Move all applicable files and folders.
 * This includes and all js for debugging with sourcemaps.
 * Concat and minify JS to scripts.js
 * ------------------------------------------------------------------------ */

/**
 * Delete all contents of dev folder.
 */
gulp.task('dev-clean', function (cb) {
    del('dev/*', cb);
});


/**
  * Move root .php files.
  */
gulp.task('dev-move-files', function() {
    return gulp.src('*.php')
        .pipe(gulp.dest('dev'));
});


/**
  * Move root directories and their contents.
  * Move css, img and js to be used with root maps on dev.
  */
gulp.task('dev-move-dir', function() {
    return gulp.src(['css/**', 'img/**', 'js/**'], { base: './'} )
        .pipe(gulp.dest('dev'));
});


/**
 * Autoprefix vendors.
 * Ensure SASS is complete first with a dependancy.
 */
gulp.task('dev-autoprefix', function () {
    return gulp.src('dev/css/style.css')
        .pipe(autoprefixer({
            browsers: ['last 5 versions']
        }))
        .pipe(gulp.dest('dev/css'));
});


/**
 * Minify our JS.
 *
 * sourceRoot sets the path where the source files are hosted relative to the source map.
 * This makes things appear in the correct folders when viewing through developer tools.
 */
gulp.task('dev-scripts', function() {
    return gulp.src('js/*.js')
		.pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('../sourcemaps', {includeContent: false, sourceRoot: '../js'}))
        .pipe(gulp.dest('dev/js'));
});


/**
 * Set up dev task.
 */
gulp.task('dev', function() {
  	runSequence('dev-clean', 
                'dev-move-files', 
                'dev-move-dir', 
                'dev-autoprefix', 
                'dev-scripts'
            );
})





/* ------------------------------------------------------------------------ *
 * Prod
 * 
 * gulp prod
 *
 * Move all applicable files and folders.
 * Concat and minify JS to scripts.js.
 * ------------------------------------------------------------------------ */

/**
 * Delete all contents of prod folder.
 */
gulp.task('prod-clean', function (cb) {
    del('prod/*', cb);
});


/**
  * Move root .php files
  */
gulp.task('prod-move-files', function() {
    return gulp.src('*.php')
        .pipe(gulp.dest('prod'));
});


/**
  * Move root directories and their contents.
  * Not js as we just need minified version as no sourcemaps are used in prod.
  */
gulp.task('prod-move-dir', function() {
    return gulp.src(['css/**', 'img/**'], { base: './'} )
        .pipe(gulp.dest('prod'));
});


/**
 * Autoprefix vendors.
 * Ensure SASS is complete first with a dependancy.
 */
gulp.task('prod-autoprefix', function () {
    return gulp.src('prod/css/style.css')
        .pipe(autoprefixer({
            browsers: ['last 5 versions']
        }))
        .pipe(gulp.dest('prod/css'));
});


/**
 * Minify our JS.
 */
gulp.task('prod-scripts', function() {
    return gulp.src('js/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('prod/js'));
});


/**
 * Set up prod task.
 */
gulp.task('prod', function() {
    runSequence('prod-clean', 
                'prod-move-files', 
                'prod-move-dir', 
                'prod-autoprefix', 
                'prod-scripts'
            );
})