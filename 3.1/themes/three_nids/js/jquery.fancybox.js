/*
 * FancyBox - simple and fancy jQuery plugin
 * Examples and documentation at: http://fancy.klade.lv/
 * Version: 1.2.1 (13/03/2009)
 * Copyright (c) 2009 Janis Skarnelis
 * Licensed under the MIT License: http://en.wikipedia.org/wiki/MIT_License
 * Requires: jQuery v1.3+
*/
;(function($) {

	$.fn.fixPNG = function() {
		return this.each(function () {
			var image = $(this).css('backgroundImage');

			if (image.match(/^url\(["']?(.*\.png)["']?\)$/i)) {
				image = RegExp.$1;
				$(this).css({
					'backgroundImage': 'none',
					'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=" + ($(this).css('backgroundRepeat') == 'no-repeat' ? 'crop' : 'scale') + ", src='" + image + "')"
				}).each(function () {
					var position = $(this).css('position');
					if (position != 'absolute' && position != 'relative')
						$(this).css('position', 'relative');
				});
			}
		});
	};
	

	var elem, opts, busy = false, imagePreloader = new Image, loadingTimer, loadingFrame = 1, imageRegExp = /\.(jpg|gif|png|bmp|jpeg)(.*)?$/i;
	var isIE = ($.browser.msie && parseInt($.browser.version.substr(0,1)) < 8);

	$.fn.fancybox = function(settings) {
		settings = $.extend({}, $.fn.fancybox.defaults, settings);

		var matchedGroup = this;

		function _initialize() {
			elem = this;
			opts = settings;

			_start();

			return false;
		};

		function _start() {
			if (busy) return;

			if ($.isFunction(opts.callbackOnStart)) {
				opts.callbackOnStart();
			}

			opts.itemArray		= [];
			opts.itemCurrent	= 0;

			if (settings.itemArray.length > 0) {
				opts.itemArray = settings.itemArray;

			} else {
				var item = {};

				if (!elem.rel || elem.rel == '') {
					var item = {href: elem.href, title: elem.title, modules: elem.name, fancyclass: elem.className};

					if ($(elem).children("img:first").length) {
						item.orig = $(elem).children("img:first");
					}

					opts.itemArray.push( item );

				} else {
					
					var subGroup = $(matchedGroup).filter("a[rel=" + elem.rel + "]");

					var item = {};

					for (var i = 0; i < subGroup.length; i++) {
						item = {href: subGroup[i].href, title: subGroup[i].title, modules: subGroup[i].name, fancyclass: subGroup[i].className};

						if ($(subGroup[i]).children("img:first").length) {
							item.orig = $(subGroup[i]).children("img:first");
						}

						opts.itemArray.push( item );
					}

					while ( opts.itemArray[ opts.itemCurrent ].href != elem.href ) {
						opts.itemCurrent++;
					}
				}
			}

			if (opts.overlayShow) {
				if (isIE) {
					$('embed, object, select').css('visibility', 'hidden');
				}

				$("#fancy_overlay").css('opacity', opts.overlayOpacity).show();
			}

			_change_item();
		};

		function _change_item() {
			$("#fancy_right, #fancy_left, #fancy_close, #fancy_title, #fancy_modules").hide();

			var href = opts.itemArray[ opts.itemCurrent ].href;
						
			if (href.match(/#/)) {
				var target = window.location.href.split('#')[0]; target = href.replace(target, ''); target = target.substr(target.indexOf('#'));

				_set_content('<div id="fancy_div">' + $(target).html() + '</div>', opts.frameWidth, opts.frameHeight);

			} else if (href.match(imageRegExp)) {
				imagePreloader = new Image; imagePreloader.src = href;

				if (imagePreloader.complete) {
					_proceed_image();

				} else {
					$.fn.fancybox.showLoading();

					$(imagePreloader).unbind().bind('load', function() {
						$(".fancy_loading").hide();

						_proceed_image();
					});
				}
			 } else if (href.match("iframe") || opts.itemArray[opts.itemCurrent].fancyclass.indexOf("iframe") >= 0) {
				 if  (href.match('w=') && href.match('h=')){
					var ifrWidth = parseInt(href.substring(href.indexOf('w=')+2,href.indexOf('xewx')));
					var ifrHeight = parseInt(href.substring(href.indexOf('h=')+2,href.indexOf('xehx')));
				 }else{
					var ifrWidth= opts.frameWidth;
					var ifrHeight= opts.frameHeight;
				 }
				 $("#fancy_content").empty();
				_set_content('<iframe id="fancy_frame" onload="$.fn.fancybox.showIframe()" name="fancy_iframe' + Math.round(Math.random()*1000) + '" frameborder="0" hspace="0" src="' + href + '"></iframe>', ifrWidth, ifrHeight);

			} else {
				$.get(href, function(data) {
					_set_content( '<div id="fancy_ajax">' + data + '</div>', opts.frameWidth, opts.frameHeight );
				});
			}
		};

		function _proceed_image() {
			if (opts.imageScale) {
				var w = $.fn.fancybox.getViewport();

				var r = Math.min(Math.min(w[0] - 36, imagePreloader.width) / imagePreloader.width, Math.min(w[1] - 60, imagePreloader.height) / imagePreloader.height);

				var width = Math.round(r * imagePreloader.width);
				var height = Math.round(r * imagePreloader.height);

			} else {
				var width = imagePreloader.width;
				var height = imagePreloader.height;
			}

			_set_content('<img alt="" id="fancy_img" src="' + imagePreloader.src + '" />', width, height);
		};

		function _preload_neighbor_images() {
			if ((opts.itemArray.length -1) > opts.itemCurrent) {
				var href = opts.itemArray[opts.itemCurrent + 1].href;
					$("<iframe>").attr("src", href);
			}

			if (opts.itemCurrent > 0) {
				var href = opts.itemArray[opts.itemCurrent -1].href;
					$("<iframe>").attr("src", href);
			}
		};

		function _set_content(value, width, height) {
			
			busy = true;
			
			
			var w = $.fn.fancybox.getViewport();
			var r = Math.min(Math.min(w[0]-36, width) / width, Math.min(w[1]-50, height )/ height);
			var width = Math.round(r * width);
			var height = Math.round(r * height);

			var pad = opts.padding;

			if (isIE) {
				$("#fancy_content")[0].style.removeExpression("height");
				$("#fancy_content")[0].style.removeExpression("width");
			}

			if (pad > 0) {
				width	+= pad * 2;
				height	+= pad * 2;

				$("#fancy_content").css({
					'top'		: pad + 'px',
					'right'		: pad + 'px',
					'bottom'	: pad + 'px',
					'left'		: pad + 'px',
					'width'	: 'auto',
					'height'	: 'auto'
				});

				if (isIE) {
					$("#fancy_content")[0].style.setExpression('height',	'(this.parentNode.clientHeight - 20)');
					$("#fancy_content")[0].style.setExpression('width',		'(this.parentNode.clientWidth - 20)');
				}

			} else {
				$("#fancy_content").css({
					'top'		: 0,
					'right'		: 0,
					'bottom'	: 0,
					'left'		: 0,
					'width'	: '100%',
					'height'	: '100%'
				});
			}

			if ($("#fancy_outer").is(":visible") && width == $("#fancy_outer").width() && height == $("#fancy_outer").height()) {
				$("#fancy_content").fadeOut("fast", function() {
					$("#fancy_content").empty().append($(value)).fadeIn("normal", function() {
						_finish();
					});
				});

				return;
			}
			

			var itemLeft	= (width + 36)	> w[0] ? w[2] : (w[2] + Math.round((w[0] - width - 36) / 2));
			var itemTop		= (height + 50)	> w[1] ? w[3] : (w[3] + Math.round((w[1] - height - 50) / 2));

			var itemOpts = {
				'left':		itemLeft,
				'top':		itemTop,
				'width':	width + 'px',
				'height':	height + 'px'
			};

			if ($("#fancy_outer").is(":visible")) {
				$("#fancy_content").fadeOut("normal", function() {
					$("#fancy_content").empty();
					$("#fancy_outer").animate(itemOpts, opts.zoomSpeedChange, opts.easingChange, function() {
						$("#fancy_content").append($(value)).fadeIn("normal", function() {
							_finish();
						});
					});
				});

			} else {

				if (opts.zoomSpeedIn > 0 && opts.itemArray[opts.itemCurrent].orig !== undefined) {
					$("#fancy_content").empty().append($(value));

					var orig_item	= opts.itemArray[opts.itemCurrent].orig;
					var orig_pos	= $.fn.fancybox.getPosition(orig_item);
					
					$("#fancy_outer").css({
						'left':		(orig_pos.left - 18) + 'px',
						'top':		(orig_pos.top  - 18) + 'px',
						'width':	$(orig_item).width(),
						'height':	$(orig_item).height()
					});

					if (opts.zoomOpacity) {
						itemOpts.opacity = 'show';
					}

					$("#fancy_outer").animate(itemOpts, opts.zoomSpeedIn, opts.easingIn, function() {
						_finish();
					});

				} else {

					$("#fancy_content").hide().empty().append($(value)).show();
					$("#fancy_outer").css(itemOpts).fadeIn("normal", function() {
						_finish();
					});
				}
			}
		};

		function _set_navigation() {
			if (opts.itemCurrent != 0) {
				$("#fancy_left, #fancy_left_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					opts.itemCurrent--;
					_change_item();

					return false;
				});

				$("#fancy_left").show();
			}

			if (opts.itemCurrent != ( opts.itemArray.length -1)) {
				$("#fancy_right, #fancy_right_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					opts.itemCurrent++;
					_change_item();

					return false;
				});

				$("#fancy_right").show();
			}
		};

		function _finish() {
			_set_navigation();

			_preload_neighbor_images();

			$(document).keydown(function(e) {
				if (e.keyCode == 27) {
					$.fn.fancybox.close();
					$(document).unbind("keydown");

				} else if(e.keyCode == 37 && opts.itemCurrent != 0) {
					opts.itemCurrent--;
					_change_item();
					$(document).unbind("keydown");

				} else if(e.keyCode == 39 && opts.itemCurrent != (opts.itemArray.length - 1)) {
 					opts.itemCurrent++;
					_change_item();
					$(document).unbind("keydown");
				}
			});

			if (opts.centerOnScroll) {
				$(window).bind("resize scroll", $.fn.fancybox.scrollBox);
			} else {
				$("div#fancy_outer").css("position", "absolute");
			}

			if (opts.hideOnContentClick) {
				$("#fancy_wrap").click($.fn.fancybox.close);
			}

			$("#fancy_overlay, #fancy_close").bind("click", $.fn.fancybox.close);

			$("#fancy_close").show();

			if (opts.itemArray[ opts.itemCurrent ].title !== undefined && opts.itemArray[ opts.itemCurrent ].title.length > 0) {
				$('#fancy_title div').html(opts.itemArray[ opts.itemCurrent ].title);
				$('#fancy_title').show();
			}
			
			if (opts.itemArray[ opts.itemCurrent ].modules !== undefined && opts.itemArray[ opts.itemCurrent ].modules.length > 0) {
				$('#fancy_modules').hide();
				$('#fancy_title').hide();
				var modules = opts.itemArray[ opts.itemCurrent ].modules;
				var modtxt = '';
				var pex = modules.search('exif::');
				if (pex != -1){
					var exifsrc = modules.substring(pex+6);
					var exifsrc = exifsrc.split(';;',1);
					modtxt += " <a href=\"" + exifsrc + "\" class=\"modclass\">EXIF</a>";
				}
				var pco = modules.search('comment::');
				if (pco != -1){
					if (modtxt != ''){modtxt += ' | ';}
					var commentsrc = modules.substring(pco+9);
					var commentsrc = commentsrc.split(';;',1);
					var commentcount = modules.substring(modules.search('comment_count::')+15);
					var commentcount = commentcount.split(';;',1);
					modtxt += " <a href=\"" + commentsrc + "\" class=\"iframe modclass\">Comments (" + commentcount + ")</a>";
				}
				if (modtxt != ''){
					$('#fancy_modules div').html(modtxt);
					$('#fancy_modules').show();
					$(document).ready(function() { $(".modclass").modbox(); });
				}
				$('#fancy_title').show();
			}

			if (opts.overlayShow && isIE) {
				$('embed, object, select', $('#fancy_content')).css('visibility', 'visible');
			}

			if ($.isFunction(opts.callbackOnShow)) {
				opts.callbackOnShow();
			}

			busy = false;
		};

		return this.unbind('click').click(_initialize);
	};

	$.fn.fancybox.scrollBox = function() {
		var pos = $.fn.fancybox.getViewport();

		$("#fancy_outer").css('left', (($("#fancy_outer").width()	+ 36) > pos[0] ? pos[2] : pos[2] + Math.round((pos[0] - $("#fancy_outer").width()	- 36)	/ 2)));
		$("#fancy_outer").css('top',  (($("#fancy_outer").height()	+ 50) > pos[1] ? pos[3] : pos[3] + Math.round((pos[1] - $("#fancy_outer").height()	- 50)	/ 2)));
	};

	$.fn.fancybox.getNumeric = function(el, prop) {
		return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
	};

	$.fn.fancybox.getPosition = function(el) {
		var pos = el.offset();

		pos.top	+= $.fn.fancybox.getNumeric(el, 'paddingTop');
		pos.top	+= $.fn.fancybox.getNumeric(el, 'borderTopWidth');

		pos.left += $.fn.fancybox.getNumeric(el, 'paddingLeft');
		pos.left += $.fn.fancybox.getNumeric(el, 'borderLeftWidth');

		return pos;
	};

	$.fn.fancybox.showIframe = function() {
		$(".fancy_loading").hide();
		$("#fancy_frame").show();
		var w = $.fn.fancybox.getViewport();
		var img = $("#fancy_frame").contents().find("#g-item-img");
		if (img.length){
			var width = img.width();
			var height = img.height();
			var ir = Math.min(Math.min(w[0]-36, width) / width, Math.min(w[1]-50, height) / height);
			var width = Math.round(ir * width);
			var height = Math.round(ir * height);
			$("#fancy_frame").contents().find("#g-item-img").width(width);
			$("#fancy_frame").contents().find("#g-item-img").height(height);
		}
	};

	$.fn.fancybox.getViewport = function() {
		return [$(window).width(), $(window).height(), $(document).scrollLeft(), $(document).scrollTop() ];
	};

	$.fn.fancybox.animateLoading = function() {
		if (!$("#fancy_loading").is(':visible')){
			clearInterval(loadingTimer);
			return;
		}

		$("#fancy_loading > div").css('top', (loadingFrame * -40) + 'px');

		loadingFrame = (loadingFrame + 1) % 12;
	};

	$.fn.fancybox.showLoading = function() {
		clearInterval(loadingTimer);

		var pos = $.fn.fancybox.getViewport();

		$("#fancy_loading").css({'left': ((pos[0] - 40) / 2 + pos[2]), 'top': ((pos[1] - 40) / 2 + pos[3])}).show();
		$("#fancy_loading").bind('click', $.fn.fancybox.close);

		loadingTimer = setInterval($.fn.fancybox.animateLoading, 66);
	};

	$.fn.fancybox.close = function() {
		busy = true;

		$(imagePreloader).unbind();

		$("#fancy_overlay, #fancy_close").unbind();

		if (opts.hideOnContentClick) {
			$("#fancy_wrap").unbind();
		}

		$("#fancy_close, .fancy_loading, #fancy_left, #fancy_right, #fancy_title, #fancy_modules").hide();
		$("#fancy_content").empty();

		if (opts.centerOnScroll) {
			$(window).unbind("resize scroll");
		}

		__cleanup = function() {
			$("#fancy_overlay, #fancy_outer").hide();

			if (opts.centerOnScroll) {
				$(window).unbind("resize scroll");
			}

			if (isIE) {
				$('embed, object, select').css('visibility', 'visible');
			}

			if ($.isFunction(opts.callbackOnClose)) {
				opts.callbackOnClose();
			}

			busy = false;
		};

		if ($("#fancy_outer").is(":visible") !== false) {
			if (opts.zoomSpeedOut > 0 && opts.itemArray[opts.itemCurrent].orig !== undefined) {
				var orig_item	= opts.itemArray[opts.itemCurrent].orig;
				var orig_pos	= $.fn.fancybox.getPosition(orig_item);

				var itemOpts = {
					'left':		(orig_pos.left - 18) + 'px',
					'top': 		(orig_pos.top  - 18) + 'px',
					'width':	$(orig_item).width(),
					'height':	$(orig_item).height()
				};

				if (opts.zoomOpacity) {
					itemOpts.opacity = 'hide';
				}

				$("#fancy_outer").stop(false, true).animate(itemOpts, opts.zoomSpeedOut, opts.easingOut, __cleanup);

			} else {
				$("#fancy_outer").stop(false, true).fadeOut("fast", __cleanup);
			}

		} else {
			__cleanup();
		}

		return false;
	};

	$.fn.fancybox.build = function() {
		var html = '';

		html += '<div id="fancy_overlay"></div>';

		html += '<div id="fancy_wrap">';

		html += '<div class="fancy_loading" id="fancy_loading"><div></div></div>';

		html += '<div id="fancy_outer">';

		html += '<div id="fancy_inner">';

		html += '<div id="fancy_close"></div>';
		
		html +=  '<div id="fancy_bg"><div class="fancy_bg fancy_bg_n"></div><div class="fancy_bg fancy_bg_ne"></div><div class="fancy_bg fancy_bg_e"></div><div class="fancy_bg fancy_bg_se"></div><div class="fancy_bg fancy_bg_s"></div><div class="fancy_bg fancy_bg_sw"></div><div class="fancy_bg fancy_bg_w"></div><div class="fancy_bg fancy_bg_nw"></div></div>';

		html +=  '<a href="javascript:;" id="fancy_left"><span class="fancy_ico" id="fancy_left_ico"></span></a><a href="javascript:;" id="fancy_right"><span class="fancy_ico" id="fancy_right_ico"></span></a>';

		html += '<div id="fancy_content"></div>';

		html +=  '<div id="fancy_title"></div>';
		html +=  '<div id="fancy_modules"></div>';

		html += '</div>';

		html += '</div>';

		html += '</div>';

		$(html).appendTo("body");

		$('<table cellspacing="0" cellpadding="0" border="0"><tr><td class="fancy_title" id="fancy_title_left"></td><td class="fancy_title" id="fancy_title_main"><div></div></td><td class="fancy_title" id="fancy_title_right"></td></tr></table>').appendTo('#fancy_title');
		$('<table cellspacing="0" cellpadding="0" border="0"><tr><td class="fancy_modules" id="fancy_modules_left"></td><td class="fancy_modules" id="fancy_modules_main"><div></div></td><td class="fancy_modules" id="fancy_modules_right"></td></tr></table>').appendTo('#fancy_modules');

		if (isIE) {
			$("#fancy_inner").prepend('<iframe class="fancy_bigIframe" scrolling="no" frameborder="0"></iframe>');
			$("#fancy_close, .fancy_bg, .fancy_title, .fancy_modules, .fancy_ico").fixPNG();
		}
	};

	$.fn.fancybox.defaults = {
		padding				:	10,
		imageScale			:	true,
		zoomOpacity			:	false,
		zoomSpeedIn			:	0,
		zoomSpeedOut		:	0,
		zoomSpeedChange		:	300,
		easingIn			:	'swing',
		easingOut			:	'swing',
		easingChange		:	'swing',
		frameWidth			:	425,
		frameHeight			:	355,
		overlayShow			:	true,
		overlayOpacity		:	0.8,
		hideOnContentClick	:	false,
		centerOnScroll		:	true,
		itemArray			:	[],
		callbackOnStart		:	null,
		callbackOnShow		:	null,
		callbackOnClose		:	null
	};

	$(document).ready(function() {
		$.fn.fancybox.build();
	});
	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
// *************************************************************************************************************************************	
	
	var modelem, modopts, modbusy = false, imagePreloader = new Image, loadingTimer, loadingFrame = 1, imageRegExp = /\.(jpg|gif|png|bmp|jpeg)(.*)?$/i;

	$.fn.modbox = function(settings) {
		settings = $.extend({}, $.fn.modbox.defaults, settings);

		var matchedGroup = this;

		function _initialize() {
			modelem = this;
			modopts = settings;

			_start();

			return false;
		};

		function _start() {
			if (modbusy) return;

			if ($.isFunction(modopts.callbackOnStart)) {
				modopts.callbackOnStart();
			}

			modopts.itemArray		= [];
			modopts.itemCurrent	= 0;

			if (settings.itemArray.length > 0) {
				modopts.itemArray = settings.itemArray;

			} else {
				var item = {};

				if (!modelem.rel || modelem.rel == '') {
					var item = {href: modelem.href, title: modelem.title};

					if ($(modelem).children("img:first").length) {
						item.orig = $(modelem).children("img:first");
					}

					modopts.itemArray.push( item );

				} else {
					
					var subGroup = $(matchedGroup).filter("a[rel=" + modelem.rel + "]");

					var item = {};

					for (var i = 0; i < subGroup.length; i++) {
						item = {href: subGroup[i].href, title: subGroup[i].title};

						if ($(subGroup[i]).children("img:first").length) {
							item.orig = $(subGroup[i]).children("img:first");
						}

						modopts.itemArray.push( item );
					}

					while ( modopts.itemArray[ modopts.itemCurrent ].href != modelem.href ) {
						modopts.itemCurrent++;
					}
				}
			}

			if (modopts.overlayShow) {
				if (isIE) {
					$('embed, object, select').css('visibility', 'hidden');
				}

				$("#mod_overlay").css('opacity', modopts.overlayOpacity).show();
			}

			_change_item();
		};

		function _change_item() {
			$("#mod_right, #mod_left, #mod_close, #mod_title").hide();

			var href = modopts.itemArray[ modopts.itemCurrent ].href;

			if (href.match(/#/)) {
				var target = window.location.href.split('#')[0]; target = href.replace(target, ''); target = target.substr(target.indexOf('#'));

				_set_content('<div id="mod_div">' + $(target).html() + '</div>', modopts.frameWidth, modopts.frameHeight);

			} else if (href.match(imageRegExp)) {
				imagePreloader = new Image; imagePreloader.src = href;

				if (imagePreloader.complete) {
					_proceed_image();

				} else {
					$.fn.modbox.showLoading();

					$(imagePreloader).unbind().bind('load', function() {
						$(".mod_loading").hide();

						_proceed_image();
					});
				}

			 } else if (href.match("iframe") || modelem.className.indexOf("iframe") >= 0) {
				_set_content('<iframe id="mod_frame" onload="$.fn.modbox.showIframe()" name="mod_iframe' + Math.round(Math.random()*1000) + '" frameborder="0" hspace="0" src="' + href + '"></iframe>', modopts.frameWidth, modopts.frameHeight);

			} else {
				$.get(href, function(data) {
					_set_content( '<div id="mod_ajax">' + data + '</div>', modopts.frameWidth, modopts.frameHeight );
				});
			}
		};

		function _proceed_image() {
			if (modopts.imageScale) {
				var w = $.fn.modbox.getViewport();

				var r = Math.min(Math.min(w[0] - 36, imagePreloader.width) / imagePreloader.width, Math.min(w[1] - 60, imagePreloader.height) / imagePreloader.height);

				var width = Math.round(r * imagePreloader.width);
				var height = Math.round(r * imagePreloader.height);

			} else {
				var width = imagePreloader.width;
				var height = imagePreloader.height;
			}

			_set_content('<img alt="" id="mod_img" src="' + imagePreloader.src + '" />', width, height);
		};

		function _preload_neighbor_images() {
			if ((modopts.itemArray.length -1) > modopts.itemCurrent) {
				var href = modopts.itemArray[modopts.itemCurrent + 1].href;

				if (href.match(imageRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}

			if (modopts.itemCurrent > 0) {
				var href = modopts.itemArray[modopts.itemCurrent -1].href;

				if (href.match(imageRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}
		};

		function _set_content(value, width, height) {
			modbusy = true;

			var pad = modopts.padding;

			if (isIE) {
				$("#mod_content")[0].style.removeExpression("height");
				$("#mod_content")[0].style.removeExpression("width");
			}

			if (pad > 0) {
				width	+= pad * 2;
				height	+= pad * 2;

				$("#mod_content").css({
					'top'		: pad + 'px',
					'right'		: pad + 'px',
					'bottom'	: pad + 'px',
					'left'		: pad + 'px',
					'width'		: 'auto',
					'height'	: 'auto'
				});

				if (isIE) {
					$("#mod_content")[0].style.setExpression('height',	'(this.parentNode.clientHeight - 20)');
					$("#mod_content")[0].style.setExpression('width',		'(this.parentNode.clientWidth - 20)');
				}

			} else {
				$("#mod_content").css({
					'top'		: 0,
					'right'		: 0,
					'bottom'	: 0,
					'left'		: 0,
					'width'		: '100%',
					'height'	: '100%'
				});
			}

			if ($("#mod_outer").is(":visible") && width == $("#mod_outer").width() && height == $("#mod_outer").height()) {
				$("#mod_content").fadeOut("fast", function() {
					$("#mod_content").empty().append($(value)).fadeIn("normal", function() {
						_finish();
					});
				});

				return;
			}

			var w = $.fn.modbox.getViewport();

			var itemLeft	= (width + 36)	> w[0] ? w[2] : (w[2] + Math.round((w[0] - width - 36) / 2));
			var itemTop		= (height + 50)	> w[1] ? w[3] : (w[3] + Math.round((w[1] - height - 50) / 2));

			var itemOpts = {
				'left':		itemLeft,
				'top':		itemTop,
				'width':	width + 'px',
				'height':	height + 'px'
			};

			if ($("#mod_outer").is(":visible")) {
				$("#mod_content").fadeOut("normal", function() {
					$("#mod_content").empty();
					$("#mod_outer").animate(itemOpts, modopts.zoomSpeedChange, modopts.easingChange, function() {
						$("#mod_content").append($(value)).fadeIn("normal", function() {
							_finish();
						});
					});
				});

			} else {

				if (modopts.zoomSpeedIn > 0 && modopts.itemArray[modopts.itemCurrent].orig !== undefined) {
					$("#mod_content").empty().append($(value));

					var orig_item	= modopts.itemArray[modopts.itemCurrent].orig;
					var orig_pos	= $.fn.modbox.getPosition(orig_item);

					$("#mod_outer").css({
						'left':		(orig_pos.left - 18) + 'px',
						'top':		(orig_pos.top  - 18) + 'px',
						'width':	$(orig_item).width(),
						'height':	$(orig_item).height()
					});

					if (modopts.zoomOpacity) {
						itemOpts.opacity = 'show';
					}

					$("#mod_outer").animate(itemOpts, modopts.zoomSpeedIn, modopts.easingIn, function() {
						_finish();
					});

				} else {

					$("#mod_content").hide().empty().append($(value)).show();
					$("#mod_outer").css(itemOpts).fadeIn("normal", function() {
						_finish();
					});
				}
			}
		};

		function _set_navigation() {
			if (modopts.itemCurrent != 0) {
				$("#mod_left, #mod_left_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					modopts.itemCurrent--;
					_change_item();

					return false;
				});

				$("#mod_left").show();
			}

			if (modopts.itemCurrent != ( modopts.itemArray.length -1)) {
				$("#mod_right, #mod_right_ico").unbind().bind("click", function(e) {
					e.stopPropagation();

					modopts.itemCurrent++;
					_change_item();

					return false;
				});

				$("#mod_right").show();
			}
		};

		function _finish() {
			_set_navigation();

			_preload_neighbor_images();

			$(document).keydown(function(e) {
				if (e.keyCode == 27) {
					$.fn.modbox.close();
					$(document).unbind("keydown");

				} else if(e.keyCode == 37 && modopts.itemCurrent != 0) {
					modopts.itemCurrent--;
					_change_item();
					$(document).unbind("keydown");

				} else if(e.keyCode == 39 && modopts.itemCurrent != (modopts.itemArray.length - 1)) {
 					modopts.itemCurrent++;
					_change_item();
					$(document).unbind("keydown");
				}
			});

			if (modopts.centerOnScroll) {
				$(window).bind("resize scroll", $.fn.modbox.scrollBox);
			} else {
				$("div#mod_outer").css("position", "absolute");
			}

			if (modopts.hideOnContentClick) {
				$("#mod_wrap").click($.fn.modbox.close);
			}

			$("#mod_overlay, #mod_close").bind("click", $.fn.modbox.close);

			$("#mod_close").show();

			if (modopts.itemArray[ modopts.itemCurrent ].title !== undefined && modopts.itemArray[ modopts.itemCurrent ].title.length > 0) {
				$('#mod_title div').html(modopts.itemArray[ modopts.itemCurrent ].title);
				$('#mod_title').show();
			}
			
			if (modopts.overlayShow && isIE) {
				$('embed, object, select', $('#mod_content')).css('visibility', 'visible');
			}

			if ($.isFunction(modopts.callbackOnShow)) {
				modopts.callbackOnShow();
			}

			modbusy = false;
		};

		return this.unbind('click').click(_initialize);
	};

	$.fn.modbox.scrollBox = function() {
		var pos = $.fn.modbox.getViewport();

		$("#mod_outer").css('left', (($("#mod_outer").width()	+ 36) > pos[0] ? pos[2] : pos[2] + Math.round((pos[0] - $("#mod_outer").width()	- 36)	/ 2)));
		$("#mod_outer").css('top',  (($("#mod_outer").height()	+ 50) > pos[1] ? pos[3] : pos[3] + Math.round((pos[1] - $("#mod_outer").height()	- 50)	/ 2)));
	};

	$.fn.modbox.getNumeric = function(el, prop) {
		return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
	};

	$.fn.modbox.getPosition = function(el) {
		var pos = el.offset();

		pos.top	+= $.fn.modbox.getNumeric(el, 'paddingTop');
		pos.top	+= $.fn.modbox.getNumeric(el, 'borderTopWidth');

		pos.left += $.fn.modbox.getNumeric(el, 'paddingLeft');
		pos.left += $.fn.modbox.getNumeric(el, 'borderLeftWidth');

		return pos;
	};

	$.fn.modbox.showIframe = function() {
		$(".mod_loading").hide();
		$("#mod_frame").show();
	};

	$.fn.modbox.getViewport = function() {
		return [$(window).width(), $(window).height(), $(document).scrollLeft(), $(document).scrollTop() ];
	};

	$.fn.modbox.animateLoading = function() {
		if (!$("#mod_loading").is(':visible')){
			clearInterval(loadingTimer);
			return;
		}

		$("#mod_loading > div").css('top', (loadingFrame * -40) + 'px');

		loadingFrame = (loadingFrame + 1) % 12;
	};

	$.fn.modbox.showLoading = function() {
		clearInterval(loadingTimer);

		var pos = $.fn.modbox.getViewport();

		$("#mod_loading").css({'left': ((pos[0] - 40) / 2 + pos[2]), 'top': ((pos[1] - 40) / 2 + pos[3])}).show();
		$("#mod_loading").bind('click', $.fn.modbox.close);

		loadingTimer = setInterval($.fn.modbox.animateLoading, 66);
	};

	$.fn.modbox.close = function() {
		modbusy = true;

		$(imagePreloader).unbind();

		$("#mod_overlay, #mod_close").unbind();

		if (modopts.hideOnContentClick) {
			$("#mod_wrap").unbind();
		}

		$("#mod_close, .mod_loading, #mod_left, #mod_right, #mod_title").hide();

		if (modopts.centerOnScroll) {
			$(window).unbind("resize scroll");
		}

		__cleanup = function() {
			$("#mod_overlay, #mod_outer").hide();

			if (modopts.centerOnScroll) {
				$(window).unbind("resize scroll");
			}

			if (isIE) {
				$('embed, object, select').css('visibility', 'visible');
			}

			if ($.isFunction(modopts.callbackOnClose)) {
				modopts.callbackOnClose();
			}

			modbusy = false;
		};

		if ($("#mod_outer").is(":visible") !== false) {
			if (modopts.zoomSpeedOut > 0 && modopts.itemArray[modopts.itemCurrent].orig !== undefined) {
				var orig_item	= modopts.itemArray[modopts.itemCurrent].orig;
				var orig_pos	= $.fn.modbox.getPosition(orig_item);

				var itemOpts = {
					'left':		(orig_pos.left - 18) + 'px',
					'top': 		(orig_pos.top  - 18) + 'px',
					'width':	$(orig_item).width(),
					'height':	$(orig_item).height()
				};

				if (modopts.zoomOpacity) {
					itemOpts.opacity = 'hide';
				}

				$("#mod_outer").stop(false, true).animate(itemOpts, modopts.zoomSpeedOut, modopts.easingOut, __cleanup);

			} else {
				$("#mod_outer").stop(false, true).fadeOut("fast", __cleanup);
			}

		} else {
			__cleanup();
		}

		return false;
	};

	$.fn.modbox.build = function() {
		var html = '';

		html += '<div id="mod_overlay"></div>';

		html += '<div id="mod_wrap">';

		html += '<div class="mod_loading" id="mod_loading"><div></div></div>';

		html += '<div id="mod_outer">';

		html += '<div id="mod_inner">';

		html += '<div id="mod_close"></div>';
		
		html +=  '<div id="mod_bg"><div class="mod_bg mod_bg_n"></div><div class="mod_bg mod_bg_ne"></div><div class="mod_bg mod_bg_e"></div><div class="mod_bg mod_bg_se"></div><div class="mod_bg mod_bg_s"></div><div class="mod_bg mod_bg_sw"></div><div class="mod_bg mod_bg_w"></div><div class="mod_bg mod_bg_nw"></div></div>';

		html +=  '<a href="javascript:;" id="mod_left"><span class="mod_ico" id="mod_left_ico"></span></a><a href="javascript:;" id="mod_right"><span class="mod_ico" id="mod_right_ico"></span></a>';

		html += '<div id="mod_content"></div>';

		html +=  '<div id="mod_title"></div>';

		html += '</div>';

		html += '</div>';

		html += '</div>';

		$(html).appendTo("body");

		$('<table cellspacing="0" cellpadding="0" border="0"><tr><td class="mod_title" id="mod_title_left"></td><td class="mod_title" id="mod_title_main"><div></div></td><td class="mod_title" id="mod_title_right"></td></tr></table>').appendTo('#mod_title');

		if (isIE) {
			$("#mod_inner").prepend('<iframe class="mod_bigIframe" scrolling="no" frameborder="0"></iframe>');
			$("#mod_close, .mod_bg, .mod_title, .mod_ico").fixPNG();
		}
	};

	$.fn.modbox.defaults = {
		padding				:	0,
		imageScale			:	true,
		zoomOpacity			:	false,
		zoomSpeedIn			:	0,
		zoomSpeedOut		:	0,
		zoomSpeedChange		:	300,
		easingIn			:	'swing',
		easingOut			:	'swing',
		easingChange		:	'swing',
		frameWidth			:	400,
		frameHeight			:	400,
		overlayShow			:	true,
		overlayOpacity		:	0.3,
		hideOnContentClick	:	false,
		centerOnScroll		:	true,
		itemArray			:	[],
		callbackOnStart		:	null,
		callbackOnShow		:	null,
		callbackOnClose		:	null
	};

	$(document).ready(function() {
		$.fn.modbox.build();
	});

})(jQuery);