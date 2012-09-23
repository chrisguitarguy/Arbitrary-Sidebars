<?php
/**
 * A list table to diplay our list of sidebars.
 *
 * @since           1.0
 * @author          Christopher Davis <chris [AT] classicalguitar.org>
 * @copyright       Christopher Davis 2012
 * @license         GPLv2
 * @package         ArbitrarySidebars
 */

namespace Chrisguitarguy\ArbitrarySidebars;

!defined('ABSPATH') && exit;

use \WP_List_Table;

class ListTable extends WP_List_Table
{
    public function __construct()
    {
        $s = get_current_screen();

        // doing this here because WP seems to have problems with it?
        $this->_column_headers = array(
            apply_filters("manage_{$s->id}_columns", $this->get_columns()),
            array(),
            apply_filters("manage_{$s->id}_sortable_coluns", $this->get_sortable_columns()),
        );

        parent::__construct(array(
            'plural'    => __('Sidebars', 'arbitrary-sb'),
            'singular'  => __('Sidebar', 'arbitrary-sb'),
            'screen'    => $s,
        ));
    }

    /**
     * Get the columns for this table.
     *
     * Theorectically WP_List_Table::__construct() adds this function to the
     * filter 'manage_{$screen}_columns'. But it doesn't seem to actually do
     * that.  so that's nice.
     *
     * @since   1.0
     */
    public function get_columns()
    {
        return array(
            'id'     => __('Sidebar ID', 'arbitrary-sb'),
            'name'   => __('Sidebar Name', 'arbitrary-sb'),
            'edit'   => __('Edit Sidebar', 'arbitrary-sb'),
            'delete' => __('Delete Sidebar', 'arbitrary-sb')
        );
    }

    /**
     * Set up the items for the list table.
     *
     * @since   1.0
     * @access  public
     */
    public function prepare_items()
    {
        $this->items = Sidebars::sidebars();
    }

    /**
     * The message that gets displayed with there are no sidebars.
     *
     * @since   1.0
     * @access  public
     */
    public function no_items()
    {
        echo '<p>';
        esc_html_e('No Sidebars Found', 'arbitrary-sb');
        echo '</p>';
    }

    /**
     * An empty table name function.  We're not goign to use pagination, etc.
     *
     * @since   1.0
     * @access  public
     * @return  null
     */
    public function display_tablenav($which) { /* Nothin' */ }

    /********** Column Callbacks **********/

    public function column_default($item, $col)
    {
        do_action("manage_{$this->screen->id}_custom_column", $item, $col);
    }

    public function column_id($item)
    {
        echo '<p>';
        echo esc_html(isset($item['id']) ? $item['id'] : '');
        echo '</p>';
    }

    public static function column_name($item)
    {
        echo '<p>';
        echo esc_html(isset($item['name']) ? $item['name'] : '');
        echo '</p>';
    }

    public static function column_edit($item)
    {
        echo '<p>';
        printf(
            '<a href="%s" class="button button-primary">%s</a>',
            Admin::get_url(array('tab' => 'edit', 'sidebar' => $item['id'])),
            esc_html__('Edit', 'arbitrary-sb')
        );
        echo '</p>';
    }

    public static function column_delete($item)
    {
        ?>
        <form method="post" action="<?php echo Admin::get_url(); ?>">
            <?php wp_nonce_field(Admin::NONCE . 'delete', Admin::NONCE); ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($item['id']); ?>" />
            <input type="hidden" name="action" value="delete" />
            <p><input type="submit" class="button button-secondary" 
                   value="<?php esc_attr_e('Delete', 'arbitrary-sb'); ?>" /></p>
        </form>
        <?php
    }

} // end ListTable
