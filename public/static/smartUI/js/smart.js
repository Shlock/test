//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

// The following code is heavily inspired by jQuery's $.fn.data()

;(function($){
  var data = {}, dataAttr = $.fn.data, camelize = $.camelCase,
    exp = $.expando = 'Zepto' + (+new Date()), emptyArray = []

  // Get value from node:
  // 1. first try key as given,
  // 2. then try camelized key,
  // 3. fall back to reading "data-*" attribute.
  function getData(node, name) {
    var id = node[exp], store = id && data[id]
    if (name === undefined) return store || setData(node)
    else {
      if (store) {
        if (name in store) return store[name]
        var camelName = camelize(name)
        if (camelName in store) return store[camelName]
      }
      return dataAttr.call($(node), name)
    }
  }

  // Store value under camelized key on node
  function setData(node, name, value) {
    var id = node[exp] || (node[exp] = ++$.uuid),
      store = data[id] || (data[id] = attributeData(node))
    if (name !== undefined) store[camelize(name)] = value
    return store
  }

  // Read all "data-*" attributes from a node
  function attributeData(node) {
    var store = {}
    $.each(node.attributes || emptyArray, function(i, attr){
      if (attr.name.indexOf('data-') == 0)
        store[camelize(attr.name.replace('data-', ''))] =
          $.zepto.deserializeValue(attr.value)
    })
    return store
  }

  $.fn.data = function(name, value) {
    return value === undefined ?
      // set multiple values via object
      $.isPlainObject(name) ?
        this.each(function(i, node){
          $.each(name, function(key, value){ setData(node, key, value) })
        }) :
        // get value from first element
        (0 in this ? getData(this[0], name) : undefined) :
      // set value on all elements
      this.each(function(){ setData(this, name, value) })
  }

  $.fn.removeData = function(names) {
    if (typeof names == 'string') names = names.split(/\s+/)
    return this.each(function(){
      var id = this[exp], store = id && data[id]
      if (store) $.each(names || store, function(key){
        delete store[names ? camelize(this) : key]
      })
    })
  }

  // Generate extended `remove` and `empty` functions
  ;['remove', 'empty'].forEach(function(methodName){
    var origFn = $.fn[methodName]
    $.fn[methodName] = function() {
      var elements = this.find('*')
      if (methodName === 'remove') elements = elements.add(this)
      elements.removeData()
      return origFn.call(this)
    }
  })
})(Zepto)




//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

// The following code is heavily inspired by jQuery's $.fn.data()

