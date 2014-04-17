/* global jQuery */
(function($){

	$(document).ready(function(){

		$('#nav-menu-meta #taxonomy-video-category input[type=hidden][class=menu-item-url]').each(function(){
			var t = $(this);
			console.log(t.val() );
			t.val( t.val().replace( dc9_root_url, dc9_root_url + '/en' ) );
		});

	});

})(jQuery);