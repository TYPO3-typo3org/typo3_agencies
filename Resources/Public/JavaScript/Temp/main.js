(function($) {
	// Calculate fixed tabs width
	$.fn.calculateTabsWidth = function() {
		
	$(this).each(function() {
		
		var o = $(this);
		var list = $(o).find('li');
		var oWidth = o.outerWidth(true);
		
		o.parent().width(oWidth); // fix for FF4 :)
		
		var tabsNo = $(list).length;
		tabsNo = (tabsNo == 0) ? 1 : tabsNo;

		var tabsWidth = 0, addWidth = 0, mod = 0, counter = 0;
		
		$(list).each(function() {
			tabsWidth += $(this).outerWidth(true);
		});
		
		mod = (oWidth - tabsWidth) % tabsNo;
		addWidth = (oWidth - mod - tabsWidth) / tabsNo;
		
		$(list).each(function() {

			newWidth = (counter < mod) ? $(this).width() + addWidth + 1 : $(this).width() + addWidth;

			$(this).css({'width': newWidth});
			$(this).find('a').css({'width': newWidth-1}); // for IE7 fix

			counter++;
		});
	
	});

	}

	// Smart label plugin: Input and label text inside span
	$.fn.smartLabel = function() {	
		$(this).each(function(){
			// clearing values after reload
			if($(this).attr('value').length > 0 ) $(this).siblings('span').fadeOut('fast');

			// Show/hide label
			$(this).focus(function(){
				$(this).siblings('span').fadeOut('fast');
			}).blur(function(){
				if (!$(this).attr('value')) $(this).siblings('span').fadeIn('fast');
			});
		});
	}

})(jQuery);


(function($) {
	// Accordion
	$.fn.accordion = function() {
		
		$(this).find('> .a-body').each(function(){
			$(this).data('height', $(this).height());
		});
		
		$(this).find('> .a-h:not(.open)').addClass('closed')
			.next().hide().css('height',0);
		
		$(this).find('> .a-h').click(function(){
		  if($(this).hasClass('closed')){
			var domCurrent = $(this);
			var intCurrentHeight = domCurrent.next().data('height');
			var domOpened = $(this).siblings('.open');

			domOpened.addClass('closed').removeClass('open')
				.next().animate({'height': 0}, function() {$(this).hide()});
			domCurrent.removeClass('closed').addClass('open')
				.next().show().animate({'height': intCurrentHeight});
		  }
		});

	}
})(jQuery);

(function($) {
// Social box
$.fn.socialBox = function() {
	
	var o = $(this),
		$body = o.find('.b-social-body'),
		$opened = o.find('.b-social-opened'),
		$closed = o.find('.b-social-closed'),
		$toggle = o.find('.b-social-toggle'),
		o_height = $opened.height(),
		o_closed_height = $closed.height(),
		sOpenedClass = 'b-social-open',
		sClosedClass = 'b-social-close';
		
	$closed.css('position', 'absolute');
	
	if(o.hasClass(sOpenedClass)) {
		$closed.hide();
	} else {
		$body.height(o_closed_height);
		$opened.hide();
	}
	
	$toggle.click(function(){
		if(o.hasClass(sOpenedClass)){
			$closed.fadeIn();
			$opened.fadeOut();

			$body.animate({'height': o_closed_height});
			o.removeClass(sOpenedClass).addClass(sClosedClass);
		} else {
			$closed.fadeOut();
			$opened.fadeIn();

			$body.animate({'height': o_height});
			o.addClass(sOpenedClass).removeClass(sClosedClass);
		}
	});

}
})(jQuery);

(function($) {
// background animate plugin
$.fn.animateBackground = function(options) {
	var defaults = {
		speed: 'normal',
		background: 'yellow',
		backgroundItemCSSClass: 'bg'
	};
	options = $.extend(defaults, options);
  
	return this.each(function(i, o){
		var oBg = $(o).
			prepend('<i class="' + options.backgroundItemCSSClass + '"></i>').
			find('> .' + options.backgroundItemCSSClass);
			
			oBg.css({
				background: options.background
			}).hide().fadeOut('fast');
			
			$(o).hover(function() {
				oBg.stop().fadeTo(options.speed, 1);
			}, function() {
				oBg.stop().fadeTo(options.speed, 0);
			});
    })
}
})(jQuery);

