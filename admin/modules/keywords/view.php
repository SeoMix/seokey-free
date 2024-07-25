<?php

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$type = ( ! empty( $_GET['tab']  ) ) ? sanitize_title( $_GET['tab'] ) : 'contents';
?>
<section class="seokey-keywords-tools">
	<?php
	switch( $type ) {
		case "contents":
			include_once plugin_dir_path( __FILE__ ) . 'view-contents.php';
			break;
		default:
			include_once plugin_dir_path( __FILE__ ) . 'view-keywords.php';
			break;
	}
	?>
</section>