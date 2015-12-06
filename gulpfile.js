var gulp = require('gulp');
var $ = require('gulp-load-plugins')();

var sass = require('gulp-sass');
var csso = require('gulp-csso');
var minifyCSS = require('gulp-minify-css');
var uglify = require('gulp-uglifyjs');
var concat = require('gulp-concat');

var paths = {
    scripts: __dirname + '/public/js/',
    styles: __dirname + '/public/css/'
};

gulp.task('serve', ['build'], function() {
    gulp.watch(
        [__dirname + '/dev/css/*.scss', __dirname + '/dev/css/_*.scss'],
        {debounceDelay: 400},
        ['sass']
        );
    gulp.watch(
        [__dirname + '/dev/js/*.js'],
        {debounceDelay: 400},
        ['js']
        );
});

var defaultJobs = ['sass', 'js'];
gulp.task('default', defaultJobs);
gulp.task('build', defaultJobs);

gulp.task('sass', function() {
    return gulp.src(__dirname + '/dev/css/*.scss')
    .pipe(
        sass({
            includePaths: ['scss'],
            errLogToConsole: true
        }))
    .pipe($.autoprefixer('> 1%', 'last 2 version', 'ff 12', 'ie 8', 'opera 12', 'chrome 12', 'safari 12', 'android 2'))
    .pipe(csso())
    .pipe(minifyCSS())
    .pipe(gulp.dest(paths.styles));
});

gulp.task('js', function() {
    gulp.src([__dirname + '/dev/js/leaflet*.js'])
        .pipe(concat('leaflet.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.scripts));

    gulp.src([__dirname + '/dev/js/intro.js',
              __dirname + '/dev/js/admin.js'])
        .pipe(concat('admin.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.scripts));

    gulp.src([__dirname + '/dev/js/bootstrap.js',
              __dirname + '/dev/js/prettify.js',
              __dirname + '/dev/js/script.js'])
        .pipe(concat('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.scripts));

    gulp.src([__dirname + '/dev/js/rdf2html.js'])
        .pipe(concat('rdf2html.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.scripts));

    return true;
});