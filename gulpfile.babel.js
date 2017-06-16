import plugin from './package.json';
import gulp from 'gulp';
import plugins from 'gulp-load-plugins';
import runSequence from 'run-sequence';

const $ = plugins();


gulp.task('dist:css', (done) => {
	const CSSNANO = {
	    autoprefixer: false,
	    calc: false,
	    colormin: true,
	    convertValues: false,
	    discardComments: true,
	    discardDuplicates: false,
	    discardEmpty: true,
	    discardUnused: false,
	    filterPlugins: false,
	    mergeIdents: false,
	    mergeLonghand: false,
	    mergeRules: false,
	    minifyFontValues: true,
	    minifyGradients: false,
	    minifySelectors: true,
	    normalizeCharset: false,
	    normalizeUrl: false,
	    orderedValues: false,
	    reduceIdents: false,
	    reduceTransforms: false,
	    svgo: true,
	    uniqueSelectors: false,
	    zindex: false
	};
	const processors = [
        require('cssnano')(CSSNANO),
        require('postcss-reporter')({clearReportedMessages: true})
    ];

    return gulp.src('assets/css/input.css')
        .pipe($.postcss(processors))
        .pipe($.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/css'));
});
gulp.task('dist:js', (done) => {
	return gulp.src('assets/js/input.js')
        .pipe($.uglify({output: {comments: /^!|@preserve|@license|@cc_on/i}}))
        .pipe($.rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/js'));
});
gulp.task('dist', ['check:js'], (done) => {
    runSequence(
        ['dist:css'],
        ['dist:js'],
        done
    );
});


gulp.task('check:js', (done) => {
	return gulp.src('assets/js/input.js')
		.pipe($.jshint())
		.pipe($.jshint.reporter('jshint-stylish'));
});