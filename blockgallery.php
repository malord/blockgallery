<?php
/*
Plugin Name: Block Gallery
Plugin URI: http://www.malord.com/
Description: Replace the default gallery with a nice lightbox-powered version
Version: 1.0
Author: Mark Lord
Author URI: http://www.malord.com/
*/

defined('ABSPATH') or die("Hi!");

remove_shortcode('gallery');
add_shortcode('gallery', 'blockgallery_shortcode');

register_activation_hook(__FILE__, 'blockgallery_install');
register_deactivation_hook(__FILE__, 'blockgallery_uninstall');

add_image_size('blockgallery_block', 220, 220, true);
add_image_size('blockgallery_full', 1200, 1200, false);

$blockgallery_once = false;

add_action('wp_enqueue_script', 'blockgallery_enqueue_jquery');

function blockgallery_install() {
}

function blockgallery_uninstall() {
}

function blockgallery_enqueue_jquery() {
	wp_enqueue_script('jquery');
}

function blockgallery_shortcode($atts) {
	global $post;
 
	if ( ! empty( $atts['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $atts['orderby'] ) )
			$atts['orderby'] = 'post__in';
		$atts['include'] = $atts['ids'];
	}
 
	extract(shortcode_atts(array(
		'orderby' => 'menu_order ASC, ID ASC',
		'include' => '',
		'id' => $post->ID,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'blockgallery_block',
		'link' => 'file'
	), $atts));
 
	$args = array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'post_mime_type' => 'image',
		'orderby' => $orderby
	);
 
	if ( !empty($include) )
		$args['include'] = $include;
	else {
		$args['post_parent'] = $id;
		$args['numberposts'] = -1;
	}
 
	$images = get_posts($args);

	ob_start();

	global $blockgallery_once;
	if (! $blockgallery_once) {
		$blockgallery_once = true;
		?>
		<link rel="stylesheet" href="<?php echo(esc_attr(plugins_url('deps/lightbox/css/lightbox.css', __FILE__))); ?>"/>
		<link rel="stylesheet" href="<?php echo(esc_attr(plugins_url('css/blockgallery.css', __FILE__))); ?>"/>
		<script type="text/javascript" src="<?php echo(esc_attr(plugins_url('deps/lightbox/js/lightbox.min.js', __FILE__))); ?>"></script>
		<script type="text/javascript" src="<?php echo(esc_attr(plugins_url('js/blockgallery.js', __FILE__))); ?>"></script>
		<?php
	}

	echo('<div class="blockgallery">');

	$unique_name = 'blockgallery-';
	foreach ( $images as $image ) {     
		$unique_name .= '-' . $image->ID;
	}

	foreach ( $images as $image ) {     
		$caption = $image->post_excerpt;
 
		$description = $image->post_content;
		if($description == '') $description = $image->post_title;
 
		$image_alt = get_post_meta($image->ID,'_wp_attachment_image_alt', true);
 
		list($src, $width, $height) = wp_get_attachment_image_src($image->ID, 'blockgallery_block', false);
		list($full_src, $full_width, $full_height) = wp_get_attachment_image_src($image->ID, 'blockgallery_full', false);
		echo('<a href="' . esc_attr($full_src) . '" data-lightbox="' . esc_attr($unique_name) . '">');
		echo('<img src="' . esc_attr($src) . '" />');
		echo('</a>');
	}

	echo('</div>');

	return ob_get_clean();
}

?>
