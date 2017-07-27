/*Load all plugin define in package.json*/
var gulp = require('gulp'),
	gulpLoadPlugins = require('gulp-load-plugins'),
	plugins = gulpLoadPlugins();

/*JS task*/
gulp.task('dist', function () {
	gulp.src([ 'assets/js/input-5.js' ])
		.pipe(plugins.jshint())
		.pipe(plugins.uglify())
		.pipe(plugins.concat('input-5.min.js'))
		.pipe(gulp.dest('assets/js/'));

	gulp.src([ 'assets/js/input-56.js' ])
		.pipe(plugins.jshint())
		.pipe(plugins.uglify())
		.pipe(plugins.concat('input-56.min.js'))
		.pipe(gulp.dest('assets/js/'));

	return gulp.src([ 'assets/css/style.css' ])
		.pipe(plugins.uglifycss())
		.pipe(plugins.concat('style.min.css'))
		.pipe(gulp.dest('assets/css/'));
});

gulp.task('check-js', function () {
	return gulp.src('assets/js/component.js')
		.pipe(plugins.jshint())
		.pipe(plugins.jshint.reporter('default'));
});

// On default task, just compile on demand
gulp.task('default', function() {
	gulp.watch('assets/js/*.js', [ 'check-js']);
});