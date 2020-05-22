const mix = require('laravel-mix');
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

let theme = null;

let distPath = mix.inProduction() ? 'resources/dist' : 'resources/pre-dist';

function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets', distPath));
  });
}

function themeCss(path) {
  let sf = theme ? '-'+theme : '';

  return `${distPath}/${path}${sf}.css`
}

function dcatPath(path) {
  return 'resources/assets/dcat/' + path;
}

function dcatDistPath(path) {
  return distPath + '/dcat/' + path;
}


/*
 |--------------------------------------------------------------------------
 | Dcat Admin assets
 |--------------------------------------------------------------------------
 */

mix.copyDirectory('resources/assets/images', distPath + '/images');
mix.copyDirectory('resources/assets/fonts', distPath + '/fonts');
mix.copyDirectory('resources/assets/vendors', distPath + '/vendors');

// AdminLTE3.0
mix.sass('resources/assets/adminlte/scss/AdminLTE.scss', themeCss('adminlte/adminlte')).sourceMaps();
mix.js('resources/assets/adminlte/js/AdminLTE.js', distPath + '/adminlte/adminlte.js').sourceMaps();

// 复制第三方插件文件夹
mix.copyDirectory(dcatPath('plugins'), dcatDistPath('plugins'));
// 打包app.js
mix.js(dcatPath('js/dcat-app.js'), dcatDistPath('js/dcat-app.js')).sourceMaps();
// 打包app.scss
mix.sass(dcatPath('sass/dcat-app.scss'), themeCss('dcat/css/dcat-app')).sourceMaps();

// 打包所有 extra 里面的所有js和css
mixAssetsDir('dcat/extra/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('dcat/extra/*.scss', (src, dest) => {
  if (theme) {
    return mix.sass(src, dest.replace('\.scss', '-'+theme+'.css'))
  }

  return mix.sass(src, dest.replace('scss', 'css'))
});

// 皮肤
// mixAssetsDir('dcat/sass/skins/*.scss', (src, dest) => {
//   if (theme) {
//     return mix.sass(src, dest.replace('\.scss', '-'+theme+'.css').replace(/sass/g, 'css'))
//   }
//
//   return mix.sass(src, dest.replace(/sass/g, 'css').replace('scss', 'css'))
// });
