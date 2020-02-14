/*
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 */(function(c){c(jQuery)})(function(c){function n(a){a=e.json?JSON.stringify(a):String(a);return e.raw?a:encodeURIComponent(a)}function m(a,d){if(e.raw)var b=a;else a:{0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{a=decodeURIComponent(a.replace(l," "));b=e.json?JSON.parse(a):a;break a}catch(h){}b=void 0}return c.isFunction(d)?d(b):b}var l=/\+/g,e=c.cookie=function(a,d,b){if(void 0!==d&&!c.isFunction(d)){b=c.extend({},e.defaults,b);if("number"===typeof b.expires){var h= b.expires,g=b.expires=new Date;g.setTime(+g+864E5*h)}return document.cookie=[e.raw?a:encodeURIComponent(a),"=",n(d),b.expires?"; expires="+b.expires.toUTCString():"",b.path?"; path="+b.path:"",b.domain?"; domain="+b.domain:"",b.secure?"; secure":""].join("")}b=a?void 0:{};h=document.cookie?document.cookie.split("; "):[];g=0;for(var l=h.length;g<l;g++){var f=h[g].split("=");var k=f.shift();k=e.raw?k:decodeURIComponent(k);f=f.join("=");if(a&&a===k){b=m(f,d);break}a||void 0===(f=m(f))||(b[k]=f)}return b};e.defaults={};c.removeCookie=function(a,d){if(void 0===c.cookie(a))return!1;c.cookie(a,"",c.extend({},d,{expires:-1}));return!c.cookie(a)}});
/*seajs*/!function(a,b){function c(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function d(){return B++}function e(a){return a.match(E)[0]}function f(a){for(a=a.replace(F,"/");a.match(G);)a=a.replace(G,"/");return a=a.replace(H,"$1/")}function g(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function h(a){var b=v.alias;return b&&x(b[a])?b[a]:a}function i(a){var b=v.paths,c;return b&&(c=a.match(I))&&x(b[c[1]])&&(a=b[c[1]]+c[2]),a}function j(a){var b=v.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(J,function(a,c){return x(b[c])?b[c]:a})),a}function k(a){var b=v.map,c=a;if(b)for(var d=0,e=b.length;e>d;d++){var f=b[d];if(c=z(f)?f(a)||a:a.replace(f[0],f[1]),c!==a)break}return c}function l(a,b){var c,d=a.charAt(0);if(K.test(a))c=a;else if("."===d)c=f((b?e(b):v.cwd)+a);else if("/"===d){var g=v.cwd.match(L);c=g?g[0]+a.substring(1):a}else c=v.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function m(a,b){if(!a)return"";a=h(a),a=i(a),a=j(a),a=g(a);var c=l(a,b);return c=k(c)}function n(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}function o(a,b,c,d){var e=T.test(a),f=M.createElement(e?"link":"script");c&&(f.charset=c),A(d)||f.setAttribute("crossorigin",d),p(f,b,e,a),e?(f.rel="stylesheet",f.href=a):(f.async=!0,f.src=a),U=f,S?R.insertBefore(f,S):R.appendChild(f),U=null}function p(a,c,d,e){function f(){a.onload=a.onerror=a.onreadystatechange=null,d||v.debug||R.removeChild(a),a=null,c()}var g="onload"in a;return!d||!W&&g?(g?(a.onload=f,a.onerror=function(){D("error",{uri:e,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},b):(setTimeout(function(){q(a,c)},1),b)}function q(a,b){var c=a.sheet,d;if(W)c&&(d=!0);else if(c)try{c.cssRules&&(d=!0)}catch(e){"NS_ERROR_DOM_SECURITY_ERR"===e.name&&(d=!0)}setTimeout(function(){d?b():q(a,b)},20)}function r(){if(U)return U;if(V&&"interactive"===V.readyState)return V;for(var a=R.getElementsByTagName("script"),b=a.length-1;b>=0;b--){var c=a[b];if("interactive"===c.readyState)return V=c}}function s(a){var b=[];return a.replace(Y,"").replace(X,function(a,c,d){d&&b.push(d)}),b}function t(a,b){this.uri=a,this.dependencies=b||[],this.exports=null,this.status=0,this._waitings={},this._remain=0}if(!a.seajs){var u=a.seajs={version:"2.2.3"},v=u.data={},w=c("Object"),x=c("String"),y=Array.isArray||c("Array"),z=c("Function"),A=c("Undefined"),B=0,C=v.events={};u.on=function(a,b){var c=C[a]||(C[a]=[]);return c.push(b),u},u.off=function(a,b){if(!a&&!b)return C=v.events={},u;var c=C[a];if(c)if(b)for(var d=c.length-1;d>=0;d--)c[d]===b&&c.splice(d,1);else delete C[a];return u};var D=u.emit=function(a,b){var c=C[a],d;if(c)for(c=c.slice();d=c.shift();)d(b);return u},E=/[^?#]*\//,F=/\/\.\//g,G=/\/[^/]+\/\.\.\//,H=/([^:/])\/\//g,I=/^([^/:]+)(\/.+)$/,J=/{([^{]+)}/g,K=/^\/\/.|:\//,L=/^.*?\/\/.*?\//,M=document,N=e(M.URL),O=M.scripts,P=M.getElementById("seajsnode")||O[O.length-1],Q=e(n(P)||N);u.resolve=m;var R=M.head||M.getElementsByTagName("head")[0]||M.documentElement,S=R.getElementsByTagName("base")[0],T=/\.css(?:\?|$)/i,U,V,W=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/(\d+).*/,"$1")<536;u.request=o;var X=/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g,Y=/\\\\/g,Z=u.cache={},$,_={},ab={},bb={},cb=t.STATUS={FETCHING:1,SAVED:2,LOADING:3,LOADED:4,EXECUTING:5,EXECUTED:6};t.prototype.resolve=function(){for(var a=this,b=a.dependencies,c=[],d=0,e=b.length;e>d;d++)c[d]=t.resolve(b[d],a.uri);return c},t.prototype.load=function(){var a=this;if(!(a.status>=cb.LOADING)){a.status=cb.LOADING;var c=a.resolve();D("load",c);for(var d=a._remain=c.length,e,f=0;d>f;f++)e=t.get(c[f]),e.status<cb.LOADED?e._waitings[a.uri]=(e._waitings[a.uri]||0)+1:a._remain--;if(0===a._remain)return a.onload(),b;var g={};for(f=0;d>f;f++)e=Z[c[f]],e.status<cb.FETCHING?e.fetch(g):e.status===cb.SAVED&&e.load();for(var h in g)g.hasOwnProperty(h)&&g[h]()}},t.prototype.onload=function(){var a=this;a.status=cb.LOADED,a.callback&&a.callback();var b=a._waitings,c,d;for(c in b)b.hasOwnProperty(c)&&(d=Z[c],d._remain-=b[c],0===d._remain&&d.onload());delete a._waitings,delete a._remain},t.prototype.fetch=function(a){function c(){u.request(g.requestUri,g.onRequest,g.charset,g.crossorigin)}function d(){delete _[h],ab[h]=!0,$&&(t.save(f,$),$=null);var a,b=bb[h];for(delete bb[h];a=b.shift();)a.load()}var e=this,f=e.uri;e.status=cb.FETCHING;var g={uri:f};D("fetch",g);var h=g.requestUri||f;return!h||ab[h]?(e.load(),b):_[h]?(bb[h].push(e),b):(_[h]=!0,bb[h]=[e],D("request",g={uri:f,requestUri:h,onRequest:d,charset:z(v.charset)?v.charset(h):v.charset,crossorigin:z(v.crossorigin)?v.crossorigin(h):v.crossorigin}),g.requested||(a?a[g.requestUri]=c:c()),b)},t.prototype.exec=function(){function a(b){return t.get(a.resolve(b)).exec()}var c=this;if(c.status>=cb.EXECUTING)return c.exports;c.status=cb.EXECUTING;var e=c.uri;a.resolve=function(a){return t.resolve(a,e)},a.async=function(b,c){return t.use(b,c,e+"_async_"+d()),a};var f=c.factory,g=z(f)?f(a,c.exports={},c):f;return g===b&&(g=c.exports),delete c.factory,c.exports=g,c.status=cb.EXECUTED,D("exec",c),g},t.resolve=function(a,b){var c={id:a,refUri:b};return D("resolve",c),c.uri||u.resolve(c.id,b)},t.define=function(a,c,d){var e=arguments.length;1===e?(d=a,a=b):2===e&&(d=c,y(a)?(c=a,a=b):c=b),!y(c)&&z(d)&&(c=s(""+d));var f={id:a,uri:t.resolve(a),deps:c,factory:d};if(!f.uri&&M.attachEvent){var g=r();g&&(f.uri=g.src)}D("define",f),f.uri?t.save(f.uri,f):$=f},t.save=function(a,b){var c=t.get(a);c.status<cb.SAVED&&(c.id=b.id||a,c.dependencies=b.deps||[],c.factory=b.factory,c.status=cb.SAVED)},t.get=function(a,b){return Z[a]||(Z[a]=new t(a,b))},t.use=function(b,c,d){var e=t.get(d,y(b)?b:[b]);e.callback=function(){for(var b=[],d=e.resolve(),f=0,g=d.length;g>f;f++)b[f]=Z[d[f]].exec();c&&c.apply(a,b),delete e.callback},e.load()},t.preload=function(a){var b=v.preload,c=b.length;c?t.use(b,function(){b.splice(0,c),t.preload(a)},v.cwd+"_preload_"+d()):a()},u.use=function(a,b){return t.preload(function(){t.use(a,b,v.cwd+"_use_"+d())}),u},t.define.cmd={},a.define=t.define,u.Module=t,v.fetchedList=ab,v.cid=d,u.require=function(a){var b=t.get(t.resolve(a));return b.status<cb.EXECUTING&&(b.onload(),b.exec()),b.exports};var db=/^(.+?\/)(\?\?)?(seajs\/)+/;v.base=(Q.match(db)||["",Q])[1],v.dir=Q,v.cwd=N,v.charset="utf-8",v.preload=function(){var a=[],b=location.search.replace(/(seajs-\w+)(&|$)/g,"$1=1$2");return b+=" "+M.cookie,b.replace(/(seajs-\w+)=1/g,function(b,c){a.push(c)}),a}(),u.config=function(a){for(var b in a){var c=a[b],d=v[b];if(d&&w(d))for(var e in c)d[e]=c[e];else y(d)?c=d.concat(c):"base"===b&&("/"!==c.slice(-1)&&(c+="/"),c=l(c)),v[b]=c}return D("config",a),u}}}(this);
window.require = window.define = window.exports = window.module = undefined;

/*NProgress*/eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(4(k,l){"4"===G V&&V.1Z?V(l):"21"===G 1z?2c.1z=l():k.2f=l()})(x,4(){4 k(a,b,d){7 a<b?b:a>d?d:a}4 l(a,b,d){a="Q"===e.B?{W:"Q("+D*(-1+a)+"%,0,0)"}:"Y"===e.B?{W:"Y("+D*(-1+a)+"%,0)"}:{"1u-2b":D*(-1+a)+"%"};a.P="U "+b+"A "+d;7 a}4 q(a,b){7 0<=("2a"==G a?a:n(a)).24(" "+b+" ")}4 r(a,b){6 d=n(a),c=d+b;q(d,b)||(a.10=c.1o(1))}4 t(a,b){6 c=n(a);q(a,b)&&(b=c.H(" "+b+" "," "),a.10=b.1o(1,b.J-1))}4 n(a){7(" "+(a.10||"")+" ").H(/\\s+/1C," ")}6 c={1W:"0.2.0"},e=c.1V={1b:.1U,1e:"1Q",B:"",1g:1P,N:!0,1n:.1O,1p:1N,1t:!0,16:\'[S="11"]\',1B:\'[S="T"]\',C:"I",19:\'<i K="11" S="11"><i K="1M"></i></i><i K="T" S="T"><i K="T-1L"></i></i>\'};c.1H=4(a){6 b;X(b 9 a){6 c=a[b];1h 0!==c&&a.1i(b)&&(e[b]=c)}7 x};c.j=1k;c.E=4(a){6 b=c.1m();a=k(a,e.1b,1);c.j=1===a?1k:a;6 d=c.1l(!b),p=d.F(e.16),h=e.1g,v=e.1e;d.1r;w(4(b){""===e.B&&(e.B=c.1s());m(p,l(a,h,v));1===a?(m(d,{P:"1D",1v:1}),d.1r,R(4(){m(d,{P:"U "+h+"A 1w",1v:0});R(4(){c.1x();b()},h)},h)):R(b,h)});7 x};c.1m=4(){7"1y"===G c.j};c.14=4(){c.j||c.E(0);6 a=4(){R(4(){c.j&&(c.N(),a())},e.1p)};e.N&&a();7 x};c.1A=4(a){7 a||c.j?c.15(.3+.5*13.12()).E(1):x};c.15=4(a){6 b=c.j;7 b?("1y"!==G a&&(a=(1-b)*k(13.12()*b,.1,.1E)),b=k(b+a,0,.1F),c.E(b)):c.14()};c.N=4(){7 c.15(13.12()*e.1n)};(4(){6 a=0,b=0;c.1G=4(d){y(!d||"1I"===d.1J())7 x;0===b&&c.14();a++;b++;d.1K(4(){b--;0===b?(a=0,c.1A()):c.E((a-b)/a)});7 x}})();c.1l=4(a){y(c.1d())7 8.Z("o");r(8.1j,"o-1f");6 b=8.1R("i");b.1S="o";b.1T=e.19;6 d=b.F(e.16),p=a?"-D":D*(-1+(c.j||0));a=8.F(e.C);m(d,{P:"U 0 1w",W:"Q("+p+"%,0,0)"});e.1t||(d=b.F(e.1B))&&d&&d.M&&d.M.1a(d);a!=8.I&&r(a,"o-17-C");a.1X(b);7 b};c.1x=4(){t(8.1j,"o-1f");t(8.F(e.C),"o-17-C");6 a=8.Z("o");a&&a&&a.M&&a.M.1a(a)};c.1d=4(){7!!8.Z("o")};c.1s=4(){6 a=8.I.L,b="1Y"9 a?"1c":"20"9 a?"18":"22"9 a?"A":"23"9 a?"O":"";7 b+"25"9 a?"Q":b+"26"9 a?"Y":"1u"};6 w=4(){4 a(){6 c=b.27();c&&c(a)}6 b=[];7 4(c){b.28(c);1==b.J&&a()}}(),m=4(){4 a(a){7 a.H(/^-A-/,"A-").H(/-([\\29-z])/1C,4(a,b){7 b.1q()})}4 b(b){b=a(b);6 d;y(!(d=e[b])){d=b;a:{6 u=8.I.L;y(!(b 9 u))X(6 h=c.J,f=b.2d(0).1q()+b.2e(1),g;h--;)y(g=c[h]+f,g 9 u){b=g;2g a}}d=e[d]=b}7 d}6 c=["1c","O","18","A"],e={};7 4(a,c){6 d=2h;y(2==d.J)X(g 9 c){6 e=c[g];y(1h 0!==e&&c.1i(g)){d=a;6 f=g;f=b(f);d.L[f]=e}}2i{6 g=a;f=d[1];d=d[2];f=b(f);g.L[f]=d}}}();7 c});',62,143,'||||function||var|return|document|in|||||||||div|status|||||nprogress|||||||||this|if||ms|positionUsing|parent|100|set|querySelector|typeof|replace|body|length|class|style|parentNode|trickle||transition|translate3d|setTimeout|role|spinner|all|define|transform|for|translate|getElementById|className|bar|random|Math|start|inc|barSelector|custom|Moz|template|removeChild|minimum|Webkit|isRendered|easing|busy|speed|void|hasOwnProperty|documentElement|null|render|isStarted|trickleRate|substring|trickleSpeed|toUpperCase|offsetWidth|getPositioningCSS|showSpinner|margin|opacity|linear|remove|number|exports|done|spinnerSelector|gi|none|95|994|promise|configure|resolved|state|always|icon|peg|800|02|200|ease|createElement|id|innerHTML|08|settings|version|appendChild|WebkitTransform|amd|MozTransform|object|msTransform|OTransform|indexOf|Perspective|Transform|shift|push|da|string|left|module|charAt|slice|NProgress|break|arguments|else'.split('|'),0,{}));
/*pjax*/eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(3(b){3 J(a,d,e){8 c=12;9 12.2x("1n.2",a,3(a){8 f=b.1m({},m(d,e));f.o||(f.o=b(12).10("11-2")||c);A(a,f)})}3 A(a,d,e){e=m(d,e);d=a.2l;6("A"!==d.2e.1g())1y"$.1H.2 2T $.2.1n 2r 3r 3p 1W";6(!(1<a.3R||a.37||a.38||a.2K||a.3Y||19.2u!==d.2u||19.2f!==d.2f||-1<d.Y.3I("#")&&d.Y.17(/#.*/,"")==19.Y.17(/#.*/,"")||a.1P())){8 c={5:d.Y,o:b(d).10("11-2"),1O:d};e=b.1m({},c,e);c=b.1w("2:1n");b(d).1c(c,[e]);c.1P()||(f(e),a.22(),b(d).1c("2:3a",[e]))}}3 K(a,d,e){e=m(d,e);d=a.2l;8 c=b(d);6("3W"!==d.2e.1g())1y"$.2.1z 2r a 2g 1W";c={Z:(c.10("2i")||"16").1g(),5:c.10("2z"),o:c.10("11-2"),1O:d};6("16"!==c.Z&&3O 0!==R.21)c.11=1N 21(d),c.2Q=!1,c.2S=!1;1f{6(b(d).1k(":39").1a)9;c.11=b(d).3e()}f(b.1m({},c,e));a.22()}3 f(a){3 d(a,d,c){c||(c={});c.3o=p;a=b.1w(a,c);h.1c(a,d);9!a.1P()}3 e(a){6(a){8 d=[];a.1i(3(){d.2n(b(12).10("1M"))});k(d,!0)}}3 c(a){6(a){8 d=[];a.1i(3(){d.2n(b(12).10("Y"))});k(d)}}3 k(a,b){1>a.1a?b&&d("2:1C"):3Z.40([a.2D()],3(){k(a,b)})}a=b.1m(!0,{},b.2P,f.1b,a);b.1X(a.5)&&(a.5=a.5());8 p=a.1O,n=u(a.5).1L,h=a.27=B(a.o);a.11||(a.11={});b.2c(a.11)?a.11.14({1d:"1I",1e:h.15}):a.11.1I=h.15;8 l;a.2v=3(b,c){"16"!==c.Z&&(c.W=0);b.2A("X-1h","2L");b.2A("X-1h-2M",h.15);6(!d("2:2v",[b,c]))9!1;0<c.W&&(l=2N(3(){d("2:W",[b,a])&&b.1G("W")},c.W),c.W=0);c=u(c.5);n&&(c.1L=n);a.1F=C(c)};a.23=3(b,c){l&&35(l);d("2:23",[b,c,a]);d("2:24",[b,a])};a.25=3(b,c,e){8 f=D("",b,a);b=d("2:25",[b,c,e,a]);"16"==a.Z&&"1G"!==c&&b&&v(f.5)};a.2a=3(k,p,l){8 q=f.j,m="3"===1u b.2.1b.1v?b.2.1b.1v():b.2.1b.1v,r=l.2j("X-1h-3S"),g=D(k,l,a),t=u(g.5);n&&(t.1L=n,g.5=t.Y);6(m&&r&&m!==r)v(g.5);1f 6(g.T){f.j={V:a.V||(1N 2t).2I(),5:g.5,7:g.7,o:h.15,13:a.13,W:a.W};(a.14||a.17)&&R.18.1A(f.j,g.7,g.5);6(b.2R(a.o,U.1D))2W{U.1D.2X()}31(Q){}g.7&&(U.7=g.7);d("2:2E",[g.T,a],{j:f.j,2F:q});h.1B(g.T);(q=h.1k("1p[1Y], 3c[1Y]").1Z()[0])&&U.1D!==q&&q.3h();c(g.1V);e(g.1E);g=a.1r;n&&(q=3u(n.3v(1)),q=U.3w(q)||U.3x(q)[0])&&(g=b(q).3y().3z);"3B"==1u g&&b(R).3D(g);d("2:2a",[k,p,l,a])}1f v(g.5)};f.j||(f.j={V:(1N 2t).2I(),5:R.19.Y,7:U.7,o:h.15,13:a.13,W:a.W},R.18.1A(f.j,U.7));E(f.1q);f.3P=a;8 m=f.1q=b.3Q(a);0<m.26&&(a.14&&!a.17&&(L(f.j.V,F(h)),R.18.28(1x,"",a.1F)),d("2:29",[m,a]),d("2:2J",[m,a]));9 f.1q}3 M(a,d){9 f(b.1m({5:R.19.Y,14:!1,17:!0,1r:!1},m(a,d)))}3 v(a){R.18.1A(1x,"",f.j.5);R.19.17(a)}3 G(a){r||E(f.1q);8 d=f.j,e=a.j;6(e&&e.o){6(r&&N==e.5)9;6(d){6(d.V===e.V)9;8 c=d.V<e.V?"2b":"2O"}8 k=l[e.V]||[];a=b(k[0]||e.o);k=k[1];6(a.1a){6(d){8 p=c,n=d.V,h=F(a);l[n]=h;"2b"===p?(p=t,h=x):(p=x,h=t);p.14(n);(n=h.2D())&&2d l[n];y(p,f.1b.1J)}c=b.1w("2:1K",{j:e,2U:c});a.1c(c);c={V:e.V,5:e.5,o:a,14:!1,13:e.13,W:e.W,1r:!1};k?(a.1c("2:29",[1x,c]),f.j=e,e.7&&(U.7=e.7),d=b.1w("2:2E",{j:e,2F:d}),a.1c(d,[k,c]),a.1B(k),a.1c("2:24",[1x,c])):f(c);a[0].2V}1f v(19.Y)}r=!1}3 O(a){8 d=b.1X(a.5)?a.5():a.5,e=a.Z?a.Z.1g():"16",c=b("<2g>",{2i:"16"===e?"16":"2h",2z:d,2Y:"2Z:30"});"16"!==e&&"2h"!==e&&c.1j(b("<1p>",{Z:"1t",1d:"32",1e:e.33()}));a=a.11;6("34"===1u a)b.1i(a.2k("&"),3(a,d){a=d.2k("=");c.1j(b("<1p>",{Z:"1t",1d:a[0],1e:a[1]}))});1f 6(b.2c(a))b.1i(a,3(a,d){c.1j(b("<1p>",{Z:"1t",1d:d.1d,1e:d.1e}))});1f 6("36"===1u a)1s(8 f 2m a)c.1j(b("<1p>",{Z:"1t",1d:f,1e:a[f]}));b(U.1l).1j(c);c.1z()}3 E(a){a&&4>a.26&&(a.3b=b.1o,a.1G())}3 F(a){8 b=a.3d();b.1k("1C").1i(3(){12.1M||2p.3f(12,"3g",!1)});9[a.15,b.T()]}3 C(a){a.2q=a.2q.17(/([?&])(1I|3i)=[^&]*/g,"");9 a.Y.17(/\\?($|#)/,"$1")}3 u(a){8 b=U.3j("a");b.Y=a;9 b}3 m(a,d){a&&d?d.o=a:d=b.3k(a)?a:{o:a};d.o&&(d.o=B(d.o));9 d}3 B(a){a=b(a);6(a.1a){6(""!==a.15&&a.27===U)9 a;6(a.10("V"))9 b("#"+a.10("V"));1y"3l 3m 15 1s 2 o!";}1y"3n 2 o 1s "+a.15;}3 w(a,b){9 a.2s(b).3q(a.1k(b))}3 D(a,d,e){8 c={},f=/<1B/i.3s(a);d=d.2j("X-1h-3t");c.5=d?C(u(d)):e.1F;f?(d=b(b.1Q(a.1R(/<2w[^>]*>([\\s\\S.]*)<\\/2w>/i)[0],U,!0)),a=b(b.1Q(a.1R(/<1l[^>]*>([\\s\\S.]*)<\\/1l>/i)[0],U,!0))):d=a=b(b.1Q(a,U,!0));6(0===a.1a)9 c;c.7=w(d,"7").1Z().2y();e.13?(f="1l"===e.13?a:w(a,e.13).3A(),f.1a&&(c.T="1l"===e.13?f:f.T(),c.7||(c.7=f.10("7")||f.11("7")))):f||(c.T=a);c.T&&(c.T=c.T.1S(3(){9 b(12).3C("7")}),c.T.1k("7").1T(),c.1E=w(c.T,"1C[1M]").1T(),c.T=c.T.1S(c.1E),c.1V=w(c.T,\'3E[Z="2y/3F"]\').1T(),c.T=c.T.1S(c.1V));c.7&&(c.7=b.3G(c.7));9 c}3 L(a,b){l[a]=b;t.14(a);y(x,0);y(t,f.1b.1J)}3 y(a,b){1s(;a.1a>b;)2d l[a.3H()]}3 P(){9 b("3J").2s(3(){8 a=b(12).10("3K-3L");9 a&&"X-1h-3M"===a.1g()}).10("3N")}3 H(){b.1H.2=J;b.2=f;b.2.2B=b.1o;b.2.2C=I;b.2.1n=A;b.2.1z=K;b.2.1U=M;b.2.1b={W:3T,14:!0,17:!1,Z:"16",3U:"1B",1r:0,1J:20,1v:P};b(R).2x("1K.2",G)}3 I(){b.1H.2=3(){9 12};b.2=O;b.2.2B=H;b.2.2C=b.1o;b.2.1n=b.1o;b.2.1z=b.1o;b.2.1U=3(){R.19.1U()};b(R).3V("1K.2",G)}8 r=!0,N=R.19.Y,z=R.18.j;z&&z.o&&(f.j=z);"j"2m R.18&&(r=!1);8 l={},x=[],t=[];0>b.3X("j",b.2G.2H)&&b.2G.2H.14("j");b.2o.2=R.18&&R.18.28&&R.18.1A&&!41.42.1R(/((43|44|45).+\\46\\s+[1-4]\\D|47\\/.+48)/);b.2o.2?H():I()})(2p);',62,257,'||pjax|function||url|if|title|var|return||||||||||state|||||container|||||||||||||||||||||||||||||window||contents|document|id|timeout||href|type|attr|data|this|fragment|push|selector|GET|replace|history|location|length|defaults|trigger|name|value|else|toUpperCase|PJAX|each|append|find|body|extend|click|noop|input|xhr|scrollTo|for|hidden|typeof|version|Event|null|throw|submit|replaceState|html|script|activeElement|scripts|requestUrl|abort|fn|_pjax|maxCacheLength|popstate|hash|src|new|target|isDefaultPrevented|parseHTML|match|not|remove|reload|styles|element|isFunction|autofocus|last||FormData|preventDefault|complete|end|error|readyState|context|pushState|start|success|forward|isArray|delete|tagName|hostname|form|POST|method|getResponseHeader|split|currentTarget|in|unshift|support|jQuery|search|requires|filter|Date|protocol|beforeSend|head|on|text|action|setRequestHeader|enable|disable|pop|beforeReplace|previousState|event|props|getTime|send|shiftKey|true|Container|setTimeout|back|ajaxSettings|processData|contains|contentType|or|direction|offsetHeight|try|blur|style|display|none|catch|_method|toLowerCase|string|clearTimeout|object|metaKey|ctrlKey|file|clicked|onreadystatechange|textarea|clone|serializeArray|_data|globalEval|focus|_|createElement|isPlainObject|cant|get|no|relatedTarget|anchor|add|an|test|URL|decodeURIComponent|slice|getElementById|getElementsByName|offset|top|first|number|is|scrollTop|link|css|trim|shift|indexOf|meta|http|equiv|VERSION|content|void|options|ajax|which|Version|650|dataType|off|FORM|inArray|altKey|seajs|use|navigator|userAgent|iPod|iPhone|iPad|bOS|WebApps|CFNetwork'.split('|'),0,{}));

/*mian*/
(function (win) {
    var $d = $(document),
        NP = NProgress,
        booting = {},
        components = LA.components,
        lang = LA.lang;

    /**
     * 全局配置
     */
    components.setup = function () {
        layer.config({maxmin: true, moveOut: true, shade: false});

        $.ajaxSetup({
            cache: true,
            error: LA.ajaxError
        });

        LA.NP = {};
        LA.NP.start = NP.start;
        LA.NP.done = function () {
            setTimeout(NP.done, 200);
        };

        LA.grid = {
            _defaultName: '_def_',
            _selectors: {},

            addSelector: function (selector, name) {
                this._selectors[name || this._defaultName] = selector;
            },

            // 获取行选择器选中的ID字符串
            selected: function (name) {
                return this._selectors[name || this._defaultName].getIds()
            },

            // 获取行选择器选中的行
            selectedRows: function (name) {
                return this._selectors[name || this._defaultName].getRows()
            },
        };

        $.pjax.defaults.timeout = 5000;
        $.pjax.defaults.maxCacheLength = 0;

    };

    // 动作按钮点击事件注册
    components.actions = {
        // 刷新按钮
        refreshAction: function () {
            $('[data-action="refresh"]').off('click').click(function () {
                LA.reload($(this).data('url'));
                LA.success(lang.refresh_succeeded, 'rb');
            });
        },
        // 删除按钮初始化
        deleteAction: function () {
            $('[data-action="delete"]').off('click').click(function() {
                var url = $(this).data('url'), redirect = $(this).data('redirect');
                LA.confirm(lang.delete_confirm, function () {
                    NP.start();
                    $.ajax({
                        method: 'post',
                        url: url,
                        data: {
                            _method:'delete',
                            _token:LA.token,
                        },
                        success: function (data) {
                            NP.done();
                            if (data.status) {
                                LA.reload(redirect);
                                LA.success(data.message);
                            } else {
                                LA.error(data.message);
                            }
                        }
                    });
                }, lang.confirm, lang.cancel);
            });
        },
        // 批量删除按钮初始化
        batchDeleteAction: function () {
            $('[data-action="batch-delete"]').off('click').on('click', function() {
                var url = $(this).data('url'),
                    name = $(this).data('name'),
                    id = LA.grid.selected(name).join();
                if (!id) {
                    return;
                }
                LA.confirm(lang.delete_confirm, function () {
                    NP.start();
                    $.ajax({
                        method: 'post',
                        url: url + '/' + id,
                        data: {
                            _method:'delete',
                            _token:LA.token,
                        },
                        success: function (data) {
                            NP.done();
                            if (data.status) {
                                LA.reload();
                                LA.success(data.message);
                            } else {
                                LA.error(data.message);
                            }
                        }
                    });
                }, lang.confirm, lang.cancel);
            });
        },
    };

    /**
     * 页面组件初始化
     */
    components.boot = function () {
        var k, i, all = [booting, components.actions, components.booting];

        for (k in all) {
            for (i in all[k]) {
                if (typeof all[k][i] == "function") {
                    try { all[k][i](); } catch (e) {console.error(e)}
                }
            }
        }
    };

    /**
     * 全局事件监听
     */
    components.listen = function () {
        $d.pjax('a:not(a[target="_blank"])', '#pjax-container', { fragment: 'body' });
        NP.configure({parent: '#pjax-container'});

        $d.on('pjax:timeout', function (event) {
            event.preventDefault();
        });

        $d.on('submit', 'form[pjax-container]', function (event) {
            $.pjax.submit(event, '#pjax-container')
        });

        $d.on("pjax:popstate", function () {

            $d.one("pjax:end", function (event) {
                $(event.target).find("script[data-exec-on-popstate]").each(function () {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });

        $d.on('pjax:send', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if ($submit_btn) {
                    $submit_btn.button('loading')
                }
            }
            NP.start();
        });

        $d.on('pjax:complete', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if ($submit_btn) {
                    $submit_btn.button('reset')
                }
            }
            NP.done();
        });

        // 新页面加载，重新初始化
        $d.on('pjax:script', components.boot);
    };

    /**
     * 初始化方法定义
     */
    booting = {
        // 菜单初始化
        leftSitebar: function () {
            $('.sidebar-menu li:not(.treeview) > a').off('click').on('click', function () {
                if ($('.sidebar-mini.sidebar-collapse').length) {
                    var $t = $(this).parents('.treeview');
                    if (!$t.hasClass('active')) {
                        $('.sidebar-menu li').removeClass('active');
                        $t.addClass('active');
                    }
                }

                var $parent = $(this).parent().addClass('active');
                $parent.siblings('.treeview.active').find('> a').trigger('click');
                $parent.siblings().removeClass('active').find('li').removeClass('active');
            });
        },

        // 进度条初始化
        progressBar: function () {
            $('.progress-bar').each(function (k, v) {
                v = $(v);
                var w = v.data('width');
                if (w) {
                    setTimeout(function () {
                        v.css({width: w});
                    }, 80);
                }
            });
        },

        // 图片预览
        imagePreview: function () {
            $('[data-init="preview"]').off('click').click(function () {
                return LA.previewImage($(this).attr('src'));
            });
        },

        // 数字动画初始化
        counterUp: function() {
            var boot = function(k, obj) {
                try {
                    obj = $(obj);
                    obj.counterUp({
                        delay: obj.attr('data-delay') || 100,
                        time: obj.attr('data-time') || 1200
                    });
                } catch (e) {}
            };
            $('[data-init="counterup"]').each(boot);
            $('number').each(boot);
        },

        popover: function () {
            $('.popover').remove();
            $('[data-init="popover"]').popover();
        },

        // 初始化waves
        waves: function () {
            var i, w = Waves, _40 = [
                    '.nav-stacked>li>a',
                    '#app .navbar-nav>li>a',
                ], _70 = [
                    '.btn-warning',
                    '.webuploader-pick',
                    '.layui-layer-btn a',
                    '.pagination>li>a',
                    '.btn-trans',
                    '.skin-blue-light:not(.sidebar-collapse) .sidebar-menu li>a',
                ], light = [
                    '.btn-primary',
                    '.btn-success',
                    '.btn-info',
                    '.btn-danger',
                    '.btn-purple',
                    '.btn-inverse',
                    '.btn-tear',
                    '.btn-pink',
                    '.btn-blue',
                    '.btn-dropbox',
                    '.btn-custom',
                    '.btn-instagram',
                    '.btn-facebook',
                    '.skin-black-light:not(.sidebar-collapse) .sidebar-menu li>a',
                    '.skin-black:not(.sidebar-collapse) .sidebar-menu li>a',
                ],
                float = [
                    '.btn',
                    '.btn-light',
                ];

            function _init() {
                for (i in _40) {
                    w.attach(_40[i], ['waves-40']);
                }
                for (i in _70) {
                    w.attach(_70[i], ['waves-70']);
                }
                for (i in light) {
                    w.attach(light[i], ['waves-light']);
                }
                for (i in float) {
                    w.attach(float[i], ['waves-float']);
                }

                w.init();
            }

            $('.sidebar-toggle').click(function () {
                setTimeout(function () {
                    if ($('body').hasClass('sidebar-collapse')) {
                        $('.sidebar-menu li>a').removeClass('waves-effect');
                    } else {
                        _init();
                    }
                }, 10)
            });

            _init();
        },

        // 返回定点按钮初始化
        goTop: function () {
            if (this.initgo) {
                return;
            }
            this.initgo = 1;

            var $top = $('#go-top');
            // 滚动锚点
            $(window).scroll(function () {
                var scrollTop = $(this).scrollTop(), // 滚动条距离顶部的高度
                    windowHeight = $(this).height();  // 当前可视的页面高度
                // 显示或隐藏滚动锚点
                if(scrollTop + windowHeight >= 1100) {
                    $top.show(20)
                } else {
                    $top.hide()
                }
            });
            // 滚动至顶部
            $top.click(function () {
                $("html, body").animate({
                    scrollTop: $(".dcat-admin-body").offset().top
                }, {duration: 500, easing: "swing"});
                return false;
            })
        },
    };

    /**
     * 全局工具方法注册
     */
    components.register = function () {
        // 默认错误处理方法
        LA.ajaxError = LA.ajaxError || function(xhr, text, msg) {
            layer.closeAll('loading');
            LA.NP.done();
            LA.loading(false);// 关闭所有loading效果

            var json = xhr.responseJSON || {}, _msg = json.message;
            switch (xhr.status) {
                case 500:
                    return LA.error(_msg || (LA.lang['500'] || 'Server internal error.'));
                case 403:
                    return LA.error(_msg || (LA.lang['403'] || 'Permission deny!'));
                case 401:
                    if (json.login) {
                        return location.href = json.login;
                    }
                    return LA.error(LA.lang['401'] || 'Unauthorized.');
                case 419:
                    return LA.error(LA.lang['419'] || 'Sorry, your page has expired.');

                case 422:
                    if (json.errors) {
                        try {
                            var err = [], i;
                            for (i in json.errors) {
                                err.push(json.errors[i].join('<br/>'));
                            }
                            LA.error(err.join('<br/>'));
                        } catch (e) {}
                        return;
                    }
            }

            LA.error(_msg || (xhr.status + ' ' + msg));
        };

        /**
         * 手动触发ready事件
         */
        LA.triggerReady = function () {
            if (typeof LA.pjaxresponse == 'undefined') return;
            $(function () {$d.trigger('pjax:script');});
        };

        LA.success = function (msg, offset, seconds) {
            var idx = layer.msg(msg, {icon:1, offset: offset||'t', time: (seconds || 2.5) * 1000});
            return layer_position(idx, offset);
        };
        LA.error = function (msg, offset, seconds) {
            var idx = layer.msg(msg, {icon:2, offset: offset||'t', time: (seconds || 4) * 1000});
            return layer_position(idx, offset);
        };
        LA.warning = function (msg, offset, seconds) {
            var idx = layer.msg(msg, {icon:7, offset: offset||'t', time: (seconds || 4) * 1000});
            return layer_position(idx, offset);
        };
        LA.info = function (msg, offset, seconds) {
            var idx = layer.msg(msg, {offset: offset||'t', time: (seconds || 4) * 1000});
            return layer_position(idx, offset);
        };
        LA.confirm = function (msg, callback, confirmBtn, cancelBtn, title) {
            return layer.msg(msg, {
                title: title || null,
                time: 0,
                icon: 3,
                btn: [confirmBtn || 'Confirm', cancelBtn || 'Cancel'],
                yes: function (i) {
                    layer.close(i);
                    callback(i);
                }
            });
        };

        // 注册自定义验证器
        LA.extendValidator = function (rule, callback, message) {
            var GLOBAL = $.fn.validator.Constructor.DEFAULTS;

            GLOBAL.custom[rule] = callback;
            GLOBAL.errors[rule] = message || null;
        };

        function layer_position(idx, p) {
            switch (p) {
                case 'rb':
                case 'lb':
                    layer.style(idx, {
                        marginTop: -20,
                        marginLeft: -8
                    });
                    break;
                case 'rt':
                    layer.style(idx, {
                        marginTop: 70,
                        marginLeft: -8
                    });
                    break;
            }
            return idx;
        }

        /**
         * 行选择器
         *
         * @constructor
         */
        LA.RowSelector = function RowSelector(opts) {
            opts = $.extend({
                checkbox: '', // checkbox css选择器
                selectAll: '', // 全选checkbox css选择器
                bg: 'rgba(255, 255,213,0.4)', // 选中效果颜色
                clickTr: false, // 点击行事件
            }, opts);

            var checkboxSelector = opts.checkbox,
                selectAllSelector = opts.selectAll,
                $ckb = $(checkboxSelector);

            $(selectAllSelector).on('change', function() {
                var cbx = $(checkboxSelector);

                for (var i = 0; i < cbx.length; i++) {
                    if (this.checked && !cbx[i].checked) {
                        cbx[i].click();
                    } else if (!this.checked && cbx[i].checked) {
                        cbx[i].click();
                    }
                }
            });
            if (opts.clickTr) {
                $ckb.click(function (e) {
                    if (typeof e.cancelBubble != "undefined") {
                        e.cancelBubble = true;
                    }
                    if (typeof e.stopPropagation != "undefined") {
                        e.stopPropagation();
                    }
                }).parents('tr').click(function (e) {
                    $(this).find(checkboxSelector).click();
                });
            }

            $ckb.on('change', function () {
                var tr = $(this).closest('tr');
                if (this.checked) {
                    tr.css('background-color', opts.bg);
                } else {
                    tr.css('background-color', '');
                }
            });

            this.getIds = function () {
                var selected = [];
                $(checkboxSelector+':checked').each(function() {
                    selected.push($(this).data('id'));
                });

                return selected;
            };
            this.getRows = function () {
                var selected = [];
                $(checkboxSelector+':checked').each(function(){
                    selected.push({'id': $(this).data('id'), 'label': $(this).data('label')})
                });

                return selected;
            };

            return this;
        };

        /**
         * 获取json对象或数组的长度
         *
         * @param obj
         * @returns {number}
         */
        LA.len = function (obj) {
            if (typeof obj !== 'object') {
                return 0;
            }
            var i, l = 0;
            for(i in obj) {
                l += 1;
            }
            return l;
        };

        /**
         * 判断变量或key是否存在
         *
         * @param _var
         * @param key
         * @returns {boolean}
         */
        LA.isset = function (_var, key) {
            var isset = (typeof _var !== 'undefined' && _var !== null);
            if (typeof key === 'undefined') {
                return isset;
            }

            return isset && typeof _var[key] !== 'undefined';
        };

        LA.empty = function (obj, key) {
            return !(LA.isset(obj, key) && obj[key]);
        };

        LA.arr = {
            get: function (arr, key, def) {
                def = null;

                if (LA.len(arr) < 1) return def;
                key = String(key).split('.');

                for (var i = 0; i < key.length; i++) {
                    if (LA.isset(arr, key[i])) {
                        arr = arr[key[i]];
                    } else {
                        return def;
                    }
                }

                return arr;
            },

            has: function (arr, key) {
                if (LA.len(arr) < 1) return def;
                key = String(key).split('.');

                for (var i = 0; i < key.length; i++) {
                    if (LA.isset(arr, key[i])) {
                        arr = arr[key[i]];
                    } else {
                        return false;
                    }
                }

                return true;
            },

            in: function (arr, val) {
                if (LA.len(arr) < 1) return false;

                for (var i in arr) {
                    if (val == arr[i]) {
                        return true;
                    }
                }
                return false;
            },

            deleteValue: function (arr, val) {
                if (LA.len(arr) < 1) return false;

                for (var i in arr) {
                    if (val == arr[i]) {
                        delete arr[i];
                        return true;
                    }
                }
                return false;
            },

            // 判断对象是否相等
            equal: function (array, array2) {
                if (!array || !array2) return false;

                var len1 = LA.len(array), len2 = LA.len(array2), i;

                if (len1 != len2) return false;

                for (i in array) {
                    if (!LA.isset(array2, i)) return false;

                    if (array[i] === null && array2[i] === null) {
                        return true;
                    }

                    if (typeof array[i] == 'object' && typeof array2[i] == 'object') {
                        if (!this.equal(array[i], array2[i]))
                            return false;
                    }
                    else if (array[i] != array2[i]) {
                        return false;
                    }
                }
                return true;
            }
        };

        LA.str = {
            replace: function (str, replace, subject) {
                if (!str) return str;

                var regExp = new RegExp(replace, "g");
                return str.replace(regExp, subject);
            }
        };

        /**
         *
         * @param lang
         * @returns {Function}
         * @constructor
         */
        LA.Translator = function (lang) {
            /**
             * 翻译
             *
             * @param {string} label exp: admin.cancel
             * @param {object} replace
             */
            return function (label, replace) {
                if (typeof lang !== 'object') return label;

                var text = LA.arr.get(lang, label), i;
                if (!LA.isset(text)) {
                    return label;
                }
                if (!replace) {
                    return text;
                }

                for (i in replace) {
                    text = LA.str.replace(text, ':'+i, replace[i]);
                }

                return text;
            }
        };

        /**
         * pjax刷新页面
         *
         * @param url
         */
        LA.reload = function (url) {
            var opt = {container:'#pjax-container'};
            if (url) {
                opt.url = url;
            }
            $.pjax.reload(opt);
        };

        // 预览图片
        LA.previewImage = function (src, width, title) {
            var img = new Image(), win = LA.isset(window.top) ? top : window,
                clientWidth = Math.ceil(win.screen.width * 0.6),
                clientHeight = Math.ceil(win.screen.height * 0.8);
            img.style.display = 'none';
            img.style.height = 'auto';
            img.style.width = width || '100%';
            img.src = src;

            document.body.appendChild(img);
            LA.loading();
            img.onload = function () {
                LA.loading(false);
                var srcw = this.width, srch = this.height;
                var width = srcw > clientWidth ? clientWidth : srcw,
                    height = Math.ceil(width * (srch/srcw));
                height = height > clientHeight ? clientHeight : height;

                title = title || src.split('/').pop();
                if (title.length > 50) {
                    title = title.substr(0, 50) + '...';
                }

                win.layer.open({
                    type: 1,
                    shade: 0.2,
                    title: false,
                    maxmin: false,
                    shadeClose: true,
                    closeBtn: 2,
                    content: $(img),
                    area: [width+'px', (height) + 'px'],
                    skin: 'layui-layer-nobg',
                    end: function () {
                        document.body.removeChild(img);
                    }
                });
            };
            img.onerror = function () {
                LA.loading(false);
                LA.warning('预览失败', 'rb');
            };
        };
    };

    ////////////////////////////////////////////////////////////////////
    // 全局配置与事件监听
    components.register();
    components.setup();
    components.listen();

    // 初始化组件
    $(components.boot);

})(window);

(function () {
    /**
     * 表单提交
     *
     * @param opts
     * @constructor
     */
    var $eColumns = {};
    LA.Form = function (opts) {
        opts = $.extend({
            $form: null,
            errorClass: 'has-error',
            groupSelector: '.form-group',
            template: '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> _message_</label><br/>',
            disableRedirect: false, //
            columnSelectors: {}, //
            disableRemoveError: false,
            before: function () {},
            after: function () {},
        }, opts);

        var originalVals = {},
            cls = opts.errorClass,
            groupSlt = opts.groupSelector,
            tpl = opts.template,
            $form = opts.$form,
            tabSelector = '.tab-pane',
            get_tab_id = function ($c) {
                return $c.parents(tabSelector).attr('id');
            },
            get_tab_title_error = function ($c) {
                var id = get_tab_id($c);
                if (!id) return $('<none></none>');
                return $("[href='#" + id + "'] .text-red");
            };

        var self = this;

        // 移除错误信息
        remove_field_error();

        $form.ajaxSubmit({
            beforeSubmit: function (d, f, o) {
                if (opts.before(d, f, o, self) === false) {
                    return false;
                }

                if (fire(LA._form_.before, d, f, o, self) === false) {
                    return false;
                }

                LA.NP.start();
            },
            success: function (d) {
                LA.NP.done();

                if (opts.after(true, d, self) === false) {
                    return;
                }

                if (fire(LA._form_.success, d, self) === false) {
                    return;
                }

                if (!d.status) {
                    LA.error(d.message || 'Save failed!');
                    return;
                }

                LA.success(d.message || 'Save succeeded!');

                if (opts.disableRedirect || d.redirect === false) return;

                if (d.redirect) {
                    return LA.reload(d.redirect);
                }

                history.back(-1);
            },
            error: function (v) {
                LA.NP.done();

                if (opts.after(false, v, self) === false) {
                    return;
                }

                if (fire(LA._form_.error, v, self) === false) {
                    return;
                }

                try {
                    var error = JSON.parse(v.responseText), i;

                    if (v.status != 422 || !error || !LA.isset(error, 'errors')) {
                        return LA.error(v.status + ' ' + v.statusText);
                    }
                    error = error.errors;

                    for (i in error) {
                        // 显示错误信息
                        $eColumns[i] = show_field_error($form, i, error[i]);
                    }

                } catch (e) {
                    return LA.error(v.status + ' ' + v.statusText);
                }
            }
        });

        // 触发钩子事件
        function fire(evs) {
            var i, j, r, args = arguments, p = [];
            delete args[0];
            args = args || [];

            for (j in args) {
                p.push(args[j]);
            }

            for (i in evs) {
                r = evs[i].apply(evs[i], p);

                if (r === false) return r; // 返回 false 会代码阻止继续执行
            }
        }

        // 删除错误有字段的错误信息
        function remove_field_error() {
            var i, p, t;
            for (i in $eColumns) {
                p = $eColumns[i].parents(groupSlt);
                p.removeClass(cls);
                p.find('error').html('');

                t = get_tab_title_error($eColumns[i]);
                if (!t.hasClass('hide')) {
                    t.addClass('hide');
                }

            }
            // 重置
            $eColumns = {};
        }

        // 显示错误信息
        function show_field_error($form, column, errors) {
            var $c = get_field_obj($form, column);

            get_tab_title_error($c).removeClass('hide');

            // 保存字段原始数据
            originalVals[column] = get_val($c);

            if (!$c) {
                if (LA.len(errors) && errors.length) {
                    LA.error(errors.join("  \n  "));
                }
                return;
            }

            var p = $c.closest(groupSlt), j;

            p.addClass(cls);

            for (j in errors) {
                p.find('error').eq(0).append(tpl.replace('_message_', errors[j]));
            }

            if (!opts.disableRemoveError) {
                remove_error_when_val_changed($c, column);
            }

            return $c;
        }

        // 获取字段对象
        function get_field_obj($form, column) {
            if (column.indexOf('.') != -1) {
                column = column.split('.');
                var first = column.shift(), i, sub = '';
                for (i in column) {
                    sub += '[' + column[i] + ']';
                }
                column = first + sub;
            }

            var $c = $form.find('[name="' + column + '"]');

            if (!$c.length) $c = $form.find('[name="' + column + '[]"]');

            if (!$c.length) {
                $c = $form.find('[name="' + column.replace(/start$/, '') + '"]');
            }
            if (!$c.length) {
                $c = $form.find('[name="' + column.replace(/end$/, '') + '"]');
            }

            if (!$c.length) {
                $c = $form.find('[name="' + column.replace(/start\]$/, ']') + '"]');
            }
            if (!$c.length) {
                $c = $form.find('[name="' + column.replace(/end\]$/, ']') + '"]');
            }

            return $c;
        }

        // 获取字段值
        function get_val($c) {
            var vals = [],
                t = $c.attr('type'),
                cked = t === 'checkbox' || t === 'radio',
                i;

            for (i = 0; i < $c.length; i++) {
                if (cked) {
                    vals.push($($c[i]).prop('checked'));
                    continue;
                }
                vals.push($($c[i]).val());
            }

            return vals;
        }

        // 当字段值变化时移除错误信息
        function remove_error_when_val_changed($c, column) {
            var p = $c.parents(groupSlt);

            $c.one('change', rm);
            $c.off('blur', rm).on('blur', function () {
                if (val_changed()) rm();
            });

            // 表单值发生变化就移除错误信息
            function autorm() {
                setTimeout(function () {
                    if (!$c.length) return;
                    if (val_changed()) return rm();

                    autorm();
                }, 500);
            }

            autorm();

            // 判断值是否改变
            function val_changed() {
                return !LA.arr.equal(originalVals[column], get_val($c));
            }

            function rm() {
                p.removeClass(cls);
                p.find('error').html('');

                // tab页下没有错误信息了，隐藏title的错误图标
                var id = get_tab_id($c), t;
                if (id && !$('#'+id).find('.'+cls).length) {
                    t = get_tab_title_error($c);
                    if (!t.hasClass('hide')) {
                        t.addClass('hide');
                    }

                }
                delete $eColumns[column];
            }

        }

    };
})();

(function (w) {
    /**
     * 表单弹窗
     * @param opt
     * @constructor
     */
    LA.ModalForm = function (opt) {
        var number = 1,
            defUrl = opt.defaultUrl,
            btn = opt.buttonSelector,
            area = opt.area,
            title = opt.title,
            lang = {
                submit: opt.lang.submit,
                reset: opt.lang.reset,
                save_failed: opt.lang.save_failed,
            },
            nullFun = function (a, b) {},
            handlers = {
                saved: opt.saved || nullFun,
                success: opt.success || nullFun,
                error: opt.error || nullFun
            },
            lay = w.layer,
            forceRefresh = opt.forceRefresh,
            disableReset = opt.disableReset,
            idx = {},
            $layWin = {},
            queryString = opt.query,
            building,
            submitting,
            $btn;

        (!btn) || $(btn).off('click').click(function () {
            var t = $(this), num = t.attr('number'), url;
            $btn = t;
            if (!num) {
                num = number;
                t.attr('number', number);
                number++;
            }

            url = t.data('url') || defUrl;  // 给弹窗页面链接追加参数
            if (url.indexOf('?') == -1) {
                url += '?'+queryString+'=1'
            } else if (url.indexOf(queryString) == -1) {
                url += '&'+queryString+'=1'
            }
            build(url, num);
        });
        btn || setTimeout(function () {
            build(defUrl, number)
        }, 400);

        // 开始构建弹窗
        function build(url, num) {
            if (!url || building) return;
            if ($layWin[num]) { // 阻止同个类型的弹窗弹出多个
                $layWin[num].show();
                try { lay.restore(idx[num]); } catch (e) {}
                return;
            }
            $(w.document).one('pjax:complete', function () { // 跳转新页面时移除弹窗
                rm(num);
            });

            building = 1;
            (!$btn) || $btn.button('loading');

            $.get(url, function (tpl) {
                building = 0;
                if ($btn) {
                    $btn.button('reset');
                    setTimeout(function () {
                        $btn.find('.waves-ripple').remove();
                    }, 50);
                }
                popup(tpl, num);
            });
        }

        // 弹出弹窗
        function popup(tpl, num) {
            tpl = LA.AssetsLoader.filterScriptAndAutoLoad(tpl).render();
            var t = $(tpl), $form, btns = [lang.submit], opts = {
                type: 1,
                area: formatArea(area),
                content: tpl,
                title: title,
                yes: submit,
                cancel: function () {
                    if (forceRefresh) { // 是否强制刷新
                        $layWin[num] = idx[num] = null;
                    } else {
                        $layWin[num].hide();
                        return false;
                    }
                }
            };

            if (!disableReset) {
                btns.push(lang.reset);

                opts.btn2 = function () { // 重置按钮
                    $form = $form || $('#'+t.find('form').attr('id'));
                    $form.trigger('reset');
                    return false;
                };
            }

            opts.btn = btns;

            idx[num] = lay.open(opts);
            $layWin[num] = w.$('#layui-layer' + idx[num]);

            // 提交表单
            function submit () {
                if (submitting) return;
                $form = $form || w.$('#'+t.find('form').attr('id'));  // 此处必须重新创建jq对象，否则无法操作页面元素

                LA.Form({
                    $form: $form,
                    disableRedirect: true,
                    before: function () {
                        $form.validator('validate');

                        if ($form.find('.has-error').length > 0) {
                            return false;
                        }

                        submitting = 1;

                        $layWin[num].find('.layui-layer-btn0').button('loading');
                    },
                    after: function (success, res) {
                        $layWin[num].find('.layui-layer-btn0').button('reset');
                        submitting = 0;

                        handlers.saved(success, res);

                        if (!success) {
                            return handlers.error(success, res);
                        }
                        if (res.status) {
                            handlers.success(success, res);
                            rm(num);
                            return;
                        }

                        handlers.error(success, res);
                        LA.error(res.message || lang.save_failed);
                    }
                });

                return false;

            }
        }

        function formatArea(area) {
            if (w.screen.width <= 800) {
                return ['100%', '100%',];
            }

            return area;
        }

        // 移除弹窗
        function rm(num) {
            lay.close(idx[num]);
            $layWin[num] && $layWin[num].remove();
            $layWin[num] = null;
        }
    };
})(top || window);

(function () {
    var tpl = '<div class="_loading_ flex items-center justify-center pin" style="{style}">{svg}</div>',
        loading = '._loading_',
        LOADING_SVG = [
            '<svg width="{width}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-disk" style="background: none;"><g transform="translate(50,50)"><g ng-attr-transform="scale({{config.scale}})" transform="scale(0.5)"><circle cx="0" cy="0" r="50" ng-attr-fill="{{config.c1}}" fill="{color}"></circle><circle cx="0" ng-attr-cy="{{config.cy}}" ng-attr-r="{{config.r}}" ng-attr-fill="{{config.c2}}" cy="-35" r="15" fill="#ffffff" transform="rotate(101.708)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 0 0;360 0 0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></g></g></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:{width};{svg_style}" viewBox="0 0 120 30" fill="{color}"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"/><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg>',
        ];

    /**
     * Loading
     *
     * @param opts
     * @constructor
     */
    function Loader(opts) {
        var defStyle = 'position:absolute;left:10px;right:10px;', content, $container;

        opts = $.extend({
            container: '#pjax-container',
            z_index: 100,
            width: '50px',
            color: '#84bdea',
            bg: '#fff',
            style: '',
            svg: LOADING_SVG[0]
        }, opts);

        $container = opts.container;
        $container = $container == 'object' ? $container : $($container);

        content = $(
            tpl
                .replace('{svg}', opts.svg)
                .replace('{color}', opts.color)
                .replace('{color}', opts.color)
                .replace('{width}', opts.width)
                .replace('{style}', defStyle + 'background:' + opts.bg + ';' + 'z-index:' + opts.z_index + ';' + opts.style)
        );
        content.appendTo($container);

        this.remove = function () {
            $container.find(loading).remove();
        };
    }

    Loader.destroyAll = function () {
        $(loading).remove();
    };

    LA.Loader = Loader;

    // 全屏居中loading
    LA.loading = function (opts) {
        if (opts === false) {
            // 关闭loading
            return setTimeout(LA.Loader.destroyAll, 70);
        }
        // 配置参数
        opts = $.extend({
            color: '#62abe4',
            z_index: 999991014,
            width: '58px',
            shade: 'rgba(255, 255, 255, 0.02)',
            top: 200,
            svg: LOADING_SVG[1],
        }, opts);

        var win = $(window),
            // 容器
            $container = $('<div class="_loading_" type="loading" times="1" showtime="0" contype="string" style="z-index:'+opts.z_index+';width:300px;position:fixed"></div>'),
            // 遮罩层直接沿用layer
            shadow = $('<div class="layui-layer-shade _loading_" style="z-index:'+(opts.z_index-2)+'; background-color:'+opts.shade+'"></div>');
        $container.appendTo('body');
        if (opts.shade) {
            shadow.appendTo('body');
        }

        function resize() {
            $container.css({
                left: (win.width() - 300)/2,
                top: (win.height() - opts.top)/2
            });
        }
        // 自适应窗口大小
        win.on('resize', resize);
        resize();

        $container.loading(opts);
    };

    $.fn.loading = function (opt) {
        if (opt === false) {
            return $(this).find(loading).remove();
        }

        opt = opt || {};
        opt.container = $(this);

        return new Loader(opt);
    };
})();

(function (win) {
    function AssetsLoader () {
    }

    AssetsLoader.prototype = {
        // 按顺序加载静态资源
        // 并在所有静态资源加载完毕后执行回调函数
        load: function (urls, callback, args) {
            var self = this;
            if (urls.length < 1) {
                (!callback) || callback(args);
                return;
            }
            seajs.use([urls.shift()], function () {
                self.load(urls, callback, args);
            });
        },
        // 过滤 <script src> 标签
        filterScripts: function (content) {
            var obj = {};

            if (typeof content == 'string') content = $(content);

            obj.scripts = findAll(content, 'script[src]').remove();
            obj.contents = content.not(obj.scripts);

            obj.contents.render = toString;
            obj.js = (function () {
                var urls = [];
                obj.scripts.each(function (k, v) {
                    if (v.src) {
                        urls.push(v.src);
                    }
                });

                return urls;
            })();

            return obj;
        },

        // 返回过滤 <script src> 标签后的内容，并在加载完 script 脚本后触发 "pjax:script" 事件
        filterScriptAndAutoLoad: function (content, callback) {
            var obj = this.filterScripts(content);

            this.load(obj.js, function () {
                (!callback) || callback(obj.contents);
                fire();
            });

            return obj.contents;
        },
    };

    function findAll(elems, selector) {
        if (typeof elems == 'string') elems = $(elems);
        return elems.filter(selector).add(elems.find(selector));
    }

    function fire () {
        LA.pjaxresponse = 1;
        // js加载完毕 触发 ready 事件
        // setTimeout用于保证在所有js代码最后执行
        setTimeout(LA.triggerReady, 1);
    }

    function toString (th) {
        var html = '', out;
        this.each(function (k, v) {
            if ((out = v.outerHTML)) {
                html += out;
            }
        });
        return html;
    };

    LA.AssetsLoader = new AssetsLoader;
})(window);

(function () {
    /* @see https://github.com/lodash/lodash/blob/master/debounce.js */
    /* @see https://www.lodashjs.com/docs/lodash.debounce */
    function debounce(func, wait, options) {
        var lastArgs,
            lastThis,
            maxWait,
            result,
            timerId,
            lastCallTime;

        var lastInvokeTime = 0;
        var leading = false;
        var maxing = false;
        var trailing = true;

        if (typeof func !== 'function') {
            throw new TypeError('Expected a function')
        }
        wait = +wait || 0;
        if (isObject(options)) {
            leading = !!options.leading;
            maxing = 'maxWait' in options;
            maxWait = maxing ? Math.max(+options.maxWait || 0, wait) : wait;
            trailing = 'trailing' in options ? !!options.trailing : trailing
        }

        function isObject(value) {
            var type = typeof value;
            return value != null && (type === 'object' || type === 'function')
        }


        function invokeFunc(time) {
            var args = lastArgs;
            var thisArg = lastThis;

            lastArgs = lastThis = undefined;
            lastInvokeTime = time;
            result = func.apply(thisArg, args);
            return result
        }

        function startTimer(pendingFunc, wait) {
            return setTimeout(pendingFunc, wait)
        }

        function cancelTimer(id) {
            clearTimeout(id)
        }

        function leadingEdge(time) {
            // Reset any `maxWait` timer.
            lastInvokeTime = time;
            // Start the timer for the trailing edge.
            timerId = startTimer(timerExpired, wait);
            // Invoke the leading edge.
            return leading ? invokeFunc(time) : result
        }

        function remainingWait(time) {
            var timeSinceLastCall = time - lastCallTime;
            var timeSinceLastInvoke = time - lastInvokeTime;
            var timeWaiting = wait - timeSinceLastCall;

            return maxing
                ? Math.min(timeWaiting, maxWait - timeSinceLastInvoke)
                : timeWaiting
        }

        function shouldInvoke(time) {
            var timeSinceLastCall = time - lastCallTime;
            var timeSinceLastInvoke = time - lastInvokeTime;

            // Either this is the first call, activity has stopped and we're at the
            // trailing edge, the system time has gone backwards and we're treating
            // it as the trailing edge, or we've hit the `maxWait` limit.
            return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
                (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait))
        }

        function timerExpired() {
            var time = Date.now();
            if (shouldInvoke(time)) {
                return trailingEdge(time)
            }
            // Restart the timer.
            timerId = startTimer(timerExpired, remainingWait(time))
        }

        function trailingEdge(time) {
            timerId = undefined;

            // Only invoke if we have `lastArgs` which means `func` has been
            // debounced at least once.
            if (trailing && lastArgs) {
                return invokeFunc(time)
            }
            lastArgs = lastThis = undefined;
            return result
        }

        function cancel() {
            if (timerId !== undefined) {
                cancelTimer(timerId)
            }
            lastInvokeTime = 0;
            lastArgs = lastCallTime = lastThis = timerId = undefined
        }

        function flush() {
            return timerId === undefined ? result : trailingEdge(Date.now())
        }

        function pending() {
            return timerId !== undefined
        }

        function debounced() {
            var time = Date.now();
            var isInvoking = shouldInvoke(time);

            lastArgs = arguments;
            lastThis = this;
            lastCallTime = time;

            if (isInvoking) {
                if (timerId === undefined) {
                    return leadingEdge(lastCallTime)
                }
                if (maxing) {
                    // Handle invocations in a tight loop.
                    timerId = startTimer(timerExpired, wait);
                    return invokeFunc(lastCallTime)
                }
            }
            if (timerId === undefined) {
                timerId = startTimer(timerExpired, wait)
            }
            return result
        }
        debounced.cancel = cancel;
        debounced.flush = flush;
        debounced.pending = pending;
        return debounced
    }

    LA.debounce = debounce;
})();
