/* global jQuery */
(function($) {

	if (!Object.create) {
		Object.create = (function(){
			function F(){}

			return function(o){
				if (arguments.length != 1) {
					throw new Error('Object.create implementation only accepts one parameter.');
				}
				F.prototype = o
				return new F()
			}
		})()
	}

	var SearchPlaceholderPolyfill = {
		init: function(elem) {
			this.elem = elem;
			this.placeholder_value = this.elem.attr('placeholder');

			this.initialProcess();
			this.bindEvents();
		},
		initialProcess: function() {
			this.elem.val( this.placeholder_value );
			this.elem.addClass('placeheld');
		},
		bindEvents: function() {
			var self = this;
			this.elem.on('focus', function() {
				if( self.elem.val() === self.placeholder_value ) {
					//clicked on placeholder
					self.elem.val('');
					self.elem.removeClass('placeheld');
				}
			});
			this.elem.on('blur', function() {
				if( self.elem.val() === '' ){
					//clicked out without text
					self.elem.val( self.placeholder_value );
					self.elem.addClass('placeheld');
				}
			});
		}
	};

	$(document).ready(function(){
		$('input[placeholder]').each(function(){
			var instance = Object.create( SearchPlaceholderPolyfill );
			instance.init( $(this) );
		});
	});
})(jQuery);