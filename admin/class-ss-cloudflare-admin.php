<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://spotlightstudios.co.uk/
 * @since      1.0.0
 *
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/admin
 * @author     Spotlight <info@spotlightstudios.co.uk>
 */
class Ss_Cloudflare_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ss_Cloudflare_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ss_Cloudflare_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ss-cloudflare-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-uikitmincss', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/uikit.min.css' );
		// wp_enqueue_style( $this->plugin_name.'-fontsgoogle', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name.'-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ss_Cloudflare_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ss_Cloudflare_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'-jqueryjs', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js', array( 'jquery' ), $this->version, false);
		wp_enqueue_script( $this->plugin_name.'-jqueryui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array( 'jquery'), $this->version, false);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ss-cloudflare-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-clipboardjs', plugin_dir_url( __FILE__ ) . 'js/clipboard.min.js', array( 'jquery'), $this->version, false);

		wp_enqueue_script( $this->plugin_name.'bootstrapjs','https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', array('jquery'), $this->version, false);
		wp_localize_script( $this->plugin_name, 'ss_cloudflare_ajax_url',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));

		wp_enqueue_script( $this->plugin_name.'-uikitjs', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/uikit.min.js', array( 'jquery' ), $this->version, false );  
		wp_enqueue_script( $this->plugin_name.'-uikitminjs', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/uikit-icons.min.js', array( 'jquery' ), $this->version, false ); 
	}

}
