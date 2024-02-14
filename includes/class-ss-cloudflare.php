<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://spotlightstudios.co.uk/
 * @since      1.0.0
 *
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/includes
 * @author     Spotlight <info@spotlightstudios.co.uk>
 */
class Ss_Cloudflare {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ss_Cloudflare_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SS_CLOUDFLARE_VERSION' ) ) {
			$this->version = SS_CLOUDFLARE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ss-cloudflare';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

		//Adding plugin menu
		add_action('admin_menu', array($this,'ss_cloudflare_add_menu_page'));

		//Hook functions to call Ajax for Domain check
		add_action('wp_ajax_domain_curl', array($this,'domain_curl'));
        add_action('wp_ajax_nopriv_domain_curl',array($this,'domain_curl') );

		//Hook functions to call Ajax for WAF Rules list
		add_action('wp_ajax_waf_rules_list', array($this,'waf_rules_list'));
        add_action('wp_ajax_nopriv_waf_rules_list',array($this,'waf_rules_list') );

		//Hook functions to call Ajax for Admin Login rules
		add_action('wp_ajax_adminLogin', array($this,'adminLogin'));
        add_action('wp_ajax_nopriv_adminLogin',array($this,'adminLogin') );

		//Hook functions to call Ajax for xmlrp rules
		add_action('wp_ajax_xmlrpcCheck', array($this,'xmlrpcCheck'));
        add_action('wp_ajax_nopriv_xmlrpcCheck',array($this,'xmlrpcCheck') );

		//Hook functions to call Ajax for Admin Security rules
		add_action('wp_ajax_adminSecurity', array($this,'adminSecurity'));
        add_action('wp_ajax_nopriv_adminSecurity',array($this,'adminSecurity') );

		//Hook functions to call Ajax for Failover rules
		add_action('wp_ajax_failover', array($this,'failover'));
        add_action('wp_ajax_nopriv_failover',array($this,'failover') );

		//Hook functions to call Ajax for Account Token rules
		add_action('wp_ajax_accountToken', array($this,'accountToken'));
        add_action('wp_ajax_nopriv_accountToken',array($this,'accountToken') );

		//Hook functions to call Ajax for Saving Cloudflare details
		add_action('wp_ajax_save_cloudflare_details', array($this,'save_cloudflare_details'));
		add_action('wp_ajax_nopriv_save_cloudflare_details',array($this,'save_cloudflare_details') );

		//Hook functions to call Ajax for Elastic Email Listing
		add_action('wp_ajax_elastic_email_list', array($this,'elastic_email_list'));
		add_action('wp_ajax_nopriv_elastic_email_list',array($this,'elastic_email_list') );
 
		//Hook functions to call Ajax for Showing Elastic Email Listing on Domain Check
		add_action('wp_ajax_show_elastic_email', array($this,'show_elastic_email'));
		add_action('wp_ajax_nopriv_show_elastic_email',array($this,'show_elastic_email') );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ss_Cloudflare_Loader. Orchestrates the hooks of the plugin.
	 * - Ss_Cloudflare_i18n. Defines internationalization functionality.
	 * - Ss_Cloudflare_Admin. Defines all hooks for the admin area.
	 * - Ss_Cloudflare_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ss-cloudflare-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ss-cloudflare-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ss-cloudflare-admin.php';

		$this->loader = new Ss_Cloudflare_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ss_Cloudflare_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ss_Cloudflare_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ss_Cloudflare_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ss_Cloudflare_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Creating Menu for the plugin
	 * 
	 */
	function ss_cloudflare_add_menu_page() {
		add_submenu_page(
			'tools.php',        // Parent slug (the "Tools" menu slug)
			'SS Cloudflare',     // Page title
			'SS Cloudflare',     // Menu title
			'manage_options',   // Capability required to access the page
			'ss-cloudflare',     // Menu slug (should be unique)
			array($this,'ss_cloudflare_admin_page') // Callback function to display the page content
		);
	}

