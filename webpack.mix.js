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

let distPath = mix.inProduction() ? 'resources/dist' : 'resources/pre-dist';


/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets', distPath));
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
mix.copyDirectory('resources/assets/images', distPath + '/images');
mix.copyDirectory('resources/assets/fonts', distPath + '/fonts');
mix.copyDirectory('resources/assets/vendors', distPath + '/vendors');



// ------------------------------------ Dcat Admin -------------------------------------------
function dcatPath(path) {
  return 'resources/assets/dcat/' + path;
}

function dcatDistPath(path) {
  return distPath + '/dcat/' + path;
}

// 复制第三方插件文件夹
mix.copyDirectory(dcatPath('plugins'), dcatDistPath('plugins'));
// 打包app.js
mix.js(dcatPath('js/dcat-app.js'), dcatDistPath('js/dcat-app.js'));
// 打包app.scss
mix.sass(dcatPath('sass/dcat-app.scss'), dcatDistPath('css/dcat-app.css'));

// 打包所有 extra 里面的所有js和css
mixAssetsDir('dcat/extra/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('dcat/extra/*.scss', (src, dest) => mix.sass(src, dest.replace('scss', 'css')));

// ------------------------------------ Dcat Admin -------------------------------------------

mix.js('resources/assets/js/core/app-menu.js', distPath + '/js/core')
    .js('resources/assets/js/core/app.js', distPath + '/js/core')
    .sass('resources/assets/sass/bootstrap.scss', distPath + '/css')
    .sass('resources/assets/sass/bootstrap-extended.scss', distPath + '/css')
    .sass('resources/assets/sass/colors.scss', distPath + '/css')
    .sass('resources/assets/sass/components.scss', distPath + '/css')
    .sass('resources/assets/sass/custom-rtl.scss', distPath + '/css')
    .sass('resources/assets/sass/custom-laravel.scss', distPath + '/css');

mix.then(() => {
  if (process.env.MIX_CONTENT_DIRECTION === "rtl") {
    let command = `node ${path.resolve('node_modules/rtlcss/bin/rtlcss.js')} -d -e ".css" ./${distPath}/css/ ./${distPath}/css/`;
    exec(command, function (err, stdout, stderr) {
      if (err !== null) {
        console.log(err);
      }
    });
    // exec(`./node_modules/rtlcss/bin/rtlcss.js -d -e ".css" ./${distPath}/css/ ./${distPath}/css/`);
  }
});
