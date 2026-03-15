<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/public
 * @author     Chandrakant <chandrakant7389@gmail.com>
 */
if (! defined('ABSPATH')) exit;
class Rise_Bulk_Discounts_For_Woocommerce_Public
{

    /**
     * Rules option key
     */
    protected $option_key = 'rise_bld_rules';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        /**
         * Simple products
         */
        add_filter('woocommerce_product_get_price', [$this, 'apply_bulk_discount'], 99, 2);
        add_filter('woocommerce_product_get_sale_price', [$this, 'apply_bulk_discount'], 99, 2);
        add_filter('woocommerce_product_get_regular_price', [$this, 'keep_regular_price'], 99, 2);

        /**
         * Variation products
         */
        add_filter('woocommerce_product_variation_get_price', [$this, 'apply_bulk_discount'], 99, 2);
        add_filter('woocommerce_product_variation_get_sale_price', [$this, 'apply_bulk_discount'], 99, 2);
        add_filter('woocommerce_product_variation_get_regular_price', [$this, 'keep_regular_price'], 99, 2);

        /**
         * Variable product price range on archive/shop
         */
        add_filter('woocommerce_variable_price_html', [$this, 'variable_product_archive_price_html'], 99, 2);

        /**
         * Mini-cart refresh
         */
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'refresh_cart']);

        /* SALE badge only if discounted */
        add_filter('woocommerce_product_is_on_sale', [$this, 'force_sale_badge_for_variable_discount'], 99, 2);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rise_Bulk_Discounts_For_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rise_Bulk_Discounts_For_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/rise-bulk-discounts-for-woocommerce-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rise_Bulk_Discounts_For_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rise_Bulk_Discounts_For_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/rise-bulk-discounts-for-woocommerce-public.js', array('jquery'), $this->version, false);
    }
    /**
     * Apply bulk discount to product price.
     *
     * Modifies the product price dynamically based on
     * quantity-based discount rules.
     *
     * @param float        $price   Original product price.
     * @param WC_Product   $product WooCommerce product object.
     *
     * @return float Modified product price.
     *
     * @since 1.0.0
     */
    public function keep_regular_price($price, $product)
    {
        return $price;
    }

    /**
     * Display discounted price range for variable products.
     *
     * Modifies the price HTML on shop and archive pages
     * to show discounted price ranges when bulk rules apply.
     *
     * @param string     $price_html Original price HTML.
     * @param WC_Product $product    WooCommerce product object.
     *
     * @return string Modified price HTML.
     *
     * @since 1.0.0
     */
    public function variable_product_archive_price_html($price_html, $product)
    {
        if (is_admin()) {
            return $price_html;
        }

        $rules = $this->get_rules();
        if (empty($rules)) {
            return $price_html;
        }

        $product_id   = $product->get_id();
        $product_cats = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
        if (empty($product_cats)) {
            return $price_html;
        }

        $variation_prices = $product->get_variation_prices(true);
        if (empty($variation_prices['regular_price'])) {
            return $price_html;
        }

        $discounted_prices = [];

        foreach ($rules as $rule) {
            if (empty($rule['status'])) {
                continue;
            }

            $parent  = intval($rule['parent_cat']);
            $include = ! empty($rule['include_children']) ? (array) $rule['include_children'] : [];
            $exclude = ! empty($rule['exclude_children']) ? (array) $rule['exclude_children'] : [];

            // skip excluded categories
            if (! empty($exclude) && array_intersect($exclude, $product_cats)) {
                continue;
            }

            // skip if not in parent or included child
            $child_cats = get_terms([
                'taxonomy'   => 'product_cat',
                'parent'     => $parent,
                'hide_empty' => false,
                'fields'     => 'ids',
            ]);

            $is_in_parent = in_array($parent, $product_cats, true);
            $is_child_match = ! empty($include)
                ? array_intersect($include, $product_cats)
                : (! empty($child_cats) && array_intersect($child_cats, $product_cats));

            if (! $is_in_parent && ! $is_child_match) {
                continue;
            }

            $percent = floatval($rule['discount_percent']);
            if ($percent <= 0) {
                continue;
            }

            // Loop through variations
            foreach ($variation_prices['regular_price'] as $vid => $regular_price) {
                $variation = wc_get_product($vid);
                if (!$variation) continue;

                // Calculate cart quantity for this variation's parent category
                $cart_qty = $this->get_cart_qty_for_rule($rule);

                // Skip if qty does not meet rule
                $min_qty = isset($rule['min_qty']) ? intval($rule['min_qty']) : 0;
                $max_qty = isset($rule['max_qty']) ? intval($rule['max_qty']) : 0;

                if ($cart_qty < $min_qty) continue;
                if ($max_qty > 0 && $cart_qty > $max_qty) continue;

                // Apply discount
                $discounted_prices[$vid] = $regular_price - ($regular_price * ($percent / 100));
            }
        }

        // If no discounted variations, return original HTML
        if (empty($discounted_prices)) {
            return $price_html;
        }

        $min_regular = min($variation_prices['regular_price']);
        $max_regular = max($variation_prices['regular_price']);
        $min_discounted = min($discounted_prices);
        $max_discounted = max($discounted_prices);

        return '<del>' . wc_price($min_regular) . ' – ' . wc_price($max_regular) . '</del>
                <ins>' . wc_price($min_discounted) . ' – ' . wc_price($max_discounted) . '</ins>';
    }

    /**
     * Apply bulk discount to product price.
     *
     * Modifies the product price dynamically based on
     * quantity-based discount rules.
     *
     * @param float        $price   Original product price.
     * @param WC_Product   $product WooCommerce product object.
     *
     * @return float Modified product price.
     *
     * @since 1.0.0
     */
    public function apply_bulk_discount($price, $product)
    {

        if ((is_admin() && ! defined('DOING_AJAX')) || empty($product)) {
            return $price;
        }

        $product_id = $product->is_type('variation')
            ? $product->get_parent_id()
            : $product->get_id();

        $rules = $this->get_rules();
        if (empty($rules)) {
            return $price;
        }

        $product_cats = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
        if (empty($product_cats)) {
            return $price;
        }

        foreach ($rules as $rule) {

            if (empty($rule['status'])) {
                continue;
            }

            $parent  = intval($rule['parent_cat']);
            $include = ! empty($rule['include_children']) ? (array) $rule['include_children'] : [];
            $exclude = ! empty($rule['exclude_children']) ? (array) $rule['exclude_children'] : [];

            if (! empty($exclude) && array_intersect($exclude, $product_cats)) {
                continue;
            }

            $child_cats = get_terms([
                'taxonomy'   => 'product_cat',
                'parent'     => $parent,
                'hide_empty' => false,
                'fields'     => 'ids',
            ]);

            $is_in_parent = in_array($parent, $product_cats, true);

            $is_child_match = ! empty($include)
                ? array_intersect($include, $product_cats)
                : (! empty($child_cats) && array_intersect($child_cats, $product_cats));

            if (! $is_in_parent && ! $is_child_match) {
                continue;
            }

            $cart_qty = $this->get_cart_qty_for_rule($rule);

            $min_qty = isset($rule['min_qty']) ? intval($rule['min_qty']) : 0;
            $max_qty = isset($rule['max_qty']) ? intval($rule['max_qty']) : 0;

            if ($cart_qty < $min_qty) {
                continue;
            }

            if ($max_qty > 0 && $cart_qty > $max_qty) {
                continue;
            }

            $regular = floatval($product->get_regular_price());
            if ($regular <= 0) {
                continue;
            }

            $percent     = floatval($rule['discount_percent']);
            $discounted  = $regular - ($regular * ($percent / 100));

            return wc_format_decimal($discounted, wc_get_price_decimals());
        }

        return $price;
    }

    /**
     * Retrieve bulk discount rules from database.
     *
     * Fetches stored rules from WordPress options table
     * and ensures a valid array is returned.
     *
     * @return array List of bulk discount rules.
     *
     * @since 1.0.0
     */

    protected function get_rules()
    {
        $rules = get_option($this->option_key, []);
        $rules = is_serialized($rules) ? maybe_unserialize($rules) : $rules;

        if (! is_array($rules)) {
            return [];
        }

        // Sort by min_qty DESC (higher first)
        usort($rules, function ($a, $b) {
            return (int) $b['min_qty'] <=> (int) $a['min_qty'];
        });

        return $rules;
    }


    /**
     * Calculate cart quantity for a specific rule.
     *
     * Counts total cart quantity of products that
     * match the rule's category conditions.
     *
     * @param array $rule Discount rule configuration.
     *
     * @return int Total matching cart quantity.
     *
     * @since 1.0.0
     */
    protected function get_cart_qty_for_rule($rule)
    {
        if (! WC()->cart) {
            return 0;
        }

        $qty     = 0;
        $parent  = (int) $rule['parent_cat'];
        $include = ! empty($rule['include_children']) ? (array) $rule['include_children'] : [];
        $exclude = ! empty($rule['exclude_children']) ? (array) $rule['exclude_children'] : [];

        foreach (WC()->cart->get_cart() as $cart_item) {

            $pid       = $cart_item['product_id'];
            $item_cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'ids']);

            if (empty($item_cats)) {
                continue;
            }

            // Excluded categories
            if (! empty($exclude) && array_intersect($exclude, $item_cats)) {
                continue;
            }

            // Rule matching (STRICT)
            $match = false;

            if (! empty($include)) {
                $match = (bool) array_intersect($include, $item_cats);
            } else {
                $match = in_array($parent, $item_cats, true);
            }

            if ($match) {
                $qty += (int) $cart_item['quantity'];
            }
        }

        return $qty;
    }


    /**
     * Refresh WooCommerce mini-cart fragments.
     *
     * Ensures mini-cart prices update correctly
     * after bulk discounts are applied.
     *
     * @param array $fragments Existing cart fragments.
     *
     * @return array Updated cart fragments.
     *
     * @since 1.0.0
     */
    public function refresh_cart($fragments)
    {

        if (function_exists('woocommerce_mini_cart')) {
            ob_start();
            woocommerce_mini_cart();
            $fragments['div.widget_shopping_cart_content'] = ob_get_clean();
        }

        return $fragments;
    }

    /**
     * Force SALE badge when bulk discount applies.
     *
     * Displays the SALE badge for variable products
     * only if at least one variation is discounted.
     *
     * @param bool       $on_sale Current sale status.
     * @param WC_Product $product WooCommerce product object.
     *
     * @return bool Whether product should show SALE badge.
     *
     * @since 1.0.0
     */
    public function force_sale_badge_for_variable_discount($on_sale, $product)
    {

        if (is_admin()) {
            return $on_sale;
        }

        if (! $product->is_type('variable')) {
            $regular = (float) $product->get_regular_price();
            $price   = (float) $product->get_price();
            return ($regular > 0 && $price < $regular);
        }

        foreach ($product->get_children() as $variation_id) {

            $variation = wc_get_product($variation_id);
            if (! $variation) continue;

            $regular = (float) $variation->get_regular_price();
            $price   = (float) $variation->get_price();

            if ($regular > 0 && $price < $regular) {
                return true;
            }
        }

        return false;
    }
}
