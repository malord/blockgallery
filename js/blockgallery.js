
jQuery(document).ready(function($) {
	$(".blockgallery img").hover(
		function(event) {
			$(this).addClass('hover');
		},
		function(event) {
			$(this).removeClass('hover');
		}
	);
});

