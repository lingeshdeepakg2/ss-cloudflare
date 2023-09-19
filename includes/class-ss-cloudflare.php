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
		echo '<h1>SS Cloudflare</h1>';?>
		<div class="container ss-cloudflare-main-page">
			<div class="row">
				<div class="heading">
					<h4><b>Cloudflare Control</b></h4>
				</div>
				<div id="errorMessage"></div>
				<div id="successMessage"></div>
				<div class="col-md-12 table-container">
					<table>
						<td>
							<div class="ss-cloudflare-right">
								<!-- Loader -->
								<div class="overlay"></div>
								<div class="">
									<div class="">
										<input id="account-email" class="input-field" type="text" value="<?php echo EMAIL; ?>" aria-label="Input" placeholder="Account Email">
									</div>
									<div class="">
										<input id="api-token" class="input-field" type="text" value="<?php echo APITOKEN; ?>" aria-label="Input" placeholder="API Token">
									</div>
									<div class="">
										<input id="bearer-token" class="input-field" type="text" value="<?php echo BEARERTOKEN; ?>" aria-label="Input" placeholder="Bearer Token">
									</div>
									<div class="">
										<input name="name" class="input-field" id="domainName" type="text" placeholder="Domain" aria-label="Input">
										<span class="material-symbols-rounded" id="domainNameCheck"></span>
									</div>
									<input class="blue-btn " name="domainCheck" id="domainCheck" type="submit" value="Domain Check">

									<input type="hidden" id="zoneId" name="zoneId">
									<input type="hidden" id="zoneName" name="zoneName">

									<div class="">
										<div class="">
											<input class="blue-btn " id="account-token" type="submit" value="Create Account token">
										</div>
										<div class="accountTokenDiv" style="display:none;">
											<input class="input-field" id="accountTokenId" readonly type="text" aria-label="Input">
											<div class="copyIcon" style="display:inline-block;">
												<a class="copy-icon" data-tooltip-location="top" uk-icon="copy" data-placement="bottom" data-clipboard-target="#accountTokenId" onclick="copyToClipboard('#accountTokenId')"></a>
											</div>
										</div>
										<br /><br />
									</div>
								</div>
							</div>
						</td>
						<td class="ss-cloudflare-left-col">
							<div class="ss-cloudflare-left">
								<div>
									<input type="submit" name="xmlrpc" value="XMLRPC Block" class="ruleBtnClick blue-btn ">
									<span class="material-symbols-rounded" id="xmlrpc"></span>
								</div>
								<div><input type="submit" name="adminLogin" value="UK Admin / Login" class="ruleBtnClick blue-btn ">
									<span class="material-symbols-rounded" id="adminLogin"></span>
								</div>
								<div><input type="submit" name="adminSecurity" value="Admin Security Check" class="ruleBtnClick blue-btn ">
									<span class="material-symbols-rounded" id="adminSecurity"></span>
								</div>
								<div><input type="submit" name="failover" value="Failover Protection" class="ruleBtnClick blue-btn ">
									<span class="material-symbols-rounded" id="failover"></span>
								</div>
							</div>
						</td>
					</table>
				</div>
			</div>
		</div>
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
								"id" => "e086da7e2179491d91ee5f35b3ca210a",
								"name" => "Workers Scripts Write"
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
							[
								"id" => "28f4b596e7d643029c524985477ae49a",
								"name" => "Workers Routes Write"
							]
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
}
