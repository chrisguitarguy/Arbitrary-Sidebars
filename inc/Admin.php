<?php
/**
 * Admin area functionality for Arbitrary Sidebars.  Creates the admin page
 * and handles saving things.
 *
 * @since           1.0
 * @author          Christopher Davis <chris [AT] classicalguitar.org>
 * @copyright       Christopher Davis 2012
 * @license         GPLv2
 * @package         ArbitrarySidebars
 */

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;

final class Admin extends Sidebars
{
    /**
     * The nonce name and for the admin page.
     *
     * @since   1.0
     */
    const NONCE = 'cgg_ab_nonce';

    /**
     * Menu slug.
     *
     * @since   1.0
     */
    const SLUG = 'arbitrary-sidebars';

    /**
     * Prefix for our display action.
     *
     * @since   1.0
     */
    const ACTION = 'arbitrary_sidebars_tab_';

    /**
     * The capability this plugin checks agains to display the admin page
     * and allow saving of sidebars
     *
     * @since   1.0
     */
    const CAP = 'edit_theme_options';

    /**
     * Container to hold the various tabs on the admin page.
     *
     * @since   1.0
     * @access  private
     * @var     array
     */
    private static $tabs = array();

    /**
     * Container for the error messages.
     *
     * @since   1.0
     * @access  private
     * @var     array
     */
    private static $msg = array();