	/**
	 * Function to Plugin Admin page
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function ss_cloudflare_admin_page() {
		// Page content goes here (you can put your HTML and PHP code for the custom tools)
		echo '<h1>SS Cloudflare</h1>';
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'cloudflarecontrol';
		?>
		<section class="setting-option">
			<div class="uk-section-default uk-section">
				<div class="uk-container">
					<div class="uk-grid tm-grid-expand uk-child-width-1-1 uk-grid-margin">
						<div class="uk-width-1-1">
							<div class="uk-margin">
								<ul class="el-nav uk-margin uk-tab" uk-tab="connect: #js-0; itemNav: #js-1; animation: uk-animation-fade;" role="tablist">
									<li class="uk-active" role="presentation"><a href="?page=ss-cloudflare&tab=cloudflarecontrol" aria-selected="true" role="tab" id="uk-tab-1" aria-controls="uk-tab-2">Settings</a></li>
									<li role="presentation"><a href="" aria-selected="false" tabindex="-1" role="tab" id="uk-tab-3" aria-controls="uk-tab-4">Options</a></li>
									<li role="presentation"><a href="?page=ss-cloudflare&tab=cloudflaredomaincheck" aria-selected="false" tabindex="-1" role="tab" id="uk-tab-5" aria-controls="uk-tab-6">Cloudflare Control</a></li>
								</ul>
								<span class="errorMessage"></span>
								<span class="successMessage"></span>
								<ul id="js-0" class="uk-switcher" uk-height-match="row: false" role="presentation" style="touch-action: pan-y pinch-zoom;">
									<li class="el-item uk-margin-remove-first-child uk-active" id="uk-tab-2" role="tabpanel" aria-labelledby="uk-tab-1" style="min-height: 337.3px;">
										<h3 class="el-title uk-margin-top uk-margin-remove-bottom">Settings</h3>
										<input type="hidden" name="from_cloudflarecontrol_form"  id="from_cloudflarecontrol_form" value="cloudflarecontrol_form"> 
										<div class="el-content uk-panel uk-margin-top">
											<form class="uk-form-stacked uk-width-large api-credentials">
												<div class="uk-margin">
													<label class="uk-form-label" for="">Cloudflare API Credentials</label>
													<div class="uk-child-width-1-1">
														<div class="uk-inline email-input">
															<span class="uk-form-icon uk-icon" uk-icon="icon: user">
																<svg width="20" height="20" viewBox="0 0 20 20">
																	<circle fill="none" stroke="#000" stroke-width="1.1" cx="9.9" cy="6.4" r="4.4"></circle>
																	<path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,19 C2.3,14.5 5.8,11.2 10,11.2 C14.2,11.2 17.7,14.6 18.5,19.2"></path>
																</svg>
															</span>
															<input class="uk-input" id="account-email" name="account-email" type="text" placeholder="your@emailaddress.com" value="<?php echo (get_option('ss_cloudflare_email') != '')?get_option('ss_cloudflare_email'):''; ?>"/>
														</div>
														<div class="uk-inline password-input">
															<span class="uk-form-icon uk-icon" uk-icon="icon: cog">
																<svg width="20" height="20" viewBox="0 0 20 20">
																	<circle fill="none" stroke="#000" cx="9.997" cy="10" r="3.31"></circle>
																	<path
																		fill="none"
																		stroke="#000"
																		d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z"
																	></path>
																</svg>
															</span>
															<input class="uk-input" id="api-token" type="password" placeholder="Cloudflare API Token" value="<?php echo (get_option('ss_cloudflare_api_token') != '')?get_option('ss_cloudflare_api_token'):''; ?>"/>
															<i class="fa fa-eye-slash toggle-password" id="toggle-password-api"></i>
														</div>
														<div class="uk-inline password-input">
															<span class="uk-form-icon uk-icon" uk-icon="icon: nut">
																<svg width="20" height="20" viewBox="0 0 20 20">
																	<polygon fill="none" stroke="#000" points="2.5,5.7 10,1.3 17.5,5.7 17.5,14.3 10,18.7 2.5,14.3"></polygon>
																	<circle fill="none" stroke="#000" cx="10" cy="10" r="3.5"></circle>
																</svg>
															</span>
															<input class="uk-input" type="password" id="bearer-token" placeholder="Cloudflare Bearer Token (for creating new API Tokens)" value="<?php echo (get_option('ss_cloudflare_bearer_token') != '')?get_option('ss_cloudflare_bearer_token'):''; ?>"/>
															<i class="fa fa-eye-slash toggle-password" id="toggle-password-bearer"></i>
														</div>
													</div>
												</div>
											</form>
											<p><a class="uk-button uk-button-default ss-cloudflare-input" id="save_cloudflare_controls">Save</a></p>
										</div>
									</li>
									<li class="el-item uk-margin-remove-first-child" id="uk-tab-4" role="tabpanel" aria-labelledby="uk-tab-3" style="">
										<h3 class="el-title uk-margin-top uk-margin-remove-bottom">Options</h3>
										<div class="el-content uk-panel uk-margin-top">
											<div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid uk-grid-stack" uk-grid="">
												<div>
													<div class="uk-card uk-card-default uk-card-body">
														<h3 class="uk-card-title">Title</h3>
														<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
														<p><a class="uk-button uk-button-primary" href="#modal" uk-toggle="" role="button">Modal Button</a></p>
													</div>
												</div>
												
											</div>
											<div id="modal" class="uk-flex-top uk-modal" uk-modal="">
												<div class="uk-modal-dialog uk-margin-auto-vertical" role="dialog" aria-modal="true">
													<button class="uk-modal-close-default uk-icon uk-close" type="button" uk-close="" aria-label="Close">
														<svg width="14" height="14" viewBox="0 0 14 14">
															<line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"></line>
															<line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"></line>
														</svg>
													</button>
													<div class="uk-modal-header"><h2 class="uk-modal-title">Modal Title</h2></div>
													<div class="uk-modal-body">
														<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
														<p><a class="uk-button uk-button-primary">Save</a></p>
													</div>
												</div>
											</div>
										</div>
									</li>
									<li class="el-item uk-margin-remove-first-child" id="uk-tab-6" role="tabpanel" aria-labelledby="uk-tab-5" style="min-height: 337.3px;">
										<h3 class="el-title uk-margin-top uk-margin-remove-bottom">Cloudflare Control</h3>
										<div class="overlay"></div>
										<div class="el-content uk-panel uk-margin-top">
											<div uk-grid="" class="uk-grid uk-grid-stack">
												<div class="uk-width-1-2@m">
													<form class="uk-form-stacked uk-width-large">
														<div class="uk-margin">
															<label class="uk-form-label" for="">Cloudflare API Credentials</label>
															<input type="hidden" name="cloudflare_email" id="cloudflare_email" value="<?php echo (get_option('ss_cloudflare_email') != '')?get_option('ss_cloudflare_email'):''; ?>">
															<input type="hidden" name="cloudflare_api_token" id="cloudflare_api_token" value="<?php echo (get_option('ss_cloudflare_api_token') != '')?get_option('ss_cloudflare_api_token'):''; ?>">
															<input type="hidden" name="cloudflare_bearer_token" id="cloudflare_bearer_token" value="<?php echo (get_option('ss_cloudflare_bearer_token') != '')?get_option('ss_cloudflare_bearer_token'):''; ?>">
															<div class="uk-child-width-1-1">
																<div class="uk-inline">
																	<span class="uk-form-icon uk-icon" uk-icon="icon: world">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<path fill="none" stroke="#000" d="M1,10.5 L19,10.5"></path>
																			<path fill="none" stroke="#000" d="M2.35,15.5 L17.65,15.5"></path>
																			<path fill="none" stroke="#000" d="M2.35,5.5 L17.523,5.5"></path>
																			<path
																				fill="none"
																				stroke="#000"
																				d="M10,19.46 L9.98,19.46 C7.31,17.33 5.61,14.141 5.61,10.58 C5.61,7.02 7.33,3.83 10,1.7 C10.01,1.7 9.99,1.7 10,1.7 L10,1.7 C12.67,3.83 14.4,7.02 14.4,10.58 C14.4,14.141 12.67,17.33 10,19.46 L10,19.46 L10,19.46 L10,19.46 Z"
																			></path>
																			<circle fill="none" stroke="#000" cx="10" cy="10.5" r="9"></circle>
																		</svg>
																	</span>
																	<input id="domainName" class="uk-input domainName" type="text" placeholder="yourdomain.com" />
																	<span class="material-symbols-rounded" id="domainNameCheck"></span>
																	<input type="hidden" id="zoneId" name="zoneId">
																	<input type="hidden" id="zoneName" name="zoneName">
																</div>
																<p><a class="uk-button uk-button-default domainCheck" name="domainCheck" id="domainCheck">Domain Check</a></p>
															</div>

															<div class="uk-child-width-1-1">
																<div class="accountTokenDiv" style="display:none;">
																	<input class="uk-input" id="accountTokenId" readonly type="text" aria-label="Input">
																	<div class="copyIcon" style="display:inline-block;">
																		<a class="copy-icon" data-tooltip-location="top" uk-icon="copy" data-placement="bottom" data-clipboard-target="#accountTokenId" onclick="copyToClipboard('#accountTokenId')"></a>
																	</div>
																</div>
															</div>

														</div>
													</form>
												</div>
												<div class="uk-width-1-2@m">
													<div uk-grid="" class="uk-grid-small uk-grid-width-auto uk-grid uk-grid-stack">
														<div>
															<p>
																<a class="uk-button accountToken_btn" id="account-token">
																	<span uk-icon="nut" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<polygon fill="none" stroke="#000" points="2.5,5.7 10,1.3 17.5,5.7 17.5,14.3 10,18.7 2.5,14.3"></polygon>
																			<circle fill="none" stroke="#000" cx="10" cy="10" r="3.5"></circle>
																		</svg>
																	</span>
																	Create Account Token
																</a>
															</p>
														</div>
														<div>
															<p>
																<a class="uk-button uk-button-secondary ruleBtnClick xmlrpc" name="xmlrpc">
																	<span uk-icon="folder" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20"><polygon fill="none" stroke="#000" points="9.5 5.5 8.5 3.5 1.5 3.5 1.5 16.5 18.5 16.5 18.5 5.5"></polygon></svg>
																	</span>
																	XMLRPC Block
																</a>
															</p>
														</div>
														<div>
															<p>
																<a class="uk-button uk-button-secondary ruleBtnClick adminLogin" name="adminLogin">
																	<span uk-icon="lock" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<rect fill="none" stroke="#000" height="10" width="13" y="8.5" x="3.5"></rect>
																			<path fill="none" stroke="#000" d="M6.5,8 L6.5,4.88 C6.5,3.01 8.07,1.5 10,1.5 C11.93,1.5 13.5,3.01 13.5,4.88 L13.5,8"></path>
																		</svg>
																	</span>
																	UK Admin / Login
																</a>
															</p>
														</div>
														<div>
															<p>
																<a class="uk-button uk-button-secondary ruleBtnClick adminSecurity" name="adminSecurity">
																	<span uk-icon="user" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<circle fill="none" stroke="#000" stroke-width="1.1" cx="9.9" cy="6.4" r="4.4"></circle>
																			<path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,19 C2.3,14.5 5.8,11.2 10,11.2 C14.2,11.2 17.7,14.6 18.5,19.2"></path>
																		</svg>
																	</span>
																	Admin Security Check
																</a>
															</p>
														</div>
														<div>
															<p>
																<a class="uk-button uk-button-secondary ruleBtnClick failover" name="failover">
																	<span uk-icon="future" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<polyline points="19 2 18 2 18 6 14 6 14 7 19 7 19 2"></polyline>
																			<path fill="none" stroke="#000" stroke-width="1.1" d="M18,6.548 C16.709,3.29 13.354,1 9.6,1 C4.6,1 0.6,5 0.6,10 C0.6,15 4.6,19 9.6,19 C14.6,19 18.6,15 18.6,10"></path>
																			<rect x="9" y="4" width="1" height="7"></rect>
																			<path d="M13.018,14.197 L9.445,10.625" fill="none" stroke="#000" stroke-width="1.1"></path>
																		</svg>
																	</span>
																	Failover Protection
																</a>
															</p>
														</div>
														<div>
															<p>
																<a class="uk-button uk-button-secondary ruleBtnClick elasticEmail" name="elasticEmail">
																	<span uk-icon="mail" class="uk-icon">
																		<svg width="20" height="20" viewBox="0 0 20 20">
																			<polyline fill="none" stroke="#000" points="1.4,6.5 10,11 18.6,6.5"></polyline>
																			<path d="M 1,4 1,16 19,16 19,4 1,4 Z M 18,15 2,15 2,5 18,5 18,15 Z"></path>
																		</svg>
																	</span>
																	Elastic Mail
																</a>
															</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>  
		</section>
		<?php
	}

	/**
	 * Common Function to get CURL response
	 * 
	 * @since    1.0.0
	 * @access   public
	 */

