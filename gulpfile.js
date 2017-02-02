/* ------------------------------------------------------------------------ *
 * Gulp Packages
 * ------------------------------------------------------------------------ */

var gulp            = require('gulp'); 

var autoprefixer    = require('gulp-autoprefixer');
var del             = require('del');
var minifyCSS       = require('gulp-minify-css');
var runSequence     = require('run-sequence');
var uglify          = require('gulp-uglify');

/* ------------------------------------------------------------------------ *
 * Dist
 * 
 * gulp dist
 *
 * Empty existing dist folder.
 * Move all applicable files and folders.
 * Minify CSS, Autoprefix.
 * Minify JS.
 * ------------------------------------------------------------------------ */

/**
 * Delete all contents of dist folder.
 */
gulp.task('dist-clean', function () {
    del('dist/*');
});

/**
  * Move root .php files.
  */
gulp.task('dist-move-files', function() {
    return gulp.src(['*.php', 'readme.txt'])
        .pipe(gulp.dest('dist'));
});

/**
  * Move root directories and their contents.
  * Move img only.
  * No sourcemaps used so no need to move css and js folders.
  */
gulp.task('dist-move-dir', function() {
    return gulp.src(['img/**', 'languages/*'], { base: './'} )
        .pipe(gulp.dest('dist'));
});

/**
 * Minify CSS, Autoprefix.
 */
gulp.task('dist-styles', function() {
    return gulp.src('css/*.css')
    .pipe(autoprefixer({browsers: ['last 5 versions']}))
    .pipe(minifyCSS())
    .pipe(gulp.dest('dist/css'));
});

/**
 * Minify JS.
 */
gulp.task('dist-scripts', function() {
    return gulp.src('js/*.js')
    .pipe(uglify())
    .pipe(gulp.dest('dist/js'));
});

/**
 * Set up dist task.
 */
gulp.task('dist', function() {
    runSequence('dist-clean', 
                'dist-move-files', 
                'dist-move-dir', 
                'dist-styles', 
                'dist-scripts'
            );
});
