/* global jQuery */
(function($){

	var DC9VideoGallery = {
		init: function() {
			this.video_gallery = $('.video_gallery');
			this.open_el = false;

			this.bindEvents();
		},
		bindEvents: function() {
			//bind click on video link
			var self = this;
			this.video_gallery.on('click', '.video_link', function(e) {
				e.preventDefault();
				self.linkClicked($(this));
			});
		},
		linkClicked: function( element ) {
			if( element.data('open') !== true ) {
				this.openVideo( element );
			} else {
				this.closeVideo( element );
			}
		},
		openVideo: function( el ) {
			//close opened video
			if( this.open_el !== false ) {
				this.closeVideo( this.open_el );
			}

			var video_id = el.data('video-id'),
				markup_tempate = '<iframe id="player_{{video_id}}" width="670" height="376.875" src="//www.youtube.com/embed/{{video_id}}?feature=player_embedded&autoplay=1&fs=1&autohide=1&color=white&theme=light" frameborder="0" allowfullscreen></iframe>',
				markup = markup_tempate.replace(new RegExp('{{video_id}}', 'g'), video_id);

			//create video div
			var wrapper_div = $('<div class="video_embed" />');
			wrapper_div.hide();
			el.after(wrapper_div);

			//insert markup
			wrapper_div.append( markup );

			//animate opening
			wrapper_div.slideDown(300);

			//add class to anchor
			el.addClass('open');

			el.data('open', true);
			this.open_el = el;
		},
		closeVideo: function( el ) {
			el.siblings('.video_embed').slideUp(300, function() {
				//slide up complete
				$(this).remove();
				el.data('open', false);
			});

			//remove class from acnhor
			el.removeClass('open');
		}
	};

	$(document).ready(function(){
		DC9VideoGallery.init();
	});

})(jQuery);