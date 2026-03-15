<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/includes
 */
if ( ! defined( 'ABSPATH' ) ) exit;
class Rise_Bulk_Discounts_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rise_Bulk_Discounts_For_Woocommerce_Loader
	 */
	protected $loader;

	/**
	 * Plugin unique name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $version;

	/**
	 * Core plugin constructor.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'RISE_BULK_DISCOUNTS_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = RISE_BULK_DISCOUNTS_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'rise-bulk-discounts-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-bulk-discounts-for-woocommerce-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rise-bulk-discounts-for-woocommerce-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rise-bulk-discounts-for-woocommerce-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rise-bulk-discounts-for-woocommerce-public.php';



		$this->loader = new Rise_Bulk_Discounts_For_Woocommerce_Loader();
	}

	/**
	 * Set plugin locale for translations.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rise_Bulk_Discounts_For_Woocommerce_i18n();

		$this->loader->add_action(
			'plugins_loaded',
			$plugin_i18n,
			'load_plugin_textdomain'
		);
	}

	/**
	 * Register admin hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rise_Bulk_Discounts_For_Woocommerce_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$this->loader->add_action(
			'admin_enqueue_scripts',
			$plugin_admin,
			'enqueue_styles'
		);

		$this->loader->add_action(
			'admin_enqueue_scripts',
			$plugin_admin,
			'enqueue_scripts'
		);

		// ⚠️ Menu hook will be added in NEXT STEP (not now)
	}

	/**
	 * Register public hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rise_Bulk_Discounts_For_Woocommerce_Public(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$this->loader->add_action(
			'wp_enqueue_scripts',
			$plugin_public,
			'enqueue_styles'
		);

		$this->loader->add_action(
			'wp_enqueue_scripts',
			$plugin_public,
			'enqueue_scripts'
		);
	}

	/**
	 * Run plugin.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