;
(function($) {

  var util = new Object();
  util.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
      options = options || {};
      if (value === null) {
        value = '';
        options.expires = -1;
      }
      var expires = '';
      if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
        var date;
        if (typeof options.expires == 'number') {
          date = new Date();
          date.setTime(date.getTime() + (options.expires * 1000));
        } else {
          date = options.expires;
        }
        expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
      }
      var path = options.path ? '; path=' + options.path : '';
      var domain = options.domain ? '; domain=' + options.domain : '';
      var secure = options.secure ? '; secure' : '';
      document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
      var cookieValue = null;
      if (document.cookie && document.cookie != '') {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
          var cookie = cookies[i].replace(/^\s*(.*?)\s*$/, "$1"); //this.trim(cookies[i]);
          // Does this cookie string begin with the name we want?
          if (cookie.substring(0, name.length + 1) == (name + '=')) {
            cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
            break;
          }
        }
      }
      return cookieValue;
    }
  };
  util.getACSRFToken = function() {
    var skey = util.cookie('skey');
    if ('undefined' == typeof(skey) || !skey) {
      skey = '';
    }
    if (skey == '') {
      var lskey = util.cookie('lskey');
      if ('undefined' == typeof(lskey) || !lskey) {
        lskey = '';
      }
      skey = lskey;
    }
    var hash = 5381;
    for (var i = 0, len = skey.length; i < len; ++i) {
      hash += (hash << 5 & 0x7fffffff) + skey.charAt(i).charCodeAt();
    }
    return hash & 0x7fffffff;
  };
  util.setACSRFToken = function() {
    //给jquery的ajax自动添加CSRFToken
    if (typeof $ != 'undefined') {
      $.ajaxSettings = $.extend($.ajaxSettings, {
        'beforeSend': function(xhr, options) {
            var token = util.getACSRFToken();
            var url = options.url;
            if (options.type.toLowerCase() == "get") {
              options.url += url.indexOf("?") > 0 ? (url.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "") : "?g_tk=" + token;
            } else {
              if (options.data) {
                if (typeof options.data == 'string') {
                  options.data += options.data.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "";
                }
              } else {
                options.data = "g_tk=" + token;
                options.url += url.indexOf("?") > 0 ? (url.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "") : "?g_tk=" + token;
              }
            }
            return options;
          }
          // whatever
      });
      /*$.ajaxSetup({
        'beforeSend': function(xhr,options){
          var token = util.getACSRFToken();
          var url = options.url;
          if(options.type.toLowerCase() == "get"){
            options.url += url.indexOf("?") > 0 ? (url.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "") : "?g_tk=" + token;
          }else{
            if(options.data){
              if(typeof options.data == 'string'){
                options.data += options.data.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "";
              }
            }else{
              options.data = "g_tk=" + token;
              options.url += url.indexOf("?") > 0 ? (url.indexOf("g_tk=") < 0 ? "&g_tk=" + token : "") : "?g_tk=" + token;
            }
          }
          return options;     
        }
      })*/
    }
  }
  util.setACSRFToken();
})(window.Zepto)
/*!art-template - Template Engine | http://aui.github.com/artTemplate/*/
!function(){function a(a){return a.replace(t,"").replace(u,",").replace(v,"").replace(w,"").replace(x,"").split(y)}function b(a){return"'"+a.replace(/('|\\)/g,"\\$1").replace(/\r/g,"\\r").replace(/\n/g,"\\n")+"'"}function c(c,d){function e(a){return m+=a.split(/\n/).length-1,k&&(a=a.replace(/\s+/g," ").replace(/<!--[\w\W]*?-->/g,"")),a&&(a=s[1]+b(a)+s[2]+"\n"),a}function f(b){var c=m;if(j?b=j(b,d):g&&(b=b.replace(/\n/g,function(){return m++,"$line="+m+";"})),0===b.indexOf("=")){var e=l&&!/^=[=#]/.test(b);if(b=b.replace(/^=[=#]?|[\s;]*$/g,""),e){var f=b.replace(/\s*\([^\)]+\)/,"");n[f]||/^(include|print)$/.test(f)||(b="$escape("+b+")")}else b="$string("+b+")";b=s[1]+b+s[2]}return g&&(b="$line="+c+";"+b),r(a(b),function(a){if(a&&!p[a]){var b;b="print"===a?u:"include"===a?v:n[a]?"$utils."+a:o[a]?"$helpers."+a:"$data."+a,w+=a+"="+b+",",p[a]=!0}}),b+"\n"}var g=d.debug,h=d.openTag,i=d.closeTag,j=d.parser,k=d.compress,l=d.escape,m=1,p={$data:1,$filename:1,$utils:1,$helpers:1,$out:1,$line:1},q="".trim,s=q?["$out='';","$out+=",";","$out"]:["$out=[];","$out.push(",");","$out.join('')"],t=q?"$out+=text;return $out;":"$out.push(text);",u="function(){var text=''.concat.apply('',arguments);"+t+"}",v="function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);"+t+"}",w="'use strict';var $utils=this,$helpers=$utils.$helpers,"+(g?"$line=0,":""),x=s[0],y="return new String("+s[3]+");";r(c.split(h),function(a){a=a.split(i);var b=a[0],c=a[1];1===a.length?x+=e(b):(x+=f(b),c&&(x+=e(c)))});var z=w+x+y;g&&(z="try{"+z+"}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:"+b(c)+".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}");try{var A=new Function("$data","$filename",z);return A.prototype=n,A}catch(B){throw B.temp="function anonymous($data,$filename) {"+z+"}",B}}var d=function(a,b){return"string"==typeof b?q(b,{filename:a}):g(a,b)};d.version="3.0.0",d.config=function(a,b){e[a]=b};var e=d.defaults={openTag:"<%",closeTag:"%>",escape:!0,cache:!0,compress:!1,parser:null},f=d.cache={};d.render=function(a,b){return q(a,b)};var g=d.renderFile=function(a,b){var c=d.get(a)||p({filename:a,name:"Render Error",message:"Template not found"});return b?c(b):c};d.get=function(a){var b;if(f[a])b=f[a];else if("object"==typeof document){var c=document.getElementById(a);if(c){var d=(c.value||c.innerHTML).replace(/^\s*|\s*$/g,"");b=q(d,{filename:a})}}return b};var h=function(a,b){return"string"!=typeof a&&(b=typeof a,"number"===b?a+="":a="function"===b?h(a.call(a)):""),a},i={"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"},j=function(a){return i[a]},k=function(a){return h(a).replace(/&(?![\w#]+;)|[<>"']/g,j)},l=Array.isArray||function(a){return"[object Array]"==={}.toString.call(a)},m=function(a,b){var c,d;if(l(a))for(c=0,d=a.length;d>c;c++)b.call(a,a[c],c,a);else for(c in a)b.call(a,a[c],c)},n=d.utils={$helpers:{},$include:g,$string:h,$escape:k,$each:m};d.helper=function(a,b){o[a]=b};var o=d.helpers=n.$helpers;d.onerror=function(a){var b="Template Error\n\n";for(var c in a)b+="<"+c+">\n"+a[c]+"\n\n";"object"==typeof console&&console.error(b)};var p=function(a){return d.onerror(a),function(){return"{Template Error}"}},q=d.compile=function(a,b){function d(c){try{return new i(c,h)+""}catch(d){return b.debug?p(d)():(b.debug=!0,q(a,b)(c))}}b=b||{};for(var g in e)void 0===b[g]&&(b[g]=e[g]);var h=b.filename;try{var i=c(a,b)}catch(j){return j.filename=h||"anonymous",j.name="Syntax Error",p(j)}return d.prototype=i.prototype,d.toString=function(){return i.toString()},h&&b.cache&&(f[h]=d),d},r=n.$each,s="break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",t=/\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g,u=/[^\w$]+/g,v=new RegExp(["\\b"+s.replace(/,/g,"\\b|\\b")+"\\b"].join("|"),"g"),w=/^\d[^,]*|,\d[^,]*/g,x=/^,+|,+$/g,y=/^$|,+/;e.openTag="{{",e.closeTag="}}";var z=function(a,b){var c=b.split(":"),d=c.shift(),e=c.join(":")||"";return e&&(e=", "+e),"$helpers."+d+"("+a+e+")"};e.parser=function(a){a=a.replace(/^\s/,"");var b=a.split(" "),c=b.shift(),e=b.join(" ");switch(c){case"if":a="if("+e+"){";break;case"else":b="if"===b.shift()?" if("+b.join(" ")+")":"",a="}else"+b+"{";break;case"/if":a="}";break;case"each":var f=b[0]||"$data",g=b[1]||"as",h=b[2]||"$value",i=b[3]||"$index",j=h+","+i;"as"!==g&&(f="[]"),a="$each("+f+",function("+j+"){";break;case"/each":a="});";break;case"echo":a="print("+e+");";break;case"print":case"include":a=c+"("+b.join(",")+");";break;default:if(/^\s*\|\s*[\w\$]/.test(e)){var k=!0;0===a.indexOf("#")&&(a=a.substr(1),k=!1);for(var l=0,m=a.split("|"),n=m.length,o=m[l++];n>l;l++)o=z(o,m[l]);a=(k?"=":"=#")+o}else a=d.helpers[c]?"=#"+c+"("+b.join(",")+");":"="+a}return a},"function"==typeof define?define(function(){return d}):"undefined"!=typeof exports?module.exports=d:this.template=d}();
/**
 * asynclist组件
 *
 * @date     2015-08-10
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Asynclist = function(that, callback) {
        this.obj = that;
        this.haveLoading = false;
        this.callback = callback;
    };
    Asynclist.prototype = {
        init: function() {
            var _this = this; 
            $(window).scroll(function() {  
                var scrollHeight = $(document).height() - $(window).height();
                var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
                if (scrollt == scrollHeight && !_this.haveLoading) {
                    _this.haveLoading = true;
                    $(_this.obj).append('<div class="' + ns + '-asynclist">加载中...</div>');
                    _this.callback();
                };
            });  
        },
        success: function() {
            $("." + ns + "-asynclist").remove();
            this.haveLoading = false;
            this.init();
        },
        finish: function() {
            $("." + ns + "-asynclist").remove();
            this.haveLoading = true;
            $(this.obj).append('<div class="' + ns + '-asynclist">没有更多了</div>');
        }
    };
    function Plugin(callback) {
        var asynclist = new Asynclist(this, callback);
        asynclist.init();
        return asynclist;
    };
    $.fn.asynclist = Plugin;
}(window.Zepto);
/**
 * countdown组件
 *
 * @date     2015-09-01
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart-countdown";
    var Countdown = function() {};
    Countdown.prototype = {
        build: function(that, terminalTime, offsetTime, callback) {
            var _this = this;
            // 获取当前浏览器时间
            var nowTime = new Date();
            // 计算现在离结束时间还剩多少毫秒，即结束时间减去当前浏览器时间再减去服务器时间与浏览器时间的差值
            var timeDifference = terminalTime - nowTime - offsetTime;
            // 根据当前的timeDifference计算剩余的天、小时、分、秒
            var d = this.timeFormat(parseInt(timeDifference / 1000 / 60 / 60 / 24));
            var h = this.timeFormat(parseInt(timeDifference / 1000 / 60 / 60 % 24));
            var i = this.timeFormat(parseInt(timeDifference / 1000 / 60 % 60));
            var s = this.timeFormat(parseInt(timeDifference / 1000 % 60));
            // 渲染页面
            this.render(that, d, h, i, s);
            if (d == "00" && h == "00" && i == "00" && s == "00") {
                // 如果时间到了执行回调函数
                callback();
            } else {
                // 如果时间没到隔一秒钟后重新执行build方法
                // 注意这里并不是每秒钟把时间减一秒，而是每秒钟重新计算一遍timeDifference，虽然有可能会出现跳秒的情况，但时间相对更准确一些
                setTimeout(function() {
                    _this.build(that, terminalTime, offsetTime, callback);
                }, 1000);
            };
        },
        render: function(that, d, h, i, s) {
            $(that).find("." + ns + "-day").text(d);
            $(that).find("." + ns + "-hour").text(h);
            $(that).find("." + ns + "-minute").text(i);
            $(that).find("." + ns + "-second").text(s);
        },
        timeFormat: function(t) {
            if (t < 0) {
                // 如果当前已过了结束时间，把显示的时间设为"00"，避免出现负值
                t = "00";
            } else if (t < 10) {
                // 如果显示时间为个位数，在前面补0来统一格式
                t = "0" + t;
            };
            return t;
        }
    };
    //  把时间字符串（格式为"2015-12-01 17:07:07"）转化为Date对象
    function dateStrToObj(timeStr) {
        var date = timeStr.split(" ")[0], time = timeStr.split(" ")[1];
        var Y, m, d, H, i, s;
        Y = date.split("-")[0];
        m = date.split("-")[1] - 1;
        d = date.split("-")[2];
        H = time.split(":")[0];
        i = time.split(":")[1];
        s = time.split(":")[2];
        var dateObj = new Date(Y, m, d, H, i, s);
        return dateObj;
    };
    function Plugin(terminalTime, offsetTime, callback) {
        var countdown = new Countdown();
        countdown.build(this, dateStrToObj(terminalTime), offsetTime, callback);
    };
    $.fn.countdown = Plugin;
}(window.Zepto);
/**
 * 日期选择器
 *
 *
 * @date     2015-07-05
 * @author   Yuxingyang<yuxingyang@tencent.com>
 */

//第三方日期选择插件
(function($) {
  function Scroller(elem, settings) {
    var m,
      hi,
      v,
      dw,
      ww, // Window width
      wh, // Window height
      rwh,
      mw, // Modal width
      mh, // Modal height
      lock,
      anim,
      debounce,
      theme,
      lang,
      click,
      scrollable,
      moved,
      start,
      startTime,
      stop,
      p,
      min,
      max,
      target,
      index,
      timer,
      readOnly,
      preventShow,
      that = this,
      ms = $.mobiscroll,
      e = elem,
      elm = $(e),
      s = extend({}, defaults),
      pres = {},
      iv = {},
      pos = {},
      pixels = {},
      wheels = [],
      input = elm.is('input'),
      visible = false,
      onStart = function(e) {
        // Scroll start
        if (testTouch(e) && !move && !isReadOnly(this) && !click) {
          // Prevent scroll
          e.preventDefault();

          move = true;
          scrollable = s.mode != 'clickpick';
          target = $('.dw-ul', this);
          setGlobals(target);
          moved = iv[index] !== undefined; // Don't allow tap, if still moving
          p = moved ? getCurrentPosition(target) : pos[index];
          start = getCoord(e, 'Y');
          startTime = new Date();
          stop = start;
          scroll(target, index, p, 0.001);

          if (scrollable) {
            target.closest('.dwwl').addClass('dwa');
          }

          $(document).on(MOVE_EVENT, onMove).on(END_EVENT, onEnd);
        }
      },
      onMove = function(e) {
        if (scrollable) {
          e.preventDefault();
          e.stopPropagation();
          stop = getCoord(e, 'Y');
          scroll(target, index, constrain(p + (start - stop) / hi, min - 1, max + 1));
        }
        moved = true;
      },
      onEnd = function(e) {
        var time = new Date() - startTime,
          val = constrain(p + (start - stop) / hi, min - 1, max + 1),
          speed,
          dist,
          tindex,
          ttop = target.offset().top;

        if (time < 300) {
          speed = (stop - start) / time;
          dist = (speed * speed) / s.speedUnit;
          if (stop - start < 0) {
            dist = -dist;
          }
        } else {
          dist = stop - start;
        }

        tindex = Math.round(p - dist / hi);

        if (!dist && !moved) { // this is a "tap"
          var idx = Math.floor((stop - ttop) / hi),
            li = $('.dw-li', target).eq(idx),
            hl = scrollable;

          if (event('onValueTap', [li]) !== false) {
            tindex = idx;
          } else {
            hl = true;
          }

          if (hl) {
            li.addClass('dw-hl'); // Highlight
            setTimeout(function() {
              li.removeClass('dw-hl');
            }, 200);
          }
        }

        if (scrollable) {
          calc(target, tindex, 0, true, Math.round(val));
        }

        move = false;
        target = null;

        $(document).off(MOVE_EVENT, onMove).off(END_EVENT, onEnd);
      },
      onBtnStart = function(e) {
        var btn = $(this);
        $(document).on(END_EVENT, onBtnEnd);
        // Active button
        if (!btn.hasClass('dwb-d')) {
          btn.addClass('dwb-a');
        }
        setTimeout(function() {
          btn.blur();
        }, 10);
        // +/- buttons
        if (btn.hasClass('dwwb')) {
          if (testTouch(e)) {
            step(e, btn.closest('.dwwl'), btn.hasClass('dwwbp') ? plus : minus);
          }
        }
      },
      onBtnEnd = function(e) {
        if (click) {
          clearInterval(timer);
          click = false;
        }
        $(document).off(END_EVENT, onBtnEnd);
        $('.dwb-a', dw).removeClass('dwb-a');
      },
      onKeyDown = function(e) {
        if (e.keyCode == 38) { // up
          step(e, $(this), minus);
        } else if (e.keyCode == 40) { // down
          step(e, $(this), plus);
        }
      },
      onKeyUp = function(e) {
        if (click) {
          clearInterval(timer);
          click = false;
        }
      },
      onScroll = function(e) {
        if (!isReadOnly(this)) {
          e.preventDefault();
          e = e.originalEvent || e;
          var delta = e.wheelDelta ? (e.wheelDelta / 120) : (e.detail ? (-e.detail / 3) : 0),
            t = $('.dw-ul', this);

          setGlobals(t);
          calc(t, Math.round(pos[index] - delta), delta < 0 ? 1 : 2);
        }
      };

    // Private functions

    function step(e, w, func) {
      e.stopPropagation();
      e.preventDefault();
      if (!click && !isReadOnly(w) && !w.hasClass('dwa')) {
        click = true;
        // + Button
        var t = w.find('.dw-ul');

        setGlobals(t);
        clearInterval(timer);
        timer = setInterval(function() {
          func(t);
        }, s.delay);
        func(t);
      }
    }

    function isReadOnly(wh) {
      if ($.isArray(s.readonly)) {
        var i = $('.dwwl', dw).index(wh);
        return s.readonly[i];
      }
      return s.readonly;
    }

    function generateWheelItems(i) {
      var html = '<div class="dw-bf">',
        ww = wheels[i],
        w = ww.values ? ww : convert(ww),
        l = 1,
        labels = w.labels || [],
        values = w.values,
        keys = w.keys || values;

      $.each(values, function(j, v) {
        if (l % 20 == 0) {
          html += '</div><div class="dw-bf">';
        }
        html += '<div role="option" aria-selected="false" class="dw-li dw-v" data-val="' + keys[j] + '"' + (labels[j] ? ' aria-label="' + labels[j] + '"' : '') + ' style="height:' + hi + 'px;line-height:' + hi + 'px;"><div class="dw-i">' + v + '</div></div>';
        l++;
      });

      html += '</div>';
      return html;
    }

    function setGlobals(t) {
      min = $('.dw-li', t).index($('.dw-v', t).eq(0));
      max = $('.dw-li', t).index($('.dw-v', t).eq(-1));
      index = $('.dw-ul', dw).index(t);
    }

    function formatHeader(v) {
      var t = s.headerText;
      return t ? (typeof t === 'function' ? t.call(e, v) : t.replace(/\{value\}/i, v)) : '';
    }

    function read() {
      that.temp = ((input && that.val !== null && that.val != elm.val()) || that.values === null) ? s.parseValue(elm.val() || '', that) : that.values.slice(0);
      setVal();
    }

    function getCurrentPosition(t) {
      var style = window.getComputedStyle ? getComputedStyle(t[0]) : t[0].style,
        matrix,
        px;

      if (has3d) {
        $.each(['t', 'webkitT', 'MozT', 'OT', 'msT'], function(i, v) {
          if (style[v + 'ransform'] !== undefined) {
            matrix = style[v + 'ransform'];
            return false;
          }
        });
        matrix = matrix.split(')')[0].split(', ');
        px = matrix[13] || matrix[5];
      } else {
        px = style.top.replace('px', '');
      }

      return Math.round(m - (px / hi));
    }

    function ready(t, i) {
      clearTimeout(iv[i]);
      delete iv[i];
      t.closest('.dwwl').removeClass('dwa');
    }

    function scroll(t, index, val, time, active) {

      var px = (m - val) * hi,
        style = t[0].style,
        i;

      if (px == pixels[index] && iv[index]) {
        return;
      }

      if (time && px != pixels[index]) {
        // Trigger animation start event
        event('onAnimStart', [dw, index, time]);
      }

      pixels[index] = px;

      style[pr + 'Transition'] = 'all ' + (time ? time.toFixed(3) : 0) + 's ease-out';

      if (has3d) {
        style[pr + 'Transform'] = 'translate3d(0,' + px + 'px,0)';
      } else {
        style.top = px + 'px';
      }

      if (iv[index]) {
        ready(t, index);
      }

      if (time && active) {
        t.closest('.dwwl').addClass('dwa');
        iv[index] = setTimeout(function() {
          ready(t, index);
        }, time * 1000);
      }

      pos[index] = val;
    }

    function scrollToPos(time, index, manual, dir, active) {

      // Call validation event
      if (event('validate', [dw, index, time]) !== false) {

        // Set scrollers to position
        $('.dw-ul', dw).each(function(i) {
          var t = $(this),
            cell = $('.dw-li[data-val="' + that.temp[i] + '"]', t),
            cells = $('.dw-li', t),
            v = cells.index(cell),
            l = cells.length,
            sc = i == index || index === undefined;

          // Scroll to a valid cell
          if (!cell.hasClass('dw-v')) {
            var cell1 = cell,
              cell2 = cell,
              dist1 = 0,
              dist2 = 0;

            while (v - dist1 >= 0 && !cell1.hasClass('dw-v')) {
              dist1++;
              cell1 = cells.eq(v - dist1);
            }

            while (v + dist2 < l && !cell2.hasClass('dw-v')) {
              dist2++;
              cell2 = cells.eq(v + dist2);
            }

            // If we have direction (+/- or mouse wheel), the distance does not count
            if (((dist2 < dist1 && dist2 && dir !== 2) || !dist1 || (v - dist1 < 0) || dir == 1) && cell2.hasClass('dw-v')) {
              cell = cell2;
              v = v + dist2;
            } else {
              cell = cell1;
              v = v - dist1;
            }
          }

          if (!(cell.hasClass('dw-sel')) || sc) {
            // Set valid value
            that.temp[i] = cell.attr('data-val');

            // Add selected class to cell
            $('.dw-sel', t).removeClass('dw-sel');

            if (!s.multiple) {
              $('.dw-sel', t).removeAttr('aria-selected');
              cell.attr('aria-selected', 'true');
            }

            cell.addClass('dw-sel');

            // Scroll to position
            scroll(t, i, v, sc ? time : 0.1, sc ? active : false);
          }
        });

        // Reformat value if validation changed something
        v = s.formatResult(that.temp);
        if (s.display == 'inline') {
          setVal(manual, 0, true);
        } else {
          $('.dwv', dw).html(formatHeader(v));
        }

        if (manual) {
          event('onChange', [v]);
        }
      }

    }

    function event(name, args) {
      var ret;
      args.push(that);
      $.each([theme.defaults, pres, settings], function(i, v) {
        if (v[name]) { // Call preset event
          ret = v[name].apply(e, args);
        }
      });
      return ret;
    }

    function calc(t, val, dir, anim, orig) {
      val = constrain(val, min, max);

      var cell = $('.dw-li', t).eq(val),
        o = orig === undefined ? val : orig,
        idx = index,
        time = anim ? (val == o ? 0.1 : Math.abs((val - o) * s.timeUnit)) : 0;

      // Set selected scroller value
      that.temp[idx] = cell.attr('data-val');

      scroll(t, idx, val, time, orig);

      setTimeout(function() {
        // Validate
        scrollToPos(time, idx, true, dir, orig !== undefined);
      }, 10);
    }

    function plus(t) {
      var val = pos[index] + 1;
      calc(t, val > max ? min : val, 1, true);
    }

    function minus(t) {
      var val = pos[index] - 1;
      calc(t, val < min ? max : val, 2, true);
    }

    function setVal(fill, time, noscroll, temp) {

      if (visible && !noscroll) {
        scrollToPos(time);
      }

      v = s.formatResult(that.temp);

      if (!temp) {
        that.values = that.temp.slice(0);
        that.val = v;
      }

      if (fill) {
        if (input) {
          elm.val(v).trigger('change');
        }
      }
    }

    // Public functions

    that.position = function(check) {

      if (s.display == 'inline' || (ww === $(window).width() && rwh === $(window).height() && check) || (event('onPosition', [dw]) === false)) {
        return;
      }

      var w,
        l,
        t,
        aw, // anchor width
        ah, // anchor height
        ap, // anchor position
        at, // anchor top
        al, // anchor left
        arr, // arrow
        arrw, // arrow width
        arrl, // arrow left
        scroll,
        totalw = 0,
        minw = 0,
        st = $(window).scrollTop(),
        wr = $('.dwwr', dw),
        d = $('.dw', dw),
        css = {},
        anchor = s.anchor === undefined ? elm : s.anchor;

      ww = $(window).width();
      rwh = $(window).height();
      wh = window.innerHeight; // on iOS we need innerHeight
      wh = wh || rwh;

      if (/modal|bubble/.test(s.display)) {
        $('.dwc', dw).each(function() {
          w = $(this).width();
          totalw += w;
          minw = (w > minw) ? w : minw;
        });
        w = totalw > ww ? minw : totalw;
        wr.width(w).css('white-space', totalw > ww ? '' : 'nowrap');
      }

      mw = d.width();
      mh = d.height();

      lock = mh <= wh && mw <= ww;

      if (s.display == 'modal') {
        l = (ww - mw) / 2;
        t = st + (wh - mh) / 2;
      } else if (s.display == 'bubble') {
        scroll = true;
        arr = $('.dw-arrw-i', dw);
        ap = anchor.offset();
        at = ap.top;
        al = ap.left;

        // horizontal positioning
        aw = anchor.width();
        ah = anchor.height();
        l = al - (d.width() - aw) / 2;
        l = l > (ww - mw) ? (ww - (mw + 20)) : l;
        l = l >= 0 ? l : 20;

        // vertical positioning
        t = at - mh; // above the input
        if ((t < st) || (at > st + wh)) { // if doesn't fit above or the input is out of the screen
          d.removeClass('dw-bubble-top').addClass('dw-bubble-bottom');
          t = at + ah; // below the input
        } else {
          d.removeClass('dw-bubble-bottom').addClass('dw-bubble-top');
        }

        // Calculate Arrow position
        arrw = arr.width();
        arrl = al + aw / 2 - (l + (mw - arrw) / 2);

        // Limit Arrow position
        $('.dw-arr', dw).css({
          left: constrain(arrl, 0, arrw)
        });
      } else {
        css.width = '100%';
        if (s.display == 'top') {
          t = st;
        } else if (s.display == 'bottom') {
          var zoom = $('html').css('zoom');
          //t = st + wh - mh;
          t = (st + wh - mh * zoom) / zoom;
        }
      }

      css.top = t < 0 ? 0 : t;
      css.left = l;
      d.css(css);

      // If top + modal height > doc height, increase doc height
      $('.dw-persp', dw).height(0).height(t + mh > $(document).height() ? t + mh : $(document).height());

      // Scroll needed
      if (scroll && ((t + mh > st + wh) || (at > st + wh))) {
        $(window).scrollTop(t + mh - wh);
      }
    };

    /**
     * Enables the scroller and the associated input.
     */
    that.enable = function() {
      s.disabled = false;
      if (input) {
        elm.prop('disabled', false);
      }
    };

    /**
     * Disables the scroller and the associated input.
     */
    that.disable = function() {
      s.disabled = true;
      if (input) {
        elm.prop('disabled', true);
      }
    };

    /**
     * Gets the selected wheel values, formats it, and set the value of the scroller instance.
     * If input parameter is true, populates the associated input element.
     * @param {Array} values - Wheel values.
     * @param {Boolean} [fill=false] - Also set the value of the associated input element.
     * @param {Number} [time=0] - Animation time
     * @param {Boolean} [temp=false] - If true, then only set the temporary value.(only scroll there but not set the value)
     */
    that.setValue = function(values, fill, time, temp) {
      that.temp = $.isArray(values) ? values.slice(0) : s.parseValue.call(e, values + '', that);
      setVal(fill, time, false, temp);
    };

    that.getValue = function() {
      return that.values;
    };

    that.getValues = function() {
      var ret = [],
        i;

      for (i in that._selectedValues) {
        ret.push(that._selectedValues[i]);
      }
      return ret;
    };

    /**
     * Changes the values of a wheel, and scrolls to the correct position
     */
    that.changeWheel = function(idx, time) {
      if (dw) {
        var i = 0,
          nr = idx.length;

        $.each(s.wheels, function(j, wg) {
          $.each(wg, function(k, w) {
            if ($.inArray(i, idx) > -1) {
              wheels[i] = w;
              $('.dw-ul', dw).eq(i).html(generateWheelItems(i));
              nr--;
              if (!nr) {
                that.position();
                scrollToPos(time, undefined, true);
                return false;
              }
            }
            i++;
          });
          if (!nr) {
            return false;
          }
        });
      }
    };

    /**
     * Return true if the scroller is currently visible.
     */
    that.isVisible = function() {
      return visible;
    };

    that.tap = function(el, handler) {
      var startX,
        startY;

      if (s.tap) {
        el.on('touchstart.dw', function(e) {
          e.preventDefault();
          startX = getCoord(e, 'X');
          startY = getCoord(e, 'Y');
        }).on('touchend.dw', function(e) {
          // If movement is less than 20px, fire the click event handler
          if (Math.abs(getCoord(e, 'X') - startX) < 20 && Math.abs(getCoord(e, 'Y') - startY) < 20) {
            handler.call(this, e);
          }
          tap = true;
          setTimeout(function() {
            tap = false;
          }, 300);
        });
      }

      el.on('click.dw', function(e) {
        if (!tap) {
          // If handler was not called on touchend, call it on click;
          handler.call(this, e);
        }
      });

    };

    /**
     * Shows the scroller instance.
     * @param {Boolean} prevAnim - Prevent animation if true
     */
    that.show = function(prevAnim) {
      if (s.disabled || visible) {
        return false;
      }

      if (s.display == 'top') {
        anim = 'slidedown';
      }

      if (s.display == 'bottom') {
        anim = 'slideup';
      }

      // Parse value from input
      read();

      event('onBeforeShow', []);

      // Create wheels
      var lbl,
        l = 0,
        mAnim = '';

      if (anim && !prevAnim) {
        mAnim = 'dw-' + anim + ' dw-in';
      }

        // Create wheels containers
      var html = '<div class="smart-datepicker ' + s.theme + ' dw-' + s.display + (prefix ? ' dw' + prefix : '') + ' ' + '">' + (s.display == 'inline' ? '<div class="dw dwbg dwi"><div class="dwwr">' : '<div class="dw-persp"><div class="dwo"></div><div class="dw dwbg ' + mAnim + '"><div class="dw-arrw"><div class="dw-arrw-i"><div class="dw-arr"></div></div></div><div class="dwwr"><div aria-live="assertive" class="dwv' + (s.headerText ? '' : ' dw-hidden') + '"></div>') + '<div class="dwcc">';

      $.each(s.wheels, function(i, wg) { // Wheel groups
        html += '<div class="dwc' + (s.mode != 'scroller' ? ' dwpm' : ' dwsc') + (s.showLabel ? '' : ' dwhl') + '"><div class="dwwc dwrc"><table cellpadding="0" cellspacing="0"><tr>';
        $.each(wg, function(j, w) { // Wheels
          wheels[l] = w;
          lbl = w.label !== undefined ? w.label : j;
          html += '<td><div class="dwwl dwrc dwwl' + l + '">' + (s.mode != 'scroller' ? '<div class="dwb-e dwwb dwwbp" style="height:' + hi + 'px;line-height:' + hi + 'px;"><span>+</span></div><div class="dwb-e dwwb dwwbm" style="height:' + hi + 'px;line-height:' + hi + 'px;"><span>&ndash;</span></div>' : '') + '<div class="dwl">' + lbl + '</div><div tabindex="0" aria-live="off" aria-label="' + lbl + '" role="listbox" class="dwww"><div class="dww" style="height:' + (s.rows * hi) + 'px;min-width:' + s.width + 'px;"><div class="dw-ul">';
          // Create wheel values
          html += generateWheelItems(l);
          html += '</div><div class="dwwol"></div></div><div class="dwwo"></div></div><div class="dwwol"></div></div></td>';
          l++;
        });

        html += '</tr></table></div></div>';
      });

      html += '</div>' + (s.display != 'inline' ? '<div class="dwbc' + (s.button3 ? ' dwbc-p' : '') + '"><span class="dwbw dwb-s"><span class="dwb dwb-e" role="button" tabindex="0">' + s.setText + '</span></span>' + (s.button3 ? '<span class="dwbw dwb-n"><span class="dwb dwb-e" role="button" tabindex="0">' + s.button3Text + '</span></span>' : '') + '<span class="dwbw dwb-c"><span class="dwb dwb-e" role="button" tabindex="0">' + s.cancelText + '</span></span></div></div>' : '') + '</div></div></div>';
      dw = $(html);

      scrollToPos();

      event('onMarkupReady', [dw]);

      // Show
      if (s.display != 'inline') {
        dw.appendTo('body');
        if (anim && !prevAnim) {
          dw.addClass('dw-trans');
          // Remove animation class
          setTimeout(function() {
            dw.removeClass('dw-trans').find('.dw').removeClass(mAnim);
          }, 350);
        }
      } else if (elm.is('div')) {
        elm.html(dw);
      } else {
        dw.insertAfter(elm);
      }

      event('onMarkupInserted', [dw]);

      visible = true;

      // Theme init
      theme.init(dw, that);

      if (s.display != 'inline') {
        // Init buttons
        that.tap($('.dwb-s span', dw), function() {
          that.select();
        });

        that.tap($('.dwb-c span', dw), function() {
          that.cancel();
        });

        if (s.button3) {
          that.tap($('.dwb-n span', dw), s.button3);
        }

        // Enter / ESC
        $(window).on('keydown.dw', function(e) {
          if (e.keyCode == 13) {
            that.select();
          } else if (e.keyCode == 27) {
            that.cancel();
          }
        });

        // Prevent scroll if not specified otherwise
        if (s.scrollLock) {
          dw.on('touchmove touchstart', function(e) {
            if (lock) {
              e.preventDefault();
            }
          });
        }

        // Disable inputs to prevent bleed through (Android bug) and set autocomplete to off (for Firefox)
        $('input,select,button').each(function() {
          if (!this.disabled) {
            if ($(this).attr('autocomplete')) {
              $(this).data('autocomplete', $(this).attr('autocomplete'));
            }
            $(this).addClass('dwtd').prop('disabled', true).attr('autocomplete', 'off');
          }
        });

        // Set position
        that.position();
        $(window).on('orientationchange.dw resize.dw scroll.dw', function(e) {
          // Sometimes scrollTop is not correctly set, so we wait a little
          clearTimeout(debounce);
          debounce = setTimeout(function() {
            var scroll = e.type == 'scroll';
            if ((scroll && lock) || !scroll) {
              that.position(!scroll);
            }
          }, 100);
        });

        //that.alert(s.ariaDesc);
      }

      // Events
      $('.dwwl', dw)
        .on('DOMMouseScroll mousewheel', onScroll)
        .on(START_EVENT, onStart)
        .on('keydown', onKeyDown)
        .on('keyup', onKeyUp);

      dw.on(START_EVENT, '.dwb-e', onBtnStart).on('keydown', '.dwb-e', function(e) {
        if (e.keyCode == 32) { // Space
          e.preventDefault();
          e.stopPropagation();
          $(this).click();
        }
      });

      event('onShow', [dw, v]);
    };

    /**
     * Hides the scroller instance.
     */
    that.hide = function(prevAnim, btn) {
      // If onClose handler returns false, prevent hide
      if (!visible || event('onClose', [v, btn]) === false) {
        return false;
      }

      // Re-enable temporary disabled fields
      $('.dwtd').each(function() {
        $(this).prop('disabled', false).removeClass('dwtd');
        if ($(this).data('autocomplete')) {
          $(this).attr('autocomplete', $(this).data('autocomplete'));
        } else {
          $(this).removeAttr('autocomplete');
        }
      });

      // Hide wheels and overlay
      if (dw) {
        if (s.display != 'inline' && anim && !prevAnim) {
          dw.addClass('dw-trans').find('.dw').addClass('dw-' + anim + ' dw-out');
          setTimeout(function() {
            dw.remove();
            dw = null;
          }, 350);
        } else {
          dw.remove();
          dw = null;
        }

        // Stop positioning on window resize
        $(window).off('.dw');
      }

      pixels = {};
      visible = false;
      preventShow = true;

      elm.focus();
    };

    that.select = function() {
      if (that.hide(false, 'set') !== false) {
        setVal(true, 0, true);
        event('onSelect', [that.val]);
      }
    };

    /**
     * Cancel and hide the scroller instance.
     */
    that.cancel = function() {
      if (that.hide(false, 'cancel') !== false) {
        event('onCancel', [that.val]);
      }
    };

    /**
     * Scroller initialization.
     */
    that.init = function(ss) {
      // Get theme defaults
      theme = extend({
        defaults: {},
        init: empty
      }, ms.themes[ss.theme || s.theme]);

      // Get language defaults
      lang = ms.i18n[ss.lang || s.lang];

      extend(settings, ss); // Update original user settings
      extend(s, theme.defaults, lang, settings);

      that.settings = s;

      // Unbind all events (if re-init)
      elm.off('.dw');

      var preset = ms.presets[s.preset];

      if (preset) {
        pres = preset.call(e, that);
        extend(s, pres, settings); // Load preset settings
      }

      // Set private members
      m = Math.floor(s.rows / 2);
      hi = s.height;
      anim = s.animate;

      if (visible) {
        that.hide();
      }

      if (s.display == 'inline') {
        that.show();
      } else {
        read();
        if (input) {
          // Set element readonly, save original state
          if (readOnly === undefined) {
            readOnly = e.readOnly;
          }
          e.readOnly = true;
          // Init show datewheel
          if (s.showOnFocus) {
            elm.on('focus.dw', function() {
              if (!preventShow) {
                that.show();
              }
              preventShow = false;
            });
          }
        }
        if (s.showOnTap) {
          that.tap(elm, function() {
            that.show();
          });
        }
      }
    };

    that.trigger = function(name, params) {
      return event(name, params);
    };

    that.option = function(opt, value) {
      var obj = {};
      if (typeof opt === 'object') {
        obj = opt;
      } else {
        obj[opt] = value;
      }
      that.init(obj);
    };

    that.destroy = function() {
      that.hide();
      elm.off('.dw');
      delete scrollers[e.id];
      if (input) {
        e.readOnly = readOnly;
      }
    };

    that.getInst = function() {
      return that;
    };

    that.values = null;
    that.val = null;
    that.temp = null;
    that._selectedValues = {};

    that.init(settings);
  }

  function testProps(props) {
    var i;
    for (i in props) {
      if (mod[props[i]] !== undefined) {
        return true;
      }
    }
    return false;
  }

  function testPrefix() {
    var prefixes = ['Webkit', 'Moz', 'O', 'ms'],
      p;

    for (p in prefixes) {
      if (testProps([prefixes[p] + 'Transform'])) {
        return '-' + prefixes[p].toLowerCase();
      }
    }
    return '';
  }

  function testTouch(e) {
    if (e.type === 'touchstart') {
      touch = true;
      /*setTimeout(function () {
       touch = false; // Reset if mouse event was not fired
       }, 500);*/
    } else if (touch) {
      touch = false;
      return false;
    }
    return true;
  }

  function getCoord(e, c) {
    var org = e.originalEvent,
      ct = e.changedTouches;
    return ct || (org && org.changedTouches) ? (org ? org.changedTouches[0]['page' + c] : ct[0]['page' + c]) : e['page' + c];

  }

  function constrain(val, min, max) {
    return Math.max(min, Math.min(val, max));
  }

  function convert(w) {
    var ret = {
      values: [],
      keys: []
    };
    $.each(w, function(k, v) {
      ret.keys.push(k);
      ret.values.push(v);
    });
    return ret;
  }

  function init(that, options, args) {
    var ret = that;

    // Init
    if (typeof options === 'object') {
      return that.each(function() {
        if (!this.id) {
          uuid += 1;
          this.id = 'mobiscroll' + uuid;
        }
        scrollers[this.id] = new Scroller(this, options);
      });
    }

    // Method call
    if (typeof options === 'string') {
      that.each(function() {
        var r,
          inst = scrollers[this.id];

        if (inst && inst[options]) {
          r = inst[options].apply(this, Array.prototype.slice.call(args, 1));
          if (r !== undefined) {
            ret = r;
            return false;
          }
        }
      });
    }

    return ret;
  }

  var move,
    tap,
    touch,
    alertTimer,
    aria,
    date = new Date(),
    uuid = date.getTime(),
    scrollers = {},
    empty = function() {},
    mod = document.createElement('modernizr').style,
    has3d = testProps(['perspectiveProperty', 'WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective']),
    prefix = testPrefix(),
    pr = prefix.replace(/^\-/, '').replace('moz', 'Moz'),
    extend = $.extend,
    START_EVENT = 'touchstart mousedown',
    MOVE_EVENT = 'touchmove mousemove',
    END_EVENT = 'touchend mouseup',
    defaults = {
      // Options
      width: 70,
      height: 40,
      rows: 3,
      delay: 300,
      disabled: false,
      readonly: false,
      showOnFocus: true,
      showOnTap: true,
      showLabel: true,
      wheels: [],
      theme: '',
      headerText: '{value}',
      display: 'modal',
      mode: 'scroller',
      preset: '',
      lang: 'en-US',
      setText: 'Set',
      cancelText: 'Cancel',
      ariaDesc: 'Select a value',
      scrollLock: true,
      tap: true,
      speedUnit: 0.0012,
      timeUnit: 0.1,
      formatResult: function(d) {
        return d.join(' ');
      },
      parseValue: function(value, inst) {
        var val = value.split(' '),
          ret = [],
          i = 0,
          keys;

        $.each(inst.settings.wheels, function(j, wg) {
          $.each(wg, function(k, w) {
            w = w.values ? w : convert(w);
            keys = w.keys || w.values;
            if ($.inArray(val[i], keys) !== -1) {
              ret.push(val[i]);
            } else {
              ret.push(keys[0]);
            }
            i++;
          });
        });
        return ret;
      }
    };


  $(document).on('mouseover mouseup mousedown click', function(e) { // Prevent standard behaviour on body click
    if (tap) {
      e.stopPropagation();
      e.preventDefault();
      return false;
    }
  });

  $.fn.mobiscroll = function(method) {
    extend(this, $.mobiscroll.shorts);
    return init(this, method, arguments);
  };

  $.mobiscroll = $.mobiscroll || {
    /**
     * Set settings for all instances.
     * @param {Object} o - New default settings.
     */
    setDefaults: function(o) {
      extend(defaults, o);
    },
    presetShort: function(name) {
      this.shorts[name] = function(method) {
        return init(this, extend(method, {
          preset: name
        }), arguments);
      };
    },
    has3d: has3d,
    shorts: {},
    presets: {},
    themes: {},
    i18n: {}
  };

  $.scroller = $.scroller || $.mobiscroll;
  $.fn.scroller = $.fn.scroller || $.fn.mobiscroll;


  $.mobiscroll.i18n.zh = $.extend({}, {
    // Core
    setText: '确定',
    cancelText: '取消',
    // Datetime component
    dateFormat: 'yy-mm-dd',
    dateOrder: 'yymmdd',
    dayNames: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
    dayNamesShort: ['日', '一', '二', '三', '四', '五', '六'],
    dayText: '日',
    hourText: '时',
    minuteText: '分',
    monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
    monthNamesShort: ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'],
    monthText: '月',
    secText: '秒',
    timeFormat: 'HH:ii',
    timeWheels: 'HHii',
    yearText: '年',
    nowText: '当前',
    // Calendar component
    dateText: '日',
    timeText: '时间',
    calendarText: '日历',
    // Measurement components
    wholeText: 'Whole',
    fractionText: 'Fraction',
    unitText: 'Unit',
    // Time / Timespan component
    labels: ['Years', 'Months', 'Days', 'Hours', 'Minutes', 'Seconds', ''],
    labelsShort: ['Yrs', 'Mths', 'Days', 'Hrs', 'Mins', 'Secs', ''],
    // Timer component
    startText: 'Start',
    stopText: 'Stop',
    resetText: 'Reset',
    lapText: 'Lap',
    hideText: 'Hide'
  });



})(window.Zepto);


(function($) {

  var ms = $.mobiscroll,
    date = new Date(),
    defaults = {
      dateFormat: 'mm/dd/yy',
      dateOrder: 'mmddy',
      timeWheels: 'hhiiA',
      timeFormat: 'hh:ii A',
      startYear: date.getFullYear() - 100,
      endYear: date.getFullYear() + 100,
      monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
      dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      shortYearCutoff: '+10',
      monthText: 'Month',
      dayText: 'Day',
      yearText: 'Year',
      hourText: 'Hours',
      minuteText: 'Minutes',
      secText: 'Seconds',
      ampmText: '&nbsp;',
      nowText: 'Now',
      showNow: false,
      stepHour: 1,
      stepMinute: 1,
      stepSecond: 1,
      separator: ' '
    },
    preset = function(inst) {
      var that = $(this),
        html5def = {},
        format;
      // Force format for html5 date inputs (experimental)
      if (that.is('input')) {
        switch (that.attr('type')) {
          case 'date':
            format = 'yy-mm-dd';
            break;
          case 'datetime':
            format = 'yy-mm-ddTHH:ii:ssZ';
            break;
          case 'datetime-local':
            format = 'yy-mm-ddTHH:ii:ss';
            break;
          case 'month':
            format = 'yy-mm';
            html5def.dateOrder = 'mmyy';
            break;
          case 'time':
            format = 'HH:ii:ss';
            break;
        }
        // Check for min/max attributes
        var min = that.attr('min'),
          max = that.attr('max');
        if (min) {
          html5def.minDate = ms.parseDate(format, min);
        }
        if (max) {
          html5def.maxDate = ms.parseDate(format, max);
        }
      }

      // Set year-month-day order
      var i,
        k,
        keys,
        values,
        wg,
        start,
        end,
        orig = $.extend({}, inst.settings),
        s = $.extend(inst.settings, defaults, html5def, orig),
        offset = 0,
        wheels = [],
        ord = [],
        o = {},
        f = {
          y: 'getFullYear',
          m: 'getMonth',
          d: 'getDate',
          h: getHour,
          i: getMinute,
          s: getSecond,
          a: getAmPm
        },
        p = s.preset,
        dord = s.dateOrder,
        tord = s.timeWheels,
        regen = dord.match(/D/),
        ampm = tord.match(/a/i),
        hampm = tord.match(/h/),
        hformat = p == 'datetime' ? s.dateFormat + s.separator + s.timeFormat : p == 'time' ? s.timeFormat : s.dateFormat,
        defd = new Date(),
        stepH = s.stepHour,
        stepM = s.stepMinute,
        stepS = s.stepSecond,
        mind = s.minDate || new Date(s.startYear, 0, 1),
        maxd = s.maxDate || new Date(s.endYear, 11, 31, 23, 59, 59);

      format = format || hformat;

      if (p.match(/date/i)) {

        // Determine the order of year, month, day wheels
        $.each(['y', 'm', 'd'], function(j, v) {
          i = dord.search(new RegExp(v, 'i'));
          if (i > -1) {
            ord.push({
              o: i,
              v: v
            });
          }
        });
        ord.sort(function(a, b) {
          return a.o > b.o ? 1 : -1;
        });
        $.each(ord, function(i, v) {
          o[v.v] = i;
        });

        wg = [];
        for (k = 0; k < 3; k++) {
          if (k == o.y) {
            offset++;
            values = [];
            keys = [];
            start = mind.getFullYear();
            end = maxd.getFullYear();
            for (i = start; i <= end; i++) {
              keys.push(i);
              values.push(dord.match(/yy/i) ? i : (i + '').substr(2, 2));
            }
            addWheel(wg, keys, values, s.yearText);
          } else if (k == o.m) {
            offset++;
            values = [];
            keys = [];
            for (i = 0; i < 12; i++) {
              var str = dord.replace(/[dy]/gi, '').replace(/mm/, i < 9 ? '0' + (i + 1) : i + 1).replace(/m/, (i + 1));
              keys.push(i);
              values.push(str.match(/MM/) ? str.replace(/MM/, '<span class="dw-mon">' + s.monthNames[i] + '</span>') : str.replace(/M/, '<span class="dw-mon">' + s.monthNamesShort[i] + '</span>'));
            }
            addWheel(wg, keys, values, s.monthText);
          } else if (k == o.d) {
            offset++;
            values = [];
            keys = [];
            for (i = 1; i < 32; i++) {
              keys.push(i);
              values.push(dord.match(/dd/i) && i < 10 ? '0' + i : i);
            }
            addWheel(wg, keys, values, s.dayText);
          }
        }
        wheels.push(wg);
      }

      if (p.match(/time/i)) {

        // Determine the order of hours, minutes, seconds wheels
        ord = [];
        $.each(['h', 'i', 's', 'a'], function(i, v) {
          i = tord.search(new RegExp(v, 'i'));
          if (i > -1) {
            ord.push({
              o: i,
              v: v
            });
          }
        });
        ord.sort(function(a, b) {
          return a.o > b.o ? 1 : -1;
        });
        $.each(ord, function(i, v) {
          o[v.v] = offset + i;
        });

        wg = [];
        for (k = offset; k < offset + 4; k++) {
          if (k == o.h) {
            offset++;
            values = [];
            keys = [];
            for (i = 0; i < (hampm ? 12 : 24); i += stepH) {
              keys.push(i);
              values.push(hampm && i == 0 ? 12 : tord.match(/hh/i) && i < 10 ? '0' + i : i);
            }
            addWheel(wg, keys, values, s.hourText);
          } else if (k == o.i) {
            offset++;
            values = [];
            keys = [];
            for (i = 0; i < 60; i += stepM) {
              keys.push(i);
              values.push(tord.match(/ii/) && i < 10 ? '0' + i : i);
            }
            addWheel(wg, keys, values, s.minuteText);
          } else if (k == o.s) {
            offset++;
            values = [];
            keys = [];
            for (i = 0; i < 60; i += stepS) {
              keys.push(i);
              values.push(tord.match(/ss/) && i < 10 ? '0' + i : i);
            }
            addWheel(wg, keys, values, s.secText);
          } else if (k == o.a) {
            offset++;
            var upper = tord.match(/A/);
            addWheel(wg, [0, 1], upper ? ['AM', 'PM'] : ['am', 'pm'], s.ampmText);
          }
        }

        wheels.push(wg);
      }

      function get(d, i, def) {
        if (o[i] !== undefined) {
          return +d[o[i]];
        }
        if (def !== undefined) {
          return def;
        }
        return defd[f[i]] ? defd[f[i]]() : f[i](defd);
      }

      function addWheel(wg, k, v, lbl) {
        wg.push({
          values: v,
          keys: k,
          label: lbl
        });
      }

      function step(v, st) {
        return Math.floor(v / st) * st;
      }

      function getHour(d) {
        var hour = d.getHours();
        hour = hampm && hour >= 12 ? hour - 12 : hour;
        return step(hour, stepH);
      }

      function getMinute(d) {
        return step(d.getMinutes(), stepM);
      }

      function getSecond(d) {
        return step(d.getSeconds(), stepS);
      }

      function getAmPm(d) {
        return ampm && d.getHours() > 11 ? 1 : 0;
      }

      function getDate(d) {
        var hour = get(d, 'h', 0);
        return new Date(get(d, 'y'), get(d, 'm'), get(d, 'd', 1), get(d, 'a') ? hour + 12 : hour, get(d, 'i', 0), get(d, 's', 0));
      }

      // Extended methods
      // ---

      /**
       * Sets the selected date
       *
       * @param {Date} d Date to select.
       * @param {Boolean} [fill=false] Also set the value of the associated input element. Default is true.
       * @return {Object} jQuery object to maintain chainability
       */
      inst.setDate = function(d, fill, time, temp) {
        var i;

        // Set wheels
        for (i in o) {
          inst.temp[o[i]] = d[f[i]] ? d[f[i]]() : f[i](d);
        }
        inst.setValue(inst.temp, fill, time, temp);
      };

      /**
       * Returns the currently selected date.
       *
       * @param {Boolean} [temp=false] If true, return the currently shown date on the picker, otherwise the last selected one
       * @return {Date}
       */
      inst.getDate = function(temp) {
        return getDate(temp ? inst.temp : inst.values);
      };

      // ---

      return {
        button3Text: s.showNow ? s.nowText : undefined,
        button3: s.showNow ? function() {
          inst.setDate(new Date(), false, 0.3, true);
        } : undefined,
        wheels: wheels,
        headerText: function(v) {
          return ms.formatDate(hformat, getDate(inst.temp), s);
        },
        /**
         * Builds a date object from the wheel selections and formats it to the given date/time format
         * @param {Array} d - An array containing the selected wheel values
         * @return {String} - The formatted date string
         */
        formatResult: function(d) {
          return ms.formatDate(format, getDate(d), s);
        },
        /**
         * Builds a date object from the input value and returns an array to set wheel values
         * @return {Array} - An array containing the wheel values to set
         */
        parseValue: function(val) {
          var d = new Date(),
            i,
            result = [];
          try {
            d = ms.parseDate(format, val, s);
          } catch (e) {}
          // Set wheels
          for (i in o) {
            result[o[i]] = d[f[i]] ? d[f[i]]() : f[i](d);
          }
          return result;
        },
        /**
         * Validates the selected date to be in the minDate / maxDate range and sets unselectable values to disabled
         * @param {Object} dw - jQuery object containing the generated html
         * @param {Integer} [i] - Index of the changed wheel, not set for initial validation
         */
        validate: function(dw, i) {
          var temp = inst.temp, //.slice(0),
            mins = {
              y: mind.getFullYear(),
              m: 0,
              d: 1,
              h: 0,
              i: 0,
              s: 0,
              a: 0
            },
            maxs = {
              y: maxd.getFullYear(),
              m: 11,
              d: 31,
              h: step(hampm ? 11 : 23, stepH),
              i: step(59, stepM),
              s: step(59, stepS),
              a: 1
            },
            minprop = true,
            maxprop = true;
          $.each(['y', 'm', 'd', 'a', 'h', 'i', 's'], function(x, i) {
            if (o[i] !== undefined) {
              var min = mins[i],
                max = maxs[i],
                maxdays = 31,
                val = get(temp, i),
                t = $('.dw-ul', dw).eq(o[i]),
                y,
                m;
              if (i == 'd') {
                y = get(temp, 'y');
                m = get(temp, 'm');
                maxdays = 32 - new Date(y, m, 32).getDate();
                max = maxdays;
                if (regen) {
                  $('.dw-li', t).each(function() {
                    var that = $(this),
                      d = that.data('val'),
                      w = new Date(y, m, d).getDay(),
                      str = dord.replace(/[my]/gi, '').replace(/dd/, d < 10 ? '0' + d : d).replace(/d/, d);
                    $('.dw-i', that).html(str.match(/DD/) ? str.replace(/DD/, '<span class="dw-day">' + s.dayNames[w] + '</span>') : str.replace(/D/, '<span class="dw-day">' + s.dayNamesShort[w] + '</span>'));
                  });
                }
              }
              if (minprop && mind) {
                min = mind[f[i]] ? mind[f[i]]() : f[i](mind);
              }
              if (maxprop && maxd) {
                max = maxd[f[i]] ? maxd[f[i]]() : f[i](maxd);
              }
              if (i != 'y') {
                var i1 = $('.dw-li', t).index($('.dw-li[data-val="' + min + '"]', t)),
                  i2 = $('.dw-li', t).index($('.dw-li[data-val="' + max + '"]', t));
                $('.dw-li', t).removeClass('dw-v').slice(i1, i2 + 1).addClass('dw-v');
                if (i == 'd') { // Hide days not in month
                  $('.dw-li', t).removeClass('dw-h').slice(maxdays).addClass('dw-h');
                }
              }
              if (val < min) {
                val = min;
              }
              if (val > max) {
                val = max;
              }
              if (minprop) {
                minprop = val == min;
              }
              if (maxprop) {
                maxprop = val == max;
              }
              // Disable some days
              if (s.invalid && i == 'd') {
                var idx = [];
                // Disable exact dates
                if (s.invalid.dates) {
                  $.each(s.invalid.dates, function(i, v) {
                    if (v.getFullYear() == y && v.getMonth() == m) {
                      idx.push(v.getDate() - 1);
                    }
                  });
                }
                // Disable days of week
                if (s.invalid.daysOfWeek) {
                  var first = new Date(y, m, 1).getDay(),
                    j;
                  $.each(s.invalid.daysOfWeek, function(i, v) {
                    for (j = v - first; j < maxdays; j += 7) {
                      if (j >= 0) {
                        idx.push(j);
                      }
                    }
                  });
                }
                // Disable days of month
                if (s.invalid.daysOfMonth) {
                  $.each(s.invalid.daysOfMonth, function(i, v) {
                    v = (v + '').split('/');
                    if (v[1]) {
                      if (v[0] - 1 == m) {
                        idx.push(v[1] - 1);
                      }
                    } else {
                      idx.push(v[0] - 1);
                    }
                  });
                }
                $.each(idx, function(i, v) {
                  $('.dw-li', t).eq(v).removeClass('dw-v');
                });
              }

              // Set modified value
              temp[o[i]] = val;
            }
          });
        }
      };
    };

  $.each(['date', 'time', 'datetime'], function(i, v) {
    ms.presets[v] = preset;
    ms.presetShort(v);
  });

  /**
   * Format a date into a string value with a specified format.
   * @param {String} format - Output format.
   * @param {Date} date - Date to format.
   * @param {Object} settings - Settings.
   * @return {String} - Returns the formatted date string.
   */
  ms.formatDate = function(format, date, settings) {
    if (!date) {
      return null;
    }
    var s = $.extend({}, defaults, settings),
      look = function(m) { // Check whether a format character is doubled
        var n = 0;
        while (i + 1 < format.length && format.charAt(i + 1) == m) {
          n++;
          i++;
        }
        return n;
      },
      f1 = function(m, val, len) { // Format a number, with leading zero if necessary
        var n = '' + val;
        if (look(m)) {
          while (n.length < len) {
            n = '0' + n;
          }
        }
        return n;
      },
      f2 = function(m, val, s, l) { // Format a name, short or long as requested
        return (look(m) ? l[val] : s[val]);
      },
      i,
      output = '',
      literal = false;

    for (i = 0; i < format.length; i++) {
      if (literal) {
        if (format.charAt(i) == "'" && !look("'")) {
          literal = false;
        } else {
          output += format.charAt(i);
        }
      } else {
        switch (format.charAt(i)) {
          case 'd':
            output += f1('d', date.getDate(), 2);
            break;
          case 'D':
            output += f2('D', date.getDay(), s.dayNamesShort, s.dayNames);
            break;
          case 'o':
            output += f1('o', (date.getTime() - new Date(date.getFullYear(), 0, 0).getTime()) / 86400000, 3);
            break;
          case 'm':
            output += f1('m', date.getMonth() + 1, 2);
            break;
          case 'M':
            output += f2('M', date.getMonth(), s.monthNamesShort, s.monthNames);
            break;
          case 'y':
            output += (look('y') ? date.getFullYear() : (date.getYear() % 100 < 10 ? '0' : '') + date.getYear() % 100);
            break;
          case 'h':
            var h = date.getHours();
            output += f1('h', (h > 12 ? (h - 12) : (h == 0 ? 12 : h)), 2);
            break;
          case 'H':
            output += f1('H', date.getHours(), 2);
            break;
          case 'i':
            output += f1('i', date.getMinutes(), 2);
            break;
          case 's':
            output += f1('s', date.getSeconds(), 2);
            break;
          case 'a':
            output += date.getHours() > 11 ? 'pm' : 'am';
            break;
          case 'A':
            output += date.getHours() > 11 ? 'PM' : 'AM';
            break;
          case "'":
            if (look("'")) {
              output += "'";
            } else {
              literal = true;
            }
            break;
          default:
            output += format.charAt(i);
        }
      }
    }
    return output;
  };

  /**
   * Extract a date from a string value with a specified format.
   * @param {String} format - Input format.
   * @param {String} value - String to parse.
   * @param {Object} settings - Settings.
   * @return {Date} - Returns the extracted date.
   */
  ms.parseDate = function(format, value, settings) {
    var def = new Date();

    if (!format || !value) {
      return def;
    }

    value = (typeof value == 'object' ? value.toString() : value + '');

    var s = $.extend({}, defaults, settings),
      shortYearCutoff = s.shortYearCutoff,
      year = def.getFullYear(),
      month = def.getMonth() + 1,
      day = def.getDate(),
      doy = -1,
      hours = def.getHours(),
      minutes = def.getMinutes(),
      seconds = 0, //def.getSeconds(),
      ampm = -1,
      literal = false, // Check whether a format character is doubled
      lookAhead = function(match) {
        var matches = (iFormat + 1 < format.length && format.charAt(iFormat + 1) == match);
        if (matches) {
          iFormat++;
        }
        return matches;
      },
      getNumber = function(match) { // Extract a number from the string value
        lookAhead(match);
        var size = (match == '@' ? 14 : (match == '!' ? 20 : (match == 'y' ? 4 : (match == 'o' ? 3 : 2)))),
          digits = new RegExp('^\\d{1,' + size + '}'),
          num = value.substr(iValue).match(digits);

        if (!num) {
          return 0;
        }
        //throw 'Missing number at position ' + iValue;
        iValue += num[0].length;
        return parseInt(num[0], 10);
      },
      getName = function(match, s, l) { // Extract a name from the string value and convert to an index
        var names = (lookAhead(match) ? l : s),
          i;

        for (i = 0; i < names.length; i++) {
          if (value.substr(iValue, names[i].length).toLowerCase() == names[i].toLowerCase()) {
            iValue += names[i].length;
            return i + 1;
          }
        }
        return 0;
        //throw 'Unknown name at position ' + iValue;
      },
      checkLiteral = function() {
        //if (value.charAt(iValue) != format.charAt(iFormat))
        //throw 'Unexpected literal at position ' + iValue;
        iValue++;
      },
      iValue = 0,
      iFormat;

    for (iFormat = 0; iFormat < format.length; iFormat++) {
      if (literal) {
        if (format.charAt(iFormat) == "'" && !lookAhead("'")) {
          literal = false;
        } else {
          checkLiteral();
        }
      } else {
        switch (format.charAt(iFormat)) {
          case 'd':
            day = getNumber('d');
            break;
          case 'D':
            getName('D', s.dayNamesShort, s.dayNames);
            break;
          case 'o':
            doy = getNumber('o');
            break;
          case 'm':
            month = getNumber('m');
            break;
          case 'M':
            month = getName('M', s.monthNamesShort, s.monthNames);
            break;
          case 'y':
            year = getNumber('y');
            break;
          case 'H':
            hours = getNumber('H');
            break;
          case 'h':
            hours = getNumber('h');
            break;
          case 'i':
            minutes = getNumber('i');
            break;
          case 's':
            seconds = getNumber('s');
            break;
          case 'a':
            ampm = getName('a', ['am', 'pm'], ['am', 'pm']) - 1;
            break;
          case 'A':
            ampm = getName('A', ['am', 'pm'], ['am', 'pm']) - 1;
            break;
          case "'":
            if (lookAhead("'")) {
              checkLiteral();
            } else {
              literal = true;
            }
            break;
          default:
            checkLiteral();
        }
      }
    }
    if (year < 100) {
      year += new Date().getFullYear() - new Date().getFullYear() % 100 +
        (year <= (typeof shortYearCutoff != 'string' ? shortYearCutoff : new Date().getFullYear() % 100 + parseInt(shortYearCutoff, 10)) ? 0 : -100);
    }
    if (doy > -1) {
      month = 1;
      day = doy;
      do {
        var dim = 32 - new Date(year, month - 1, 32).getDate();
        if (day <= dim) {
          break;
        }
        month++;
        day -= dim;
      } while (true);
    }
    hours = (ampm == -1) ? hours : ((ampm && hours < 12) ? (hours + 12) : (!ampm && hours == 12 ? 0 : hours));
    var date = new Date(year, month - 1, day, hours, minutes, seconds);
    if (date.getFullYear() != year || date.getMonth() + 1 != month || date.getDate() != day) {
      throw 'Invalid date';
    }
    return date;
  };

})(window.Zepto);

(function($) {
  var Datepicker = function(container, options) {
    this.init(container, options);
  }
  Datepicker.prototype = {
    //可配置参数
    options: {
      id: '', //容器 DOM 节点 ID
      min: null,
      max: null,
      minuteStep: 5,
      callback: null, //加载完成后执行的回调函数
      type: 'date'
    },

    //枚举值
    type: ['date', 'time'],

    theme: {
      defaults: {
        dateOrder: 'Mddyy',
        mode: 'mixed',
        rows: 5,
        width: 70,
        height: 40,
        showLabel: false,
        useShortLabels: true
      }
    },

    setting: {
      theme: "android-ics",
      mode: "scroller",
      display: "bottom",
      lang: "zh"
    },

    //组件容器对象
    container: null,

    init: function(container, options) {
      this.options = $.extend({}, this.options, options);

      this.checkOptions();

      $.mobiscroll.themes['android-ics'] = this.theme;
      $.mobiscroll.themes['android-ics light'] = this.theme;

      this.container = container;

      this.setLimit();
      this.render();
      this.bindEvent();

      if (this.options.callback) {
        this.options.callback(this);
      }
    },

    //参数校验
    checkOptions: function() {

      if ($.inArray(this.options.type, this.type) == -1) {
        this.options.type = this.type[0];
      }

      this.options.minuteStep = parseInt(this.options.minuteStep);
      if (this.options.minuteStep > 60) {
        this.options.minuteStep = 60;
      }
      if (this.options.minuteStep < 1) {
        this.options.minuteStep = 1;
      }

    },


    setLimit: function() {
      var self = this;
      var options = self.options;
      var dateReg = /^([1|2]\w{3})-(1[0-2]|0?[1-9])-((30|31)|((1|2)[0-9])|(0?[1-9]))$/;
      var timeReg = /^((20|21|22|23)|[0-1][0-9]):([0-5][0-9])$/;
      var now = new Date();

      if (options.min) {
        var min;

        if (options.type == 'time') {
          if (timeReg.test(options.min)) {
            var time = options.min.split(":");
            min = new Date(now.getFullYear(), now.getMonth(), now.getDate(), time[0], time[1]);
            self.setting.minDate = min;
          }
        } else {
          if (dateReg.test(options.min)) {
            var date = options.min.split("-");
            min = new Date(date[0] * 1, date[1] - 1, date[2] * 1);
            self.setting.minDate = min;
          }
        }
      }

      if (options.max) {
        var max;

        if (options.type == 'time') {
          if (timeReg.test(options.max)) {
            var time = options.max.split(":");
            max = new Date(now.getFullYear(), now.getMonth(), now.getDate(), time[0], time[1]);
            self.setting.maxDate = max;
          }
        } else {
          if (dateReg.test(options.max)) {
            var date = options.max.split("-");
            max = new Date(date[0] * 1, date[1] - 1, date[2] * 1);
            self.setting.maxDate = max;
          }
        }
      }
    },

    getValue: function() {
      return this.container.val();
    },

    setValue: function(value) {
      this.container.val(value);
    },

    //绑定事件
    bindEvent: function() {
      var self = this;
      var options = self.options;

      self.container.change(function() {
        if (options.onChange) {
          options.onChange(self.getValue());
        }
      })
    },

    render: function() {
      var self = this;
      var options = self.options;

      self.setting.preset = options.type;
      self.setting.stepMinute = options.minuteStep;
      self.setting.exClass = 'i_mui_' + options.skin;
      self.container.scroller('destroy').scroller(self.setting);

    }
  }

  $.fn.datepicker = function(params) {
    return this.each(function() {
      $(this).data('Datepiker', new Datepicker($(this), params));
    });
  }

})(window.Zepto);
/**
 * formcheck组件
 *
 * @date     2015-07-14
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Formcheck = function() {};
    Formcheck.prototype = {
        validate: function(that, pattern, errorMessage) {
            if (isNaN(pattern)) {
                return this._text(that, pattern, errorMessage);
            } else {
                return this._checkbox(that, pattern, errorMessage);
            };
        },
        _text: function(that, pattern, errorMessage) {
            var value = $(that).val();
            var $parentDiv = $(that).parent("div");
            switch (pattern) {
            case "mobile":
                pattern = /^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
                break;
            case "email":
                pattern = /^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/;
                break;
            default:
                pattern = eval(pattern);
            };
            $parentDiv.next("." + ns + "-form-error-item").remove();
            if (!value.match(pattern) && errorMessage) {
                $parentDiv.after('<div class="' + ns + '-form-error-item">' + errorMessage + '</div>');
            };
            return !!value.match(pattern);
        },
        _checkbox: function(that, pattern, errorMessage) {
            var checked_n = 0;
            var $lastParentDiv = $(that).last().parent("div");
            $(that).each(function() {
                if ($(this).attr("checked")) {
                    checked_n += 1;
                };
            });
            $lastParentDiv.next("." + ns + "-form-error-checkbox-item").remove();
            if (checked_n < pattern && errorMessage) {
                $lastParentDiv.after('<div class="' + ns + '-form-error-checkbox-item">' + errorMessage + '</div>');
                return false;
            };
            return true;
        }
    };
    function Plugin(pattern, errorMessage) {
        var formcheck = new Formcheck();
        return formcheck.validate(this, pattern, errorMessage);
    };
    $.fn.formcheck = Plugin;
}(window.Zepto);
/**
 * gotop组件
 *
 * @date     2015-07-07
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Gotop = function(option) {
        this.opacity = option.opacity ? option.opacity : 1;
        this.distance = option.distance ? parseInt(option.distance) : 200;
    };
    Gotop.prototype = {
        build: function() {
            $('<div class="' + ns + '-gotop"><i class="icon ' + ns + '-icon">&#xe69e;</i></div>').appendTo('body');
            this._bindCss();
            this._bindEvent();
        },
        _bindCss: function() {
            $("." + ns + "-gotop").css({
                "opacity": this.opacity
            });
        },
        _bindEvent: function() {
            var self = this;
            $(window).scroll(function() {
                var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
                if (scrollt > self.distance) {
                    $("." + ns + "-gotop").show();
                } else {      
                    $("." + ns + "-gotop").hide();
                };
            });
            $("." + ns + "-gotop").on("click", function() {
                window.scroll(0, 0);
            });
        }
    };
    function Plugin(option) {
        var gotop = new Gotop(option);
        gotop.build();
    };
    $.gotop = Plugin;
}(window.Zepto);
/**
 * linkage组件
 *
 * @date     2015-07-11
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    // 构造函数，参数data为json格式数据，参数that为当前的zepto对象
    var Linkage = function(data) {
        this.data = data;
    };
    Linkage.prototype = {
        build: function(that) {
            var _this = this;
            // 获取that对象内所有的select对象
            var $select_obj = $(that).find("select");       
            // 获取that对象内所有的select对象的数量
            var select_num = $select_obj.size();    
            // 构建第一层select数据
            this._resetSelect($select_obj.eq(0));
            if (this._type(this.data) === "Object") {
                this._renderSelect01(this.data, $select_obj.eq(0), 1);
            } else if (this._type(this.data) === "Array") {
                this._renderSelect02(this.data, $select_obj.eq(0), null);
            };
            // 逐层绑定change事件
            $select_obj.each(function(index, element) {  
                // 如果到了最后一层，则不需要绑定change事件，跳出循环
                if (index + 1 == select_num) {
                    return false;
                };
                // 绑定change事件
                $(element).change(function() {
                    // 清空当前select的所有子层select数据
                    for (var i = index + 1; i < select_num; i++) {
                        _this._resetSelect($select_obj.eq(i));
                    };
                    // 如果当前层select数据不为空，则填充下一层select数据
                    if ($(element).val()) {
                        if (_this._type(_this.data) === "Object") {
                            // 如果是最后一层type为2，否则type为1
                            var type = (index + 2 == select_num) ? 2 : 1;
                            // 构建下一层data
                            var _data = _this.data;
                            for (var i = 0; i < index + 1; i++) {
                                _data = _data[$select_obj.eq(i).val()];
                            };
                            _this._renderSelect01(_data, $select_obj.eq(index + 1), type);
                        } else if (_this._type(_this.data) === "Array") {
                            _this._renderSelect02(_this.data, $select_obj.eq(index + 1), $(element).val());
                        };
                    };
                });
            });
        },
        // 填充select数据
        _renderSelect01: function(data, select, type) {
            $.each(data, function(i, n) {
                switch (type) {
                case 1:
                    select.append("<option value='" + i + "'>" + i + "</option>");
                    break;
                case 2:
                    select.append("<option value='" + n + "'>" + n + "</option>");
                    break;
                default:
                };
            });
        },
        _renderSelect02: function(data, select, parent_id) {
            $.each(data, function(i, n) {      
                if (n.parent_id == parent_id) {
                    select.append("<option value='" + n.id + "'>" + n.name + "</option>");
                };
            });
        },
        // 清空select数据
        _resetSelect: function(select) {
            select.empty();
            select.append("<option value=''></option>");
        },
        // 判断数据类型
        _type: function(obj) {
            if (Object.prototype.toString.call(obj) === "[object Object]") {
                return "Object";
            } else if (Object.prototype.toString.call(obj) === "[object Array]") {
                return "Array";
            };
            return null;
        }
    };
    function Plugin(data) {
        var linkage = new Linkage(data);
        linkage.build(this);
    };
    $.fn.linkage = Plugin;
}(window.Zepto);
/**
 * list组件
 *
 * @date     2015-07-07
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var List = function() {};
    List.prototype = {
        bindEvent: function(that) {
            $(that).find(".smart-list-flex-item").on("click", function() {
                var thisDiv = $(this).next("div");
                $(that).find("i").html("&#xe6a3;");
                if ($(thisDiv).css("display") == "none") {
                    $(thisDiv).show();
                    $(this).find("i").html("&#xe661;");
                } else {
                    $(thisDiv).hide();
                    $(this).find("i").html("&#xe6a3;");
                };
                $(that).find("div").not(thisDiv).hide();
            })
        }
    };
    function Plugin() {
        var list = new List();
        list.bindEvent(this);
    };
    $.fn.list = Plugin;
}(window.Zepto);
/**
 * 弹窗组件
 *
 * 支持两种方式创建，dom和js
 * 依赖artemplate
 *
 * @date     2015-06-29
 * @author   Yuxingyang<yuxingyang@tencent.com>
 */

! function($) {

  // 默认模板
  var _modalTpl = '<div class="smart-loading-overlay">' +
    '<div class="smart-loading">' +
    '<div class="smart-loading-icon">' +
    '<i class="icon smart-icon">&#xe64f;</i>' +
    '<div>{{content}}</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  var render = template.compile(_modalTpl);
  // 默认参数
  var defaults = {
      title: '',
      content: '正在加载...'
    }
    // 构造函数
  var Loading = function(el, option, isFromTpl) {
    /*
    this.option = option;
    this.element = $(el);
    this._isFromTpl = isFromTpl;
    this._bindEvent();
    this.toggle();
    */
  }
  Loading.prototype = {
    _bindEvent: function() {
      var self = this;
      self.element.on("click", function() {
        self.hide();
      });
      if (this.option.time) {
        setTimeout(function() {
          self.hide();
        }, this.option.time);
      }
    },
    toggle: function() {
      if (this.element.hasClass("show")) {
        this.hide();
      } else {
        this.show();
      }
    },
    show: function() {
      var self = this;
      self.element.trigger($.Event("loading:show"));
      self.element.addClass("show");
    },
    hide: function() {
      var self = this;
      self.element.trigger($.Event("loading:hide"));
      self.element.removeClass("show");
      self._isFromTpl && self.element.remove();
    }
  }

  function Plugin(option) {
    var isFromTpl = false;
    if ($.isArray(this)) {
      //从现有dom创建
      var element = this;
    } else {
      //从模板创建并插入body
      var element = $(render($.extend({}, defaults, typeof option == 'object' && option))).appendTo('body');
      isFromTpl = true;
    }
    return element.each(function() {
      var $this = $(this)
      var data = $this.data('smart.loading')
      var options = $.extend({}, defaults, typeof option == 'object' && option)

      if (!data) $this.data('smart.loading', (data = new Loading(this, options, isFromTpl)))
      if (typeof option == 'string') data[option]()
    })
  }
  $.fn.loading = $.loading = Plugin;
}(window.Zepto);
/**
 * 弹窗组件
 *
 * 支持两种方式创建，dom和js
 * 依赖artemplate
 *
 * @date     2015-06-29
 * @author   Yuxingyang<yuxingyang@tencent.com>
 */

! function($) {

  // 默认模板
  var _modalTpl = '<div class="smart-modal-overlay">' +
    '<div class="smart-modal">' +
    '{{if (title)}}' +
    '<div class="smart-modal-head">' +
    '{{title}}' +
    '</div>' +
    '<div class="smart-modal-body">' +
    '{{content}}' +
    '</div>' +
    '{{else}}' +
    '<div class="smart-modal-body center">' +
    '{{content}}' +
    '</div>' +
    '{{/if}}' +
    '<div class="smart-modal-foot">' +
    '{{each button as value}}' +
    '<button type="button" data-type="{{value.type}}">{{value.content}}</button>' +
    '{{/each}}' +
    '</div>' +
    '</div>' +
    '</div>';
  var render = template.compile(_modalTpl);
  // 默认参数
  var defaults = {
      content: '欢迎使用城市服务',
      button: [{
        type: "success",
        content: '确认'
      }]
    }
    // 构造函数
  var Modal = function(el, option, isFromTpl) {
    this.option = option;
    this.element = $(el);
    this._isFromTpl = isFromTpl;
    this.button = $(el).find('button');
    this._bindEvent();
    this.toggle();
  }
  Modal.prototype = {
    _bindEvent: function() {
      var self = this;
      self.button.on("click", function() {
        var type = $(this).data("type");
        if (type == "cancel") {
          self.element.trigger($.Event("modal:close"));
          self.hide();
        } else if (type == "success") {
          self.element.trigger($.Event("modal:success"));
          self.hide();
        }
      });
    },
    toggle: function() {
      if (this.element.hasClass("show")) {
        this.hide();
      } else {
        this.show();
      }
    },
    show: function() {
      var self = this;
      self.element.trigger($.Event("modal:show"));
      self.element.addClass("show");
    },
    hide: function() {
      var self = this;
      self.element.trigger($.Event("modal:hide"));
      self.element.removeClass("show");
      self._isFromTpl && self.element.remove();
    }
  }

  function Plugin(option) {
    var isFromTpl = false;
    if ($.isArray(this)) {
      //从现有dom创建
      var element = this;
    } else {
      //从模板创建并插入body
      var element = $(render($.extend({}, defaults, typeof option == 'object' && option))).appendTo('body');
      isFromTpl = true;
    }
    return element.each(function() {
      var $this = $(this)
      var data = $this.data('smart.modal')
      var options = $.extend({}, defaults, typeof option == 'object' && option)

      if (!data) $this.data('smart.modal', (data = new Modal(this, options, isFromTpl)))
      if (typeof option == 'string') data[option]()
    })
  }
  $.fn.modal = $.modal = Plugin;
}(window.Zepto);
/**
 * numberinput组件
 *
 * @date     2015-08-04
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Numberinput = function(that) {
        this.obj = that; 
    };
    Numberinput.prototype = {
        build: function(option, callback) {
            var _this = this;
            this.min = option.min ? option.min : 0;
            this.max = option.max ? option.max : 100;
            this.span = option.span ? option.span : 1;
            $.each(this.obj, function() {
                _this._draw(this);
                _this._bindEvent(this);
            });
        },
        val: function(val) {
            if (val || val === 0) {          
                $.each(this.obj, function() {   
                    var min = parseInt($(this).find("." + ns + "-numberinput").attr("min"));
                    var max = parseInt($(this).find("." + ns + "-numberinput").attr("max"));
                    var span = parseInt($(this).find("." + ns + "-numberinput").attr("span"));
                    var minus = $(this).find(".minus");
                    var plus = $(this).find(".plus");
                    var value = $(this).find(".value");
                    $(value).val(val);
                    if (val > min) {
                        $(minus).css({"background-color":"#04be02"});
                    };
                    if (val + span > max) {
                        $(plus).css({"background-color":"#ddd"});
                    };
                });
            } else {
                return parseInt($(this.obj).find(".value").val());
            };
        },
        _draw: function(that) {
            var tpl = '' +
            '<div class="' + ns + '-numberinput" min="' + this.min + '" max="' + this.max + '" span="' + this.span + '">' +
              '<div class="minus">-</div>' +
              '<div class="plus">+</div>' +
              '<input type="text" class="value" disabled="true" value="0" />' +
            '</div>';
            $(that).html(tpl);
        },
        _bindEvent: function(that) {
            var _this = this;
            var minus = $(that).find(".minus");
            var plus = $(that).find(".plus");
            var value = $(that).find(".value");
            $(minus).on("click", function() {
                var val = parseInt($(value).val());
                val -= _this.span;
                if (val < _this.max) {
                    $(plus).css({"background-color":"#04be02"});
                };
                if (val < _this.min) {
                    return false;
                };
                if (val - _this.span < _this.min) {
                    $(minus).css({"background-color":"#ddd"});
                    $(value).val(val);
                } else {
                    $(value).val(val); 
                };
                $(that).trigger($.Event("numberinput:change"), val);
            });
            $(plus).on("click", function() {
                var val = parseInt($(value).val());
                val += _this.span;
                if (val > _this.min) {
                    $(minus).css({"background-color":"#04be02"});
                };
                if (val > _this.max) {
                    return false;
                };
                if (val + _this.span > _this.max) {
                    $(plus).css({"background-color":"#ddd"});
                    $(value).val(val);
                } else {
                    $(value).val(val); 
                };
                $(that).trigger($.Event("numberinput:change"), val);
            });
        }
    };
    function Plugin() {
        var numberinput = new Numberinput(this);
        return numberinput;
    };
    $.fn.numberinput = Plugin;
}(window.Zepto);
/**
 * scrollnav组件
 *
 * @date     2015-08-12
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Scrollnav = function() {};
    Scrollnav.prototype = {
        build: function(that) {
            var _this = this;
            var offset = 0;
            var navHeight = $("." + ns + "-nav-top").height();
            var itemIdArr = [];
            var itemTopArr = [];
            var pattern = /^.*-nav-top.*$/;
            if (pattern.test($(that).attr("class"))) {
                $("body").css("padding-top", navHeight);
                offset = navHeight * -1;
            };
            $(that).find(".item").on("click", function() {
                var index = this.id;
                var id = '#item_' + index;
                _this._changeChosen(that, this);
                $("html, body").scrollTop($(id).offset().top + offset);
            });
            $(".item").each(function() {
                itemIdArr.push(this.id);
            });
            for (var i = 0; i < itemIdArr.length; i++) {
                var id = '#item_' + itemIdArr[i];
                itemTopArr.push($(id).offset().top + offset);
            };
            for (var i = 0; i < itemTopArr.length; i++) {
                if (i == 0) {
                    $(window).on("scroll", {key: i}, function(event) {
                        var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
                        if (scrollt == itemTopArr[event.data.key]) {
                            _this._changeChosen(that, $("#" + itemIdArr[event.data.key]));
                        };
                    });
                } else if (i == itemTopArr.length - 1) {
                    $(window).on("scroll", {key: i}, function(event) {
                        var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
                        if (scrollt > itemTopArr[event.data.key - 1]) {
                            _this._changeChosen(that, $("#" + itemIdArr[event.data.key]));
                        };
                    });
                } else {
                    $(window).on("scroll", {key: i}, function(event) {
                        var scrollt = document.documentElement.scrollTop + document.body.scrollTop;
                        if (scrollt > itemTopArr[event.data.key - 1] && scrollt <= itemTopArr[event.data.key]) {
                            _this._changeChosen(that, $("#" + itemIdArr[event.data.key]));
                        };
                    });
                };
            };
        },
        _changeChosen: function(all, chosen) {
            $(all).find(".item").removeClass("chosen");
            $(chosen).addClass("chosen");
        }
    };
    function Plugin() {
        var scrollnav = new Scrollnav();
        scrollnav.build(this);
    };
    $.fn.scrollnav = Plugin;
}(window.Zepto);
/**
 * share组件
 *
 * @date     2015-08-11
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var ns = "smart";
    var Share = function() {};
    Share.prototype = {
        init: function(text) {
            var tpl = '' +
            '<div class="smart-share">' +
              '<div class="text"></div>' +
              '<div class="smart-icon-arrow"></div>' +
            '</div>';
            $("body").append(tpl);
            $("." + ns + "-share").find(".text").text(text);
            $("." + ns + "-share").on("click", function() {
                $("." + ns + "-share").remove();
            });
        }
    };
    function Plugin(text) {
        var share = new Share();
        share.init(text);
    };
    $.share = Plugin;
}(window.Zepto);
/**
 * 滑屏组件
 *
 * @version   1.0
 * @date      2015-07-01
 * @author    Yuxingyang<yuxingyang@tencent.com>
 */

!(function($) {
  function Swipe(container, options) {

    "use strict";

    // utilities
    var noop = function() {}; // simple no operation function
    var offloadFn = function(fn) {
      setTimeout(fn || noop, 0)
    }; // offload a functions execution

    // check browser capabilities
    var browser = {
      addEventListener: !!window.addEventListener,
      touch: ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch,
      transitions: (function(temp) {
        var props = ['transitionProperty', 'WebkitTransition', 'MozTransition', 'OTransition', 'msTransition'];
        for (var i in props)
          if (temp.style[props[i]] !== undefined) return true;
        return false;
      })(document.createElement('swipe'))
    };

    // quit if no root element
    if (!container) return;
    var element = container.children[0];
    var slides, slidePos, width, length;
    options = options || {};
    var index = parseInt(options.startSlide, 10) || 0;
    var speed = options.speed || 300;
    options.continuous = options.continuous !== undefined ? options.continuous : true;

    function setup() {

      // cache slides
      slides = element.children;
      length = slides.length;

      // set continuous to false if only one slide
      if (slides.length < 2) options.continuous = false;

      //special case if two slides
      if (browser.transitions && options.continuous && slides.length < 3) {
        element.appendChild(slides[0].cloneNode(true));
        element.appendChild(element.children[1].cloneNode(true));
        slides = element.children;
      }

      // create an array to store current positions of each slide
      slidePos = new Array(slides.length);

      // determine width of each slide
      width = container.getBoundingClientRect().width || container.offsetWidth;

      element.style.width = (slides.length * width) + 'px';

      // stack elements
      var pos = slides.length;
      while (pos--) {

        var slide = slides[pos];

        slide.style.width = width + 'px';
        slide.setAttribute('data-index', pos);

        if (browser.transitions) {
          slide.style.left = (pos * -width) + 'px';
          move(pos, index > pos ? -width : (index < pos ? width : 0), 0);
        }

      }

      // reposition elements before and after index
      if (options.continuous && browser.transitions) {
        move(circle(index - 1), -width, 0);
        move(circle(index + 1), width, 0);
      }

      if (!browser.transitions) element.style.left = (index * -width) + 'px';

      container.style.visibility = 'visible';
      pagination();
    }

    function prev() {

      if (options.continuous) slide(index - 1);
      else if (index) slide(index - 1);

    }

    function next() {

      if (options.continuous) slide(index + 1);
      else if (index < slides.length - 1) slide(index + 1);

    }

    function circle(index) {

      // a simple positive modulo using slides.length
      return (slides.length + (index % slides.length)) % slides.length;

    }

    function slide(to, slideSpeed) {

      // do nothing if already on requested slide
      if (index == to) return;

      if (browser.transitions) {

        var direction = Math.abs(index - to) / (index - to); // 1: backward, -1: forward

        // get the actual position of the slide
        if (options.continuous) {
          var natural_direction = direction;
          direction = -slidePos[circle(to)] / width;

          // if going forward but to < index, use to = slides.length + to
          // if going backward but to > index, use to = -slides.length + to
          if (direction !== natural_direction) to = -direction * slides.length + to;

        }

        var diff = Math.abs(index - to) - 1;

        // move all the slides between index and to in the right direction
        while (diff--) move(circle((to > index ? to : index) - diff - 1), width * direction, 0);

        to = circle(to);

        move(index, width * direction, slideSpeed || speed);
        move(to, 0, slideSpeed || speed);

        if (options.continuous) move(circle(to - direction), -(width * direction), 0); // we need to get the next in place

      } else {

        to = circle(to);
        animate(index * -width, to * -width, slideSpeed || speed);
        //no fallback for a circular continuous if the browser does not accept transitions
      }

      index = to;
      offloadFn(options.callback && options.callback(index, slides[index]));
    }

    function move(index, dist, speed) {

      translate(index, dist, speed);
      slidePos[index] = dist;

    }

    function translate(index, dist, speed) {

      var slide = slides[index];
      var style = slide && slide.style;

      if (!style) return;

      style.webkitTransitionDuration =
        style.MozTransitionDuration =
        style.msTransitionDuration =
        style.OTransitionDuration =
        style.transitionDuration = speed + 'ms';

      style.webkitTransform = 'translate(' + dist + 'px,0)' + 'translateZ(0)';
      style.msTransform =
        style.MozTransform =
        style.OTransform = 'translateX(' + dist + 'px)';

    }

    function animate(from, to, speed) {

      // if not an animation, just reposition
      if (!speed) {

        element.style.left = to + 'px';
        return;

      }

      var start = +new Date;

      var timer = setInterval(function() {

        var timeElap = +new Date - start;

        if (timeElap > speed) {

          element.style.left = to + 'px';

          if (delay) begin();

          options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);

          clearInterval(timer);
          return;

        }

        element.style.left = (((to - from) * (Math.floor((timeElap / speed) * 100) / 100)) + from) + 'px';

      }, 4);

    }

    // setup auto slideshow
    var delay = options.auto || 0;
    var interval;

    function begin() {

      interval = setTimeout(next, delay);

    }

    function stop() {

      //delay = 0;
      clearTimeout(interval);

    }

    function pagination(active) {
      $(container).find(".smart-swipe-pagination").remove();
      active = active || 0;
      var pagination = "<ol class='smart-swipe-pagination'>";
      for (var i = 0; i < length; i++) {

        pagination += "<li><a " + ((active == i) ? "class='active'" : "") + ">" + i + "</a></li>";
      }
      pagination += "</ol>";
      $(container).append(pagination);
    }


    // setup initial vars
    var start = {};
    var delta = {};
    var isScrolling;

    // setup event capturing
    var events = {

      handleEvent: function(event) {

        switch (event.type) {
          case 'touchstart':
            this.start(event);
            break;
          case 'touchmove':
            this.move(event);
            break;
          case 'touchend':
            offloadFn(this.end(event));
            break;
          case 'webkitTransitionEnd':
          case 'msTransitionEnd':
          case 'oTransitionEnd':
          case 'otransitionend':
          case 'transitionend':
            offloadFn(this.transitionEnd(event));
            break;
          case 'resize':
            offloadFn(setup);
            break;
        }

        if (options.stopPropagation) event.stopPropagation();

      },
      start: function(event) {

        var touches = event.touches[0];

        // measure start values
        start = {

          // get initial touch coords
          x: touches.pageX,
          y: touches.pageY,

          // store time to determine touch duration
          time: +new Date

        };

        // used for testing first move event
        isScrolling = undefined;

        // reset delta and end measurements
        delta = {};

        // attach touchmove and touchend listeners
        element.addEventListener('touchmove', this, false);
        element.addEventListener('touchend', this, false);

      },
      move: function(event) {

        // ensure swiping with one touch and not pinching
        if (event.touches.length > 1 || event.scale && event.scale !== 1) return

        if (options.disableScroll) event.preventDefault();

        var touches = event.touches[0];

        // measure change in x and y
        delta = {
          x: touches.pageX - start.x,
          y: touches.pageY - start.y
        }

        // determine if scrolling test has run - one time test
        if (typeof isScrolling == 'undefined') {
          isScrolling = !!(isScrolling || Math.abs(delta.x) < Math.abs(delta.y));
        }

        // if user is not trying to scroll vertically
        if (!isScrolling) {

          // prevent native scrolling
          event.preventDefault();

          // stop slideshow
          stop();

          // increase resistance if first or last slide
          if (options.continuous) { // we don't add resistance at the end

            translate(circle(index - 1), delta.x + slidePos[circle(index - 1)], 0);
            translate(index, delta.x + slidePos[index], 0);
            translate(circle(index + 1), delta.x + slidePos[circle(index + 1)], 0);

          } else {

            delta.x =
              delta.x /
              ((!index && delta.x > 0 // if first slide and sliding left
                  || index == slides.length - 1 // or if last slide and sliding right
                  && delta.x < 0 // and if sliding at all
                ) ?
                (Math.abs(delta.x) / width + 1) // determine resistance level
                : 1); // no resistance if false

            // translate 1:1
            translate(index - 1, delta.x + slidePos[index - 1], 0);
            translate(index, delta.x + slidePos[index], 0);
            translate(index + 1, delta.x + slidePos[index + 1], 0);
          }

        }

      },
      end: function(event) {

        // measure duration
        var duration = +new Date - start.time;

        // determine if slide attempt triggers next/prev slide
        var isValidSlide =
          Number(duration) < 250 // if slide duration is less than 250ms
          && Math.abs(delta.x) > 20 // and if slide amt is greater than 20px
          || Math.abs(delta.x) > width / 2; // or if slide amt is greater than half the width

        // determine if slide attempt is past start and end
        var isPastBounds = !index && delta.x > 0 // if first slide and slide amt is greater than 0
          || index == slides.length - 1 && delta.x < 0; // or if last slide and slide amt is less than 0

        if (options.continuous) isPastBounds = false;

        // determine direction of swipe (true:right, false:left)
        var direction = delta.x < 0;

        // if not scrolling vertically
        if (!isScrolling) {

          if (isValidSlide && !isPastBounds) {

            if (direction) {

              if (options.continuous) { // we need to get the next in this direction in place

                move(circle(index - 1), -width, 0);
                move(circle(index + 2), width, 0);

              } else {
                move(index - 1, -width, 0);
              }

              move(index, slidePos[index] - width, speed);
              move(circle(index + 1), slidePos[circle(index + 1)] - width, speed);
              index = circle(index + 1);

            } else {
              if (options.continuous) { // we need to get the next in this direction in place

                move(circle(index + 1), width, 0);
                move(circle(index - 2), -width, 0);

              } else {
                move(index + 1, width, 0);
              }

              move(index, slidePos[index] + width, speed);
              move(circle(index - 1), slidePos[circle(index - 1)] + width, speed);
              index = circle(index - 1);

            }

            options.callback && options.callback(index, slides[index]);

          } else {

            if (options.continuous) {

              move(circle(index - 1), -width, speed);
              move(index, 0, speed);
              move(circle(index + 1), width, speed);

            } else {

              move(index - 1, -width, speed);
              move(index, 0, speed);
              move(index + 1, width, speed);
            }

          }

        }

        // kill touchmove and touchend event listeners until touchstart called again
        element.removeEventListener('touchmove', events, false)
        element.removeEventListener('touchend', events, false)

      },
      transitionEnd: function(event) {

        if (parseInt(event.target.getAttribute('data-index'), 10) == index) {

          if (delay) begin();

          options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);
          pagination(index)
        }

      }

    }

    // trigger setup
    setup();

    // start auto slideshow if applicable
    if (delay) begin();


    // add event listeners
    if (browser.addEventListener) {

      // set touchstart event on element
      if (browser.touch) element.addEventListener('touchstart', events, false);

      if (browser.transitions) {
        element.addEventListener('webkitTransitionEnd', events, false);
        element.addEventListener('msTransitionEnd', events, false);
        element.addEventListener('oTransitionEnd', events, false);
        element.addEventListener('otransitionend', events, false);
        element.addEventListener('transitionend', events, false);
      }

      // set resize event on window
      window.addEventListener('resize', events, false);

    } else {

      window.onresize = function() {
        setup()
      }; // to play nice with old IE

    }

    // expose the Swipe API
    return {
      setup: function() {

        setup();

      },
      slide: function(to, speed) {

        // cancel slideshow
        stop();

        slide(to, speed);

      },
      prev: function() {

        // cancel slideshow
        stop();

        prev();

      },
      next: function() {

        // cancel slideshow
        stop();

        next();

      },
      stop: function() {

        // cancel slideshow
        stop();

      },
      getPos: function() {

        // return current index position
        return index;

      },
      getNumSlides: function() {

        // return total number of slides
        return length;
      },
      kill: function() {

        // cancel slideshow
        stop();

        // reset element
        element.style.width = '';
        element.style.left = '';

        // reset slides
        var pos = slides.length;
        while (pos--) {

          var slide = slides[pos];
          slide.style.width = '';
          slide.style.left = '';

          if (browser.transitions) translate(pos, 0, 0);

        }

        // removed event listeners
        if (browser.addEventListener) {

          // remove current event listeners
          element.removeEventListener('touchstart', events, false);
          element.removeEventListener('webkitTransitionEnd', events, false);
          element.removeEventListener('msTransitionEnd', events, false);
          element.removeEventListener('oTransitionEnd', events, false);
          element.removeEventListener('otransitionend', events, false);
          element.removeEventListener('transitionend', events, false);
          window.removeEventListener('resize', events, false);

        } else {

          window.onresize = null;

        }

      }
    }

  }
  $.fn.swipe = function(params) {
    return this.each(function() {
      $(this).data('Swipe', new Swipe($(this)[0], params));
    });
  }
})(window.jQuery || window.Zepto);
/**
 * tab组件
 *
 * @date     2015-07-06
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var Tab = function() {};
    Tab.prototype = {
        bindEvent: function(that){
            $(that).find(".title").find(".item").on("click", function(){
                $(that).find(".title").find(".item").removeClass("chosen");
                $(this).addClass("chosen");
                $(that).find(".content").find(".item").removeClass("chosen");
                $(that).find(".content").find(".item").eq($(this).index()).addClass("chosen");
            });
        }
    };
    function Plugin() {
        var tab = new Tab();
        tab.bindEvent(this);
    };
    $.fn.tab = Plugin;
}(window.Zepto);
/**
 * 弹窗组件
 *
 * 票据服务请求组件
 * 依赖artemplate
 *
 * @date     2015-06-29
 * @author   Yuxingyang<yuxingyang@tencent.com>
 */

