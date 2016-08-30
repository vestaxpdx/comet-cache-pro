(function(b){var a={namespace:"comet_cache"},d=b(window),c=b(document);a.onReady=function(){
/*![pro strip-from='lite']*/
;a.statsData=null;a.statsRunning=false;
/*![/pro]*/
;a.hideAJAXResponseTimeout=null;a.vars=b("#"+a.namespace+"-admin-bar-vars").data("json");b("#wp-admin-bar-"+a.namespace+"-clear > a").on("click",a.clearCache);
/*![pro strip-from='lite']*/
;b("#wp-admin-bar-"+a.namespace+"-wipe > a").on("click",a.wipeCache);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-home-url-only > a").on("click",a.clearCacheHomeUrlOnly);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-current-url-only > a").on("click",a.clearCacheCurrentUrlOnly);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-specific-url-only > a").on("click",a.clearCacheSpecificUrlOnly);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-opcache-only > a").on("click",a.clearCacheOpCacheOnly);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-cdn-only > a").on("click",a.clearCacheCdnOnly);b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-transients-only > a").on("click",a.clearExpiredTransientsOnly);c.on("click","."+a.namespace+"-ajax-response",a.hideAJAXResponse);var f=b("#wp-admin-bar-"+a.namespace+"-stats"),e=f.find(".-wrapper"),g=e.find(".-container");if(f.length&&a.MutationObserver){(new a.MutationObserver(function(h){b.each(h,function(k,i){if(i.type!=="attributes"){return}if(i.attributeName!=="class"){return}var j=i.oldValue,l=b(i.target).prop(i.attributeName);if(!/\bhover\b/i.test(j)&&/\bhover\b/i.test(l)){a.stats()}return false})})).observe(f[0],{attributes:true,attributeOldValue:true,childList:true,characterData:true})}};a.wipeCache=function(i){a.preventDefault(i);a.statsData=null;var f={_wpnonce:a.vars._wpnonce};f[a.namespace]={ajaxWipeCache:"1"};var e=b("#wp-admin-bar-"+a.namespace+"-wipe > a");var h=b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-label");var g=b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-options");a.removeAJAXResponse();e.parent().addClass("-processing");e.add(g.find("a")).attr("disabled","disabled");b.post(a.vars.ajaxURL,f,function(k){a.removeAJAXResponse();e.parent().removeClass("-processing");e.add(g.find("a")).removeAttr("disabled");var j=b('<div class="'+a.namespace+'-ajax-response -wipe">'+k+"</div>");b("body").append(j);a.showAJAXResponse()})};a.clearCache=function(j,f){a.preventDefault(j);a.statsData=null;f=f||{};var l=b.extend({},{urlOnly:"",opCacheOnly:false,cdnOnly:false,transientsOnly:false},f);var e={_wpnonce:a.vars._wpnonce};var i=false;if(l.urlOnly){i=true;e[a.namespace]={ajaxClearCacheUrl:l.urlOnly}}else{if(l.opCacheOnly){i=true;e[a.namespace]={ajaxClearOpCache:"1"}}else{if(l.cdnOnly){i=true;e[a.namespace]={ajaxClearCdnCache:"1"}}else{if(l.transientsOnly){i=true;e[a.namespace]={ajaxClearExpiredTransients:"1"}}else{e[a.namespace]={ajaxClearCache:"1"}}}}}var k=b("#wp-admin-bar-"+a.namespace+"-clear > a");var h=b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper .-label");var g=b("#wp-admin-bar-"+a.namespace+"-clear-options-wrapper  .-options");a.removeAJAXResponse();if(i&&h.length){h.addClass("-processing")}else{k.parent().addClass("-processing")}k.add(g.find("a")).attr("disabled","disabled");b.post(a.vars.ajaxURL,e,function(n){a.removeAJAXResponse();if(i&&h.length){h.removeClass("-processing")}else{k.parent().removeClass("-processing")}k.add(g.find("a")).removeAttr("disabled");var m=b('<div class="'+a.namespace+'-ajax-response -clear">'+n+"</div>");b("body").append(m);a.showAJAXResponse()})};a.clearCacheHomeUrlOnly=function(e){a.clearCache(e,{urlOnly:"home"})};a.clearCacheCurrentUrlOnly=function(e){a.clearCache(e,{urlOnly:document.URL})};a.clearCacheSpecificUrlOnly=function(f){var e=b.trim(prompt(a.vars.i18n.enterSpecificUrl,"http://"));if(e&&e!=="http://"){a.clearCache(f,{urlOnly:e})}else{a.preventDefault(f)}};a.clearCacheOpCacheOnly=function(e){a.clearCache(e,{opCacheOnly:true})};a.clearCacheCdnOnly=function(e){a.clearCache(e,{cdnOnly:true})};a.clearExpiredTransientsOnly=function(e){a.clearCache(e,{transientsOnly:true})};
/*![/pro]*/
;a.showAJAXResponse=function(){clearTimeout(a.hideAJAXResponseTimeout);b("."+a.namespace+"-ajax-response").off(a.animationEndEvents).on(a.animationEndEvents,function(){a.hideAJAXResponseTimeout=setTimeout(a.hideAJAXResponse,2500)}).addClass(a.namespace+"-admin-bar-animation-zoom-in-down").show().on("mouseover",function(){clearTimeout(a.hideAJAXResponseTimeout);b(this).addClass("-hovered")})};a.hideAJAXResponse=function(e){a.preventDefault(e);clearTimeout(a.hideAJAXResponseTimeout);b("."+a.namespace+"-ajax-response").off(a.animationEndEvents).on(a.animationEndEvents,function(){a.removeAJAXResponse()}).addClass(a.namespace+"-admin-bar-animation-zoom-out-up")};a.removeAJAXResponse=function(){clearTimeout(a.hideAJAXResponseTimeout);b("."+a.namespace+"-ajax-response").off(a.animationEndEvents).remove()};
/*![pro strip-from='lite']*/
;a.stats=function(){if(a.statsRunning){return}a.statsRunning=true;var t=!a.vars.isMultisite||a.vars.currentUserHasNetworkCap;var p=b("body"),o=b("#wp-admin-bar-"+a.namespace+"-stats"),s=o.find(".-wrapper"),q=s.find(".-container"),e=q.find(".-refreshing"),g=q.find(".-chart-a"),f=q.find(".-chart-b"),i=q.find(".-totals"),k=i.find(".-files"),r=i.find(".-size"),j=i.find(".-dir"),h=q.find(".-disk"),l=h.find(".-free"),m=h.find(".-size"),n=q.find(".-more-info");var u=function(){if(!o.hasClass("hover")){a.statsRunning=false;return}e.show();g.hide();f.hide();i.removeClass("-no-charts");i.css("visibility","hidden");if(!t||b.trim(j.text()).length>30){j.hide()}h.css("visibility","hidden");if(t){n.css("visibility","hidden")}else{n.hide()}if(!a.statsData){var w={_wpnonce:a.vars._wpnonce};w[a.namespace]={ajaxDirStats:"1"};b.post(a.vars.ajaxURL,w,function(x){console.log("Admin Bar :: statsData :: %o",x);a.statsData=x;v()})}else{setTimeout(v,500)}},v=function(){if(!a.statsData){a.statsRunning=false;return}if(!o.hasClass("hover")){a.statsRunning=false;return}e.hide();g.css("display","block");f.css("display","block");var Y=null,X=null,T=null,Z=null;var x=t?"forCache":"forHostCache",M=t?"forHtmlCCache":"forHtmlCHostCache",B=t?"largestCacheSize":"largestHostCacheSize",z=t?"largestCacheCount":"largestHostCacheCount";var C=a.statsData[B].size,L=a.statsData[B].days,E=a.statsData[z].count,S=a.statsData[z].days,R=a.statsData[x].stats.total_links_files,aa=a.statsData[M].stats.total_links_files,F=R+aa,J=a.statsData[x].stats.total_size,O=a.statsData[M].stats.total_size,P=J+O,N=a.statsData[x].stats.disk_total_space,K=a.statsData[x].stats.disk_free_space,G=0,W=0,w=0,I=0,D=0,Q=0;if(a.vars.isMultisite&&a.vars.currentUserHasNetworkCap){G=a.statsData.forHostCache.stats.total_links_files;W=a.statsData.forHtmlCHostCache.stats.total_links_files;w=G+W;I=a.statsData.forHostCache.stats.total_size;D=a.statsData.forHtmlCHostCache.stats.total_size;Q=I+D}var y=function(af,ad){if(!(af instanceof Array)){return{}}if(typeof ad!=="number"||ad<=0){ad=10}var ac=[];b.each(af,function(ai,aj){ac.push(Number(aj.value))});var ah=0,ae=Math.min.apply(null,ac),ab=Math.max.apply(null,ac),ag=Math.ceil((ab-ah)/ad);return{scaleSteps:ad,scaleStartValue:ah,scaleStepWidth:ag,scaleIntegersOnly:true,scaleOverride:true}},H={responsive:true,maintainAspectRatio:true,animationSteps:35,scaleFontSize:10,scaleShowLine:true,scaleFontFamily:"sans-serif",scaleShowLabelBackdrop:true,scaleBackdropPaddingY:2,scaleBackdropPaddingX:4,scaleFontColor:"rgba(0,0,0,1)",scaleBackdropColor:"rgba(255,255,255,1)",scaleLineColor:b("body").hasClass("admin-color-light")?"rgba(0,0,0,0.25)":"rgba(255,255,255,0.25)",scaleLabel:function(ab){return a.bytesToSizeLabel(Number(ab.value))},tooltipFontSize:12,tooltipFillColor:"rgba(0,0,0,1)",tooltipFontFamily:"Georgia, serif",tooltipTemplate:function(ab){return ab.label+": "+a.bytesToSizeLabel(Number(ab.value))},segmentShowStroke:true,segmentStrokeWidth:1,segmentStrokeColor:b("body").hasClass("admin-color-light")?"rgba(0,0,0,1)":"rgba(255,255,255,1)"},V=H;var A=[],U=[];A.push({value:C,label:a.vars.i18n.xDayHigh.replace("%s",L),color:"#ff5050",highlight:"#c63f3f"});A.push({value:P,label:a.vars.i18n.currentTotal,color:"#46bf52",highlight:"#33953e"});A.push({value:J,label:a.vars.i18n.pageCache,color:"#0096CC",highlight:"#057ca7"});A.push({value:O,label:a.vars.i18n.htmlCompressor,color:"#FFC870",highlight:"#d6a85d"});if(a.vars.isMultisite&&a.vars.currentUserHasNetworkCap){A.push({value:Q,label:a.vars.i18n.currentSite,color:"#46bfb4",highlight:"#348f87"})}b.extend(H,y(A,5));U=A;b.extend(V,y(U,5));if((Y=o.data("chartA"))){Y.destroy()}if((X=o.data("chartB"))){X.destroy()}if((T=o.data("chartADimensions"))){g.attr("width",parseInt(T.width)).attr("height",parseInt(T.height)).css(T)}if((Z=o.data("chartBDimensions"))){f.attr("width",parseInt(Z.width)).attr("height",parseInt(Z.height)).css(Z)}if(g.length&&A[0].value>0){Y=new Chart(g[0].getContext("2d")).PolarArea(A,H);o.data("chartA",Y).data("chartADimensions",{width:g.width()+"px",height:g.height()+"px"})}else{Y=null;g.hide()}if(f.length&&U[0].value>0){X=new Chart(f[0].getContext("2d")).PolarArea(U,V);o.data("chartB",X).data("chartBDimensions",{width:f.width()+"px",height:f.height()+"px"})}else{X=null;f.hide()}if(!Y&&!X){i.addClass("-no-charts")}i.css("visibility","visible");k.find(".-value").html(a.escHtml(a.numberFormat(F)+" "+(F===1?a.vars.i18n.file:a.vars.i18n.files)));r.find(".-value").html(a.escHtml(a.bytesToSizeLabel(P)));h.css("visibility","visible");m.find(".-value").html(a.escHtml(a.bytesToSizeLabel(N)));l.find(".-value").html(a.escHtml(a.bytesToSizeLabel(K)));if(t){n.css("visibility","visible")}a.statsRunning=false};u()};a.bytesToSizeLabel=function(f,e){if(typeof f!=="number"||f<=1){return f===1?"1 byte":"0 bytes"}if(typeof e!=="number"||e<=0){e=0}var i=1024,j=Math.floor(Math.log(f)/Math.log(i)),h=["bytes","KB","MB","GB","TB","PB","EB","ZB","YB"],g=(f/Math.pow(i,j));return g.toFixed(e)+" "+h[j]};a.numberFormat=function(f,e){if(typeof f!=="number"){return String(f)}if(typeof e!=="number"||e<=0){e=0}return f.toFixed(e).replace(/./g,function(g,i,h){return i&&g!=="."&&((h.length-i)%3===0)?","+g:g})};a.escHtml=function(f){var e={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"};return String(f).replace(/[&<>"']/g,function(g){return e[g]})};
/*![/pro]*/
;a.preventDefault=function(f,e){if(!f){return}f.preventDefault();if(e){f.stopImmediatePropagation()}};a.MutationObserver=(function(){var e=null;b.each(["","WebKit","O","Moz","Ms"],function(f,g){if(g+"MutationObserver" in window){e=window[g+"MutationObserver"];return false}});return e}());a.animationEndEvents="webkitAnimationEnd mozAnimationEnd msAnimationEnd oAnimationEnd animationEnd";c.ready(a.onReady)})(jQuery);