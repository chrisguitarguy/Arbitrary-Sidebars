<?php
/**
 * Unnamespaced functions.
 *
 * @category    WordPress
 * @package     ArbitrarySidebars
 * @since       1.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

/**
 * A simple, PSR-0 compliant autoloader.
 *
 * @since   1.1
 * @param   string $cls The class name
 * @return  void
 */
function arbitrary_sidebars_loader($cls)
{
    static $ns = 'Chrisguitarguy\\ArbitrarySidebars';

    $cls = ltrim($cls, '\\');

    if(strpos($cls, $ns) !== 0)
        return; // $cls isn't in this namespace

    $cls = str_replace($ns, '', $cls);

    $path = CGG_AB_PATH . 'inc' .
        str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $cls) . '.php';

    if (file_exists($path)) {
        require_once $path;
        return true;
    }

    return false;
}
