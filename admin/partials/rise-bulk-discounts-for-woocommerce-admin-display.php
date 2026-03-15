<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing
 * rule setting UI for the plugin.
 *
 * @link       https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

if (! defined('ABSPATH')) {
    exit;
}

$rise_bld_is_edit = ! empty($editing);
?>

<div class="wrap">
    <h1>
        <?php echo esc_html(
            $rise_bld_is_edit
                ? __('Edit Rule', 'rise-bulk-discounts-for-woocommerce')
                : __('Add New Rule', 'rise-bulk-discounts-for-woocommerce')
        ); ?>
    </h1>

    <form method="post">
        <?php wp_nonce_field('rise_bld_save_rule'); ?>

        <input type="hidden" name="id"
            value="<?php echo esc_attr($editing['id'] ?? ''); ?>">

        <table class="form-table">
            <tr>
                <th><?php esc_html_e('Title', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <input type="text" name="title" style="width:400px"
                        value="<?php echo esc_attr($editing['title'] ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Parent Category', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <?php
                    wp_dropdown_categories([
                        'taxonomy' => 'product_cat',
                        'name'     => 'parent_cat',
                        'selected' => intval($editing['parent_cat'] ?? 0),
                        'show_option_none' => __('Select Category', 'rise-bulk-discounts-for-woocommerce'),
                    ]);
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Include Child Categories', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <select id="rise_bld_include_children" name="include_children[]" multiple style="min-width:300px;height:120px;">
                        <?php
                        $rise_bld_parent = intval($editing['parent_cat'] ?? 0);

                        if ($rise_bld_parent) {
                            $rise_bld_children = get_terms(
                                [
                                    'taxonomy'   => 'product_cat',
                                    'parent'     => $rise_bld_parent,
                                    'hide_empty' => false,
                                ]
                            );

                            $rise_bld_selected = (array) ($editing['include_children'] ?? []);

                            foreach ($rise_bld_children as $rise_bld_child) {
                                echo '<option value="' . esc_attr($rise_bld_child->term_id) . '" ' .
                                    selected(in_array($rise_bld_child->term_id, $rise_bld_selected, true), true, false) .
                                    '>' . esc_html($rise_bld_child->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <p class="description"><?php esc_html_e('If none selected, all children are included.', 'rise-bulk-discounts-for-woocommerce'); ?></p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Exclude Child Categories', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <select id="rise_bld_exclude_children" name="exclude_children[]" multiple style="min-width:300px;height:120px;">
                        <?php
                        $rise_bld_parent = intval($editing['parent_cat'] ?? 0);
                        if ($rise_bld_parent) {
                            $rise_bld_children = get_terms(
                                [
                                    'taxonomy'   => 'product_cat',
                                    'parent'     => $rise_bld_parent,
                                    'hide_empty' => false,
                                ]
                            );

                            $rise_bld_selected_exclude = (array) ($editing['exclude_children'] ?? []);

                            foreach ($rise_bld_children as $rise_bld_child) {
                                echo '<option value="' . esc_attr($rise_bld_child->term_id) . '" ' .
                                    selected(in_array($rise_bld_child->term_id, $rise_bld_selected_exclude, true), true, false) .
                                    '>' . esc_html($rise_bld_child->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Min Quantity', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <input type="number" name="min_qty"
                        value="<?php echo esc_attr($editing['min_qty'] ?? 0); ?>">
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Max Quantity', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <input type="number" name="max_qty"
                        value="<?php echo esc_attr($editing['max_qty'] ?? 0); ?>">
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Discount (%)', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <input type="number" step="0.01" name="discount_percent"
                        value="<?php echo esc_attr($editing['discount_percent'] ?? 0); ?>">
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e('Active', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="status"
                            <?php checked($editing['status'] ?? 1, 1); ?>>
                        <?php esc_html_e('Enable rule', 'rise-bulk-discounts-for-woocommerce'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" name="rise_bld_save_rule"
                class="button button-primary">
                <?php esc_html_e('Save Rule', 'rise-bulk-discounts-for-woocommerce'); ?>
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=rise-bld-rules')); ?>"
                class="button">
                <?php esc_html_e('Cancel', 'rise-bulk-discounts-for-woocommerce'); ?>
            </a>
        </p>
    </form>
</div>