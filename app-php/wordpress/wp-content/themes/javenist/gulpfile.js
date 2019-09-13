var gulp = require('gulp');
var less = require('gulp-less');
// var sourcemaps = require('gulp-sourcemaps');
// var autoprefixer = require('gulp-autoprefixer');
var browserSync = require('browser-sync').create();
 
var input = './less/**/*.less';
var output = './css';
 
gulp.task('less', function () {
	return gulp.src(input)
	.pipe(gulp.dest(output))
	.pipe(browserSync.stream());
});
 
// Watch files for change and set Browser Sync
gulp.task('watch', function() {
	// BrowserSync settings
	browserSync.init({
	proxy: "http://127.0.0.1/wp/javenist/",
	files: "styes.css"
});
 
// Scss file watcher
gulp.watch(input, ['less'])
	.on('change', function(event){
		console.log('File' + event.path + ' was ' + event.type + ', running tasks...')
	});
});
 
// Default task
gulp.task('start', ['less', 'watch']);