! function($) {

  // 默认模板
  var _modalTpl = '<div class="smart-loading-overlay">' +
    '<div class="smart-loading">' +
    '<div class="smart-loading-icon">' +
    '<i class="icon smart-icon">&#xe64f;</i>' +
    '<div>{{content}}</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  var render = template.compile(_modalTpl);
  // 默认参数
  var defaults = {
      retry: [0, 1, 3, 5],
      times: 0,
      content: '正在加载...',
      remote: "http://beta.city.act.qq.com/api/ticket"
    }
    // 构造函数
  var TicketAjax = function(el, option, isFromTpl) {
    this.option = option;
    this.element = $(el);
    this._isFromTpl = isFromTpl;
    this.show();
    this.getTicket();
  }
  TicketAjax.prototype = {
    getTicket: function() {
      var self = this;
      var token = false;
      if (!this.option.api_key) {
        throw "api_key invalid";
      }
      $.ajax({
        type: "get",
        dataType: "jsonp",
        url: defaults.remote,
        data: {
          api_key: this.option.api_key,
          format: "jsonp"
        },
        success: function(json) {
          //数据预处理
          if (json.data.visit) {
              self.ajax();
          } else {
            if (!defaults.retry[time]) return;
            setTimeout(function() {
                self.getTicket();
              },
              defaults.retry[time] * 1000);
          }
        },
        error: function() {
          throw "get ticket error";
        }
      })
    },
    ajax: function() {
      var self = this;
      this.option.complete = function(xhr, ts) {
        self.hide();
        self.option.complete(xhr, ts)
      }
      $.ajax(self.option);
    },
    show: function() {
      var self = this;
      self.element.trigger($.Event("ticket:show"));
      self.element.addClass("show");
    },
    hide: function() {
      var self = this;
      self.element.trigger($.Event("ticket:hide"));
      self.element.removeClass("show");
      self._isFromTpl && self.element.remove();
    }
  }

  function Plugin(option) {
    var isFromTpl = false;
    if ($.isArray(this)) {
      //从现有dom创建
      var element = this;
    } else {
      //从模板创建并插入body
      var element = $(render(defaults)).appendTo('body');
      isFromTpl = true;
    }
    return element.each(function() {
      var $this = $(this)
      var data = $this.data('smart.ticket')
      var options = option;

      if (!data) $this.data('smart.ticket', (data = new TicketAjax(this, options, isFromTpl)))
      if (typeof option == 'string') data[option]()
    })
  }
  $.ticketAjax = Plugin;
}(window.Zepto)
/**
 * tips组件
 *
 * 依赖artemplate
 *
 * @date     2015-08-06
 * @author   Yuxingyang<yuxingyang@tencent.com>
 */

