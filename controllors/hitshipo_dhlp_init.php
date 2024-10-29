<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'hitshipo_dhlp' ) ) {
    class hitshipo_dhlp extends WC_Shipping_Method {
        /**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->id                 = 'hitshipo_dhlp';
			$this->method_title       = __( 'DHL Parcel' );  // Title shown in admin
			$this->title       = __( 'DHL Parcel' );
            $this->method_description = __( '' ); //
            $this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
            $this->init();
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

            // Save settings in admin if you have any defined
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping( $package = array() ) {
        	return;		//Currently no rates

			$pack_aft_hook = apply_filters('hitshipo_dhlp_rate_packages', $package);

			if(empty($pack_aft_hook)){
				return;
			}

			$execution_status = get_option('hitshipo_dhlp_working_status');
			if(!empty($execution_status)){
				if($execution_status == 'stop_working'){
					return;
				}
			}


			$dhlp_core = array();
			$_carriers = array(
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

			$dhlp_core['AD'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AE'] = array('currency' =>'AED', 'weight' => 'KG_CM');
			$dhlp_core['AF'] = array('currency' =>'AFN', 'weight' => 'KG_CM');
			$dhlp_core['AG'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['AI'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['AL'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AM'] = array('currency' =>'AMD', 'weight' => 'KG_CM');
			$dhlp_core['AN'] = array('currency' =>'ANG', 'weight' => 'KG_CM');
			$dhlp_core['AO'] = array('currency' =>'AOA', 'weight' => 'KG_CM');
			$dhlp_core['AR'] = array('currency' =>'ARS', 'weight' => 'KG_CM');
			$dhlp_core['AS'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['AT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['AU'] = array('currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['AW'] = array('currency' =>'AWG', 'weight' => 'LB_IN');
			$dhlp_core['AZ'] = array('currency' =>'AZN', 'weight' => 'KG_CM');
			$dhlp_core['AZ'] = array('currency' =>'AZN', 'weight' => 'KG_CM');
			$dhlp_core['GB'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['BA'] = array('currency' =>'BAM', 'weight' => 'KG_CM');
			$dhlp_core['BB'] = array('currency' =>'BBD', 'weight' => 'LB_IN');
			$dhlp_core['BD'] = array('currency' =>'BDT', 'weight' => 'KG_CM');
			$dhlp_core['BE'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['BF'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['BG'] = array('currency' =>'BGN', 'weight' => 'KG_CM');
			$dhlp_core['BH'] = array('currency' =>'BHD', 'weight' => 'KG_CM');
			$dhlp_core['BI'] = array('currency' =>'BIF', 'weight' => 'KG_CM');
			$dhlp_core['BJ'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['BM'] = array('currency' =>'BMD', 'weight' => 'LB_IN');
			$dhlp_core['BN'] = array('currency' =>'BND', 'weight' => 'KG_CM');
			$dhlp_core['BO'] = array('currency' =>'BOB', 'weight' => 'KG_CM');
			$dhlp_core['BR'] = array('currency' =>'BRL', 'weight' => 'KG_CM');
			$dhlp_core['BS'] = array('currency' =>'BSD', 'weight' => 'LB_IN');
			$dhlp_core['BT'] = array('currency' =>'BTN', 'weight' => 'KG_CM');
			$dhlp_core['BW'] = array('currency' =>'BWP', 'weight' => 'KG_CM');
			$dhlp_core['BY'] = array('currency' =>'BYR', 'weight' => 'KG_CM');
			$dhlp_core['BZ'] = array('currency' =>'BZD', 'weight' => 'KG_CM');
			$dhlp_core['CA'] = array('currency' =>'CAD', 'weight' => 'LB_IN');
			$dhlp_core['CF'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CG'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CH'] = array('currency' =>'CHF', 'weight' => 'KG_CM');
			$dhlp_core['CI'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['CK'] = array('currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['CL'] = array('currency' =>'CLP', 'weight' => 'KG_CM');
			$dhlp_core['CM'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['CN'] = array('currency' =>'CNY', 'weight' => 'KG_CM');
			$dhlp_core['CO'] = array('currency' =>'COP', 'weight' => 'KG_CM');
			$dhlp_core['CR'] = array('currency' =>'CRC', 'weight' => 'KG_CM');
			$dhlp_core['CU'] = array('currency' =>'CUC', 'weight' => 'KG_CM');
			$dhlp_core['CV'] = array('currency' =>'CVE', 'weight' => 'KG_CM');
			$dhlp_core['CY'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['CZ'] = array('currency' =>'CZF', 'weight' => 'KG_CM');
			$dhlp_core['DE'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['DJ'] = array('currency' =>'DJF', 'weight' => 'KG_CM');
			$dhlp_core['DK'] = array('currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['DM'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['DO'] = array('currency' =>'DOP', 'weight' => 'LB_IN');
			$dhlp_core['DZ'] = array('currency' =>'DZD', 'weight' => 'KG_CM');
			$dhlp_core['EC'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['EE'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['EG'] = array('currency' =>'EGP', 'weight' => 'KG_CM');
			$dhlp_core['ER'] = array('currency' =>'ERN', 'weight' => 'KG_CM');
			$dhlp_core['ES'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ET'] = array('currency' =>'ETB', 'weight' => 'KG_CM');
			$dhlp_core['FI'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['FJ'] = array('currency' =>'FJD', 'weight' => 'KG_CM');
			$dhlp_core['FK'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['FM'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['FO'] = array('currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['FR'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GA'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['GB'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GD'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['GE'] = array('currency' =>'GEL', 'weight' => 'KG_CM');
			$dhlp_core['GF'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GG'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GH'] = array('currency' =>'GBS', 'weight' => 'KG_CM');
			$dhlp_core['GI'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['GL'] = array('currency' =>'DKK', 'weight' => 'KG_CM');
			$dhlp_core['GM'] = array('currency' =>'GMD', 'weight' => 'KG_CM');
			$dhlp_core['GN'] = array('currency' =>'GNF', 'weight' => 'KG_CM');
			$dhlp_core['GP'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GQ'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['GR'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['GT'] = array('currency' =>'GTQ', 'weight' => 'KG_CM');
			$dhlp_core['GU'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['GW'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['GY'] = array('currency' =>'GYD', 'weight' => 'LB_IN');
			$dhlp_core['HK'] = array('currency' =>'HKD', 'weight' => 'KG_CM');
			$dhlp_core['HN'] = array('currency' =>'HNL', 'weight' => 'KG_CM');
			$dhlp_core['HR'] = array('currency' =>'HRK', 'weight' => 'KG_CM');
			$dhlp_core['HT'] = array('currency' =>'HTG', 'weight' => 'LB_IN');
			$dhlp_core['HU'] = array('currency' =>'HUF', 'weight' => 'KG_CM');
			$dhlp_core['IC'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ID'] = array('currency' =>'IDR', 'weight' => 'KG_CM');
			$dhlp_core['IE'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['IL'] = array('currency' =>'ILS', 'weight' => 'KG_CM');
			$dhlp_core['IN'] = array('currency' =>'INR', 'weight' => 'KG_CM');
			$dhlp_core['IQ'] = array('currency' =>'IQD', 'weight' => 'KG_CM');
			$dhlp_core['IR'] = array('currency' =>'IRR', 'weight' => 'KG_CM');
			$dhlp_core['IS'] = array('currency' =>'ISK', 'weight' => 'KG_CM');
			$dhlp_core['IT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['JE'] = array('currency' =>'GBP', 'weight' => 'KG_CM');
			$dhlp_core['JM'] = array('currency' =>'JMD', 'weight' => 'KG_CM');
			$dhlp_core['JO'] = array('currency' =>'JOD', 'weight' => 'KG_CM');
			$dhlp_core['JP'] = array('currency' =>'JPY', 'weight' => 'KG_CM');
			$dhlp_core['KE'] = array('currency' =>'KES', 'weight' => 'KG_CM');
			$dhlp_core['KG'] = array('currency' =>'KGS', 'weight' => 'KG_CM');
			$dhlp_core['KH'] = array('currency' =>'KHR', 'weight' => 'KG_CM');
			$dhlp_core['KI'] = array('currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['KM'] = array('currency' =>'KMF', 'weight' => 'KG_CM');
			$dhlp_core['KN'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['KP'] = array('currency' =>'KPW', 'weight' => 'LB_IN');
			$dhlp_core['KR'] = array('currency' =>'KRW', 'weight' => 'KG_CM');
			$dhlp_core['KV'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['KW'] = array('currency' =>'KWD', 'weight' => 'KG_CM');
			$dhlp_core['KY'] = array('currency' =>'KYD', 'weight' => 'KG_CM');
			$dhlp_core['KZ'] = array('currency' =>'KZF', 'weight' => 'LB_IN');
			$dhlp_core['LA'] = array('currency' =>'LAK', 'weight' => 'KG_CM');
			$dhlp_core['LB'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['LC'] = array('currency' =>'XCD', 'weight' => 'KG_CM');
			$dhlp_core['LI'] = array('currency' =>'CHF', 'weight' => 'LB_IN');
			$dhlp_core['LK'] = array('currency' =>'LKR', 'weight' => 'KG_CM');
			$dhlp_core['LR'] = array('currency' =>'LRD', 'weight' => 'KG_CM');
			$dhlp_core['LS'] = array('currency' =>'LSL', 'weight' => 'KG_CM');
			$dhlp_core['LT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LU'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LV'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['LY'] = array('currency' =>'LYD', 'weight' => 'KG_CM');
			$dhlp_core['MA'] = array('currency' =>'MAD', 'weight' => 'KG_CM');
			$dhlp_core['MC'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MD'] = array('currency' =>'MDL', 'weight' => 'KG_CM');
			$dhlp_core['ME'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MG'] = array('currency' =>'MGA', 'weight' => 'KG_CM');
			$dhlp_core['MH'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['MK'] = array('currency' =>'MKD', 'weight' => 'KG_CM');
			$dhlp_core['ML'] = array('currency' =>'COF', 'weight' => 'KG_CM');
			$dhlp_core['MM'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['MN'] = array('currency' =>'MNT', 'weight' => 'KG_CM');
			$dhlp_core['MO'] = array('currency' =>'MOP', 'weight' => 'KG_CM');
			$dhlp_core['MP'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['MQ'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MR'] = array('currency' =>'MRO', 'weight' => 'KG_CM');
			$dhlp_core['MS'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['MT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['MU'] = array('currency' =>'MUR', 'weight' => 'KG_CM');
			$dhlp_core['MV'] = array('currency' =>'MVR', 'weight' => 'KG_CM');
			$dhlp_core['MW'] = array('currency' =>'MWK', 'weight' => 'KG_CM');
			$dhlp_core['MX'] = array('currency' =>'MXN', 'weight' => 'KG_CM');
			$dhlp_core['MY'] = array('currency' =>'MYR', 'weight' => 'KG_CM');
			$dhlp_core['MZ'] = array('currency' =>'MZN', 'weight' => 'KG_CM');
			$dhlp_core['NA'] = array('currency' =>'NAD', 'weight' => 'KG_CM');
			$dhlp_core['NC'] = array('currency' =>'XPF', 'weight' => 'KG_CM');
			$dhlp_core['NE'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['NG'] = array('currency' =>'NGN', 'weight' => 'KG_CM');
			$dhlp_core['NI'] = array('currency' =>'NIO', 'weight' => 'KG_CM');
			$dhlp_core['NL'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['NO'] = array('currency' =>'NOK', 'weight' => 'KG_CM');
			$dhlp_core['NP'] = array('currency' =>'NPR', 'weight' => 'KG_CM');
			$dhlp_core['NR'] = array('currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['NU'] = array('currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['NZ'] = array('currency' =>'NZD', 'weight' => 'KG_CM');
			$dhlp_core['OM'] = array('currency' =>'OMR', 'weight' => 'KG_CM');
			$dhlp_core['PA'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['PE'] = array('currency' =>'PEN', 'weight' => 'KG_CM');
			$dhlp_core['PF'] = array('currency' =>'XPF', 'weight' => 'KG_CM');
			$dhlp_core['PG'] = array('currency' =>'PGK', 'weight' => 'KG_CM');
			$dhlp_core['PH'] = array('currency' =>'PHP', 'weight' => 'KG_CM');
			$dhlp_core['PK'] = array('currency' =>'PKR', 'weight' => 'KG_CM');
			$dhlp_core['PL'] = array('currency' =>'PLN', 'weight' => 'KG_CM');
			$dhlp_core['PR'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['PT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['PW'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['PY'] = array('currency' =>'PYG', 'weight' => 'KG_CM');
			$dhlp_core['QA'] = array('currency' =>'QAR', 'weight' => 'KG_CM');
			$dhlp_core['RE'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['RO'] = array('currency' =>'RON', 'weight' => 'KG_CM');
			$dhlp_core['RS'] = array('currency' =>'RSD', 'weight' => 'KG_CM');
			$dhlp_core['RU'] = array('currency' =>'RUB', 'weight' => 'KG_CM');
			$dhlp_core['RW'] = array('currency' =>'RWF', 'weight' => 'KG_CM');
			$dhlp_core['SA'] = array('currency' =>'SAR', 'weight' => 'KG_CM');
			$dhlp_core['SB'] = array('currency' =>'SBD', 'weight' => 'KG_CM');
			$dhlp_core['SC'] = array('currency' =>'SCR', 'weight' => 'KG_CM');
			$dhlp_core['SD'] = array('currency' =>'SDG', 'weight' => 'KG_CM');
			$dhlp_core['SE'] = array('currency' =>'SEK', 'weight' => 'KG_CM');
			$dhlp_core['SG'] = array('currency' =>'SGD', 'weight' => 'KG_CM');
			$dhlp_core['SH'] = array('currency' =>'SHP', 'weight' => 'KG_CM');
			$dhlp_core['SI'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SK'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SL'] = array('currency' =>'SLL', 'weight' => 'KG_CM');
			$dhlp_core['SM'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['SN'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['SO'] = array('currency' =>'SOS', 'weight' => 'KG_CM');
			$dhlp_core['SR'] = array('currency' =>'SRD', 'weight' => 'KG_CM');
			$dhlp_core['SS'] = array('currency' =>'SSP', 'weight' => 'KG_CM');
			$dhlp_core['ST'] = array('currency' =>'STD', 'weight' => 'KG_CM');
			$dhlp_core['SV'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['SY'] = array('currency' =>'SYP', 'weight' => 'KG_CM');
			$dhlp_core['SZ'] = array('currency' =>'SZL', 'weight' => 'KG_CM');
			$dhlp_core['TC'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['TD'] = array('currency' =>'XAF', 'weight' => 'KG_CM');
			$dhlp_core['TG'] = array('currency' =>'XOF', 'weight' => 'KG_CM');
			$dhlp_core['TH'] = array('currency' =>'THB', 'weight' => 'KG_CM');
			$dhlp_core['TJ'] = array('currency' =>'TJS', 'weight' => 'KG_CM');
			$dhlp_core['TL'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['TN'] = array('currency' =>'TND', 'weight' => 'KG_CM');
			$dhlp_core['TO'] = array('currency' =>'TOP', 'weight' => 'KG_CM');
			$dhlp_core['TR'] = array('currency' =>'TRY', 'weight' => 'KG_CM');
			$dhlp_core['TT'] = array('currency' =>'TTD', 'weight' => 'LB_IN');
			$dhlp_core['TV'] = array('currency' =>'AUD', 'weight' => 'KG_CM');
			$dhlp_core['TW'] = array('currency' =>'TWD', 'weight' => 'KG_CM');
			$dhlp_core['TZ'] = array('currency' =>'TZS', 'weight' => 'KG_CM');
			$dhlp_core['UA'] = array('currency' =>'UAH', 'weight' => 'KG_CM');
			$dhlp_core['UG'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$dhlp_core['US'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['UY'] = array('currency' =>'UYU', 'weight' => 'KG_CM');
			$dhlp_core['UZ'] = array('currency' =>'UZS', 'weight' => 'KG_CM');
			$dhlp_core['VC'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['VE'] = array('currency' =>'VEF', 'weight' => 'KG_CM');
			$dhlp_core['VG'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['VI'] = array('currency' =>'USD', 'weight' => 'LB_IN');
			$dhlp_core['VN'] = array('currency' =>'VND', 'weight' => 'KG_CM');
			$dhlp_core['VU'] = array('currency' =>'VUV', 'weight' => 'KG_CM');
			$dhlp_core['WS'] = array('currency' =>'WST', 'weight' => 'KG_CM');
			$dhlp_core['XB'] = array('currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XC'] = array('currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XE'] = array('currency' =>'ANG', 'weight' => 'LB_IN');
			$dhlp_core['XM'] = array('currency' =>'EUR', 'weight' => 'LB_IN');
			$dhlp_core['XN'] = array('currency' =>'XCD', 'weight' => 'LB_IN');
			$dhlp_core['XS'] = array('currency' =>'SIS', 'weight' => 'KG_CM');
			$dhlp_core['XY'] = array('currency' =>'ANG', 'weight' => 'LB_IN');
			$dhlp_core['YE'] = array('currency' =>'YER', 'weight' => 'KG_CM');
			$dhlp_core['YT'] = array('currency' =>'EUR', 'weight' => 'KG_CM');
			$dhlp_core['ZA'] = array('currency' =>'ZAR', 'weight' => 'KG_CM');
			$dhlp_core['ZM'] = array('currency' =>'ZMW', 'weight' => 'KG_CM');
			$dhlp_core['ZW'] = array('currency' =>'USD', 'weight' => 'KG_CM');
			$general_settings = get_option('hitshipo_dhlp_main_settings');

			if(empty($general_settings)){
				return false;
			}

			$general_settings = empty($general_settings) ? array() : $general_settings;

			//excluded Countries

			if(isset($general_settings['hitshipo_dhlp_exclude_countries'])){

				if(in_array($pack_aft_hook['destination']['country'],$general_settings['hitshipo_dhlp_exclude_countries'])){
				return;
				}
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
												'hitshipo_dhlp_currency' => $general_settings['hitshipo_dhlp_currency'],
											);
			$vendor_settings = array();

			if(isset($general_settings['hitshipo_dhlp_rates']) && $general_settings['hitshipo_dhlp_rates'] == 'yes' && isset($pack_aft_hook['destination']['country']) && !empty($pack_aft_hook['destination']['country'])) {
				if(isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes' && isset($general_settings['hitshipo_dhlp_v_rates']) && $general_settings['hitshipo_dhlp_v_rates'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_aft_hook['contents'] as $key => $value) {
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

										if(isset($user_account['hitshipo_dhlp_con_rate'])){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_con_rate'] = $user_account['hitshipo_dhlp_con_rate'];
										}

										if(isset($user_account['hitshipo_dhlp_currency'])){
											$vendor_settings[$dhlp_account]['hitshipo_dhlp_currency'] = $user_account['hitshipo_dhlp_currency'];
										}
									}

								}

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
					$custom_settings['default']['products'] = $pack_aft_hook['contents'];
				}else{
					$custom_settings = $vendor_settings;
				}
				$shipping_rates = [];
				foreach ($custom_settings as $key => $cust_set) {

				}

				if(!empty($shipping_rates)){
					$i=0;
					$final_price = array();
					foreach ($shipping_rates as $mkey => $rate) {
						$cheap_p = 0;
						$cheap_s = '';
						foreach ($rate as $key => $cvalue) {
							if ($i > 0){
								if($cheap_p == 0 && $cheap_s == ''){
									$cheap_p = $cvalue;
									$cheap_s = $key;

								}else if ($cheap_p > $cvalue){
									$cheap_p = $cvalue;
									$cheap_s = $key;
								}
							}else{
								$final_price[] = array('price' => $cvalue, 'code' => $key, 'multi_v' => $mkey.'_'. $key);
							}
						}

						if($cheap_p != 0 && $cheap_s != ''){
							foreach ($final_price as $key => $value) {
								$value['price'] = $value['price'] + $cheap_p;
								$value['multi_v'] = $value['multi_v'] . '|' . $mkey . '_' . $cheap_s;
								$final_price[$key] = $value;
							}
						}

						$i++;

					}

					foreach ($final_price as $key => $value) {

						$rate_cost = $value['price'];
						$rate_code = $value['code'];
						$multi_ven = $value['multi_v'];

						// $rate_cost = apply_filters('hitshipo_dhlp_rate_cost',$rate_cost,$rate_code);

						// if($rate_cost > 0)
						// {

						// 	$rate_name = $_carriers[$rate_code];
						// 	$name = isset($carriers_name_available[$rate_code]) && !empty($carriers_name_available[$rate_code]) ? $carriers_name_available[$rate_code] : $rate_name;

						// 	$rate = array(
						// 		'id'       => 'hitshippo'.$rate_code,
						// 		'label'    => $name,
						// 		'cost'     => $rate_cost,
						// 		'meta_data' => array('hitshipo_dhlp_service' => $rate_code, 'hitshipo_dhlp_shipping_charge' => $rate_cost));
						// 	$this->add_rate( $rate );

						// }

						$rate_cost = round($rate_cost, 2);

						// $carriers_available = isset($general_settings['hitshipo_dhlp_carrier']) && is_array($general_settings['hitshipo_dhlp_carrier']) ? $general_settings['hitshipo_dhlp_carrier'] : array();

						$carriers_name_available = isset($general_settings['hitshipo_dhlp_carrier_name']) && is_array($general_settings['hitshipo_dhlp_carrier']) ? $general_settings['hitshipo_dhlp_carrier_name'] : array();

						if(array_key_exists($rate_code,$carriers_available))
							{
								$name = isset($carriers_name_available[$rate_code]) && !empty($carriers_name_available[$rate_code]) ? $carriers_name_available[$rate_code] : $_carriers[$rate_code];
								$dest_zip_code = isset($pack_aft_hook['destination']['postcode']) ? $pack_aft_hook['destination']['postcode'] : '';
								$rate_cost = apply_filters('hitshipo_dhlp_rate_cost', $rate_cost, $rate_code, $order_total, $pack_aft_hook['destination']['country'], $dest_zip_code);
								if($rate_cost < 1){
									$name .= ' - Free';
								}

								if(!isset($general_settings['hitshipo_dhlp_v_rates']) || $general_settings['hitshipo_dhlp_v_rates'] != 'yes'){
									$multi_ven = '';
								}


								// This is where you'll add your rates
								$rate = array(
									'id'       => 'hitshippo'.$rate_code,
									'label'    => $name,
									'cost'     => apply_filters("hitstacks_dhlp_shipping_cost_conversion", $rate_cost, $total_weight_for_rate, $pack_aft_hook['destination']['country'], $rate_code),
									'meta_data' => array('hitshipo_dhlp_multi_ven' => $multi_ven, 'hitshipo_dhlp_service' => $rate_code, 'hitshipo_dhlp_shipping_charge' => $rate_cost)
								);


								// Register the rate

								$this->add_rate( $rate );
							}

					}
				}
			}

        }
		private function hitshippo_get_zipcode_or_city($country, $city, $postcode) {
			$no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
									 'CL', 'CM', 'CO', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
									 'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
									 'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
									 'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

			$postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
			if( !empty($city) ){
				$postcode_city .= "<City>{$city}</City>";
			}
			return $postcode_city;
		}
		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {
			 $this->form_fields = array('hitshipo_dhlp' => array('type'=>'hitshipo_dhlp'));
		}
		 public function generate_hitshipo_dhlp_html() {

			$general_settings = get_option('hitshipo_dhlp_main_settings');
			$general_settings = empty($general_settings) ? array() : $general_settings;
			if(!empty($general_settings)){
				wp_redirect(admin_url('options-general.php?page=hits-dhlp-configuration'));
			}

			if(isset($_POST['configure_the_plugin'])){
				// global $woocommerce;
				// $countries_obj   = new WC_Countries();
				// $countries   = $countries_obj->__get('countries');
				// $default_country = $countries_obj->get_base_country();

				// if(!isset($general_settings['hitshipo_dhlp_country'])){
				// 	$general_settings['hitshipo_dhlp_country'] = $default_country;
				// 	update_option('hitshipo_dhlp_main_settings', $general_settings);
				
				// }
				wp_redirect(admin_url('options-general.php?page=hits-dhlp-configuration'));	
			}
		?>
			<style>

			.card {
				background-color: #fff;
				border-radius: 5px;
				width: 800px;
				max-width: 800px;
				height: auto;
				text-align:center;
				margin: 10px auto 100px auto;
				box-shadow: 0px 1px 20px 1px hsla(213, 33%, 68%, .6);
			}  

			.content {
				padding: 20px 20px;
			}


			h2 {
				text-transform: uppercase;
				color: #000;
				font-weight: bold;
			}


			.boton {
				text-align: center;
			}

			.boton button {
				font-size: 18px;
				border: none;
				outline: none;
				color: #166DB4;
				text-transform: capitalize;
				background-color: #fff;
				cursor: pointer;
				font-weight: bold;
			}

			button:hover {
				text-decoration: underline;
				text-decoration-color: #166DB4;
			}
						</style>
						<!-- Fuente Mulish -->
						

			<div class="card">
				<div class="content">
					<div class="logo">
					<img src="<?php echo plugin_dir_url(__FILE__); ?>views/hdhl_p.png" style="width:250px;height: 50px;" alt="logo DELL" />
					</div>
					<h2><strong>HITShipo + DHL Parcel</strong></h2>
					<p style="font-size: 14px;line-height: 27px;">
					<?php _e('Welcome to HITSHIPO! You are at just one-step ahead to configure the DHL Parcel with HITSHIPO.','a2z_dhlexpress') ?><br>
					<?php _e('We have lot of features that will take your e-commerce store to another level.','a2z_dhlexpress') ?><br><br>
					<?php _e('HITSHIPO helps you to save time, reduce errors, and worry less when you automate your tedious, manual tasks. HITSHIPO + our plugin can generate shipping labels, Commercial invoice, display real time rates, track orders, audit shipments, and supports both domestic & international DHL services.','a2z_dhlexpress') ?><br><br>
					<?php _e('Make your customers happier by reacting faster and handling their service requests in a timely manner, meaning higher store reviews and more revenue.','a2z_dhlexpress') ?><br>
					</p>
						
				</div>
				<div class="boton" style="padding-bottom:10px;">
				<button class="button-primary" name="configure_the_plugin" style="padding:8px;">Configure the plugin</button>
				</div>
				</div>
			<?php
			echo '<style>button.button-primary.woocommerce-save-button{display:none;}</style>';
			
		 }

		public function hit_get_dhlp_packages($package,$general_settings,$orderCurrency,$chk = false)
		{
			switch ($general_settings['hitshipo_dhlp_packing_type']) {
				case 'box' :
					return $this->box_shipping($package,$general_settings,$orderCurrency,$chk);
					break;
				case 'weight_based' :
					return $this->weight_based_shipping($package,$general_settings,$orderCurrency,$chk);
					break;
				case 'per_item' :
				default :
					return $this->per_item_shipping($package,$general_settings,$orderCurrency,$chk);
					break;
			}
		}
		private function weight_based_shipping($package,$general_settings,$orderCurrency,$chk = false)
		{
			// echo '<pre>';
			// print_r($package);
			// die();
			if ( ! class_exists( 'WeightPack' ) ) {
				include_once 'classes/weight_pack/class-hit-weight-packing.php';
			}
			$max_weight = isset($general_settings['hitshipo_dhlp_max_weight']) && $general_settings['hitshipo_dhlp_max_weight'] !=''  ? $general_settings['hitshipo_dhlp_max_weight'] : 10 ;
			$weight_pack=new WeightPack('pack_ascending');
			$weight_pack->set_max_weight($max_weight);

			$package_total_weight = 0;
			$insured_value = 0;

			$ctr = 0;
			foreach ($package as $item_id => $values) {
				$ctr++;
				// $product = $values['data'];
				// $product_data = $product->get_data();
				$product_data = $values;
				// echo '<pre>';print_r($values);die();

				if (!$product_data['weight']) {
					$product_data['weight'] = 0.001;
				}
				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				$weight_pack->add_item($product_data['weight'], $values, $chk_qty);
			}

			$pack   =   $weight_pack->pack_items();
			$errors =   $pack->get_errors();
			if( !empty($errors) ){
				//do nothing
				return;
			} else {
				$boxes    =   $pack->get_packed_boxes();
				$unpacked_items =   $pack->get_unpacked_items();

				$insured_value        =   0;

				$packages      =   array_merge( $boxes, $unpacked_items ); // merge items if unpacked are allowed
				$package_count  =   sizeof($packages);
				// get all items to pass if item info in box is not distinguished
				$packable_items =   $weight_pack->get_packable_items();
				$all_items    =   array();
				if(is_array($packable_items)){
					foreach($packable_items as $packable_item){
						$all_items[]    =   $packable_item['data'];
					}
				}
				//pre($packable_items);
				$order_total = '';

				$to_ship  = array();
				$group_id = 1;
				foreach($packages as $package){//pre($package);
					$packed_products = array();
					if(($package_count  ==  1) && isset($order_total)){
						$insured_value  =  (isset($product_data['product_price']) ? $product_data['product_price'] : $product_data['price']) * (isset($values['product_quantity']) ? $values['product_quantity'] : $values['quantity']);
					}else{
						$insured_value  =   0;
						if(!empty($package['items'])){
							foreach($package['items'] as $item){

								$insured_value        =   $insured_value; //+ $item->price;
							}
						}else{
							if( isset($order_total) && $package_count){
								$insured_value  =   $order_total/$package_count;
							}
						}
					}
					$packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
					// Creating package request
					$package_total_weight   = $package['weight'];

					$insurance_array = array(
						'Amount' => $insured_value,
						'Currency' => $orderCurrency
					);

					$group = array(
						'GroupNumber' => $group_id,
						'GroupPackageCount' => 1,
						'Weight' => array(
						'Value' => round($package_total_weight, 3),

						'Units' => (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] ==='KG_CM') ? 'KG' : 'LBS'
					),
						'packed_products' => $packed_products,
					);
					$group['InsuredValue'] = $insurance_array;
					$group['packtype'] = 'BOX';

					$to_ship[] = $group;
					$group_id++;
				}
			}
			return $to_ship;
		}
		private function box_shipping($package,$general_settings,$orderCurrency,$chk = false)
		{
			if (!class_exists('HIT_Boxpack')) {
				include_once 'classes/hit-box-packing.php';
			}
			$boxpack = new HIT_Boxpack();
			$boxes = isset($general_settings['hitshipo_dhlp_boxes']) ? $general_settings['hitshipo_dhlp_boxes'] : [];
			if(empty($boxes))
			{
				return false;
			}
			// $boxes = unserialize($boxes);
			// Define boxes
			foreach ($boxes as $key => $box) {
				if (!$box['enabled']) {
					continue;
				}
				$box['pack_type'] = !empty($box['pack_type']) ? $box['pack_type'] : 'BOX';

				$newbox = $boxpack->add_box($box['length'], $box['width'], $box['height'], $box['box_weight'], $box['pack_type']);

				if (isset($box['id'])) {
					$newbox->set_id(current(explode(':', $box['id'])));
				}

				if ($box['max_weight']) {
					$newbox->set_max_weight($box['max_weight']);
				}

				if ($box['pack_type']) {
					$newbox->set_packtype($box['pack_type']);
				}
			}

			// Add items
			foreach ($package as $item_id => $product_data) {

				// $product = $values['data'];
				// $product_data = $product->get_data();
				// $get_prod = wc_get_product($values['product_id']);
				// $parent_prod_data = [];

				// if ($get_prod->is_type('variable')) {
				// 	$parent_prod_data = $product->get_parent_data();
				// }

				if (isset($product_data['weight']) && !empty($product_data['weight'])) {
					$item_weight = round($product_data['weight'] > 0.001 ? $product_data['weight'] : 0.001, 3);
				}

				if (isset($product_data['width']) && isset($product_data['height']) && isset($product_data['length']) && !empty($product_data['width']) && !empty($product_data['height']) && !empty($product_data['length'])) {
					$item_dimension = array(
						'Length' => max(1, round($product_data['length'], 3)),
						'Width' => max(1, round($product_data['width'], 3)),
						'Height' => max(1, round($product_data['height'], 3))
					);
				}

				if (isset($item_weight) && isset($item_dimension)) {

					// $dimensions = array($values['depth'], $values['height'], $values['width']);
					$chk_qty = $chk ? $product_data['product_quantity'] : $product_data['quantity'];
					for ($i = 0; $i < $chk_qty; $i++) {
						$boxpack->add_item($item_dimension['Width'], $item_dimension['Height'], $item_dimension['Length'], $item_weight, round($product_data['price']), array(
							'data' => $product_data
						));
					}
				} else {
					//    $this->debug(sprintf(__('Product #%s is missing dimensions. Aborting.', 'wf-shipping-dhl'), $item_id), 'error');
					return;
				}
			}

			// Pack it
			$boxpack->pack();
			$packages = $boxpack->get_packages();
			$to_ship = array();
			$group_id = 1;
			foreach ($packages as $package) {
				if ($package->unpacked === true) {
					//$this->debug('Unpacked Item');
				} else {
					//$this->debug('Packed ' . $package->id);
				}

				$dimensions = array($package->length, $package->width, $package->height);

				sort($dimensions);
				$insurance_array = array(
					'Amount' => round($package->value),
					'Currency' => $orderCurrency
				);


				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => round($package->weight, 3),
						'Units' => (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] ==='KG_CM') ? 'KG' : 'LBS'
					),
					'Dimensions' => array(
						'Length' => max(1, round($dimensions[2], 3)),
						'Width' => max(1, round($dimensions[1], 3)),
						'Height' => max(1, round($dimensions[0], 3)),
						'Units' => (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] ==='KG_CM') ? 'KG' : 'LBS'
					),
					'InsuredValue' => $insurance_array,
					'packed_products' => array(),
					'package_id' => $package->id,
					'packtype' => 'BOX'
				);
// echo '<pre>';print_r($packages);die();
				if (!empty($package->packed) && is_array($package->packed)) {
					foreach ($package->packed as $packed) {
						$group['packed_products'][] = $packed->meta['data'];
					}
				}

				if (!isset($package->packed)) {
					foreach ($package->unpacked as $unpacked) {
						$group['packed_products'][] = $unpacked->meta['data'];
					}
				}

				$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
		private function per_item_shipping($package,$general_settings,$orderCurrency,$chk = false) {
			$to_ship = array();
			$group_id = 1;

			// Get weight of order
			foreach ($package as $item_id => $values) {
				// $product = $values['data'];
				// $product_data = $product->get_data();

				$product_data = $values;
				$group = array();
				$insurance_array = array(
					'Amount' => round($product_data['price']),
					'Currency' => $orderCurrency
				);

				if($product_data['weight'] < 0.001){
					$dhl_per_item_weight = 0.001;
				}else{
					$dhl_per_item_weight = round($product_data['weight'], 3);
				}
				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
					'Value' => $dhl_per_item_weight,
					'Units' => (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] == 'KG_CM') ? 'KG' : 'LBS'
				),
					'packed_products' => $product_data
				);

				if ($product_data['width'] && $product_data['height'] && $product_data['length']) {

					$group['Dimensions'] = array(
						'Length' => max(1, round($product_data['length'],3)),
						'Width' => max(1, round($product_data['width'],3)),
						'Height' => max(1, round($product_data['height'],3)),
						'Units' => (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] == 'KG_CM') ? 'CM' : 'IN'
					);
				}

				$group['packtype'] = 'BOX';

				$group['InsuredValue'] = $insurance_array;

				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				for ($i = 0; $i < $chk_qty; $i++)
					$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
    }
}
