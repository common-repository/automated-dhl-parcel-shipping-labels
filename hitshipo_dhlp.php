<?php
/**
 * Plugin Name: Automated DHL Parcel shipping labels. 
 * Plugin URI: https://wordpress.org/plugins/automated-dhl-parcel-shipping-labels
 * Description: Automatic and manual shipping labels.
 * Version: 1.1.4
 * Author: HITShipo
 * Author URI: https://hitshipo.com/
 * Developer: HITShipo
 * Developer URI: https://hitshipo.com/
 * Text Domain: HITShipo
 * Domain Path: /i18n/languages/
 *
 * WC requires at least: 2.6
 * WC tested up to: 5.8
 *
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'HITSHIPPO_DHLP_PLUGIN_FILE' ) ) {
	define( 'HITSHIPPO_DHLP_PLUGIN_FILE', __FILE__ );
}

function hit_woo_dhlp_plugin_activation( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        $setting_value = version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
    	// Don't forget to exit() because wp_redirect doesn't exit automatically
    	exit( wp_redirect( admin_url( 'admin.php?page=' . $setting_value  . '&tab=shipping&section=hitshipo_dhlp' ) ) );
    }
}
add_action( 'activated_plugin', 'hit_woo_dhlp_plugin_activation' );


// Include the main WooCommerce class.
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if( !class_exists('hitshipo_dhlp_parent') ){
		Class hitshipo_dhlp_parent
		{
			public function __construct() {
				add_action( 'woocommerce_shipping_init', array($this,'hitshipo_dhlp_init') );
				add_action( 'init', array($this,'hitshipo_dhlp_order_status_update') );
				add_filter( 'woocommerce_shipping_methods', array($this,'hitshipo_dhlp_method') );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'hitshipo_dhlp_plugin_action_links' ) );
				add_action( 'add_meta_boxes', array($this, 'create_dhlp_shipping_meta_box'), 10, 1);
				add_action( 'save_post', array($this, 'hitshippo_create_dhlp_shipping'), 10, 1 );
				add_action( 'admin_menu', array($this, 'hit_dhlp_menu_page' ));
				add_action( 'woocommerce_order_status_processing', array( $this, 'hitshipo_dhlp_wc_checkout_order_processed' ) );
				// add_action( 'woocommerce_thankyou', array( $this, 'hitshipo_dhlp_wc_checkout_order_processed' ) );

				$general_settings = get_option('hitshipo_dhlp_main_settings');
				$general_settings = empty($general_settings) ? array() : $general_settings;

				if(isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes' ){
					add_action( 'woocommerce_product_options_shipping', array($this,'hit_choose_vendor_address' ));
					add_action( 'woocommerce_process_product_meta', array($this,'hit_save_product_meta' ));

					// Edit User Hooks
					add_action( 'edit_user_profile', array($this,'hit_define_dhlp_credentails') );
					add_action( 'edit_user_profile_update', array($this, 'save_user_fields' ));

				}
			}

			function hit_dhlp_menu_page() {
				$general_settings = get_option('hitshipo_dhlp_main_settings');
				if (isset($general_settings['hitshipo_dhlp_shippo_int_key']) && !empty($general_settings['hitshipo_dhlp_shippo_int_key'])) {
					add_menu_page(__( 'DHL Parsel Labels', 'hitshipo_dhlp' ), 'DHL Parsel Labels', 'manage_options', 'hit-dhlp-labels', array($this,'my_label_page_contents'), '', 6);
				}
								
				add_submenu_page( 'options-general.php', 'DHL Parcel Config', 'DHL Parcel Config', 'manage_options', 'hits-dhlp-configuration', array($this, 'my_admin_page_contents') ); 

			}
			function my_label_page_contents(){
				$general_settings = get_option('hitshipo_dhlp_main_settings');
				$url = site_url();
				if (isset($general_settings['hitshipo_dhlp_shippo_int_key']) && !empty($general_settings['hitshipo_dhlp_shippo_int_key'])) {
					echo "<iframe style='width: 100%;height: 100vh;' src='https://app.hitshipo.com/embed/label.php?shop=".$url."&key=".$general_settings['hitshipo_dhlp_shippo_int_key']."&show=ship'></iframe>";
				}
            }
			function my_admin_page_contents(){
				include_once('controllors/views/hitshipo_dhlp_settings_view.php');
			}

			public function hit_choose_vendor_address(){
				global $woocommerce, $post;
				$hit_multi_vendor = get_option('hit_multi_vendor');
				$hit_multi_vendor = empty($hit_multi_vendor) ? array() : $hit_multi_vendor;
				$selected_addr = get_post_meta( $post->ID, 'dhlp_address', true);

				$main_settings = get_option('hitshipo_dhlp_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				if(!isset($main_settings['hitshipo_dhlp_v_roles']) || empty($main_settings['hitshipo_dhlp_v_roles'])){
					return;
				}
				$v_users = get_users( [ 'role__in' => $main_settings['hitshipo_dhlp_v_roles'] ] );

				?>
				<div class="options_group">
				<p class="form-field dhlp_shipment">
					<label for="dhlp_shipment"><?php _e( 'DHL Parcel Account', 'woocommerce' ); ?></label>
					<select id="dhlp_shipment" style="width:240px;" name="dhlp_shipment" class="wc-enhanced-select" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
						<option value="default" >Default Account</option>
						<?php
							if ( $v_users ) {
								foreach ( $v_users as $value ) {
									echo '<option value="' .  esc_html($value->data->ID)  . '" '.($selected_addr == $value->data->ID ? 'selected="true"' : '').'>' . esc_html($value->data->display_name) . '</option>';
								}
							}
						?>
					</select>
					</p>
				</div>
				<?php
			}

			public function hit_save_product_meta( $post_id ){
				if(isset( $_POST['dhlp_shipment'])){
					$dhlp_shipment = sanitize_text_field($_POST['dhlp_shipment']);
					if( !empty( $dhlp_shipment ) )
					update_post_meta( $post_id, 'dhlp_address', (string) esc_html( $dhlp_shipment ) );
				}

			}

			public function hit_define_dhlp_credentails( $user ){

				$main_settings = get_option('hitshipo_dhlp_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				$allow = false;

				if(!isset($main_settings['hitshipo_dhlp_v_roles'])){
					return;
				}else{
					foreach ($user->roles as $value) {
						if(in_array($value, $main_settings['hitshipo_dhlp_v_roles'])){
							$allow = true;
						}
					}
				}

				if(!$allow){
					return;
				}

				$general_settings = get_post_meta($user->ID,'hitshipo_dhlp_vendor_settings',true);
				$general_settings = empty($general_settings) ? array() : $general_settings;
				$countires =  array(
									'AF' => 'Afghanistan',
									'AL' => 'Albania',
									'DZ' => 'Algeria',
									'AS' => 'American Samoa',
									'AD' => 'Andorra',
									'AO' => 'Angola',
									'AI' => 'Anguilla',
									'AG' => 'Antigua and Barbuda',
									'AR' => 'Argentina',
									'AM' => 'Armenia',
									'AW' => 'Aruba',
									'AU' => 'Australia',
									'AT' => 'Austria',
									'AZ' => 'Azerbaijan',
									'BS' => 'Bahamas',
									'BH' => 'Bahrain',
									'BD' => 'Bangladesh',
									'BB' => 'Barbados',
									'BY' => 'Belarus',
									'BE' => 'Belgium',
									'BZ' => 'Belize',
									'BJ' => 'Benin',
									'BM' => 'Bermuda',
									'BT' => 'Bhutan',
									'BO' => 'Bolivia',
									'BA' => 'Bosnia and Herzegovina',
									'BW' => 'Botswana',
									'BR' => 'Brazil',
									'VG' => 'British Virgin Islands',
									'BN' => 'Brunei',
									'BG' => 'Bulgaria',
									'BF' => 'Burkina Faso',
									'BI' => 'Burundi',
									'KH' => 'Cambodia',
									'CM' => 'Cameroon',
									'CA' => 'Canada',
									'CV' => 'Cape Verde',
									'KY' => 'Cayman Islands',
									'CF' => 'Central African Republic',
									'TD' => 'Chad',
									'CL' => 'Chile',
									'CN' => 'China',
									'CO' => 'Colombia',
									'KM' => 'Comoros',
									'CK' => 'Cook Islands',
									'CR' => 'Costa Rica',
									'HR' => 'Croatia',
									'CU' => 'Cuba',
									'CY' => 'Cyprus',
									'CZ' => 'Czech Republic',
									'DK' => 'Denmark',
									'DJ' => 'Djibouti',
									'DM' => 'Dominica',
									'DO' => 'Dominican Republic',
									'TL' => 'East Timor',
									'EC' => 'Ecuador',
									'EG' => 'Egypt',
									'SV' => 'El Salvador',
									'GQ' => 'Equatorial Guinea',
									'ER' => 'Eritrea',
									'EE' => 'Estonia',
									'ET' => 'Ethiopia',
									'FK' => 'Falkland Islands',
									'FO' => 'Faroe Islands',
									'FJ' => 'Fiji',
									'FI' => 'Finland',
									'FR' => 'France',
									'GF' => 'French Guiana',
									'PF' => 'French Polynesia',
									'GA' => 'Gabon',
									'GM' => 'Gambia',
									'GE' => 'Georgia',
									'DE' => 'Germany',
									'GH' => 'Ghana',
									'GI' => 'Gibraltar',
									'GR' => 'Greece',
									'GL' => 'Greenland',
									'GD' => 'Grenada',
									'GP' => 'Guadeloupe',
									'GU' => 'Guam',
									'GT' => 'Guatemala',
									'GG' => 'Guernsey',
									'GN' => 'Guinea',
									'GW' => 'Guinea-Bissau',
									'GY' => 'Guyana',
									'HT' => 'Haiti',
									'HN' => 'Honduras',
									'HK' => 'Hong Kong',
									'HU' => 'Hungary',
									'IS' => 'Iceland',
									'IN' => 'India',
									'ID' => 'Indonesia',
									'IR' => 'Iran',
									'IQ' => 'Iraq',
									'IE' => 'Ireland',
									'IL' => 'Israel',
									'IT' => 'Italy',
									'CI' => 'Ivory Coast',
									'JM' => 'Jamaica',
									'JP' => 'Japan',
									'JE' => 'Jersey',
									'JO' => 'Jordan',
									'KZ' => 'Kazakhstan',
									'KE' => 'Kenya',
									'KI' => 'Kiribati',
									'KW' => 'Kuwait',
									'KG' => 'Kyrgyzstan',
									'LA' => 'Laos',
									'LV' => 'Latvia',
									'LB' => 'Lebanon',
									'LS' => 'Lesotho',
									'LR' => 'Liberia',
									'LY' => 'Libya',
									'LI' => 'Liechtenstein',
									'LT' => 'Lithuania',
									'LU' => 'Luxembourg',
									'MO' => 'Macao',
									'MK' => 'Macedonia',
									'MG' => 'Madagascar',
									'MW' => 'Malawi',
									'MY' => 'Malaysia',
									'MV' => 'Maldives',
									'ML' => 'Mali',
									'MT' => 'Malta',
									'MH' => 'Marshall Islands',
									'MQ' => 'Martinique',
									'MR' => 'Mauritania',
									'MU' => 'Mauritius',
									'YT' => 'Mayotte',
									'MX' => 'Mexico',
									'FM' => 'Micronesia',
									'MD' => 'Moldova',
									'MC' => 'Monaco',
									'MN' => 'Mongolia',
									'ME' => 'Montenegro',
									'MS' => 'Montserrat',
									'MA' => 'Morocco',
									'MZ' => 'Mozambique',
									'MM' => 'Myanmar',
									'NA' => 'Namibia',
									'NR' => 'Nauru',
									'NP' => 'Nepal',
									'NL' => 'Netherlands',
									'NC' => 'New Caledonia',
									'NZ' => 'New Zealand',
									'NI' => 'Nicaragua',
									'NE' => 'Niger',
									'NG' => 'Nigeria',
									'NU' => 'Niue',
									'KP' => 'North Korea',
									'MP' => 'Northern Mariana Islands',
									'NO' => 'Norway',
									'OM' => 'Oman',
									'PK' => 'Pakistan',
									'PW' => 'Palau',
									'PA' => 'Panama',
									'PG' => 'Papua New Guinea',
									'PY' => 'Paraguay',
									'PE' => 'Peru',
									'PH' => 'Philippines',
									'PL' => 'Poland',
									'PT' => 'Portugal',
									'PR' => 'Puerto Rico',
									'QA' => 'Qatar',
									'CG' => 'Republic of the Congo',
									'RE' => 'Reunion',
									'RO' => 'Romania',
									'RU' => 'Russia',
									'RW' => 'Rwanda',
									'SH' => 'Saint Helena',
									'KN' => 'Saint Kitts and Nevis',
									'LC' => 'Saint Lucia',
									'VC' => 'Saint Vincent and the Grenadines',
									'WS' => 'Samoa',
									'SM' => 'San Marino',
									'ST' => 'Sao Tome and Principe',
									'SA' => 'Saudi Arabia',
									'SN' => 'Senegal',
									'RS' => 'Serbia',
									'SC' => 'Seychelles',
									'SL' => 'Sierra Leone',
									'SG' => 'Singapore',
									'SK' => 'Slovakia',
									'SI' => 'Slovenia',
									'SB' => 'Solomon Islands',
									'SO' => 'Somalia',
									'ZA' => 'South Africa',
									'KR' => 'South Korea',
									'SS' => 'South Sudan',
									'ES' => 'Spain',
									'LK' => 'Sri Lanka',
									'SD' => 'Sudan',
									'SR' => 'Suriname',
									'SZ' => 'Swaziland',
									'SE' => 'Sweden',
									'CH' => 'Switzerland',
									'SY' => 'Syria',
									'TW' => 'Taiwan',
									'TJ' => 'Tajikistan',
									'TZ' => 'Tanzania',
									'TH' => 'Thailand',
									'TG' => 'Togo',
									'TO' => 'Tonga',
									'TT' => 'Trinidad and Tobago',
									'TN' => 'Tunisia',
									'TR' => 'Turkey',
									'TC' => 'Turks and Caicos Islands',
									'TV' => 'Tuvalu',
									'VI' => 'U.S. Virgin Islands',
									'UG' => 'Uganda',
									'UA' => 'Ukraine',
									'AE' => 'United Arab Emirates',
									'GB' => 'United Kingdom',
									'US' => 'United States',
									'UY' => 'Uruguay',
									'UZ' => 'Uzbekistan',
									'VU' => 'Vanuatu',
									'VE' => 'Venezuela',
									'VN' => 'Vietnam',
									'YE' => 'Yemen',
									'ZM' => 'Zambia',
									'ZW' => 'Zimbabwe',
								);
				 $_dhlp_carriers = array(
							//domestic
							'220'	=>	'Next Day (Parcel)',
							'221'	=>	'Next Day 12:00 (Parcel)',
							'222'	=>	'Next Day 10:30 (Parcel)',
							'3'		=>	'Next Day 09:00 (Parcel)',
							'225'	=>	'Saturday (Parcel)',
							'226'	=>	'Saturday 10:30 (Parcel)',
							'5'		=>	'Saturday 09:00 (Parcel)',
							'97'	=>	'Pallet 24hrs (Pallets)',
							'98'	=>	'Pallet 48hrs (Pallets)',
							'401'	=>	'Next Day (RTS)',
							'402'	=>	'Next Day 12:00 (RTS)',
							'409'	=>	'Next Day 10:30 (RTS)',
							'403'	=>	'Next Day 09:00 (RTS)',
							'404'	=>	'Saturday (RTS)',
							'240'	=>	'Next Day (Bagit 1kg)',
							'241'	=>	'Next Day 12:00 (Bagit 1kg)',
							'242'	=>	'Next Day 10:30 (Bagit 1kg)',
							'42'	=>	'Next Day 09:00 (Bagit 1kg)',
							'245'	=>	'Saturday (Bagit 1kg)',
							'246'	=>	'Saturday 10:30 (Bagit 1kg)',
							'44'	=>	'Saturday 09:00 (Bagit 1kg)',
							'250'	=>	'Next Day (Bagit 2kg)',
							'251'	=>	'Next Day 12:00 (Bagit 2kg)',
							'252'	=>	'Next Day 10:30 (Bagit 2kg)',
							'32'	=>	'Next Day 09:00 (Bagit 2kg)',
							'255'	=>	'Saturday (Bagit 2kg)',
							'256'	=>	'Saturday 10:30 (Bagit 2kg)',
							'34'	=>	'Saturday 09:00 (Bagit 2kg)',
							'260'	=>	'Next Day (Bagit 5kg)',
							'261'	=>	'Next Day 12:00 (Bagit 5kg)',
							'262'	=>	'Next Day 10:30 (Bagit 5kg)',
							'22'	=>	'Next Day 09:00 (Bagit 5kg)',
							'265'	=>	'Saturday (Bagit 5kg)',
							'266'	=>	'Saturday 10:30 (Bagit 5kg)',
							'24'	=>	'Saturday 09:00 (Bagit 5kg)',
							'270'	=>	'Next Day (Bagit 10kg)',
							'271'	=>	'Next Day 12:00 (Bagit 10kg)',
							'272'	=>	'Next Day 10:30 (Bagit 10kg)',
							'12'	=>	'Next Day 09:00 (Bagit 10kg)',
							'275'	=>	'Saturday (Bagit 10kg)',
							'276'	=>	'Saturday 10:30 (Bagit 10kg)',
							'14'	=>	'Saturday 09:00 (Bagit 10kg)',

							//international

							'101'	=>	'Worldwide Air (Intl)',
							'102'	=>	'DHL Parcel International (Intl)',
							'204'	=>	'International Road Economy (Intl)',
							'206'	=>	'DHL Parcel Connect (Intl)',
							);

			$dhlp_core = array();
			$dhlp_core['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
			$dhlp_core['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
			$dhlp_core['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
			$dhlp_core['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
			$dhlp_core['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
			$dhlp_core['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
			$dhlp_core['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
			$dhlp_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
			$dhlp_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
			$dhlp_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
			$dhlp_core['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
			$dhlp_core['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
			$dhlp_core['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
			$dhlp_core['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
			$dhlp_core['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
			$dhlp_core['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
			$dhlp_core['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
			$dhlp_core['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
			$dhlp_core['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
			$dhlp_core['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
			$dhlp_core['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
			$dhlp_core['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
			$dhlp_core['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
			$dhlp_core['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
			$dhlp_core['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
			$dhlp_core['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
			$dhlp_core['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
			$dhlp_core['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
			$dhlp_core['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
			$dhlp_core['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
			$dhlp_core['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
			$dhlp_core['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
			$dhlp_core['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['CZ'] = array('region' => 'EU', 'currency' =>'CZK', 'weight' => 'KG_CM');
			$dhlp_core['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
			$dhlp_core['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
			$dhlp_core['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
			$dhlp_core['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
			$dhlp_core['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
			$dhlp_core['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
			$dhlp_core['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
			$dhlp_core['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
			$dhlp_core['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GH'] = array('region' => 'AP', 'currency' =>'GBS', 'weight' => 'KG_CM');
			$dhlp_core['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
			$dhlp_core['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
			$dhlp_core['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
			$dhlp_core['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
			$dhlp_core['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
			$dhlp_core['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
			$dhlp_core['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
			$dhlp_core['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
			$dhlp_core['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
			$dhlp_core['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
			$dhlp_core['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
			$dhlp_core['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
			$dhlp_core['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
			$dhlp_core['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
			$dhlp_core['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
			$dhlp_core['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
			$dhlp_core['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
			$dhlp_core['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
			$dhlp_core['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
			$dhlp_core['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
			$dhlp_core['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
			$dhlp_core['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
			$dhlp_core['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
			$dhlp_core['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
			$dhlp_core['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
			$dhlp_core['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
			$dhlp_core['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
			$dhlp_core['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
			$dhlp_core['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
			$dhlp_core['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
			$dhlp_core['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
			$dhlp_core['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
			$dhlp_core['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
			$dhlp_core['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
			$dhlp_core['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
			$dhlp_core['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
			$dhlp_core['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
			$dhlp_core['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
			$dhlp_core['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
			$dhlp_core['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
			$dhlp_core['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
			$dhlp_core['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
			$dhlp_core['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
			$dhlp_core['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
			$dhlp_core['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
			$dhlp_core['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
			$dhlp_core['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
			$dhlp_core['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
			$dhlp_core['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
			$dhlp_core['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
			$dhlp_core['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
			$dhlp_core['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
			$dhlp_core['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
			$dhlp_core['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
			$dhlp_core['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
			$dhlp_core['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
			$dhlp_core['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
			$dhlp_core['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
			$dhlp_core['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
			$dhlp_core['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
			$dhlp_core['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
			$dhlp_core['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
			$dhlp_core['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
			$dhlp_core['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
			$dhlp_core['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
			$dhlp_core['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
			$dhlp_core['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
			$dhlp_core['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
			$dhlp_core['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
			$dhlp_core['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
			$dhlp_core['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
			$dhlp_core['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
			$dhlp_core['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
			$dhlp_core['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
			$dhlp_core['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
			$dhlp_core['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
			$dhlp_core['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
			$dhlp_core['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
			$dhlp_core['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
			$dhlp_core['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
			$dhlp_core['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
			$dhlp_core['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
			$dhlp_core['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
			$dhlp_core['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
			$dhlp_core['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
			$dhlp_core['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
			$dhlp_core['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
			$dhlp_core['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
			$dhlp_core['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
			$dhlp_core['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
			$dhlp_core['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
			$dhlp_core['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
			$dhlp_core['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
			$dhlp_core['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
			$dhlp_core['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
			$dhlp_core['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
			$dhlp_core['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
			$dhlp_core['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
			$dhlp_core['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
			$dhlp_core['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
			$dhlp_core['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
			$dhlp_core['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
			$dhlp_core['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');

				 echo '<hr><h3 class="heading">DHL Parcel - <a href="https://hitshipo.com/" target="_blank">HITShipo</a></h3>';
				    ?>

				    <table class="form-table">
						<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DHL Parcel Integration Team will give this details to you.','hitshipo_dhlp') ?>"></span>	<?php _e('DHL Parcel Username','hitshipo_dhlp') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','hitshipo_dhlp') ?> </p>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_site_id" value="<?php echo (isset($general_settings['hitshipo_dhlp_site_id'])) ? esc_html($general_settings['hitshipo_dhlp_site_id']) : ''; ?>">
						</td>

					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DHL Parcel Integration Team will give this details to you.','hitshipo_dhlp') ?>"></span>	<?php _e('DHL Parcel Password','hitshipo_dhlp') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','hitshipo_dhlp') ?> </p>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_site_pwd" value="<?php echo (isset($general_settings['hitshipo_dhlp_site_pwd'])) ? esc_html($general_settings['hitshipo_dhlp_site_pwd']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DHL Parcel Integration Team will give this details to you.','hitshipo_dhlp') ?>"></span>	<?php _e('DHL Parcel Account Number','hitshipo_dhlp') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','hitshipo_dhlp') ?> </p>
						</td>
						<td>

							<input type="text" name="hitshipo_dhlp_acc_no" value="<?php echo (isset($general_settings['hitshipo_dhlp_acc_no'])) ? esc_html($general_settings['hitshipo_dhlp_acc_no']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DHL Parcel Integration Team will give this details to you.','hitshipo_dhlp') ?>"></span>	<?php _e('DHL Parcel API Key','hitshipo_dhlp') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','hitshipo_dhlp') ?> </p>
						</td>
						<td>

							<input type="text" name="hitshipo_dhlp_access_key" value="<?php echo (isset($general_settings['hitshipo_dhlp_access_key'])) ? esc_html($general_settings['hitshipo_dhlp_access_key']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipping Person Name','hitshipo_dhlp') ?>"></span>	<?php _e('Shipper Name','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_shipper_name" value="<?php echo (isset($general_settings['hitshipo_dhlp_shipper_name'])) ? esc_html($general_settings['hitshipo_dhlp_shipper_name']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Company Name.','hitshipo_dhlp') ?>"></span>	<?php _e('Company Name','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_company" value="<?php echo (isset($general_settings['hitshipo_dhlp_company'])) ? esc_html($general_settings['hitshipo_dhlp_company']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Mobile / Contact Number.','hitshipo_dhlp') ?>"></span>	<?php _e('Contact Number','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_mob_num" value="<?php echo (isset($general_settings['hitshipo_dhlp_mob_num'])) ? esc_html($general_settings['hitshipo_dhlp_mob_num']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Email Address of the Shipper.','hitshipo_dhlp') ?>"></span>	<?php _e('Email Address','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_email" value="<?php echo (isset($general_settings['hitshipo_dhlp_email'])) ? esc_html($general_settings['hitshipo_dhlp_email']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 1 of the Shipper from Address.','hitshipo_dhlp') ?>"></span>	<?php _e('Address Line 1','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_address1" value="<?php echo (isset($general_settings['hitshipo_dhlp_address1'])) ? esc_html($general_settings['hitshipo_dhlp_address1']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 2 of the Shipper from Address.','hitshipo_dhlp') ?>"></span>	<?php _e('Address Line 2','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_address2" value="<?php echo (isset($general_settings['hitshipo_dhlp_address2'])) ? esc_html($general_settings['hitshipo_dhlp_address2']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%;padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the Shipper from address.','hitshipo_dhlp') ?>"></span>	<?php _e('City','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_city" value="<?php echo (isset($general_settings['hitshipo_dhlp_city'])) ? esc_html($general_settings['hitshipo_dhlp_city']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the Shipper from address.','hitshipo_dhlp') ?>"></span>	<?php _e('State (Two Digit String)','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_state" value="<?php echo (isset($general_settings['hitshipo_dhlp_state'])) ? esc_html($general_settings['hitshipo_dhlp_state']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal/Zip Code.','hitshipo_dhlp') ?>"></span>	<?php _e('Postal/Zip Code','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_zip" value="<?php echo (isset($general_settings['hitshipo_dhlp_zip'])) ? esc_html($general_settings['hitshipo_dhlp_zip']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the Shipper from Address.','hitshipo_dhlp') ?>"></span>	<?php _e('Country','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<select name="hitshipo_dhlp_country" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($countires as $key => $value)
								{

									if(isset($general_settings['hitshipo_dhlp_country']) && ($general_settings['hitshipo_dhlp_country'] == $key))
									{
										echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)." [". esc_html($dhlp_core[$key]['currency']) ."]</option>";
									}
									else
									{
										echo "<option value=".esc_html($key).">".esc_html($value)." [". esc_html($dhlp_core[$key]['currency']) ."]</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Conversion Rate from Site Currency to DHL Parcel Currency.','hitshipo_dhlp') ?>"></span>	<?php _e('Conversion Rate from Site Currency to DHL Parcel Currency ( Ignore if auto conversion is Enabled )','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<input type="text" name="hitshipo_dhlp_con_rate" value="<?php echo (isset($general_settings['hitshipo_dhlp_con_rate'])) ? esc_html($general_settings['hitshipo_dhlp_con_rate']) : ''; ?>">
						</td>
					</tr>
					<tr>
						<td>
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose currency that return by DHL Parcel, currency will be converted from this currency to woocommerce currency while showing rates on frontoffice.','hitshipo_dhlp') ?>"></span><?php _e('DHL Parcel Currency Code','hitshipo_dhlp') ?></h4>
						</td>
						<td>
							<select name="hitshipo_dhlp_currency" style="width:153px;">
								<?php foreach($dhlp_core as  $currency)
								{
									if(isset($general_settings['hitshipo_dhlp_currency']) && ($general_settings['hitshipo_dhlp_currency'] == $currency['currency']))
									{
										echo "<option value=".esc_html($currency['currency'])." selected='true'>".esc_html($currency['currency'])."</option>";
									}
									else
									{
										echo "<option value=".esc_html($currency['currency']).">".esc_html($currency['currency'])."</option>";
									}
								}

								if (!isset($general_settings['hitshipo_dhlp_currency']) || ($general_settings['hitshipo_dhlp_currency'] != "NMP")) {
										echo "<option value=NMP>NMP</option>";
								}elseif (isset($general_settings['hitshipo_dhlp_currency']) && ($general_settings['hitshipo_dhlp_currency'] == "NMP")) {
										echo "<option value=NMP selected='true'>NMP</option>";
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default Domestic Shipping Service.','hitshipo_dhlp') ?>"></span>	<?php _e('Default Domestic Service','hitshipo_dhlp') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','hitshipo_dhlp') ?></p>
						</td>
						<td>
							<select name="hitshipo_dhlp_def_dom" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dhlp_carriers as $key => $value)
								{
									if(isset($general_settings['hitshipo_dhlp_def_dom']) && ($general_settings['hitshipo_dhlp_def_dom'] == $key))
									{
										echo "<option value=".esc_html($key)." selected='true'>[".esc_html($key)."] ".esc_html($value)."</option>";
									}
									else
									{
										echo "<option value=".esc_html($key).">[".esc_html($key)."] ".esc_html($value)."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default International Shipping Service.','hitshipo_dhlp') ?>"></span>	<?php _e('Default International Service','hitshipo_dhlp') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','hitshipo_dhlp') ?></p>
						</td>
						<td>
							<select name="hitshipo_dhlp_def_inter" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dhlp_carriers as $key => $value)
								{
									if(isset($general_settings['hitshipo_dhlp_def_inter']) && ($general_settings['hitshipo_dhlp_def_inter'] == $key))
									{
										echo "<option value=".esc_html($key)." selected='true'>[".esc_html($key)."] ".esc_html($value)."</option>";
									}
									else
									{
										echo "<option value=".esc_html($key).">[".esc_html($key)."] ".esc_html($value)."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				    </table>
				    <hr>
				    <?php
			}

			public function save_user_fields($user_id){
				if(isset($_POST['hitshipo_dhlp_country'])){
					$general_settings['hitshipo_dhlp_site_id'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_site_id']) ? $_POST['hitshipo_dhlp_site_id'] : '');
					$general_settings['hitshipo_dhlp_site_pwd'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_site_pwd']) ? $_POST['hitshipo_dhlp_site_pwd'] : '');
					$general_settings['hitshipo_dhlp_acc_no'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_acc_no']) ? $_POST['hitshipo_dhlp_acc_no'] : '');
					$general_settings['hitshipo_dhlp_access_key'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_access_key']) ? $_POST['hitshipo_dhlp_access_key'] : '');
					$general_settings['hitshipo_dhlp_shipper_name'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shipper_name']) ? $_POST['hitshipo_dhlp_shipper_name'] : '');
					$general_settings['hitshipo_dhlp_company'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_company']) ? $_POST['hitshipo_dhlp_company'] : '');
					$general_settings['hitshipo_dhlp_mob_num'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_mob_num']) ? $_POST['hitshipo_dhlp_mob_num'] : '');
					$general_settings['hitshipo_dhlp_email'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_email']) ? $_POST['hitshipo_dhlp_email'] : '');
					$general_settings['hitshipo_dhlp_address1'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_address1']) ? $_POST['hitshipo_dhlp_address1'] : '');
					$general_settings['hitshipo_dhlp_address2'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_address2']) ? $_POST['hitshipo_dhlp_address2'] : '');
					$general_settings['hitshipo_dhlp_city'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_city']) ? $_POST['hitshipo_dhlp_city'] : '');
					$general_settings['hitshipo_dhlp_state'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_state']) ? $_POST['hitshipo_dhlp_state'] : '');
					$general_settings['hitshipo_dhlp_zip'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_zip']) ? $_POST['hitshipo_dhlp_zip'] : '');
					$general_settings['hitshipo_dhlp_country'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_country']) ? $_POST['hitshipo_dhlp_country'] : '');
					// $general_settings['hitshipo_dhlp_gstin'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_gstin']) ? $_POST['hitshipo_dhlp_gstin'] : '');
					$general_settings['hitshipo_dhlp_con_rate'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_con_rate']) ? $_POST['hitshipo_dhlp_con_rate'] : '');
					$general_settings['hitshipo_dhlp_currency'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_currency']) ? $_POST['hitshipo_dhlp_currency'] : '');
					$general_settings['hitshipo_dhlp_def_dom'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_def_dom']) ? $_POST['hitshipo_dhlp_def_dom'] : '');

					$general_settings['hitshipo_dhlp_def_inter'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_def_inter']) ? $_POST['hitshipo_dhlp_def_inter'] : '');

					update_post_meta($user_id,'hitshipo_dhlp_vendor_settings',$general_settings);
				}

			}

			public function hitshipo_dhlp_init()
			{
				include_once("controllors/hitshipo_dhlp_init.php");
			}
			public function hitshipo_dhlp_method( $methods )
			{
				$methods['hitshipo_dhlp'] = 'hitshipo_dhlp';
				return $methods;
			}
			public function hitshipo_dhlp_plugin_action_links($links)
			{
				$setting_value = version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=' . $setting_value  . '&tab=shipping&section=hitshipo_dhlp' ) . '" style="color:green;">' . __( 'Configure', 'hitshipo_dhlp' ) . '</a>',
					'<a href="https://app.hitshipo.com/support" target="_blank" >' . __('Support', 'hitshipo_dhlp') . '</a>'
					);
				return array_merge( $plugin_links, $links );
			}

			public function create_dhlp_shipping_meta_box() {
	       		add_meta_box( 'hitshippo_create_dhlp_shipping', __('Automated DHL Parcel Shipping Label','hitshipo_dhlp'), array($this, 'create_dhlp_shipping_label_genetation'), 'shop_order', 'side', 'core' );
		    }

		    public function hitshipo_dhlp_order_status_update(){
		    	global $woocommerce;
				if(isset($_GET['hitshipo_key'])){
					$hitshipo_key = sanitize_text_field($_GET['hitshipo_key']);
					if($hitshipo_key == 'fetch' && get_transient('hitshipo_dhlp_express_nonce_temp')){
						echo json_encode(array(get_transient('hitshipo_dhlp_express_nonce_temp')));
						die();
					}
				}

				if(isset($_GET['hitshipo_integration_key']) && isset($_GET['hitshipo_action'])){
					$integration_key = sanitize_text_field($_GET['hitshipo_integration_key']);
					$hitshipo_action = sanitize_text_field($_GET['hitshipo_action']);
					$general_settings = get_option('hitshipo_dhlp_main_settings');
					$general_settings = empty($general_settings) ? array() : $general_settings;
					if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $integration_key == $general_settings['hitshipo_dhlp_shippo_int_key']){
						if($hitshipo_action == 'stop_working'){
							update_option('hitshipo_dhlp_working_status', 'stop_working');
						}else if ($hitshipo_action = 'start_working'){
							update_option('hitshipo_dhlp_working_status', 'start_working');
						}
					}
					
				}

		    	if (isset($_GET['carrier']) && $_GET['carrier'] == "dhl_p") {
				if(isset($_GET['h1t_updat3_0rd3r']) && isset($_GET['key']) && isset($_GET['action'])){
					$order_id = sanitize_text_field($_GET['h1t_updat3_0rd3r']);
					$key = sanitize_text_field($_GET['key']);
					$action = sanitize_text_field($_GET['action']);
					$order_ids = explode(",",$order_id);
					$general_settings = get_option('hitshipo_dhlp_main_settings',array());

					if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] == $key){
						if($action == 'processing'){
							foreach ($order_ids as $order_id) {
								$order = wc_get_order( $order_id );
								$order->update_status( 'processing' );
							}
						}else if($action == 'completed'){
							foreach ($order_ids as $order_id) {
								  $order = wc_get_order( $order_id );
								  $order->update_status( 'completed' );

							}
						}
					}
					die();
				}

				if(isset($_GET['h1t_updat3_sh1pp1ng']) && isset($_GET['key']) && isset($_GET['user_id']) && isset($_GET['carrier']) && isset($_GET['track'])){

					$order_id = sanitize_text_field($_GET['h1t_updat3_sh1pp1ng']);
					$key = sanitize_text_field($_GET['key']);
					$general_settings = get_option('hitshipo_dhlp_main_settings',array());
					$user_id = sanitize_text_field($_GET['user_id']);
					$carrier = sanitize_text_field($_GET['carrier']);
					$track = sanitize_text_field($_GET['track']);
					$output['status'] = 'success';
					$output['tracking_num'] = $track;
					// $output['label'] = "localhost/hitshippo/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_label.pdf";
					// $output['invoice'] = "localhost/hitshippo/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_invoice.pdf";
					$output['label'] = "https://app.hitshipo.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_label.pdf";
					if (isset($_GET['inv_data']) && $_GET['inv_data'] == "yes") {
						$output['invoice'] = "https://app.hitshipo.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_invoice.pdf";
					}
					
					$result_arr = array();

					if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] == $key){

						if(isset($_GET['label'])){
							$output['user_id'] = sanitize_text_field($_GET['label']);
							$val = get_option('hitshipo_dhlp_values_'.$order_id, []);
							$result_arr = array();
							if(!empty($val)){
								$result_arr = json_decode($val, true);
							}
							$result_arr[] = $output;

						}else{
							$result_arr[] = $output;
						}

						update_option('hitshipo_dhlp_values_'.$order_id, json_encode($result_arr));
					}
					die();
				}
		    }
		}
		    public function create_dhlp_shipping_label_genetation($post){
		    	// print_r('expression');
		    	// die();
		        if($post->post_type !='shop_order' ){
		    		return;
		    	}
		    	$order = wc_get_order( $post->ID );
		    	$ship_met = $order->get_shipping_methods();

		    	$order_id = $order->get_id();
		        $_dhlp_carriers = array(
							//domestic
							'220'	=>	'Next Day (Parcel)',
							'221'	=>	'Next Day 12:00 (Parcel)',
							'222'	=>	'Next Day 10:30 (Parcel)',
							'3'		=>	'Next Day 09:00 (Parcel)',
							'225'	=>	'Saturday (Parcel)',
							'226'	=>	'Saturday 10:30 (Parcel)',
							'5'		=>	'Saturday 09:00 (Parcel)',
							'97'	=>	'Pallet 24hrs (Pallets)',
							'98'	=>	'Pallet 48hrs (Pallets)',
							'401'	=>	'Next Day (RTS)',
							'402'	=>	'Next Day 12:00 (RTS)',
							'409'	=>	'Next Day 10:30 (RTS)',
							'403'	=>	'Next Day 09:00 (RTS)',
							'404'	=>	'Saturday (RTS)',
							'240'	=>	'Next Day (Bagit 1kg)',
							'241'	=>	'Next Day 12:00 (Bagit 1kg)',
							'242'	=>	'Next Day 10:30 (Bagit 1kg)',
							'42'	=>	'Next Day 09:00 (Bagit 1kg)',
							'245'	=>	'Saturday (Bagit 1kg)',
							'246'	=>	'Saturday 10:30 (Bagit 1kg)',
							'44'	=>	'Saturday 09:00 (Bagit 1kg)',
							'250'	=>	'Next Day (Bagit 2kg)',
							'251'	=>	'Next Day 12:00 (Bagit 2kg)',
							'252'	=>	'Next Day 10:30 (Bagit 2kg)',
							'32'	=>	'Next Day 09:00 (Bagit 2kg)',
							'255'	=>	'Saturday (Bagit 2kg)',
							'256'	=>	'Saturday 10:30 (Bagit 2kg)',
							'34'	=>	'Saturday 09:00 (Bagit 2kg)',
							'260'	=>	'Next Day (Bagit 5kg)',
							'261'	=>	'Next Day 12:00 (Bagit 5kg)',
							'262'	=>	'Next Day 10:30 (Bagit 5kg)',
							'22'	=>	'Next Day 09:00 (Bagit 5kg)',
							'265'	=>	'Saturday (Bagit 5kg)',
							'266'	=>	'Saturday 10:30 (Bagit 5kg)',
							'24'	=>	'Saturday 09:00 (Bagit 5kg)',
							'270'	=>	'Next Day (Bagit 10kg)',
							'271'	=>	'Next Day 12:00 (Bagit 10kg)',
							'272'	=>	'Next Day 10:30 (Bagit 10kg)',
							'12'	=>	'Next Day 09:00 (Bagit 10kg)',
							'275'	=>	'Saturday (Bagit 10kg)',
							'276'	=>	'Saturday 10:30 (Bagit 10kg)',
							'14'	=>	'Saturday 09:00 (Bagit 10kg)',

							//international

							'101'	=>	'Worldwide Air (Intl)',
							'102'	=>	'DHL Parcel International (Intl)',
							'204'	=>	'International Road Economy (Intl)',
							'206'	=>	'DHL Parcel Connect (Intl)',
							);

		        $general_settings = get_option('hitshipo_dhlp_main_settings',array());

		        $items = $order->get_items();

    		    $custom_settings = array();
		    	$custom_settings['default'] =  array();
		    	$vendor_settings = array();

		    	$pack_products = array();

				foreach ( $items as $item ) {
					$product_data = $item->get_data();
				    $product = array();
				    $product['product_name'] = $product_data['name'];
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];

				    $pack_products[] = $product;

				}

				if(isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes' && isset($general_settings['hitshipo_dhlp_v_labels']) && $general_settings['hitshipo_dhlp_v_labels'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						$dhlp_account = get_post_meta($product_id,'dhlp_address', true);
						if(empty($dhlp_account) || $dhlp_account == 'default'){
							$dhlp_account = 'default';
							$vendor_settings[$dhlp_account] = $custom_settings['default'];
							$vendor_settings[$dhlp_account]['products'][] = $value;
						}

						if($dhlp_account != 'default'){
							$user_account = get_post_meta($dhlp_account,'hitshipo_dhlp_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dhlp_account])){

									$vendor_settings[$dhlp_account] = $custom_settings['default'];
									unset($value['product_id']);
									$vendor_settings[$dhlp_account]['products'][] = $value;
								}
							}else{
								$dhlp_account = 'default';
								$vendor_settings[$dhlp_account] = $custom_settings['default'];
								$vendor_settings[$dhlp_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}
// echo '<pre>';print_r($custom_settings);die();
		       	$shipment_data = json_decode(get_option('hitshipo_dhlp_values_'.$order_id), true); // using "true" to convert stdobject to array
		       	$notice = get_option('hitshipo_dhlp_status_'.$order_id, null);
		       	// echo '<pre>';
		       	// print_r($shipment_data);
		       	// echo '<h3>Notice</h3>';
		       	// print_r($notice);
		       	// die();

		       	if ($notice && $notice == 'success') {
			       	echo "<p style='color:green'>Shipment created successfully</p>";
			       	delete_option('hitshipo_dhlp_status_'.$order_id);
			    }elseif($notice && $notice != 'success'){
			       	echo "<p style='color:red'>".esc_html($notice)."</p>";
			       	delete_option('hitshipo_dhlp_status_'.$order_id);
			    }

		       	if(!empty($shipment_data)){
		       		if(isset($shipment_data[0])){
			       		foreach ($shipment_data as $key => $value) {
			       			if(isset($value['user_id'])){
		       					unset($custom_settings[$value['user_id']]);
		       				}
		       				if(isset($value['user_id']) && $value['user_id'] == 'default'){
		       					echo '<br/><b>Default Account</b><br/>';
		       				}else{
		       					$user = get_user_by( 'id', $value['user_id'] );
		       					echo '<br/><b>Account:</b> <small>'.esc_html($user->display_name).'</small><br/>';
		       				}
			       			echo '<b>Shipment ID: <font style = "color:green;">'.esc_html($value['tracking_num']).'</font></b>';
				       		echo '<a href="'.esc_url($value['label']).'" target="_blank" style="background:#FFCC00; color: #D40511;border-color: #FFCC00;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511; margin-top: 5px;" class="button button-primary"> Shipping Label '.esc_html($key).' </a> ';
				       		if (isset($value['invoice'])) {
				       			echo '<a href="'.esc_url($value['invoice']).'" target="_blank" style = "margin-top: 5px;" class="button button-primary"> Invoice </a>';
				       		}
			       		}
			        }else {
			        	$custom_settings = array();
			        	echo '<b>Shipment ID: <font style = "color:green;">'.esc_html($shipment_data['tracking_num']).'</font></b>';
			       		echo '<a href="'.esc_url($shipment_data['label']).'" target="_blank" style="background:#FFCC00; color: #D40511;border-color: #FFCC00;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511; margin-top: 5px;" class="button button-primary"> Shipping Label '.esc_html($key).' </a> ';
			       		if ($shipment_data['invoice']) {
			       			echo '<a href="'.esc_url($shipment_data['invoice']).'" target="_blank" style = "margin-top: 5px;" class="button button-primary"> Invoice </a>';
			       		}
			        }
			        echo '<br/><br/> <button name="hitshipo_dhlp_reset" class="button button-secondary" style = "margin-top: 5px;"> Reset All </button><br/>';
		       	}
// echo '<pre>';print_r($shipment_data);die();
		       	foreach ($custom_settings as $ukey => $value) {

						if(!empty($shipment_data) && isset($shipment_data[0])){
				       		foreach ($shipment_data as $value) {
				       			if ($value['user_id'] == $ukey) {
				       				continue;
				       			}
				       		}
						}elseif(!empty($shipment_data) && $shipment_data['user_id'] == $ukey){
							continue;
						}

		       			if($ukey == 'default'){

		       				echo '<br/><u><b>Default Account</b></u>';
					        echo '<br/><br/><b>Choose Service to Ship</b>';
					        echo '<br/><select name="hitshipo_dhlp_service_code_default">';
					        if(!empty($general_settings['hitshipo_dhlp_carrier'])){
					        	foreach ($general_settings['hitshipo_dhlp_carrier'] as $key => $value) {
					        		echo "<option value='".esc_html($key)."'>".esc_html($_dhlp_carriers[$key])."</option>";
					        	}
					        }
					        echo '</select>';

					        echo '<br/><b>Shipment Content</b>';
					        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hitshipo_dhlp_shipment_content_default" value="Shipment Number ' . esc_html($order_id) . '" >';
					        echo '<button name="hitshipo_dhlp_create_label" value="default" style="background:#FFCC00; color: #D40511;border-color: #FFCC00;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;" class="button button-primary">Create Shipment</button><br/>';
		       			}else {
		       				$user = get_user_by( 'id', $ukey );
		       				echo '<br/><u><b>Account:</b> <small>'.esc_html($user->display_name).'</small></u>';
		       				echo '<br/><br/><b>Choose Service to Ship</b>';
					        echo '<br/><select name="hitshipo_dhlp_service_code_'.esc_html($ukey).'">';
					        if(!empty($general_settings['hitshipo_dhlp_carrier'])){
					        	foreach ($general_settings['hitshipo_dhlp_carrier'] as $key => $value) {
					        		echo "<option value='".esc_html($key)."'>".esc_html($_dhlp_carriers[$key])."</option>";
					        	}
					        }
					        echo '</select>';

					        echo '<br/><b>Shipment Content</b>';
					        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hitshipo_dhlp_shipment_content_'.esc_html($ukey).'" value="Shipment Number ' . esc_html($order_id) . '" >';
					        echo '<button name="hitshipo_dhlp_create_label" value="'.esc_html($ukey).'" style="background:#FFCC00; color: #D40511;border-color: #FFCC00;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;" class="button button-primary">Create Shipment</button><br/>';
		       			}
		       		}
		    }

		public function hitshipo_dhlp_wc_checkout_order_processed($order_id){
		    	$post = get_post($order_id);

		    	if($post->post_type !='shop_order' ){
		    		return;
		    	}
		        $order = wc_get_order( $order_id );
		        $service_code = $multi_ven ='';
				$shipping_charge = 0;
		  //       foreach( $order->get_shipping_methods() as $item_id => $item ){
				// 	$service_code = $item->get_meta('hitshipo_dhlp_service');
				// 	$shipping_charge = $item->get_meta('hitshipo_dhlp_shipping_charge');
				// 	$multi_ven = $item->get_meta('hitshipo_dhlp_multi_ven');
				// }

				$general_settings = get_option('hitshipo_dhlp_main_settings',array());
		    	$order_data = $order->get_data();
		    	$items = $order->get_items();

		    	if(!isset($general_settings['hitshipo_dhlp_shippo_label_gen']) || $general_settings['hitshipo_dhlp_shippo_label_gen'] != 'yes'){
		    		return;
		    	}

		    	$service_code = $general_settings['hitshipo_dhlp_intl_srvc'];
				$shipping_charge = $order_data['shipping_total'];

	       		$order_id = $order_data['id'];
	       		$order_currency = $order_data['currency'];

	       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
				// $order_shipping_last_name = $order_data['shipping']['last_name'];
				// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
				// $order_shipping_address_1 = $order_data['shipping']['address_1'];
				// $order_shipping_address_2 = $order_data['shipping']['address_2'];
				// $order_shipping_city = $order_data['shipping']['city'];
				// $order_shipping_state = $order_data['shipping']['state'];
				// $order_shipping_postcode = $order_data['shipping']['postcode'];
				// $order_shipping_country = $order_data['shipping']['country'];
				// $order_shipping_phone = $order_data['billing']['phone'];
				// $order_shipping_email = $order_data['billing']['email'];

				$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
                $order_shipping_first_name = $shipping_arr['first_name'];
                $order_shipping_last_name = $shipping_arr['last_name'];
                $order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
                $order_shipping_address_1 = $shipping_arr['address_1'];
                $order_shipping_address_2 = $shipping_arr['address_2'];
                $order_shipping_city = $shipping_arr['city'];
                $order_shipping_state = $shipping_arr['state'];
                $order_shipping_postcode = $shipping_arr['postcode'];
                $order_shipping_country = $shipping_arr['country'];
                $order_shipping_phone = $order_data['billing']['phone'];
                $order_shipping_email = $order_data['billing']['email'];

				if ($order_shipping_country == "GB") {
					return;
				}
				$pack_products = array();
				$total_weg = 0;

				//weight conversion wc_get_weight( $weight, $to_unit, $from_unit )
				// $general_settings = get_option('hit_ups_auto_main_settings',array());
				$woo_weg_unit = get_option('woocommerce_weight_unit');
				$woo_dim_unit = get_option('woocommerce_dimension_unit');
				$config_weg_unit = $general_settings['hitshipo_dhlp_weight_unit'];
				$mod_weg_unit = (!empty($config_weg_unit) && $config_weg_unit == 'LB_IN') ? 'lbs' : 'kg';
				$mod_dim_unit = (!empty($config_weg_unit) && $config_weg_unit == 'LB_IN') ? 'in' : 'cm';

				foreach ( $items as $item ) {
					$product_data = $item->get_data();
				    $product = array();
				    $product['product_name'] = str_replace('"', '', $product_data['name']);
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];

				    $product_variation_id = $item->get_variation_id();
				    if(empty($product_variation_id)){
				    	$getproduct = wc_get_product( $product_data['product_id'] );
				    }else{
				    	$getproduct = wc_get_product( $product_variation_id );
				    }

				    $product['price'] = $getproduct->get_price();
				    $product['width'] = (!empty($getproduct->get_width())) ? round(wc_get_dimension($getproduct->get_width(),$mod_dim_unit,$woo_dim_unit)) : '';
				    $product['height'] = (!empty($getproduct->get_height())) ? round(wc_get_dimension($getproduct->get_height(),$mod_dim_unit,$woo_dim_unit)) : '';
				    $product['depth'] = (!empty($getproduct->get_length())) ? round(wc_get_dimension($getproduct->get_length(),$mod_dim_unit,$woo_dim_unit)) : '';
					$product['weight'] = (!empty($getproduct->get_weight())) ? (float)round(wc_get_weight($getproduct->get_weight(),$mod_weg_unit,$woo_weg_unit),2) : '';
					$total_weg += (!empty($product['weight'])) ? $product['weight'] : 0;
				    $pack_products[] = $product;

				}

				$custom_settings = array();
				$custom_settings['default'] = array(
												'hitshipo_dhlp_site_id' => $general_settings['hitshipo_dhlp_site_id'],
												'hitshipo_dhlp_site_pwd' => $general_settings['hitshipo_dhlp_site_pwd'],
												'hitshipo_dhlp_acc_no' => $general_settings['hitshipo_dhlp_acc_no'],
												'hitshipo_dhlp_access_key' => $general_settings['hitshipo_dhlp_access_key'],
												'hitshipo_dhlp_shipper_name' => $general_settings['hitshipo_dhlp_shipper_name'],
												'hitshipo_dhlp_company' => $general_settings['hitshipo_dhlp_company'],
												'hitshipo_dhlp_mob_num' => $general_settings['hitshipo_dhlp_mob_num'],
												'hitshipo_dhlp_email' => $general_settings['hitshipo_dhlp_email'],
												'hitshipo_dhlp_address1' => $general_settings['hitshipo_dhlp_address1'],
												'hitshipo_dhlp_address2' => $general_settings['hitshipo_dhlp_address2'],
												'hitshipo_dhlp_city' => $general_settings['hitshipo_dhlp_city'],
												'hitshipo_dhlp_state' => $general_settings['hitshipo_dhlp_state'],
												'hitshipo_dhlp_zip' => $general_settings['hitshipo_dhlp_zip'],
												'hitshipo_dhlp_country' => $general_settings['hitshipo_dhlp_country'],
												'hitshipo_dhlp_con_rate' => isset($general_settings['hitshipo_dhlp_con_rate']) ? $general_settings['hitshipo_dhlp_con_rate'] : '',
												'service_code' => $service_code,
												'hitshipo_dhlp_shippo_mail' => $general_settings['hitshipo_dhlp_shippo_mail'],
												'hitshipo_dhlp_currency' => $general_settings['hitshipo_dhlp_currency']
											);

				$vendor_settings = array();

				if(isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes' && isset($general_settings['hitshipo_dhlp_v_labels']) && $general_settings['hitshipo_dhlp_v_labels'] == 'yes'){
					// Multi Vendor Enabled

					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						$dhlp_account = get_post_meta($product_id,'dhlp_address', true);
						if(empty($dhlp_account) || $dhlp_account == 'default'){
							$dhlp_account = 'default';
							if (!isset($vendor_settings[$dhlp_account])) {
								$vendor_settings[$dhlp_account] = $custom_settings['default'];
							}
							$vendor_settings[$dhlp_account]['products'][] = $value;
						}

						if($dhlp_account != 'default'){
							$user_account = get_post_meta($dhlp_account,'hitshipo_dhlp_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dhlp_account])){

									$vendor_settings[$dhlp_account] = $custom_settings['default'];

									if($user_account['hitshipo_dhlp_site_id'] != '' && $user_account['hitshipo_dhlp_site_pwd'] != '' && $user_account['hitshipo_dhlp_acc_no'] != '' && $user_account['hitshipo_dhlp_access_key'] != ''){
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_site_id'] = $user_account['hitshipo_dhlp_site_id'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_site_pwd'] = $user_account['hitshipo_dhlp_site_pwd'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_acc_no'] = $user_account['hitshipo_dhlp_acc_no'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_access_key'] = $user_account['hitshipo_dhlp_access_key'];
									}

									if ($user_account['hitshipo_dhlp_shipper_name'] != '' && $user_account['hitshipo_dhlp_address1'] != '' && $user_account['hitshipo_dhlp_city'] != '' && $user_account['hitshipo_dhlp_state'] != '' && $user_account['hitshipo_dhlp_zip'] != '' && $user_account['hitshipo_dhlp_country'] != ''){

										if($user_account['hitshipo_dhlp_shipper_name'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_shipper_name'] = $user_account['hitshipo_dhlp_shipper_name'];
										}

										if($user_account['hitshipo_dhlp_company'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_company'] = $user_account['hitshipo_dhlp_company'];
										}

										if($user_account['hitshipo_dhlp_mob_num'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_mob_num'] = $user_account['hitshipo_dhlp_mob_num'];
										}

										if($user_account['hitshipo_dhlp_email'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_email'] = $user_account['hitshipo_dhlp_email'];
										}

										if($user_account['hitshipo_dhlp_address1'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_address1'] = $user_account['hitshipo_dhlp_address1'];
										}

										$vendor_settings[$dhlp_account]['hitshipo_dhlp_address2'] = !empty($user_account['hitshipo_dhlp_address2']) ? $user_account['hitshipo_dhlp_address2'] : '';

										if($user_account['hitshipo_dhlp_city'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_city'] = $user_account['hitshipo_dhlp_city'];
										}

										if($user_account['hitshipo_dhlp_state'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_state'] = $user_account['hitshipo_dhlp_state'];
										}

										if($user_account['hitshipo_dhlp_zip'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_zip'] = $user_account['hitshipo_dhlp_zip'];
										}

										if($user_account['hitshipo_dhlp_country'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_country'] = $user_account['hitshipo_dhlp_country'];
										}
										if (isset($user_account['hitshipo_dhlp_con_rate'])) {
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_con_rate'] = $user_account['hitshipo_dhlp_con_rate'];
										}
										if (isset($user_account['hitshipo_dhlp_currency'])) {
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_currency'] = $user_account['hitshipo_dhlp_currency'];
										}

									}

									if(isset($general_settings['hitshipo_dhlp_v_email']) && $general_settings['hitshipo_dhlp_v_email'] == 'yes'){
										$user_dat = get_userdata($dhlp_account);
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_shippo_mail'] = $user_dat->data->user_email;
									}


									if($multi_ven !=''){
										$array_ven = explode('|',$multi_ven);
										$scode = '';
										foreach ($array_ven as $key => $svalue) {
											$ex_service = explode("_", $svalue);
											if($ex_service[0] == $dhlp_account){
												$vendor_settings[$dhlp_account]['service_code'] = $ex_service[1];
											}
										}

										if($scode == ''){
											if($order_data['shipping']['country'] != $vendor_settings[$dhlp_account]['hitshipo_dhlp_country']){
												$vendor_settings[$dhlp_account]['service_code'] = $user_account['hitshipo_dhlp_def_inter'];
											}else{
												$vendor_settings[$dhlp_account]['service_code'] = $user_account['hitshipo_dhlp_def_dom'];
											}
										}

									}else{
										if($order_data['shipping']['country'] != $vendor_settings[$dhlp_account]['hitshipo_dhlp_country']){
											$vendor_settings[$dhlp_account]['service_code'] = $user_account['hitshipo_dhlp_def_inter'];
										}else{
											$vendor_settings[$dhlp_account]['service_code'] = $user_account['hitshipo_dhlp_def_dom'];
										}

									}
								}
								unset($value['product_id']);
								$vendor_settings[$dhlp_account]['products'][] = $value;
							}else {
								$dhlp_account = 'default';
								if (!isset($vendor_settings[$dhlp_account])) {
									$vendor_settings[$dhlp_account] = $custom_settings['default'];
								}
								$vendor_settings[$dhlp_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}
// echo '<pre>';print_r($general_settings);echo '<br/><h3>Custom</h3>';print_r($custom_settings); die();
		    	$ship_content = !empty($general_settings['hitshipo_dhlp_shipment_content']) ? $general_settings['hitshipo_dhlp_shipment_content'] : 'Shipment Content';
				if(!empty($general_settings) && isset($general_settings['hitshipo_dhlp_shippo_int_key'])){
					$mode = 'live';
					if(isset($general_settings['hitshipo_dhlp_test']) && $general_settings['hitshipo_dhlp_test']== 'yes'){
						$mode = 'test';
					}
					$execution = 'manual';
					if(isset($general_settings['hitshipo_dhlp_shippo_label_gen']) && $general_settings['hitshipo_dhlp_shippo_label_gen']== 'yes'){
						$execution = 'auto';
					}

					$boxes_to_shipo = array();
					if (isset($general_settings['hitshipo_dhlp_packing_type']) && $general_settings['hitshipo_dhlp_packing_type'] == "box") {
						if (isset($general_settings['hitshipo_dhlp_boxes']) && !empty($general_settings['hitshipo_dhlp_boxes'])) {
							foreach ($general_settings['hitshipo_dhlp_boxes'] as $box) {
								if ($box['enabled'] != 1) {
									continue;
								}else {
									$boxes_to_shipo[] = $box;
								}
							}
						}
					}


						foreach ($custom_settings as $key => $cvalue) {

							$data = array();
							$data['integrated_key'] = $general_settings['hitshipo_dhlp_shippo_int_key'];
							$data['order_id'] = $order_id;
							$data['exec_type'] = $execution;
							$data['mode'] = $mode;
							$data['carrier_type'] = "dhl_p";
							$data['ship_price'] = $order_data['shipping_total'];
							$data['meta'] = array(
								"site_id" => $cvalue['hitshipo_dhlp_site_id'],
								"password"  => $cvalue['hitshipo_dhlp_site_pwd'],
								"accountnum" => $cvalue['hitshipo_dhlp_acc_no'],
								"api_key" => $cvalue['hitshipo_dhlp_access_key'],
								"t_company" => $order_shipping_company,
								"t_address1" => $order_shipping_address_1,
								"t_address2" => $order_shipping_address_2,
								"t_city" => $order_shipping_city,
								"t_state" => $order_shipping_state,
								"t_postal" => $order_shipping_postcode,
								"t_country" => $order_shipping_country,
								"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
								"t_phone" => $order_shipping_phone,
								"t_email" => $order_shipping_email,
								"shipping_charge" => $shipping_charge,
								"products" => $cvalue['products'],
								"pack_algorithm" => $general_settings['hitshipo_dhlp_packing_type'],
								"boxes" => $boxes_to_shipo,
								"max_weight" => $general_settings['hitshipo_dhlp_max_weight'],
								"wight_dim_unit" => $general_settings['hitshipo_dhlp_weight_unit'],
								"total_product_weg" => $total_weg,
								"service_code" => $service_code,
								"shipment_content" => $ship_content,
								"s_company" => $cvalue['hitshipo_dhlp_company'],
								"s_address1" => $cvalue['hitshipo_dhlp_address1'],
								"s_address2" => $cvalue['hitshipo_dhlp_address2'],
								"s_city" => $cvalue['hitshipo_dhlp_city'],
								"s_state" => $cvalue['hitshipo_dhlp_state'],
								"s_postal" => $cvalue['hitshipo_dhlp_zip'],
								"s_country" => $cvalue['hitshipo_dhlp_country'],
								// "gstin" => $general_settings['hitshipo_dhlp_gstin'],
								"s_name" => $cvalue['hitshipo_dhlp_shipper_name'],
								"s_phone" => $cvalue['hitshipo_dhlp_mob_num'],
								"s_email" => $cvalue['hitshipo_dhlp_email'],
								"label_format" => "PDF",
								"label_size" => $general_settings['hitshipo_dhlp_label_size'],
								"sent_email_to" => $cvalue['hitshipo_dhlp_shippo_mail'],
								"label" => $key,
								"woo_curr" => get_option('woocommerce_currency'),
								"dhlp_curr" => $cvalue['hitshipo_dhlp_currency'],
								"con_rate" => $cvalue['hitshipo_dhlp_con_rate'],
								"ext_cover" => $general_settings['hitshipo_dhlp_ext_cover'],
								"close_lunch" => $general_settings['hitshipo_dhlp_lunch'],
								"cover_units" => $general_settings['hitshipo_dhlp_ext_cover_units'],
								"cc" => $general_settings['hitshipo_dhlp_shipment_cc'],
								"e_time" => $general_settings['hitshipo_dhlp_e_time'],
								"l_time"=> $general_settings['hitshipo_dhlp_l_time'],
								"col_aftr" => $general_settings['hitshipo_dhlp_col_aftr'],
								"tod" => ($service_code == 101) ? "DAP" : "DDP",
							);

							  // echo '<pre>';
							  // print_r($data);
							  // die();
							// $run = 0;
							// if ($run == 1) {
							// auto 
							$auto_ship_url = "https://app.hitshipo.com/label_api/create_shipment.php";
							// $auto_ship_url = "http://localhost/hitshippo/label_api/create_shipment.php";
							wp_remote_post( $auto_ship_url , array(
								'method'      => 'POST',
								'timeout'     => 45,
								'redirection' => 5,
								'httpversion' => '1.0',
								'blocking'    => false,
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
								'body'        => json_encode($data),
								'sslverify'   => FALSE
								)
							);
							
							// }

						}
					}
			}

		public function hitshippo_create_dhlp_shipping($order_id){
			$post = get_post($order_id);
		    	if($post->post_type !='shop_order' ){
		    		return;
		    	}

		    	if (  isset( $_POST[ 'hitshipo_dhlp_reset' ] ) ) {
		    		delete_option('hitshipo_dhlp_values_'.$order_id);
		    	}

		    	if (isset($_POST['hitshipo_dhlp_create_label'])) {
		    		$create_shipment_for = sanitize_text_field($_POST['hitshipo_dhlp_create_label']);

		    		$service_code = sanitize_text_field($_POST['hitshipo_dhlp_service_code_'.$create_shipment_for]);
		        	$ship_content = !empty($_POST['hitshipo_dhlp_shipment_content_'.$create_shipment_for]) ? sanitize_text_field($_POST['hitshipo_dhlp_shipment_content_'.$create_shipment_for]) : 'Shipment Content';

					$order = wc_get_order( $order_id );
			       if($order){
		        	$order_data = $order->get_data();

		       		$order_id = $order_data['id'];
		       		$order_currency = $order_data['currency'];

		       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
					// $order_shipping_last_name = $order_data['shipping']['last_name'];
					// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
					// $order_shipping_address_1 = $order_data['shipping']['address_1'];
					// $order_shipping_address_2 = $order_data['shipping']['address_2'];
					// $order_shipping_city = $order_data['shipping']['city'];
					// $order_shipping_state = $order_data['shipping']['state'];
					// $order_shipping_postcode = $order_data['shipping']['postcode'];
					// $order_shipping_country = $order_data['shipping']['country'];
					// $order_shipping_phone = $order_data['billing']['phone'];
					// $order_shipping_email = $order_data['billing']['email'];

					$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
					$order_shipping_first_name = $shipping_arr['first_name'];
					$order_shipping_last_name = $shipping_arr['last_name'];
					$order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
					$order_shipping_address_1 = $shipping_arr['address_1'];
					$order_shipping_address_2 = $shipping_arr['address_2'];
					$order_shipping_city = $shipping_arr['city'];
					$order_shipping_state = $shipping_arr['state'];
					$order_shipping_postcode = $shipping_arr['postcode'];
					$order_shipping_country = $shipping_arr['country'];
					$order_shipping_phone = $order_data['billing']['phone'];
					$order_shipping_email = $order_data['billing']['email'];
					$shipping_charge = $order_data['shipping_total'];

					$items = $order->get_items();
					$pack_products = array();
					$total_weg = 0;
					$general_settings = get_option('hitshipo_dhlp_main_settings',array());

				//weight conversion wc_get_weight( $weight, $to_unit, $from_unit )
				// $general_settings = get_option('hit_ups_auto_main_settings',array());
				$woo_weg_unit = get_option('woocommerce_weight_unit');
				$woo_dim_unit = get_option('woocommerce_dimension_unit');
				$config_weg_unit = $general_settings['hitshipo_dhlp_weight_unit'];
				$mod_weg_unit = (!empty($config_weg_unit) && $config_weg_unit == 'LB_IN') ? 'lbs' : 'kg';
				$mod_dim_unit = (!empty($config_weg_unit) && $config_weg_unit == 'LB_IN') ? 'in' : 'cm';

					foreach ( $items as $item ) {
						$product_data = $item->get_data();
					    $product = array();
					    $product['product_name'] = str_replace('"', '', $product_data['name']);
					    $product['product_quantity'] = $product_data['quantity'];
					    $product['product_id'] = $product_data['product_id'];

					    $product_variation_id = $item->get_variation_id();
					    if(empty($product_variation_id)){
					    	$getproduct = wc_get_product( $product_data['product_id'] );
					    }else{
					    	$getproduct = wc_get_product( $product_variation_id );
					    }

						$product['price'] = $getproduct->get_price();
						$product['width'] = (!empty($getproduct->get_width())) ? round(wc_get_dimension($getproduct->get_width(),$mod_dim_unit,$woo_dim_unit)) : '';
				    	$product['height'] = (!empty($getproduct->get_height())) ? round(wc_get_dimension($getproduct->get_height(),$mod_dim_unit,$woo_dim_unit)) : '';
				   		$product['depth'] = (!empty($getproduct->get_length())) ? round(wc_get_dimension($getproduct->get_length(),$mod_dim_unit,$woo_dim_unit)) : '';
						$product['weight'] = (!empty($getproduct->get_weight())) ? (float)round(wc_get_weight($getproduct->get_weight(),$mod_weg_unit,$woo_weg_unit),2) : '';
						$total_weg += (!empty($product['weight'])) ? $product['weight'] : 0;

					    $pack_products[] = $product;

					}

					$custom_settings = array();
					$custom_settings['default'] = array(
													'hitshipo_dhlp_site_id' => $general_settings['hitshipo_dhlp_site_id'],
													'hitshipo_dhlp_site_pwd' => $general_settings['hitshipo_dhlp_site_pwd'],
													'hitshipo_dhlp_acc_no' => $general_settings['hitshipo_dhlp_acc_no'],
													'hitshipo_dhlp_access_key' => $general_settings['hitshipo_dhlp_access_key'],
													'hitshipo_dhlp_shipper_name' => $general_settings['hitshipo_dhlp_shipper_name'],
													'hitshipo_dhlp_company' => $general_settings['hitshipo_dhlp_company'],
													'hitshipo_dhlp_mob_num' => $general_settings['hitshipo_dhlp_mob_num'],
													'hitshipo_dhlp_email' => $general_settings['hitshipo_dhlp_email'],
													'hitshipo_dhlp_address1' => $general_settings['hitshipo_dhlp_address1'],
													'hitshipo_dhlp_address2' => $general_settings['hitshipo_dhlp_address2'],
													'hitshipo_dhlp_city' => $general_settings['hitshipo_dhlp_city'],
													'hitshipo_dhlp_state' => $general_settings['hitshipo_dhlp_state'],
													'hitshipo_dhlp_zip' => $general_settings['hitshipo_dhlp_zip'],
													'hitshipo_dhlp_country' => $general_settings['hitshipo_dhlp_country'],
													'hitshipo_dhlp_con_rate' => isset($general_settings['hitshipo_dhlp_con_rate']) ? $general_settings['hitshipo_dhlp_con_rate'] : '',
													'service_code' => $service_code,
													'hitshipo_dhlp_shippo_mail' => $general_settings['hitshipo_dhlp_shippo_mail'],
													'hitshipo_dhlp_currency' => $general_settings['hitshipo_dhlp_currency'],
												);

					$vendor_settings = array();

				if(isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes' && isset($general_settings['hitshipo_dhlp_v_labels']) && $general_settings['hitshipo_dhlp_v_labels'] == 'yes'){
					// Multi Vendor Enabled

					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						$dhlp_account = get_post_meta($product_id,'dhlp_address', true);
						if(empty($dhlp_account) || $dhlp_account == 'default'){
							$dhlp_account = 'default';
							if (!isset($vendor_settings[$dhlp_account])) {
								$vendor_settings[$dhlp_account] = $custom_settings['default'];
							}
							$vendor_settings[$dhlp_account]['products'][] = $value;
						}

						if($dhlp_account != 'default'){
							$user_account = get_post_meta($dhlp_account,'hitshipo_dhlp_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dhlp_account])){

									$vendor_settings[$dhlp_account] = $custom_settings['default'];

									if($user_account['hitshipo_dhlp_site_id'] != '' && $user_account['hitshipo_dhlp_site_pwd'] != '' && $user_account['hitshipo_dhlp_acc_no'] != '' && $user_account['hitshipo_dhlp_access_key'] != ''){
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_site_id'] = $user_account['hitshipo_dhlp_site_id'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_site_pwd'] = $user_account['hitshipo_dhlp_site_pwd'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_acc_no'] = $user_account['hitshipo_dhlp_acc_no'];
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_access_key'] = $user_account['hitshipo_dhlp_access_key'];
									}

									if ($user_account['hitshipo_dhlp_shipper_name'] != '' && $user_account['hitshipo_dhlp_address1'] != '' && $user_account['hitshipo_dhlp_city'] != '' && $user_account['hitshipo_dhlp_state'] != '' && $user_account['hitshipo_dhlp_zip'] != '' && $user_account['hitshipo_dhlp_country'] != ''){

										if($user_account['hitshipo_dhlp_shipper_name'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_shipper_name'] = $user_account['hitshipo_dhlp_shipper_name'];
										}

										if($user_account['hitshipo_dhlp_company'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_company'] = $user_account['hitshipo_dhlp_company'];
										}

										if($user_account['hitshipo_dhlp_mob_num'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_mob_num'] = $user_account['hitshipo_dhlp_mob_num'];
										}

										if($user_account['hitshipo_dhlp_email'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_email'] = $user_account['hitshipo_dhlp_email'];
										}

										if($user_account['hitshipo_dhlp_address1'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_address1'] = $user_account['hitshipo_dhlp_address1'];
										}

										$vendor_settings[$dhlp_account]['hitshipo_dhlp_address2'] = !empty($user_account['hitshipo_dhlp_address2']) ? $user_account['hitshipo_dhlp_address2'] : '';

										if($user_account['hitshipo_dhlp_city'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_city'] = $user_account['hitshipo_dhlp_city'];
										}

										if($user_account['hitshipo_dhlp_state'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_state'] = $user_account['hitshipo_dhlp_state'];
										}

										if($user_account['hitshipo_dhlp_zip'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_zip'] = $user_account['hitshipo_dhlp_zip'];
										}

										if($user_account['hitshipo_dhlp_country'] != ''){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_country'] = $user_account['hitshipo_dhlp_country'];
										}

										if (isset($user_account['hitshipo_dhlp_con_rate'])) {
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_con_rate'] = $user_account['hitshipo_dhlp_con_rate'];
										}

										if (isset($user_account['hitshipo_dhlp_currency'])) {
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_currency'] = $user_account['hitshipo_dhlp_currency'];
										}

									}

									if(isset($general_settings['hitshipo_dhlp_v_email']) && $general_settings['hitshipo_dhlp_v_email'] == 'yes'){
										$user_dat = get_userdata($dhlp_account);
										$vendor_settings[$dhlp_account]['hitshipo_dhlp_shippo_mail'] = $user_dat->data->user_email;
									}

								}
								// unset($value['product_id']);
								$vendor_settings[$dhlp_account]['products'][] = $value;
							}else {
								$dhlp_account = 'default';
								if (!isset($vendor_settings[$dhlp_account])) {
									$vendor_settings[$dhlp_account] = $custom_settings['default'];
								}
								$vendor_settings[$dhlp_account]['products'][] = $value;
							}
						}
					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}

					if(!empty($general_settings) && isset($general_settings['hitshipo_dhlp_shippo_int_key'])){
						$mode = 'live';
						if(isset($general_settings['hitshipo_dhlp_test']) && $general_settings['hitshipo_dhlp_test']== 'yes'){
							$mode = 'test';
						}
						$execution = 'manual';
						// if(isset($general_settings['hitshipo_dhlp_shippo_label_gen']) && $general_settings['hitshipo_dhlp_shippo_label_gen']== 'yes'){
						// 	$execution = 'auto';
						// }

						$boxes_to_shipo = array();
						if (isset($general_settings['hitshipo_dhlp_packing_type']) && $general_settings['hitshipo_dhlp_packing_type'] == "box") {
							if (isset($general_settings['hitshipo_dhlp_boxes']) && !empty($general_settings['hitshipo_dhlp_boxes'])) {
								foreach ($general_settings['hitshipo_dhlp_boxes'] as $box) {
									if ($box['enabled'] != 1) {
										continue;
									}else {
										$boxes_to_shipo[] = $box;
									}
								}
							}
						}

						$data = array();
						$data['integrated_key'] = $general_settings['hitshipo_dhlp_shippo_int_key'];
						$data['order_id'] = $order_id;
						$data['exec_type'] = $execution;
						$data['mode'] = $mode;
						$data['carrier_type'] = "dhl_p";
						$data['ship_price'] = $shipping_charge;
						$data['meta'] = array(
							"site_id" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_site_id'],
							"password"  => $custom_settings[$create_shipment_for]['hitshipo_dhlp_site_pwd'],
							"accountnum" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_acc_no'],
							"api_key" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_access_key'],
							"t_company" => $order_shipping_company,
							"t_address1" => $order_shipping_address_1,
							"t_address2" => $order_shipping_address_2,
							"t_city" => $order_shipping_city,
							"t_state" => $order_shipping_state,
							"t_postal" => $order_shipping_postcode,
							"t_country" => $order_shipping_country,
							"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
							"t_phone" => $order_shipping_phone,
							"t_email" => $order_shipping_email,
							"shipping_charge" => $shipping_charge,
							"products" => $custom_settings[$create_shipment_for]['products'],
							"pack_algorithm" => $general_settings['hitshipo_dhlp_packing_type'],
							"boxes" => $boxes_to_shipo,
							"max_weight" => $general_settings['hitshipo_dhlp_max_weight'],
							"wight_dim_unit" => $general_settings['hitshipo_dhlp_weight_unit'],
							"total_product_weg" => $total_weg,
							"service_code" => $custom_settings[$create_shipment_for]['service_code'],
							"shipment_content" => $ship_content,
							"s_company" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_company'],
							"s_address1" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_address1'],
							"s_address2" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_address2'],
							"s_city" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_city'],
							"s_state" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_state'],
							"s_postal" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_zip'],
							"s_country" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_country'],
							// "gstin" => $general_settings['hitshipo_dhlp_gstin'],
							"s_name" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_shipper_name'],
							"s_phone" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_mob_num'],
							"s_email" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_email'],
							"label_format" => "PDF",
							"label_size" => $general_settings['hitshipo_dhlp_label_size'],
							"sent_email_to" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_shippo_mail'],
							"woo_curr" => get_option('woocommerce_currency'),
							"dhlp_curr" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_currency'],
							"con_rate" => $custom_settings[$create_shipment_for]['hitshipo_dhlp_con_rate'],
							"ext_cover" => $general_settings['hitshipo_dhlp_ext_cover'],
							"close_lunch" => $general_settings['hitshipo_dhlp_lunch'],
							"cover_units" => $general_settings['hitshipo_dhlp_ext_cover_units'],
							"cc" => $general_settings['hitshipo_dhlp_shipment_cc'],
							"e_time" => $general_settings['hitshipo_dhlp_e_time'],
							"l_time" => $general_settings['hitshipo_dhlp_l_time'],
							"col_aftr" => $general_settings['hitshipo_dhlp_col_aftr'],
							"tod" => ($custom_settings[$create_shipment_for]['service_code'] == 101) ? "DAP" : "DDP",
							'label' => $create_shipment_for
						);

						//  echo '<pre>';
						//  print_r($data);
						//  print_r(json_encode($data));
						//  die();
						// manual
						$manual_ship_url = "https://app.hitshipo.com/label_api/create_shipment.php";
						// $manual_ship_url = "http://localhost/hitshipo/label_api/create_shipment.php";
						$response = wp_remote_post( $manual_ship_url , array(
							'method'      => 'POST',
							'timeout'     => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking'    => true,
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
							'body'        => json_encode($data),
							'sslverify'   => FALSE
							)
						);

						$output = (is_array($response) && isset($response['body'])) ? json_decode($response['body'],true) : [];

								if($output){
									if(isset($output['status'])){

										if(isset($output['status']) && $output['status'] != 'success'){
											   update_option('hitshipo_dhlp_status_'.$order_id, $output['status']);
										}else if(isset($output['status']) && $output['status'] == 'success'){
											$output['user_id'] = $create_shipment_for;
											$val = get_option('hitshipo_dhlp_values_'.$order_id, []);
											$result_arr = array();
											if(!empty($val)){
												$result_arr = json_decode($val, true);
											}
											$result_arr[] = $output;

											update_option('hitshipo_dhlp_values_'.$order_id, json_encode($result_arr));
											update_option('hitshipo_dhlp_status_'.$order_id, $output['status']);
										}
									}else{
										update_option('hitshipo_dhlp_status_'.$order_id, 'Site not Connected with HITShipo. Contact HITShipo Team.');
									}
								}else{
									update_option('hitshipo_dhlp_status_'.$order_id, 'Site not Connected with HITShipo. Contact HITShipo Team.');
								}

			    	}
			}

		}
		}

	}
	new hitshipo_dhlp_parent();
}
}
