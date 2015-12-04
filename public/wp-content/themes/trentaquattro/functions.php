<?php

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

if (is_null($symfonyLoader)) {
	add_action( 'admin_notices', function() {
		print '<div class="error"><p>Symfony Form Plugin not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	});
	return;
}

define("ROOT_DIR", __dir__ );

/*
 * AutoLoader for the application
 */
$symfonyLoader->add('App',__dir__ . '/app/src');
new App\Site();
