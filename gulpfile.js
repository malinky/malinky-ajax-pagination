/* ------------------------------------------------------------------------ *
 * Gulp Packages
 * ------------------------------------------------------------------------ */

var gulp            = require('gulp'); 

var autoprefixer    = require('gulp-autoprefixer');
var del             = require('del');
var minifyCSS       = require('gulp-minify-css');
var runSequence     = require('run-sequence');
var uglify          = require('gulp-uglify');

/*
Browser List for Autoprefixer https://github.com/ai/browserslist
https://www.npmjs.com/package/del
https://github.com/sindresorhus/del
https://www.npmjs.com/package/gulp-minify-css
https://github.com/jonathanepollack/gulp-minify-css
https://github.com/OverZealous/run-sequence
https://www.npmjs.com/package/run-sequence
https://github.com/terinjokes/gulp-uglify
https://www.npmjs.com/package/gulp-uglify
https://github.com/terinjokes/gulp-uglify/issues/56
*/


/* ------------------------------------------------------------------------ *
 * Dev
 * 
 * gulp dev
 *
 * Move all applicable files and folders.
 * Minify CSS, Autoprefix.
 * Minify JS.
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
  * Move img only.
  * No sourcemaps used so no need to move css and js folders.
  */
gulp.task('dev-move-dir', function() {
    return gulp.src('img/**', { base: './'} )
        .pipe(gulp.dest('dev'));
});


/**
 * Minify CSS, Autoprefix.
 */
gulp.task('dev-styles', function() {
    return gulp.src('css/*.css')
    .pipe(autoprefixer({browsers: ['last 5 versions']}))
    .pipe(minifyCSS())
    .pipe(gulp.dest('dev/css'));
});


/**
 * Minify JS.
 */
gulp.task('dev-scripts', function() {
    return gulp.src('js/*.js')
    .pipe(uglify())
    .pipe(gulp.dest('dev/js'));
});


/**
 * Set up dev task.
 */
gulp.task('dev', function() {
    runSequence('dev-clean', 
                'dev-move-files', 
                'dev-move-dir', 
                'dev-styles', 
                'dev-scripts'
            );
})


/* ------------------------------------------------------------------------ *
 * Prod
 * 
 * gulp prod
 *
 * Move all applicable files and folders.
 * Minify CSS, Autoprefix.
 * Minify JS.
 * ------------------------------------------------------------------------ */

/**
 * Delete all contents of prod folder.
 */
gulp.task('prod-clean', function (cb) {
    del('prod/*', cb);
});


/**
  * Move root .php files.
  */
gulp.task('prod-move-files', function() {
    return gulp.src('*.php')
        .pipe(gulp.dest('prod'));
});


/**
  * Move root directories and their contents.
  * Move img only.
  * No sourcemaps used so no need to move css and js folders.
  */
gulp.task('prod-move-dir', function() {
    return gulp.src('img/**', { base: './'} )
        .pipe(gulp.dest('prod'));
});


/**
 * Minify CSS, Autoprefix.
 */
gulp.task('prod-styles', function() {
    return gulp.src('css/*.css')
    .pipe(autoprefixer({browsers: ['last 5 versions']}))
    .pipe(minifyCSS())
    .pipe(gulp.dest('prod/css'));
});


/**
 * Minify JS.
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
                'prod-styles', 
                'prod-scripts'
            );
})