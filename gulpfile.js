var gulp = require('gulp');
var concat = require('gulp-concat');
var filter = require('gulp-filter');
var uglify = require('gulp-uglify');
var cleanCss = require('gulp-clean-css');
var rework = require('gulp-rework');
var reworkUrl = require('rework-plugin-url');
var foreach = require('gulp-foreach');
var mainBowerFiles = require('main-bower-files');
var less = require('gulp-less');
var fs = require('fs');
var path = require('path');
var nodeUrl = require('url') ;
var $ = require('gulp-load-plugins')();

function filterByExtension(extension) {
    return filter(function(file){
        return file.path.match(new RegExp('.' + extension + '$'));
    });
}

function existsSync(filename) {
    try {
        fs.accessSync(filename);
        return true;
    } catch(ex) {
        return false;
    }
}

var dest = './public/';
var css_path = 'css/client';
var js_path = 'js/client';
var assets_path = 'assets/client';

gulp.task('create-vendor-js', function(){
    var mainFiles = mainBowerFiles();

    if(!mainFiles.length){
        return;
    }

    return gulp.src(mainFiles)
        .pipe(filterByExtension('js'))
        .pipe(concat('vendor.js'))
        .pipe(uglify())
        .pipe(gulp.dest(dest + js_path));
});

gulp.task('create-vendor-css', function(){
    var mainFiles = mainBowerFiles();

    if(!mainFiles.length){
        return;
    }

    return gulp.src(mainFiles)
        .pipe(filterByExtension('css'))
        .pipe(foreach(function (stream, file) {
            var dirName = path.dirname(file.path);
            return stream
                .pipe(rework(reworkUrl(function(url) {
                    var checkUrl = path.join(dirName, nodeUrl.parse(url).pathname);
                    if (existsSync(checkUrl)) {
                        var fullUrl = path.join(dirName, url);
                        var new_path = path.relative('css', fullUrl).replace(/bower_components/, '..\\assets\\client');
                        console.log(new_path);
                        return new_path;
                    }
                    return url;
                })));
        }))
        .pipe(concat('vendor.css'))
        .pipe(cleanCss())
        .pipe(gulp.dest(dest + css_path));
});

gulp.task('create-vendor-assets', function() {
    var mainFiles = mainBowerFiles();

    if(!mainFiles.length){
        return;
    }

    var module_name = '/';

    return gulp.src(mainFiles, {
            base: './bower_components'
        })
        .pipe(filter([
            '**/*.{png,gif,svg,jpeg,jpg,woff,woff2,eot,ttf}'
        ]))
        .pipe(gulp.dest(dest + assets_path + module_name));
});

gulp.task('create-result-css', function() {
    return gulp.src([dest + css_path + '/vendor.css', dest + css_path + '/client.css'])
        .pipe(concat('styles.min.css'))
        .pipe(cleanCss({
            inline: ['local', 'remote', 'fonts.googleapis.com'],
            specialComments : 'none'
        }))
        .pipe(gulp.dest(dest + css_path));
});

gulp.task('create-result-js', function() {
    return gulp.src([dest + js_path + '/vendor.js', dest + js_path + '/client.js'])
        .pipe(concat('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(dest + js_path));
});

// Watch files for changes & reload
gulp.task('watch', ['create-result-css', 'create-result-js'], function () {
    gulp.watch([dest + js_path + '/client.js'], ['create-result-js']);
    gulp.watch([dest + css_path + '/client.css'], ['create-result-css']);
});