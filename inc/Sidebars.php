<?php
/**
 * Base class for this plugin. Also handles registering the added sidebars.
 *
 * @since           1.0
 * @author          Christopher Davis <chris [AT] classicalguitar.org>
 * @copyright       Christopher Davis 2012
 * @license         GPLv2
 * @package         ArbitrarySidebars
 */

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;

class Sidebars
{
    /**
     * Option where the plugin will store things.
     *
     * @since   1.0
     */
    const OPTION = 'cgg_arbitrary_sidebars';

    /**
     * Container for the default sidebar args (outside of name/id).
     *
     * @since   1.0
     * @var     array
     */
    private static $args = array();

    /**
     * Adds actions and such.
     *
     * @since   1.0
     * @access  public
     * @uses    add_action
     * @uses    apply_filters
     * @return  null
     */
    public static function init()
    {
        static::$args = apply_filters('arbitrary_sidebars_args', array(
            'before_widget'     => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'      => '</aside>',
            'before_title'      => '<h3 class="widgettitle>',
            'after_title'       => '</h3>',
        ));

        add_action('widgets_init', array(__CLASS__, 'register'), 100);
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
    public static function register()
    {
        foreach(static::sidebars() as $id => $args)
        {
            $args['id'] = static::get_unique($args['id']);

            register_sidebar(wp_parse_args(
                $args,
                apply_filters('arbitrary_sidebars_single_args', static::$args, $id)
            ));
        }
    }

    /**
     * Check if a sidebar exists.  Either from our plugin or via the theme.
     *
     * @since   1.0
     * @access  protected
     * @uses    $wp_registered_sidebars
     * @return  bool whether or not the sidebar exists.
     */
    protected static function sidebar_exists($id)
    {
        global $wp_registered_sidebars;

        $sidebars = static::sidebars();

        return isset($sidebars[$id]) || isset($wp_registered_sidebars[$id]);
    }

    /**
     * Get all sidebars currently registered by this plugin.
     * 
     * @since   1.0
     * @access  protected
     * @uses    get_option
     * @return  array The registered sidebars
     */
    public static function sidebars()
    {
        return get_option(static::OPTION, array());
    }

    /**
     * Save a sidebar defined by $args, which should be an associative array
     * with at least an id field
     *
     * @since   1.0
     * @access  protected
     * @uses    update_option
     * @return  bool Whether or not it worked.
     */
    protected static function save_sidebar($args, $old_id=false)
    {
        if(!isset($args['id']))
            return false;

        $sidebars = static::sidebars();

        if($old_id && isset($sidebars[$old_id]))
            unset($sidebars[$old_id]);

        $sidebars[$args['id']] = $args;

        update_option(static::OPTION, $sidebars);

        return true;
    }

    /**
     * Delete a sidebar
     *
     * @since   1.0
     * @access  protected
     * @uses    update_option
     * @return  bool Did it work?
     */
    protected static function delete_sidebar($id)
    {
        if(!$id)
            return false;

        $sidebars = static::sidebars();

        $res = false;

        if(isset($sidebars[$id]))
        {
            unset($sidebars[$id]);
            $res = true;
            update_option(static::OPTION, $sidebars);
        }

        return $res;
    }

    /**
     * Get a unique sidebar id.
     *
     * @since   1.0
     * @access  protected
     * @param   string $id The ID to make unique
     * @return  string
     */
    protected function get_unique($id)
    {
        $c = 1;
        while(static::sidebar_exists($id))
            $id .= $c++;

        return $id;
    }
} // end class sidebars
