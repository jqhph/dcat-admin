const mix = require('laravel-mix');
const exec = require('child_process').exec;
require('dotenv').config();

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const glob = require('glob')
const path = require('path')

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets', 'resources/dist'));
  });
}

const sassOptions = {
  precision: 5
};

// plugins Core stylesheets
mixAssetsDir('sass/plugins/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\\|\/)sass(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// themes Core stylesheets
mixAssetsDir('sass/themes/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\\|\/)sass(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// pages Core stylesheets
mixAssetsDir('sass/pages/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\\|\/)sass(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// Core stylesheets
mixAssetsDir('sass/core/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\\|\/)sass(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// script js
mixAssetsDir('js/scripts/**/*.js', (src, dest) => mix.scripts(src, dest));

/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

mixAssetsDir('assets/vendors/js/**/*.js', (src, dest) => mix.scripts(src, dest));
mixAssetsDir('assets/vendors/css/**/*.css', (src, dest) => mix.copy(src, dest));
mixAssetsDir('assets/vendors/css/editors/quill/fonts/', (src, dest) => mix.copy(src, dest));
mix.copyDirectory('resources/assets/images', 'resources/dist/images');
mix.copyDirectory('resources/assets/fonts', 'resources/dist/fonts');
mix.copyDirectory('resources/assets/vendors', 'resources/dist/vendors');



// ------------------------------------ Dcat Admin -------------------------------------------
function dcatPath(path) {
  return 'resources/assets/dcat/' + path;
}

function dcatDistPath(path) {
  return 'resources/dist/dcat/' + path;
}

// 复制第三方插件文件夹
mix.copyDirectory(dcatPath('plugins'), dcatDistPath('plugins'));
// 打包app.js
mix.js(dcatPath('js/dcat-app.js'), dcatDistPath('js/dcat-app.js'));
// 打包app.scss
mix.sass(dcatPath('sass/dcat-app.scss'), dcatDistPath('css/dcat-app.css'));

// 打包所有 extra 里面的所有js
mixAssetsDir('dcat/extra/*.js', (src, dest) => mix.js(src, dest));

// ------------------------------------ Dcat Admin -------------------------------------------

mix.js('resources/assets/js/core/app-menu.js', 'resources/dist/js/core')
    .js('resources/assets/js/core/app.js', 'resources/dist/js/core')
    .sass('resources/assets/sass/bootstrap.scss', 'resources/dist/css')
    .sass('resources/assets/sass/bootstrap-extended.scss', 'resources/dist/css')
    .sass('resources/assets/sass/colors.scss', 'resources/dist/css')
    .sass('resources/assets/sass/components.scss', 'resources/dist/css')
    .sass('resources/assets/sass/custom-rtl.scss', 'resources/dist/css')
    .sass('resources/assets/sass/custom-laravel.scss', 'resources/dist/css');

mix.then(() => {
  if (process.env.MIX_CONTENT_DIRECTION === "rtl") {
    let command = `node ${path.resolve('node_modules/rtlcss/bin/rtlcss.js')} -d -e ".css" ./resources/dist/css/ ./resources/dist/css/`;
    exec(command, function (err, stdout, stderr) {
      if (err !== null) {
        console.log(err);
      }
    });
    // exec('./node_modules/rtlcss/bin/rtlcss.js -d -e ".css" ./resources/dist/css/ ./resources/dist/css/');
  }
});


// if (mix.inProduction()) {
//   mix.version();
//   mix.webpackConfig({
//     output: {
//       resources/distPath: '/demo/vuexy-bootstrap-laravel-admin-template/demo-1/'
//     }
//   });
//   mix.setResourceRoot("/demo/vuexy-bootstrap-laravel-admin-template/demo-1/");
// }
