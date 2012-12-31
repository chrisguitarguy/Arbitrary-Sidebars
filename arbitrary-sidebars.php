<?php
/**
 * Plugin Name: Arbitrary Sidebars
 * Plugin URI: https://github.com/chrisguitarguy/Arbitrary-Sidebars
 * Description: Add widget areas via a nice admin interface.
 * Version: 1.1
 * Text Domain: arbitrary-sb
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: GPL-2.0+
 *
 * Copyright 2012 Christopher Davis <http://christopherdavis.me>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @package     ArbitrarySidebars
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;

/**
 * The path to this plugin.
 *
 * @since   1.0
 */
define('CGG_AB_PATH', plugin_dir_path(__FILE__));

require_once CGG_AB_PATH . 'inc/functions.php';

spl_autoload_register('arbitrary_sidebars_loader');

Sidebars::init();
if(is_admin())
    Admin::init();
