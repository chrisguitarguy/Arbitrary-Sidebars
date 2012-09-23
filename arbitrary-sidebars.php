<?php
/*
Plugin Name: Arbitrary Sidebars
Plugin URI: https://github.com/chrisguitarguy/Arbitrary-Sidebars
Description: Add sidebars via an admin page.
Version: 1.0
Text Domain: abitrary-sb
Domain Path: /lang
Author: Christopher Davis
Author URI: http://christopherdavis.me
License: GPL2

    Copyright 2012 Christopher Davis

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;


/**
 * The path to this plugin.
 *
 * @since   1.0
 */
define('CGG_AB_PATH', plugin_dir_path(__FILE__));


spl_autoload_register(__NAMESPACE__ . '\\loader');
/**
 * Simple autoloader. Checks of the class is in the current namespace (see
 * above), locates its file and includes it.
 *
 * @since   1.0
 * @param   string $cls The class name
 * @return  null
 */
function loader($cls)
{
    $cls = ltrim($cls, '\\');

    if(strpos($cls, __NAMESPACE__) !== 0)
        return; // $cls isn't in this namespace

    $cls = str_replace(__NAMESPACE__, '', $cls);

    $path = CGG_AB_PATH . 'inc' .
        str_replace('\\', DIRECTORY_SEPARATOR, $cls) . '.php';

    require_once($path);
}

Sidebars::init();
if(is_admin())
    Admin::init();