	function getCurl($url, $email, $apiToken)
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"Content-Type: application/json",
				"X-Auth-Email: $email",
				"X-Auth-Key: $apiToken"
			]
		]);

		// Convert JSON array to PHP array
		$response = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);
		if (!$response->success) {
			return $data = [
				'status' => 'error',
				'data' => $response->errors[0]->message
			];
		} else if ($err) {
			return $data = [
				'status' => 'error',
				'data' => json_decode($err->errors)
			];
		} else {
			return $data = [
				'status' => 'success',
				'data' => $response
			];
		}
	}

	/**
	 * Common Function to push CURL response
	 * 
	 * @since    1.0.0
	 * @access   public
	 */

	function postCurl($url, $param, $headers, $method)
	{
		array_merge($headers, array("condition" => ""));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $param,
			CURLOPT_HTTPHEADER => $headers,

		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $data = [
				'status' => 'error',
				'data' => "cURL Error #:" . $err
			];
		} else {
			// Convert JSON array to PHP array
			return $data = [
				'status' => 'success',
				'data' => json_decode($response)
			];
		}
	}

	/**
	 * Function to Check Domain name
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function domain_curl(){
		$url = $_POST['url'];
		$email = $_POST['email'];
		$apiToken = $_POST['apiToken'];

		$res = $this->getCurl($url, $email, $apiToken);

		if ($res['status'] == 'error') {
			echo json_encode(['status' => false, 'output' => $res['data']]);
			die;
		}
		$data = $res['data'];

		// Get the form values
		$name = $_POST['name'];
		$found = false;
		if(isset($data->result) && !empty($data->result)){
			foreach ($data->result as $item) {
				if ($item->name === $name) {
					$found = true;
					break;
				}
			}
		}

		$data = [];
		if ($found) {
			$data = [
				'zoneId' => $item->id,
				'zoneName' => $item->name
			];

			echo json_encode(['status' => true, 'output' => $data]);
			die;
		} else {

			echo json_encode(['status' => true, 'output' => $data]);
			die;
		}

	}

	/**
	 * Function to set WAF rules
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function waf_rules_list(){

		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {
    
			$zoneId = $_POST['zoneId'];
			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];
			$url = "https://api.cloudflare.com/client/v4/zones/" . $zoneId . "/rulesets";
			$res = $this->getCurl($url, $email, $apiToken);
		
			if ($res['status'] == 'error') {
				echo json_encode(['status' => false, 'output' => $res['data']]);
				die;
			}
			$rulesetList = $res['data']->result;
		
			$rulesArray = [];
			foreach($rulesetList as $key => $res) {
				// We need only this condition. Once is satisfied then proceed for next step.
				// given Kind & phase have permission for create rulesets
				if ($res->kind == 'zone' && $res->phase == 'http_request_firewall_custom') {
					$getUrl = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/rulesets/' . $res->id;
					$res = $this->getCurl($getUrl, $email, $apiToken);
					
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
						die;
					}
					$response = $res['data'];
					// getting rule names in array format for showing on the tickmark
					if ($response->success == true && isset($response->result->rules) && count($response->result->rules) > 0) {
						foreach($response->result->rules as $row) {
							$rulesArray[] = $row->description;
						}
					}
				}
			}
		
			echo json_encode(['status' => true, 'output' => $rulesArray]);
			die;
		}
	}
	
	/**
	 * Function to update Admin Login Rules
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function adminLogin()
	{
		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {

			$action = $_POST['action'];
			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];

			$ruleName = 'UK Admin / Login';
			$postParam = json_encode([
				"description" => "UK Admin / Login",
				"expression" => "(not ip.geoip.country in {\"GB\"} and http.request.uri eq \"/wp-login.php\")",
				"action" => "block",
				"enabled" => true
			]);

			$this->updateWafRule($_POST['zoneId'], $email, $apiToken, $ruleName, $postParam);

		} else {
			$result = [
				'success' => false,
				'message' => 'Zone details not available.'
			];
			echo json_encode($result);
		}
	}
	
	/**
	 * function to update xmlrp Block Rules
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function xmlrpcCheck()
	{
		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {

			$action = $_POST['action'];
			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];

			$ruleName = 'XMLRPC Block';
			$postParam = json_encode([
				"description" => "XMLRPC Block",
				"expression" => "(http.request.uri contains \"/xmlrpc.php\")",
				"action" => "block",
				"enabled" => true
			]);

			$this->updateWafRule($_POST['zoneId'], $email, $apiToken, $ruleName, $postParam);

		} else {
			$result = [
				'success' => false,
				'message' => 'Zone details not available.'
			];
			echo json_encode($result);
		}
	}
	
	/**
	 * Function to update Admin Security rules
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function adminSecurity()
	{
		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {

			$action = $_POST['action'];
			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];

			$ruleName = 'Admin Security Check';
			$postParam = json_encode([
				"description" => "Admin Security Check",
				"expression" => "(http.request.uri eq \"/wp-login.php\")",
				"action" => "managed_challenge",
				"enabled" => true
			]);

			$this->updateWafRule($_POST['zoneId'], $email, $apiToken, $ruleName, $postParam);

		} else {
			$result = [
				'success' => false,
				'message' => 'Zone details not available.'
			];
			echo json_encode($result);
		}
	}
	
	/**
	 * Function to update Failover Rule
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function failover()
	{
		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {
			$action = $_POST['action'];
			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];

			$ruleName = 'Failover';
			$postParam = json_encode([
				"description" => "Failover",
				"expression" => "(http.request.uri contains \"failover.php\")",
				"action" => "block",
				"enabled" => true
			]);

			$this->updateWafRule($_POST['zoneId'], $email, $apiToken, $ruleName, $postParam);

		} else {
			$result = [
				'success' => false,
				'message' => 'Zone details not available.'
			];
			echo json_encode($result);
		}
	}
	

	/**
	 * Function to update WAF Rules
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function updateWafRule($zoneId, $email, $apiToken, $ruleName, $postParam)
	{
		$url = "https://api.cloudflare.com/client/v4/zones/" . $zoneId . "/rulesets";
		$res = $this->getCurl($url, $email, $apiToken);
	
		if ($res['status'] == 'error') {
			echo json_encode(['status' => false, 'output' => $res['data']]);
			die;
		}
		$rulesetList = $res['data']->result;
		$kindSearch = array_search('zone', array_column($rulesetList, 'kind'));
		$phaseSearch = array_search('http_request_firewall_custom', array_column($rulesetList, 'phase'));
		if (count($res['data']->result) > 0 && !empty($kindSearch) && !empty($phaseSearch)) {
			foreach ($rulesetList as $key => $res) {
				if ($res->kind == 'zone' && $res->phase == 'http_request_firewall_custom') {
					$getUrl = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/rulesets/' . $res->id;
					$res = $this->getCurl($getUrl, $email, $apiToken);
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
						die;
					}
					$response = $res['data'];
					$rulesArray = [];
					if ($response->success == true && isset($response->result->rules) && count($response->result->rules) > 0) {
						foreach ($response->result->rules as $row) {
							$rulesArray[] = $row->description;
						}
					}
	
					if (in_array($ruleName, $rulesArray)) {
						echo json_encode(['status' => true, 'output' => 'Rules already exist']);
						die;
					} else {
						$url = $getUrl . "/rules";
						$headers = [
							"Content-Type: application/json",
							"X-Auth-Email: $email",
							"X-Auth-Key: $apiToken"
						];
						$this->postCurl($url, $postParam, $headers, 'POST');
						echo json_encode(['status' => true, 'output' => 'Added successfully!']);
						die;
					}
				}
			}
		} else {
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/rulesets';
			$createRulesetData = [
				'description' => 'My ruleset to execute managed rulesets',
				"kind" => "zone",
				"name" => "My ruleset",
				"phase" => "http_request_firewall_custom",
				"rules" => [json_decode($postParam)]
			];
			$headers = [
				"Content-Type: application/json",
				"X-Auth-Email: $email",
				"X-Auth-Key: $apiToken"
			];
			$this->postCurl($url, json_encode($createRulesetData), $headers, 'POST');
			echo json_encode(['status' => true, 'output' => 'Added successfully!']);
			die;
		}
	}


	/**
	 * Function to get create Account Token
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	function accountToken(){

		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {
			$zoneId = $_POST['zoneId'];
			$zoneName = $_POST['zoneName'];
			$bearer_token = $_POST['bearerToken'];
			// Now, Some permission groups only enabled here. Based on client requirement need to proceed some other permission enable later.
			$paramData = [
				"name" => $zoneName,
				"policies" => [
					[
						"effect" => "allow",
						"resources" => [
							"com.cloudflare.api.account.*" => "*",
							"com.cloudflare.api.account.zone.$zoneId" => "*"
						],
						"permission_groups" => [
							[
								"id" => "c1fde68c7bcc44588cbb6ddbc16d6480",
								"name" => "Account Settings Read"
							],
							[
								"id" => "e17beae8b8cb423a99b1730f21238bed",
								"name" => "Cache Purge"
							],
							[
								"id" => "ed07f6c337da4195b4e72a1fb2c6bcae",
								"name" => "Page Rules Write"
							],
							[
								"id" => "3030687196b94b638145a3953da2b699",
								"name" => "Zone Settings Write"
							],
							[
								"id" => "e6d2666161e84845a636613608cee8d5",
								"name" => "Zone Write"
							],
						]
					]
				],
				"not_before" => "",
				"expires_on" => "",
				"condition" => [
					"request.ip" => [
						"in" => [],
						"not_in" => []
					]
				]
			];
			$url = 'https://api.cloudflare.com/client/v4/user/tokens';
			$headers = [
				"Content-Type: application/json",
				"Authorization: Bearer $bearer_token"
			];
			$res = $this->postCurl($url, json_encode($paramData), $headers, 'POST');
		
			if ($res['status'] == 'error') {
				echo json_encode(['status' => false, 'output' => $res['data']]);
				die;
			}
			$data = $res['data'];
		
			if (!$data->success) {
				echo json_encode(['status' => false, 'output' => $data->errors[0]->message]);
				die;
			} else {
				// Access the "value" field from the JSON data
				$value = $data->result->value;
				echo json_encode(['status' => true, 'output' => $value]);
				die;
			}
		} else {
			echo json_encode(['status' => false, 'output' => 'Zone details not available']);
			die;
		}
		
	}

	/**
	 * Function to save cloudflare details
	 * 
	 * Since 1.0.0
	 */
	function save_cloudflare_details(){
		$message = '';
		if($_POST['from_cloudflare_form'] == 'cloudflarecontrol_form'){
			
			if(get_option('ss_cloudflare_email') != $_POST['account_email']){
				update_option('ss_cloudflare_email',$_POST['account_email']);
				$message = "Cloudflare Email updated";
			}

			if(get_option('ss_cloudflare_api_token') != $_POST['api_token']){
				update_option('ss_cloudflare_api_token',$_POST['api_token']);
				$message = "Cloudflare API Token updated";
			}
		
			if(get_option('ss_cloudflare_bearer_token') != $_POST['bearer_token']){
				update_option('ss_cloudflare_bearer_token',$_POST['bearer_token']);
				$message = "Cloudflare Bearer Token updated";
			}
			
		}

		$return = array(
			'message' => __( $message, 'SSCloudflare' ),
			'status'      => true
		);
		wp_send_json_success( $return );  
	}

	function elastic_email_list(){
		$url = 'https://api.cloudflare.com/client/v4/zones/' . $_POST['zoneId'] . '/dns_records';

		$email = $_POST['email'];
		$apiToken = $_POST['apiToken'];

		$headers = [
			"Content-Type: application/json",
			"X-Auth-Email: $email",
			"X-Auth-Key: $apiToken"
		];
		$res = $this->getCurl($url, $email, $apiToken);

		if ($res['status'] == 'error') {
			echo json_encode(['status' => false, 'output' => $res['data']]);
			die;
		}
		$response = $res['data']->result;
		
		$responseArray = [];
		if ($response != null) {
			for ($i = 0; $i < count($response); $i++) {
				$spf_content = explode(" ", $response[$i]->content);
				if ($response[$i]->name == $_POST['zoneName'] && $response[$i]->type == 'TXT' && !in_array('include:_spf.elasticemail.com', $spf_content)) {
					array_splice($spf_content, count($spf_content) - 1, 0, 'include:_spf.elasticemail.com'); // added value in before the ~all word. 
					$updateUrl = $url . '/' . $response[$i]->id;
					$updateParam = [
						'name' => $response[$i]->name,
						'type' => 'TXT',
						'content' =>  implode(' ', $spf_content),
						'comment' => $response[$i]->comment
					];
					$res = $this->postCurl($updateUrl, json_encode($updateParam), $headers, 'PUT');
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
					}
					$response = $res['data'];
					array_push($responseArray, '@');

				} else if ($response[$i]->name == $_POST['zoneName'] && $response[$i]->type == 'TXT' && in_array('include:_spf.elasticemail.com', $spf_content)) {
					array_push($responseArray, '@');
				}
			}
			
			$DNSRecordsArray = DNS_RECORDS_ARRAY;
			foreach ($DNSRecordsArray as $row) {
				if (count($responseArray) > 0 && $row['name'] != '@') {
					$createRulesetData = [
						"content" => $row['content'],
						"name" => $row['name'],
						"proxied" => $row['proxied'],
						"type" => $row['type'],
						"comment" => $row['comment'],
					];
					$res = $this->postCurl($url, json_encode($createRulesetData), $headers, 'POST');
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
					}
					$response = $res['data'];
					array_push($responseArray, $row['name']);

				} else if (count($responseArray) == 0 && !in_array('@', $responseArray)) {
					$createRulesetData = [
						"content" => $row['content'],
						"name" => $row['name'],
						"proxied" => $row['proxied'],
						"type" => $row['type'],
						"comment" => $row['comment'],
					];
					$res = $this->postCurl($url, json_encode($createRulesetData), $headers, 'POST');
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
					}
					$response = $res['data'];
					array_push($responseArray, $row['name']);

				} else if (count($responseArray) > 0 && !in_array('@', $responseArray) && $row['name'] == '@') {
					$createRulesetData = [
						"content" => $row['content'],
						"name" => $row['name'],
						"proxied" => $row['proxied'],
						"type" => $row['type'],
						"comment" => $row['comment'],
					];
					$res = $this->postCurl($url, json_encode($createRulesetData), $headers, 'POST');
					if ($res['status'] == 'error') {
						echo json_encode(['status' => false, 'output' => $res['data']]);
					}
					$response = $res['data'];
					array_push($responseArray, $row['name']);
				}
			}
		}

		if (!empty($responseArray)) {
			echo json_encode(['status' => true, 'output' => 'are already exist']);
			exit;
		} else {
			echo json_encode(['status' => true, 'output' => 'Added successfully!']);
			exit;
		}
	}

	function show_elastic_email(){
		$responseArray = [];
		if (!empty($_POST['zoneId']) && !empty($_POST['zoneName'])) {
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $_POST['zoneId'] . '/dns_records';

			$email = $_POST['email'];
			$apiToken = $_POST['apiToken'];
			$res = $this->getCurl($url, $email, $apiToken);

			if ($res['status'] == 'error') {
				echo json_encode(['status' => false, 'output' => $res['data']]);
				die;
			}
			$response = $res['data']->result;

			$contents = array_column(DNS_RECORDS_ARRAY, 'content');
			$names = array_column(DNS_RECORDS_ARRAY, 'name');
			// getting DNS names in array format for showing on the tickmark
			for ($i = 0; $i < count($response); $i++) {
				$spf_content = explode(" ", $response[$i]->content);
				if (in_array($response[$i]->content, $contents)) {
					array_push($responseArray, $response[$i]->name);
				} else if ($response[$i]->name == $_POST['zoneName'] && $response[$i]->type == 'TXT' && in_array('include:_spf.elasticemail.com', $spf_content)) {
					array_push($responseArray, $response[$i]->name);
				}
			}
			echo json_encode(['status' => true, 'output' => $responseArray]);
			die;
		}
	}
}