    /**
     * Adds actions and such.
     *
     * @since   1.0
     * @access  public
     * @uses    add_action
     * @return  null
     */
    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'page'));
        add_action(static::ACTION . 'view', array(__CLASS__, 'view_list'));
        add_action(static::ACTION . 'add', array(__CLASS__, 'view_add'));
        add_action(static::ACTION .'edit', array(__CLASS__, 'view_edit'));

        static::$tabs = apply_filters('arbitrary_sidebars_tabs', array(
            'view'  => __('Sidebars', 'arbitrary-sb'),
            'add'   => __('Add', 'arbitrary-sb'),
        ));

        static::$msg = apply_filters('arbitrary_sidebars_errors', array(
            1   => __('Sidebars must have a name and id.', 'arbitrary-sb'),
            2   => __('Sidebar saved.', 'arbitrary-sb'),
            3   => __('An error occured. Please try again!', 'arbitrary-sb'),
            4   => __('Sidebar deleted', 'arbitrary-sb'),
            5   => __('An error occured while deleting the sidebar', 'arbitrar-sb'),
        ));
    }

    /**
     * Adds a page under themes.
     *
     * @since   1.0
     * @access  public
     * @uses    add_theme_page
     * @uses    add_action
     * @return  null
     */
    public static function page()
    {
        $p = add_theme_page(
            __('Arbitrary Sidebars', 'arbitrary-sb'),
            __('Sidebars', 'arbitrary-sb'),
            static::CAP,
            static::SLUG,
            array(__CLASS__, 'page_cb')
        );

        add_action("load-{$p}", array(__CLASS__, 'load'));
    }

    /**
     * Theme page callback function.  Outputs the admin page.
     *
     * @since   1.0
     * @access  public
     * @uses    screen_icon
     * @uses    do_action
     * @return  null
     */
    public static function page_cb()
    {
        ?>
        <div class="wrap">

            <?php screen_icon(); ?>

            <h2 class="nav-tab-wrapper" style="margin-bottom:1em;">
                <?php foreach(static::$tabs as $t => $label): ?>
                    <a href="<?php static::tab_url($t); ?>" 
                       class="nav-tab <?php if($_GET['tab'] == $t): ?>nav-tab-active<?php endif; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </h2>

            <?php static::display_errors(); ?>

            <?php do_action(static::ACTION . $_GET['tab']); ?>

        </div>
        <?php
    }

    /**
     * Hooked into load-{$the_plugin_page}.  Dispatches to the save method
     * if this is a POST request or make sure $_GET['tab'] is always set.
     *
     * @since   1.0
     * @access  public
     * @return  null
     */
    public static function load()
    {
        if('post' == strtolower($_SERVER['REQUEST_METHOD']))
            static::save($_POST);

        if(!isset($_GET['tab']))
            $_GET['tab'] = 'view';
        else if('edit' == $_GET['tab'])
            static::$tabs['edit'] = __('Edit', 'arbitrary-sb');
    }

    /********** Tab Callback Functions **********/

    public static function view_list()
    {
        $t = new ListTable();
        $t->prepare_items();

        $t->display();
    }

    public static function view_add()
    {
        echo '<h4>';
        esc_html_e('Add a Sidebar', 'arbitrary-sb');
        echo '</h4>';
        static::show_form();
    }

    public static function view_edit()
    {
        $sidebars = static::sidebars();

        if(isset($_GET['sidebar']) && isset($sidebars[$_GET['sidebar']]))
        {
            $sb = $sidebars[$_GET['sidebar']];
            $sb['old_id'] = $sb['id'];
            static::show_form($sb, 'edit');
        }
        else
        {
            echo '<h4>';
            esc_html_e('Invalid sidebar', 'arbitrary-sb');
            echo '</h4>';
        }
    }

    /********** Internals and Utilities **********/

    /**
     * Process the form data and save a new sidebar.
     *
     * @since   1.0
     * @access  private
     * @return  null
     */
    private static function save($data)
    {
        if(!isset($data['action']))
        {
            wp_die(
                __('Invalid Action', 'arbitrary-sb'),
                __('Invalid Action', 'arbitrary-sb'),
                array('response' => 400, 'back_link' => true)
            );
        }

        check_admin_referer(static::NONCE . $data['action'], static::NONCE);

        if(!current_user_can(static::CAP))
        {
            wp_die(
                __('Cheatin&#8217; uh?', 'arbitrary-sb'),
                __('WordPress Failure Notice', 'arbitrary-sb'),
                array('response' => 401, 'back_link' => true)
            );
        }

        $id = isset($data['id']) ? sanitize_title_with_dashes($data['id']) : false;
        $name = isset($data['name']) ? $data['name'] : false;

        if('delete' == $data['action'])
        {
            $res = static::delete_sidebar($id);

            if($res)
                static::redirect(4);
            else
                static::redirect(5);
        }
            

        if(!$id || !$name)
            static::redirect(1);

        if(isset($data['old_id']) && $id != $data['old_id'])
            $id = static::get_unique($id);

        // do I need to do more validation here for the old sidebar id?
        $res = static::save_sidebar(
            array(
                'id'    => $id,
                'name'  => $name,
            ),
            isset($data['old_id']) && $id != $data['old_id'] ? $data['old_id'] : false
        );

        if($res)
            static::redirect(2);
        else
            static::redirect(3);
    }

    /**
     * Get a url relative to this page.
     *
     * @since   1.0
     * @access  private
     * @param   array $params url parametere to add
     * @uses    add_query_arg
     * @return  string The full url
     */
    public static function get_url($params=array())
    {
        return add_query_arg(array_merge($params, array(
            'page' => static::SLUG
        )), admin_url('themes.php'));
    }

    /**
     * Get a tab url. Includes some simple logic to avoid putting the 'view'
     * tab in the url -- that's pretty much it's only purpose.
     *
     *
     * @since   1.0
     * @access  private
     * @param   string $tab The tab url to fetch
     * @return  string The full URL
     */
    private static function get_tab_url($tab='view')
    {
        $params = array();
        if('view' != $tab)
            $params['tab'] = $tab;

        echo static::get_url($params);
    }

    /**
     * Echo out a tab url.
     *
     * @since   1.0
     * @access  private
     * @param   string $tab The tab url to display
     * @return  null
     */
    private static function tab_url($tab='view')
    {
        echo static::get_tab_url($tab);
    }

    /**
     * Kill the current request and redirect to the default page with an
     * error message.
     *
     * @since   1.0
     * @access  private
     * @uses    wp_redirect
     * @param   int $msg The message id
     * @return  null
     */
    private static function redirect($msg)
    {
        wp_redirect(static::get_url(array('msg' => $msg)));
        exit;
    }

    /**
     * Show the form that gets used to add and update sidebars.
     *
     * @since   1.0
     * @access  private
     * @uses    admin_url
     * @return  null
     */
    private static function show_form($sidebar=array(), $action='add')
    {
        $sidebar = wp_parse_args($sidebar, array(
            'id'    => '',
            'name'  => '',
        ));

        $fields = array(
            'id'    => __('Sidebar ID', 'arbitrary-sb'),
            'name'  => __('Sidebar Name', 'arbitrary-sb'),
        );

        ?>
        <form method="post" action="<?php static::tab_url(); ?>">

            <?php wp_nonce_field(static::NONCE . $action, static::NONCE); ?>

            <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>" />

            <?php if(isset($sidebar['old_id'])): ?>
                <input type="hidden" name="old_id" value="<?php echo esc_attr($sidebar['old_id']); ?>" />
            <?php endif; ?>

            <table class="form-table" id="arbitrary-sidebars-table">

                <?php foreach($fields as $key => $label): ?>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                        </th>
                        <td>
                            <?php
                            printf(
                                '<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" />',
                                esc_attr($key),
                                esc_attr($sidebar[$key])
                            );
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php do_action('arbitrary_sidebar_fields', $action); ?>

            </table>

            <?php 
            submit_button(apply_filters(
                'arbitrary_sidebars_submit',
                __('Save', 'arbitrary-sb'),
                $action
            ));
            ?>

        </form>
        <?php
    }

    /**
     * Display an error message if it's there.
     *
     * @since   1.0
     * @access  private
     * @uses    do_action
     * @return  null
     */
    public static function display_errors()
    {
        if(isset($_GET['msg']) && isset(static::$msg[$_GET['msg']]))
        {
            echo '<div class="updated"><p>';
            echo esc_html(static::$msg[$_GET['msg']]);
            echo '</p></div>';
        }

        do_action('arbitrary_sidebar_errors');
    }
} // end Admin