! function($) {

  // 默认模板
  var _tipsTpl = '<div class="smart-tips" style="top:{{top}}px">' +
    '<div class="smart-tips-content smart-tips-{{type}}">' +
    '{{content}}' +
    '</div>' +
    '</div>';
  var render = template.compile(_tipsTpl);
  // 默认参数
  var defaults = {
      content: '欢迎使用SmartUI',
      type: 'info',
      stayTime: 2000,
      top: 0
    }
    // 构造函数
  var Tips = function(el, option, isFromTpl) {
    var self = this;
    this.option = option;
    this.element = $(el);
    this._isFromTpl = isFromTpl;
    this.elementHeight = this.element.height();

    this.element.css({
      "-webkit-transform": "translateY(-" + this.elementHeight + "px)"
    });
    setTimeout(function() {
      self.element.css({
        "-webkit-transition": "all .5s"
      });
      self.show();
    }, 20);
  }
  Tips.prototype = {
    show: function() {
      var self = this;
      self.element.trigger($.Event("tips:show"));
      this.element.css({
        "-webkit-transform": "translateY(0px)"
      });
      if (self.option.stayTime > 0) {
        setTimeout(function() {
          self.hide();
        }, self.option.stayTime)
      }
    },
    hide: function() {
      var self = this;
      self.element.trigger($.Event("tips:hide"));
      this.element.css({
        "-webkit-transform": "translateY(-" + this.elementHeight + "px)"
      });
      setTimeout(function() {
        self._isFromTpl && self.element.remove();
      }, 500)
    }
  }

  function Plugin(option) {
    var isFromTpl = false;
    if ($.isArray(this)) {
      //从现有dom创建
      var element = this;
    } else {
      //从模板创建并插入body
      var element = $(render($.extend({}, defaults, typeof option == 'object' && option))).appendTo('body');
      isFromTpl = true;
    }
    return element.each(function() {
      var $this = $(this)
      var data = $this.data('smart.tips')
      var options = $.extend({}, defaults, typeof option == 'object' && option)

      if (!data) $this.data('smart.tips', (data = new Tips(this, options, isFromTpl)))
      if (typeof option == 'string') data[option]()
    })
  }
  $.fn.tips = $.tips = Plugin;
}(window.Zepto)
/**
 * video组件
 *
 * @date     2015-07-09
 * @author   yangjiewu<yangjiewu@tencent.com>
 */

