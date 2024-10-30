var gulp = require('gulp');

gulp.task('css', function () {
    gulp.src('src/css/*.pcss')
        .pipe( require('gulp-postcss')([
            require("postcss-cssnext")()
        ]) )
        .pipe( require('gulp-cssnano')() )
        .pipe( require('gulp-rename')({extname: '.css'}) )
        .pipe( gulp.dest('assets/css') );
});

gulp.task('js', function () {
    gulp.src('src/js/*.js')
        .pipe(require('gulp-minify')({
            ext: {
                src: '.js',
                min: '.min.js'
            }
        }))
        .pipe(gulp.dest('assets/js'));
});

gulp.task('copy', function () {
    gulp.src(require('gulp-npm-files')(), {base: './node_modules'})
        .pipe(gulp.dest('assets/lib'));


    gulp.src('node_modules/components-jqueryui/themes/smoothness/**/*', {base:"./node_modules"})
        .pipe(gulp.dest('assets/lib'));

});

gulp.task('svn', function () {
    gulp.src(['**/*', '!node_modules', '!node_modules/**'], {base:"."})
        .pipe(gulp.dest('../../svn/common-wish-and-bridal-lists/trunk'));

});


gulp.task('pot', function () {
    return gulp.src('includes/**/*.php')
        .pipe(require('gulp-wp-pot')( {
            domain: 'wb-list',
            package: 'Common Wish and Bridal Lists'
        } ))
        .pipe(gulp.dest('languages/wb-list.pot'));
});

gulp.task('watch', function(){
  gulp.watch('src/css/*.pcss', ['css']);
  gulp.watch('src/js/*.js', ['js']);
});

gulp.task('prod', ['css', 'js', 'copy', 'pot']);