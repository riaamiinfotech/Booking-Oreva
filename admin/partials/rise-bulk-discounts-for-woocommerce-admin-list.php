<?php

/**
 * Provide an admin area view for the plugin rules list.
 *
 * This file is used to display the list of
 * bulk discount rules in the WordPress admin panel.
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
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Discount Rules', 'rise-bulk-discounts-for-woocommerce'); ?></h1>

    <a href="<?php echo esc_url(admin_url('admin.php?page=rise-bld-rules&action=add')); ?>"
        class="page-title-action"><?php esc_html_e('Add New Rule', 'rise-bulk-discounts-for-woocommerce'); ?></a>

    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Title', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Parent Category', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Min', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Max', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Discount %', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Status', 'rise-bulk-discounts-for-woocommerce'); ?></th>
                <th style="width:260px;"><?php esc_html_e('Actions', 'rise-bulk-discounts-for-woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($rules)) : ?>
                <?php foreach ($rules as $rise_bld_rule) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($rise_bld_rule['title']); ?></strong><br>
                            <small>
                                <?php esc_html_e('Created:', 'rise-bulk-discounts-for-woocommerce'); ?> <?php echo esc_html($rise_bld_rule['created_at']); ?>
                                <?php if (! empty($rise_bld_rule['updated_at'])) : ?>
                                    — <?php esc_html_e('Updated:', 'rise-bulk-discounts-for-woocommerce'); ?> <?php echo esc_html($rise_bld_rule['updated_at']); ?>
                                <?php endif; ?>
                            </small>
                        </td>

                        <td><?php echo esc_html(get_cat_name($rise_bld_rule['parent_cat'])); ?></td>
                        <td><?php echo esc_html($rise_bld_rule['min_qty']); ?></td>
                        <td><?php echo $rise_bld_rule['max_qty'] ? esc_html($rise_bld_rule['max_qty']) : '-'; ?></td>
                        <td><?php echo esc_html($rise_bld_rule['discount_percent']); ?></td>

                        <td>
                            <?php if (! empty($rise_bld_rule['status'])) : ?>
                                <span style="color:green;font-weight:600;"><?php esc_html_e('Enabled', 'rise-bulk-discounts-for-woocommerce'); ?></span>
                            <?php else : ?>
                                <span style="color:#999;"><?php esc_html_e('Disabled', 'rise-bulk-discounts-for-woocommerce'); ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a class="button"
                                href="<?php echo esc_url(admin_url('admin.php?page=rise-bld-rules&action=edit&id=' . $rise_bld_rule['id'])); ?>">
                                <?php esc_html_e('Edit', 'rise-bulk-discounts-for-woocommerce'); ?>
                            </a>

                            <a class="button"
                                href="<?php echo esc_url(wp_nonce_url(
                                            admin_url('admin.php?page=rise-bld-rules&action=duplicate&id=' . $rise_bld_rule['id']),
                                            'rise_bld_action_nonce'
                                        )); ?>">
                                <?php esc_html_e('Duplicate', 'rise-bulk-discounts-for-woocommerce'); ?>
                            </a>

                            <a class="button"
                                href="<?php echo esc_url(wp_nonce_url(
                                            admin_url('admin.php?page=rise-bld-rules&action=toggle&id=' . $rise_bld_rule['id']),
                                            'rise_bld_action_nonce'
                                        )); ?>">
                                <?php echo esc_html(! empty($rise_bld_rule['status'])
                                    ? __('Disable', 'rise-bulk-discounts-for-woocommerce')
                                    : __('Enable', 'rise-bulk-discounts-for-woocommerce')); ?>
                            </a>

                            <a class="button button-danger"
                                onclick="return confirm('<?php echo esc_js(__('Delete this rule?', 'rise-bulk-discounts-for-woocommerce')); ?>')"
                                href="<?php echo esc_url(wp_nonce_url(
                                            admin_url('admin.php?page=rise-bld-rules&action=delete&id=' . $rise_bld_rule['id']),
                                            'rise_bld_action_nonce'
                                        )); ?>">
                                <?php esc_html_e('Delete', 'rise-bulk-discounts-for-woocommerce'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <?php esc_html_e('No rules found.', 'rise-bulk-discounts-for-woocommerce'); ?>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>