! function($) {
    var Video = function() {};
    Video.prototype = {
        init: function(option) {
            var id = option.id;
            var vid = option.vid;
            var width = option.width;
            var height = option.height;
            var type = option.type;
            var autoplay = option.autoplay;
            switch (type) {
            case 1:
                this._setDemandVideo(id, vid, width, height, autoplay);
                break;
            case 2:
                this._setLiveVideo(id, vid, width, height, autoplay);
                break;
            default:
            };
        },
        _setDemandVideo: function(id, vid, width, height, autoplay) {
            var video = new tvp.VideoInfo();
            video.setVid(vid);
            var player = new tvp.Player();
            player.create({
                width: width,
                height: height,
                video: video,
                playerType: 'html5',
                isHtml5ShowLoadingAdOnStart: false,
                isHtml5ShowLoadingAdOnChange: false,
                modId: id,
                autoplay: autoplay
            });
        },
        _setLiveVideo: function(id, vid, width, height, autoplay) {
            var video = new tvp.VideoInfo();
            video.setChannelId(vid);  
            var player = new tvp.Player();
            player.create({
                width: width,
                height: height,
                type: 1,
                video: video,
                playerType: 'html5',
                isHtml5ShowLoadingAdOnStart: false,
                isHtml5ShowLoadingAdOnChange: false,
                modId: id,
                autoplay: autoplay
            });
        }
    };
    function Plugin(option) {
        var video = new Video();
        video.init(option);
    };
    $.video = Plugin;
}(window.Zepto);