<?php
/**
 * Arbitrary Sidebars
 *
 * @category    WordPress
 * @package     ArbitrarySidebars
 * @since       1.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;

/**
 * Takes care of registering the sidebars added via this plugin.
 *
 * @since   1.1
 */
class Sidebars extends SidebarBase
{
    /**
     * {@inheritdoc}
     */
    public function _setup()
    {
        add_action('widgets_init', array($this, 'register'));
    }

    /**
     * Register all the widgets that are added via this plugin.
     *
     * @since   1.0
     * @access  public
     * @uses    register_sidebar
     * @uses    apply_filters
     * @return  null
     */
    public function register()
    {
        foreach(static::sidebars() as $id => $args)
        {
            $args['id'] = static::get_unique($args['id']);

            register_sidebar(wp_parse_args($args,
                apply_filters('arbitrary_sidebars_single_args', static::get_default_args(), $id)
            ));
        }
    }
}
