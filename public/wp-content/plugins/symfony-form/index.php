<?php
/*
Plugin Name: Symfony Form
Description: Add Symfony Form on WordPress
Version: 1.0.0
Author: Frédéric Faltin
Author URI: http://ffaltin.com
License: GPL2

------------------------------------------------------------------------
Copyright Frédéric Faltin

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

$symfonyLoader = require __DIR__ . '/vendor/autoload.php';

define ("SYMFONY_PLUGIN_DIR", __DIR__);
define('DEFAULT_FORM_THEME', 'form_div_layout.html.twig');
define('VENDOR_DIR', realpath(__DIR__ . '/vendor'));
define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form');
define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/validator');
define('VENDOR_TWIG_BRIDGE_DIR', VENDOR_DIR . '/symfony/twig-bridge');
define('VIEWS_DIR', realpath(__DIR__ . '/views'));


/**
 * Dump given parameters and stop PHP execution.
 *
 * @param mixed  ...
 */
function s() {
    if (func_num_args() > 0) {
        call_user_func_array('var_dump', func_get_args());
    }

    exit(0);
}

/**
 * Printf given message and stop PHP execution.
 *
 * @param string $message
 * @param mixed  ...
 */
function sf($message) {
    if (func_num_args() > 0) {
        call_user_func_array('printf', func_get_args());
    }

    exit(0);
}