$(document).ready(function(){
				
	$('body').removeClass('js-off');

	$.tools.tabs.addEffect("default", function(tabIndex, done) {	// Removed display none for inactive tabs
		this.getPanes().removeClass('show-tab').addClass('hide-tab').eq(tabIndex).removeClass('hide-tab').addClass('show-tab');
		done.call();
	});

	$(".tabs:not(.lite-tabs)").tabs("> .tab-panes > div", {tabs: 'li', current: 'act', initialIndex: 0}).calculateTabsWidth();


	// Home page Main Scroller/ Tabs
	if ($('#top-slider').length > 0) {
		$('#top-slider .slider-nav').tabs('#top-slider .slides > .slide', {tabs: 'li', current: 'active', effect: 'fade', fadeInSpeed: 1000, fadeOutSpeed: 1000, rotate: true}).slideshow({autoplay:true});
	}
	// END Home page Main Scroller/ Tabs

	if ($('.small-slider').length > 0) {
		$('.small-slider:not(.auto-scroll) .slider-content').scrollable({circular: true, next: '.slider-nav .next', prev: '.slider-nav .prev'});
		$('.small-slider.auto-scroll .slider-content').scrollable({circular: true, next: '.slider-nav .next', prev: '.slider-nav .prev'}).autoscroll({interval:4000});
		$('.slider-nav .next, .slider-nav .prev').click(function() { return false; })
	}
	
	if ($('.ticker-slider').length > 0) {
		//$(".ticker-slider .slider-content").scrollable({circular: true}).autoscroll({interval:3000});
		$('.ticker-slider .slider-nav').tabs('.slide', {tabs: 'li', current: 'active', effect: 'fade', fadeInSpeed: 1000, fadeOutSpeed: 1000, rotate: true}).slideshow({autoplay:true});
	}

	if ($('.b-twitter').length > 0) {
		$('.b-twitter .slider-nav').tabs('.slide', {tabs: 'li', current: 'active', effect: 'fade', fadeInSpeed: 1000, fadeOutSpeed: 1000, rotate: true}).slideshow({autoplay:true});
	}

	$('.accordion').accordion();

	//Cufon.replace('h1', { fontFamily: 'Share-Bold' });
	Cufon.replace('.b-numbers li, .b-numbers h4, .b-numbers p, .b-numbers h3, .b-communities-text, .b-social-closed, .b-find-out-link', { fontFamily: 'Share-Italic' });
	//Cufon.replace('h1, .d h2, .d h3, .d h4, #header .nav > li > a', { fontFamily: 'Share-Regular' });
	Cufon.replace('h1, .d h2, .d h3, .d h4, .b-filter-head h4', { fontFamily: 'Share-Regular' });
	
	// Social block on homepage
	$('.b-social').socialBox();
	
	// Main nav animation fix
	$('.nav-sub').wrapInner('<div class="animate"></div>');
	
		$('.nav-sub').each(function(){
			if($(this).find('.col:only-child').length) {
				$(this).css('width', '190px'); 

			} else {
				$(this).css('width', '388px'); 
			}
		});
	
	// Main navigation animation
	$(".nav li").hover(function() {	
		
		// $(this).parent().show();
		$(this).find('.nav-sub').show().find(".animate").stop().animate({
			marginTop: "0" 
		}, 1350, 'easeOutQuint');
	} , function() {
		$(this).find(".animate").stop().animate({
			marginTop: "-285px"  
		}, 1350, 'easeOutQuint' ,function() { $(this).parent().hide()});
	});
	
	if (typeof $.fn.MultiFile == 'function') {
		if ($('.multi-file').length > 0) {
			var textWidth = $('.form-file-main-text').outerWidth(true);
			var inputWidth = $('.multi-file').outerWidth(true);
			var inputLeft = (inputWidth - textWidth) * (-1);
			$('.multi-file').css({'left': inputLeft})
		}
		$('.multi-file').MultiFile({
			onFileRemove: function(element, value, master_element){
				$(element).closest('.b-form-file').removeClass('disabled');
			},
			afterFileSelect: function(element, value, master_element){
				var disabled = $(element).next('input').attr('disabled');
				if (typeof disabled !== 'undefined' && disabled !== false) {
					$(element).closest('.b-form-file').addClass('disabled');
				}
			}
		});
	}
	
	// Adding number to ol lists
	$('.d ol li').each(function(i) {
		$(this).prepend('<span class="num">' + (i + 1) + '</span>');
	});
	
	// Sidebar navigation background animation
	if(typeof $.fn.animateBackground == 'function') {
		$('.nav-aside a, .nav-aside span').animateBackground({
			background: '#f4f4f4'
		});
	}
	
	if (typeof $.fn.prettyCheckboxes == 'function') {
		$('.b-form input[type=checkbox], .specialist-filter input[type=checkbox], .b-form input[type=radio]').prettyCheckboxes();
	}
	
	// Smart search
	if($('.smart-search').length) smartSearch();
	
	// Smart label 
	if($('.s-input')) $('.s-input input[type=text]').smartLabel();
	
	$('.fancybox').fancybox({overlayColor: '#000', overlayOpacity: '0.3', autoScale: false, titlePosition: 'over', onComplete : applyCufon});
});

// $(document).ready(function() {
// 	$("# show-image ").fancybox({
// 		'titlePosition'	: 'inside',
// 		'onComplete' : applyCufon
// 	});
// });
function applyCufon(){
	Cufon.replace('#fancybox-title', {fontFamily: 'Share-Italic'});
}

function 		smartSearch() { 

				$('.smart-search').click(function(event){
					
					$(this).toggleClass('open');
					
					$('body').one('click',function() {
					    $('.smart-search').removeClass('open');
					});
					
					event.stopPropagation();
					
					
				});
				
				$('.smart-search ul a').click(function(){
					$(this).parent().siblings().removeClass('current');
					$(this).parent().addClass('current');
					$(this).parents('.smart-search').find('.selector .ico').replaceWith($(this).find('.ico').clone());					
					var inpt = $(this).parents('.quick-search').find('.s-input input[type=text]');
					
					if(inpt) inpt.siblings('span').text($(this).text()); 
					
					
				});

}
