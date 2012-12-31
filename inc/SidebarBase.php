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
 * Base class for this plugin.
 *
 * @since   1.1
 */
abstract class SidebarBase
{
    /**
     * Option where the plugin will store things.
     *
     * @since   1.0
     */
    const OPTION = 'cgg_arbitrary_sidebars';

    /**
     * Container for the class instances of this plugin.
     *
     * @since   1.0
     * @var     array
     */
    private static $reg = array();

    /**
     * Get an instance of the calling class.
     *
     * @since   1.1
     * @access  public
     * @return  object
     * @static
     */
    public static function instance()
    {
        $cls = get_called_class();
        if (!isset(self::$reg[$cls])) {
            self::$reg[$cls] = new $cls;
        }

        return self::$reg[$cls];

    }

    /**
     * Kick everything off.  Hook the calling classes `_setup` method into
     * plugins_loaded
     *
     * @since   1.1
     * @access  public
     * @uses    add_action
     * @return  void
     * @static
     */
    public static function init()
    {
        add_action('plugins_loaded', array(static::instance(), '_setup'));
    }

    /**
     * This is where subclasses will add all their actions.
     *
     * @since   1.1
     * @access  public
     * @return  void
     */
    abstract public function _setup();

    /**
     * Get the default args for a sidebar.  These are the HTML wrappers.
     *
     * @since   1.0
     * @access  protected
     * @uses    apply_filters
     * @return  array
     */
    protected static function get_default_args()
    {
        return apply_filters('arbitrary_sidebars_args', array(
            'before_widget'     => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'      => '</aside>',
            'before_title'      => '<h3 class="widgettitle>',
            'after_title'       => '</h3>',
        ));
    }

    /**
     * Check if a sidebar exists.  Either from our plugin or via the theme.
     *
     * @since   1.0
     * @access  protected
     * @uses    $wp_registered_sidebars
     * @return  bool whether or not the sidebar exists.
     * @static
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
     * @static
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
     * @static
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
     * @static
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
     * @static
     */
    protected static function get_unique($id)
    {
        $c = 1;
        while(static::sidebar_exists($id))
            $id .= $c++;

        return $id;
    }
} // end class sidebars
