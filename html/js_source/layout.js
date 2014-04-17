/* global jQuery, Modernizr */
(function($){

	/*
	IE8 polyfill
	 */
	if (!Object.create) {
		Object.create = (function(){
			function F(){}

			return function(o){
				if (arguments.length !== 1) {
					throw new Error('Object.create implementation only accepts one parameter.');
				}
				F.prototype = o;
				return new F();
			};
		})();
	}

	/*
	Form placeholder polyfill
	 */
	if( typeof Modernizr !== "undefined") {
		if( Modernizr.input.placeholder !== true ) {
			$.getScript( window.theme_url + 'js/polyfills/placeholder.js' );
		}
	}

	/*
	Main menu handling
	 */
	var SubmenuHandling = {
		init: function(li) {
			this.li = li;
			this.ul = this.li.children('ul');
			this.main_span = this.li.children('span');
			this.current_state = 'closed';

			this.bindEvents();
		},
		bindEvents: function() {
			var self = this;
			this.main_span.on('click', function(e) {
				e.preventDefault();
				self.handleClick();
			});
		},
		handleClick: function() {
			if( this.current_state === 'closed' ) {
				//open in
				this.li.addClass('open');
				this.ul.slideDown(300, function(){
					$.event.trigger('menu_animation_complete');
				});
				this.current_state = 'open';
			} else {
				//close it
				this.li.removeClass('open');
				this.ul.slideUp(300, function(){
					$.event.trigger('menu_animation_complete');
				});
				this.current_state = 'closed';
			}
		}
	};

	$(document).ready(function() {
		$('.main_menu .has_submenu:not(.active)').each(function(){
			var instance = Object.create( SubmenuHandling );
			instance.init( $(this) );
		});
	});

	/*
	Check for longer menu than content (because of active ones)
	 */
	var CheckForMenuHeight = {
		init: function() {
			//bind events when to perform check

			var self = this;
			$(window).load(function(){
				self.performCheck();
			});

			$(document).on('menu_animation_complete', function(){
				self.performCheck();
			});

		},
		performCheck: function() {
			var last_active_menu = $('.main_menu .active:last');
			if( last_active_menu.length > 0 ) {
				//we have some active menu
				var position_to_parent = last_active_menu.position(),
					item_height = last_active_menu.outerHeight();
				$('.main_article').css('min-height', position_to_parent.top + item_height);
			}
		}
	};
	CheckForMenuHeight.init();

	/*
	Wordpress image to fancybox
	 */
	$(document).ready(function() {

		var i = 1;
		$('.main_article .gallery').each(function(){
			$('a', this).attr('data-fancybox-group', i);
		});

		var for_fancy = [];

		$('.main_article a:has(img)').each(function() {
			var $this = $(this),
				url_href = $this.attr('href'),
				url = url_href.substring(0, url_href.length - 4),
				$img = $this.find('img'),
				img_url = $img.attr('src');

			if (img_url.indexOf(url) !== -1) {
				for_fancy.push( this );
			}

		});

		/*
		Gallery from Picasa
		 */
		$('.pe2-album a').each(function(){
			for_fancy.push(this);
		});

		$(for_fancy).fancybox({
			'padding': 0,
			'margin': 40,
			'openEffect': 'elastic',
			'closeEffect': 'elastic'
		});

	});

})(jQuery);