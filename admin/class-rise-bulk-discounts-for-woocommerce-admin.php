<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/admin
 * @author     Chandrakant <chandrakant7389@gmail.com>
 */
if (! defined('ABSPATH')) {
	exit;
}

class Rise_Bulk_Discounts_For_Woocommerce_Admin
{
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */


	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
		add_action('wp_ajax_rise_bld_get_child_categories', [$this, 'get_child_categories']);
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/rise-bulk-discounts-for-woocommerce-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/rise-bulk-discounts-for-woocommerce-admin.js', array('jquery'), $this->version, false);
	}

	/* -------------------------------------------------
	 * Admin Menu
	 * ------------------------------------------------- */
	public function add_plugin_admin_menu()
	{

		add_menu_page(
			__('Rise Bulk Discounts', 'rise-bulk-discounts-for-woocommerce'),
			__('Rise Bulk Discounts', 'rise-bulk-discounts-for-woocommerce'),
			'manage_options',
			'rise-bld-rules',
			[$this, 'rules_page'],
			'dashicons-tag',
			56
		);
	}

	/* -------------------------------------------------
	 * Admin Assets
	 * ------------------------------------------------- */
	public function enqueue_assets($hook)
	{

		if ($hook !== 'toplevel_page_rise-bld-rules') {
			return;
		}

		wp_enqueue_script(
			'rise-bld-admin-js',
			plugin_dir_url(__FILE__) . 'js/rise-bulk-discounts-for-woocommerce-admin.js',
			['jquery'],
			$this->version,
			true
		);

		wp_localize_script(
			'rise-bld-admin-js',
			'rise_bld_ajax',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('rise_bld_ajax_nonce'),
			]
		);
	}

	/* -------------------------------------------------
	 * AJAX: Child categories
	 * ------------------------------------------------- */
	public function get_child_categories()
	{
		check_ajax_referer('rise_bld_ajax_nonce', 'nonce');

		if (! current_user_can('manage_woocommerce')) {
			wp_send_json_error('Unauthorized');
		}
		if (! isset($_POST['parent'])) {
			wp_die();
		}

		$parent = intval($_POST['parent'] ?? 0);

		$children = get_terms([
			'taxonomy'   => 'product_cat',
			'parent'     => $parent,
			'hide_empty' => false,
		]);

		foreach ($children as $child) {
			echo '<option value="' . esc_attr($child->term_id) . '">' . esc_html($child->name) . '</option>';
		}

		wp_die();
	}

	/* -------------------------------------------------
	 * Helpers (same logic)
	 * ------------------------------------------------- */
	private function get_rules()
	{

		$raw = get_option(RISE_BLD_OPTION_KEY, []);

		if (is_serialized($raw)) {
			$raw = maybe_unserialize($raw);
		}

		return is_array($raw) ? $raw : [];
	}

	private function save_rules($rules)
	{
		update_option(RISE_BLD_OPTION_KEY, maybe_serialize($rules));
	}

	/* -------------------------------------------------
	 * Admin Controller (CORE LOGIC)
	 * ------------------------------------------------- */
	public function rules_page()
	{

		if (! current_user_can('manage_options')) {
			wp_die(
				esc_html__('Unauthorized', 'rise-bulk-discounts-for-woocommerce')
			);
		}

		$action = isset($_GET['action'])
			? sanitize_text_field(wp_unslash($_GET['action']))
			: 'list';
		$rules  = $this->get_rules();

		/* Notices */
		if (! empty($_GET['message'])) {
			$msg = isset($_GET['message'])
				? sanitize_text_field(wp_unslash($_GET['message']))
				: '';

			echo '<div class="notice notice-success is-dismissible"><p>';
			/* translators: %s is the name of the rule that was successfully updated */
			$message = sprintf(__('%s successfully.', 'rise-bulk-discounts-for-woocommerce'), ucfirst($msg));
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
			echo '</p></div>';
		}

		/* ---------------------------------
		 * GET actions
		 * --------------------------------- */
		if (in_array($action, ['delete', 'duplicate', 'toggle'], true) && ! empty($_GET['id'])) {

			check_admin_referer('rise_bld_action_nonce');

			$id = isset($_GET['id'])
				? sanitize_text_field(wp_unslash($_GET['id']))
				: '';

			foreach ($rules as $i => $rule) {

				if ((string) $rule['id'] !== (string) $id) {
					continue;
				}

				if ($action === 'delete') {
					unset($rules[$i]);
				}

				if ($action === 'duplicate') {
					$new               = $rule;
					$new['id']         = time() . wp_rand(100, 999);
					$new['title'] 	  .= ' ' . esc_html__('(Copy)', 'rise-bulk-discounts-for-woocommerce');
					$new['created_at'] = current_time('mysql');
					$new['updated_at'] = null;
					$rules[]           = $new;
				}

				if ($action === 'toggle') {
					$rules[$i]['status']     = empty($rule['status']) ? 1 : 0;
					$rules[$i]['updated_at'] = current_time('mysql');
				}

				break;
			}

			$this->save_rules(array_values($rules));

			wp_safe_redirect(admin_url('admin.php?page=rise-bld-rules&message=deleted'));
			exit;
		}

		/* ---------------------------------
		 * POST save
		 * --------------------------------- */
		if (isset($_SERVER['REQUEST_METHOD']) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['rise_bld_save_rule'])) {

			check_admin_referer('rise_bld_save_rule');

			if (! current_user_can('manage_woocommerce')) {
				wp_die(esc_html__('You are not allowed to perform this action.', 'rise-bulk-discounts-for-woocommerce'));
			}

			$id = isset($_POST['id'])
				? sanitize_text_field(wp_unslash($_POST['id']))
				: '';
			$now = current_time('mysql');

			$data = [
				'title'            => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
				'parent_cat'       => intval($_POST['parent_cat'] ?? 0),
				'min_qty'          => intval($_POST['min_qty'] ?? 0),
				'max_qty'          => intval($_POST['max_qty'] ?? 0),
				'discount_percent' => floatval($_POST['discount_percent'] ?? 0),
				'include_children' => array_map('intval', (array) ($_POST['include_children'] ?? [])),
				'exclude_children' => array_map('intval', (array) ($_POST['exclude_children'] ?? [])),
				'status'           => isset($_POST['status']) ? 1 : 0,
			];

			if ($id) {
				foreach ($rules as $i => $rule) {
					if ((string) $rule['id'] === (string) $id) {
						$rules[$i] = array_merge($rule, $data, ['updated_at' => $now]);
						break;
					}
				}
			} else {
				$rules[] = array_merge($data, [
					'id'         => time() . wp_rand(100, 999),
					'title' 	 => $data['title'] ?: esc_html__('Untitled rule', 'rise-bulk-discounts-for-woocommerce'),
					'created_at' => $now,
					'updated_at' => null,
				]);
			}

			$this->save_rules($rules);

			wp_safe_redirect(
				admin_url(
					'admin.php?page=rise-bld-rules&message=' . ($id ? 'updated' : 'saved')
				)
			);
			exit;
		}

		/* ---------------------------------
		 * Render UI
		 * --------------------------------- */
		if ($action === 'add' || $action === 'edit') {

			$editing = [];

			if ($action === 'edit' && ! empty($_GET['id'])) {
				foreach ($rules as $rule) {
					$edit_id = isset($_GET['id'])
						? sanitize_text_field(wp_unslash($_GET['id']))
						: '';

					if ((string) $rule['id'] === (string) $edit_id) {
						$editing = $rule;
						break;
					}
				}
			}

			require plugin_dir_path(__FILE__) . 'partials/rise-bulk-discounts-for-woocommerce-admin-display.php';
		} else {
			require plugin_dir_path(__FILE__) . 'partials/rise-bulk-discounts-for-woocommerce-admin-list.php';
		}
	}
}
