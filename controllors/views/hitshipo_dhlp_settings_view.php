<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_script("jquery");
$error = $success =  '';

global $woocommerce;

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

$intl_carriers = array(
				'101'	=>	'Worldwide Air (Intl)',
				'102'	=>	'DHL Parcel International (Intl)',
				'204'	=>	'International Road Economy (Intl)',
				'206'	=>	'DHL Parcel Connect (Intl)',
			);

$countires =  array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
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
			'BQ' => 'Bonaire, Saint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
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
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curacao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'CD' => 'Democratic Republic of the Congo',
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
			'TF' => 'French Southern Territories',
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
			'HM' => 'Heard Island and McDonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
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
			'XK' => 'Kosovo',
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
			'NF' => 'Norfolk Island',
			'KP' => 'North Korea',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'CG' => 'Republic of the Congo',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
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
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'KR' => 'South Korea',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'VI' => 'U.S. Virgin Islands',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		$printer_doc_size = array(
			"PDF200dpi6x4" => "PDF200dpi6x4",
			// "PNG6x4" => "PNG6x4",
			// "ZPL200dpi6x4" => "ZPL200dpi6x4",
		);
		$col_time = array("00:00" => "00:00",
						"01:00" => "01:00",
						"02:00" => "02:00",
						"03:00" => "03:00",
						"04:00" => "04:00",
						"05:00" => "05:00",
						"06:00" => "06:00",
						"07:00" => "07:00",
						"08:00" => "08:00",
						"09:00" => "09:00",
						"10:00" => "10:00",
						"11:00" => "11:00",
						"12:00" => "12:00",
						"13:00" => "13:00",
						"14:00" => "14:00",
						"15:00" => "15:00",
						"16:00" => "16:00",
						"17:00" => "17:00",
						"18:00" => "18:00",
						"19:00" => "19:00",
						"20:00" => "20:00",
						"21:00" => "21:00",
						"22:00" => "22:00",
						"23:00" => "23:00",
						"24:00" => "24:00",
					);

		$col_aftr = array(
						'0' => 'Day of placing order',
						'1' => '1 day after placing order',
						'2' => '2 day after placing order',
						'3' => '3 day after placing order',
						'4' => '4 day after placing order',
						'5' => '5 day after placing order',
						'6' => '6 day after placing order',
						'7' => '7 day after placing order',
					);


		$packing_type = array("per_item" => "Pack Items Induviually", "box" => "Box Based Packing");

		$value = array();
		$value['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
		$value['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
		$value['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
		$value['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
		$value['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
		$value['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
		$value['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
		$value['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
		$value['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
		$value['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
		$value['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
		$value['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
		$value['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
		$value['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
		$value['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
		$value['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
		$value['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
		$value['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
		$value['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
		$value['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
		$value['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
		$value['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
		$value['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
		$value['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
		$value['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
		$value['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
		$value['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
		$value['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
		$value['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
		$value['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['CZ'] = array('region' => 'EU', 'currency' =>'CZF', 'weight' => 'KG_CM');
		$value['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
		$value['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
		$value['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
		$value['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
		$value['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
		$value['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
		$value['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
		$value['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
		$value['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GH'] = array('region' => 'AP', 'currency' =>'GBS', 'weight' => 'KG_CM');
		$value['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
		$value['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
		$value['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
		$value['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
		$value['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
		$value['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
		$value['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
		$value['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
		$value['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
		$value['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
		$value['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
		$value['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
		$value['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
		$value['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
		$value['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
		$value['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
		$value['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
		$value['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
		$value['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
		$value['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
		$value['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
		$value['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
		$value['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
		$value['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
		$value['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
		$value['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
		$value['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
		$value['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
		$value['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
		$value['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
		$value['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
		$value['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
		$value['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
		$value['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
		$value['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
		$value['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
		$value['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
		$value['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
		$value['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
		$value['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
		$value['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
		$value['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
		$value['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
		$value['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
		$value['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
		$value['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
		$value['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
		$value['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
		$value['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
		$value['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
		$value['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
		$value['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
		$value['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
		$value['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
		$value['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
		$value['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
		$value['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
		$value['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
		$value['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
		$value['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
		$value['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
		$value['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
		$value['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
		$value['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
		$value['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
		$value['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
		$value['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
		$value['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
		$value['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
		$value['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
		$value['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
		$value['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
		$value['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
		$value['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
		$value['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
		$value['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
		$value['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
		$value['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
		$value['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
		$value['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
		$value['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
		$value['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
		$value['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
		$value['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
		$value['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
		$value['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
		$value['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
		$value['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
		$value['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
		$value['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
		$value['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
		$value['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
		$value['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
		$value['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
		$value['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
		$value['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
		$value['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
		$value['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
		$value['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');

		function hitshipo_sanitize_array($arr_to_san = []){
			$sanitized_data = [];
			if (!empty($arr_to_san) && is_array($arr_to_san)) {
				foreach ($arr_to_san as $key => $value) {
					$sanitized_data[sanitize_text_field($key)] = sanitize_text_field($value);
				}
			}
			return $sanitized_data;
		}
		
		$currencys = $value; 
	$general_settings = get_option('hitshipo_dhlp_main_settings');
	$general_settings = empty($general_settings) ? array() : $general_settings;
	$boxes = array(array(
		'name'       => 'Sample BOX',
		'id'         => 'HITS_DHLP_SAMPLE_BOX',
		'max_weight' => 10,
		'box_weight' => 0,
		'length'     => 10,
		'width'      => 10,
		'height'     => 10,
		'enabled'    => true,
		'pack_type' => 'BOX'
	));
	$package_type = array('BOX' => 'Box Pack', 'YP' => 'Your Pack');

	if(isset($_POST['save']))
	{
		if(isset($_POST['hitshipo_dhlp_site_id'])){
		
			$general_settings['hitshipo_dhlp_site_id'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_site_id']) ? $_POST['hitshipo_dhlp_site_id'] : '');
			$general_settings['hitshipo_dhlp_site_pwd'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_site_pwd']) ? $_POST['hitshipo_dhlp_site_pwd'] : '');
			$general_settings['hitshipo_dhlp_acc_no'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_acc_no']) ? $_POST['hitshipo_dhlp_acc_no'] : '');
			$general_settings['hitshipo_dhlp_access_key'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_access_key']) ? $_POST['hitshipo_dhlp_access_key'] : '');
			$general_settings['hitshipo_dhlp_weight_unit'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_weight_unit']) ? $_POST['hitshipo_dhlp_weight_unit'] : '');
			$general_settings['hitshipo_dhlp_test'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_test']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_rates'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_rates']) ? 'yes' : 'no');
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
			$general_settings['hitshipo_dhlp_carrier'] = !empty($_POST['hitshipo_dhlp_carrier']) ? hitshipo_sanitize_array($_POST['hitshipo_dhlp_carrier']) : array();
			$general_settings['hitshipo_dhlp_carrier_name'] = !empty($_POST['hitshipo_dhlp_carrier_name']) ? hitshipo_sanitize_array($_POST['hitshipo_dhlp_carrier_name']) : array();
			$general_settings['hitshipo_dhlp_developer_rate'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_developer_rate']) ? 'yes' :'no');
			// $general_settings['hitshipo_dhlp_developer_shipment'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_developer_shipment']) ? 'yes' :'no');
			// $general_settings['hitshipo_dhlp_insure'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_insure']) ? 'yes' :'no');
			// $general_settings['hitshipo_dhlp_sd'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_sd']) ? 'yes' :'no');
			$general_settings['hitshipo_dhlp_shippo_label_gen'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shippo_label_gen']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_ext_cover'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_ext_cover']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_lunch'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_lunch']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_shippo_mail'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shippo_mail']) ? $_POST['hitshipo_dhlp_shippo_mail'] : '');
			$general_settings['hitshipo_dhlp_ext_cover_units'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_ext_cover_units']) ? $_POST['hitshipo_dhlp_ext_cover_units'] : '');
			$general_settings['hitshipo_dhlp_label_size'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_label_size']) ? $_POST['hitshipo_dhlp_label_size'] : '');
			$general_settings['hitshipo_dhlp_e_time'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_e_time']) ? $_POST['hitshipo_dhlp_e_time'] : '');
			$general_settings['hitshipo_dhlp_l_time'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_l_time']) ? $_POST['hitshipo_dhlp_l_time'] : 'CASH');
			$general_settings['hitshipo_dhlp_intl_srvc'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_intl_srvc']) ? $_POST['hitshipo_dhlp_intl_srvc'] : '101');
			$general_settings['hitshipo_dhlp_shipment_content'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shipment_content']) ? $_POST['hitshipo_dhlp_shipment_content'] : '');
			$general_settings['hitshipo_dhlp_shipment_cc'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shipment_cc']) ? $_POST['hitshipo_dhlp_shipment_cc'] : '');
			$general_settings['hitshipo_dhlp_col_aftr'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_col_aftr']) ? $_POST['hitshipo_dhlp_col_aftr'] : 0);
			$general_settings['hitshipo_dhlp_packing_type'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_packing_type']) ? $_POST['hitshipo_dhlp_packing_type'] : '');
			$general_settings['hitshipo_dhlp_max_weight'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_max_weight']) ? $_POST['hitshipo_dhlp_max_weight'] : '');
			$general_settings['hitshipo_dhlp_con_rate'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_con_rate']) ? $_POST['hitshipo_dhlp_con_rate'] : '');
			$general_settings['hitshipo_dhlp_auto_con_rate'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_auto_con_rate']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_currency'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_currency']) ? $_POST['hitshipo_dhlp_currency'] : '');
			// $general_settings['hitshipo_dhlp_exclude_countries'] = !empty($_POST['hitshipo_dhlp_exclude_countries']) ? $_POST['hitshipo_dhlp_exclude_countries'] : array();
			// update_option('hitshipo_dhlp_main_settings', $general_settings);
		
			// Multi Vendor Settings

			$general_settings['hitshipo_dhlp_v_enable'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_v_enable']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_v_rates'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_v_rates']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_v_labels'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_v_labels']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_v_roles'] = !empty($_POST['hitshipo_dhlp_v_roles']) ? hitshipo_sanitize_array($_POST['hitshipo_dhlp_v_roles']) : array();
			$general_settings['hitshipo_dhlp_v_email'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_v_email']) ? 'yes' : 'no');

			//Save boxes
			$boxes_id = isset($_POST['boxes_id']) ? hitshipo_sanitize_array($_POST['boxes_id']) : array();
			$boxes_name = isset($_POST['boxes_name']) ? hitshipo_sanitize_array($_POST['boxes_name']) : array();
			$boxes_length = isset($_POST['boxes_length']) ? hitshipo_sanitize_array($_POST['boxes_length']) : array();
			$boxes_width = isset($_POST['boxes_width']) ? hitshipo_sanitize_array($_POST['boxes_width']) : array();
			$boxes_height = isset($_POST['boxes_height']) ? hitshipo_sanitize_array($_POST['boxes_height']) : array();
			$boxes_box_weight = isset($_POST['boxes_box_weight']) ? hitshipo_sanitize_array($_POST['boxes_box_weight']) : array();
			$boxes_max_weight = isset($_POST['boxes_max_weight']) ? hitshipo_sanitize_array($_POST['boxes_max_weight']) : array();
			$boxes_enabled = isset($_POST['boxes_enabled']) ? hitshipo_sanitize_array($_POST['boxes_enabled']) : array();
			$boxes_pack_type = isset($_POST['boxes_pack_type']) ? hitshipo_sanitize_array($_POST['boxes_pack_type']) : array();

			$all_boxes = array();
			if (!empty($boxes_name)) {
				
				foreach ($boxes_name as $key => $value) {
					if (empty($value)) {
						continue;
					}
					$ind_box_id = $boxes_id[$key];
					$ind_box_name = empty($boxes_name[$key]) ? "New Box" : $boxes_name[$key];
					$ind_box_length = empty($boxes_length[$key]) ? 0 : $boxes_length[$key];
					$ind_boxes_width = empty($boxes_width[$key]) ? 0 : $boxes_width[$key];
					$ind_boxes_height = empty($boxes_height[$key]) ? 0 : $boxes_height[$key];
					$ind_boxes_box_weight = empty($boxes_box_weight[$key]) ? 0 : $boxes_box_weight[$key];
					$ind_boxes_max_weight = empty($boxes_max_weight[$key]) ? 0 : $boxes_max_weight[$key];
					$ind_box_enabled = isset($boxes_enabled[$key]) ? true : false;

					$all_boxes[$key] = array(
						'id' => $ind_box_id,
						'name' => $ind_box_name,
						'length' => $ind_box_length,
						'width' => $ind_boxes_width,
						'height' => $ind_boxes_height,
						'box_weight' => $ind_boxes_box_weight,
						'max_weight' => $ind_boxes_max_weight,
						'enabled' => $ind_box_enabled,
						'pack_type' => $boxes_pack_type[$key]
					);
				}
			}
			$general_settings['hitshipo_dhlp_boxes'] = !empty($all_boxes) ? $all_boxes : array();
			
			update_option('hitshipo_dhlp_main_settings', $general_settings);
			$success = 'Settings Saved Successfully.';
		}

		if ((!isset($general_settings['hitshipo_dhlp_shippo_int_key']) || empty($general_settings['hitshipo_dhlp_shippo_int_key'])) && isset($_POST['shipo_link_type']) && $_POST['shipo_link_type'] == "WITH") {
			$general_settings['hitshipo_dhlp_shippo_int_key'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shippo_int_key']) ? $_POST['hitshipo_dhlp_shippo_int_key'] : '');
			update_option('hitshipo_dhlp_main_settings', $general_settings);
			update_option('hitshipo_dhlp_working_status', 'start_working');
			$success = 'Site Linked Successfully.<br><br> It\'s great to have you here.';
		}

		if(!isset($general_settings['hitshipo_dhlp_shippo_int_key']) || empty($general_settings['hitshipo_dhlp_shippo_int_key'])){
			$random_nonce = wp_generate_password(16, false);
			set_transient( 'hitshipo_dhlp_express_nonce_temp', $random_nonce, HOUR_IN_SECONDS );
			
			$general_settings['hitshipo_dhlp_track_audit'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_track_audit']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_daily_report'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_daily_report']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_monthly_report'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_monthly_report']) ? 'yes' : 'no');
			$general_settings['hitshipo_dhlp_shipo_signup'] = sanitize_text_field(isset($_POST['hitshipo_dhlp_shipo_signup']) ? $_POST['hitshipo_dhlp_shipo_signup'] : '');
			update_option('hitshipo_dhlp_main_settings', $general_settings);
			$shipo_signup_pass = sanitize_text_field(isset($_POST['hitshipo_dhlp_shipo_signup_pass']) ? $_POST['hitshipo_dhlp_shipo_signup_pass'] : '');
			$link_hitshipo_request = json_encode(array('site_url' => site_url(),
				'site_name' => get_bloginfo('name'),
				'email_address' => $general_settings['hitshipo_dhlp_shipo_signup'],
				'password' => $shipo_signup_pass,
				'nonce' => $random_nonce,
				'audit' => $general_settings['hitshipo_dhlp_track_audit'],
				'd_report' => $general_settings['hitshipo_dhlp_daily_report'],
				'm_report' => $general_settings['hitshipo_dhlp_monthly_report'],
				'pulgin' => 'DHL parcel',
				'platfrom' => 'Woocommerce',
			));
			
			$link_site_url = "https://app.hitshipo.com/api/link-site.php";
			// $link_site_url = "http://localhost/hitshipo-v2/api/link-site.php";
			$link_site_response = wp_remote_post( $link_site_url , array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
					'body'        => $link_hitshipo_request,
					'sslverify'   => FALSE
					)
				);
				
				$link_site_response = ( is_array($link_site_response) && isset($link_site_response['body'])) ? json_decode($link_site_response['body'], true) : array();
				if($link_site_response){
					if($link_site_response['status'] != 'error'){
						$general_settings['hitshipo_dhlp_shippo_int_key'] = sanitize_text_field($link_site_response['integration_key']);
						update_option('hitshipo_dhlp_main_settings', $general_settings);
						update_option('hitshipo_dhlp_working_status', 'start_working');
						$success = 'Site Linked Successfully.<br><br> It\'s great to have you here. ' . (isset($link_site_response['trail']) ? 'Your 60days Trail period is started. To know about this more, please check your inbox.' : '' ) . '<br><br><button class="button" type="submit">Back to Settings</button>';
					}else{
						$error = '<p style="color:red;">'. $link_site_response['message'] .'</p>';
						$success = '';
					}
				}else{
					$error = '<p style="color:red;">Failed to connect with HITShipo</p>';
					$success = '';
				}
				
		
		}
		
	}
	$initial_setup = empty($general_settings) ? true : false;
	$countries_obj   = new WC_Countries();
	$default_country = $countries_obj->get_base_country();
	$general_settings['hitshipo_dhlp_currency'] = isset($value[(isset($general_settings['hitshipo_dhlp_country']) ? $general_settings['hitshipo_dhlp_country'] : 'A2Z')]) ? $value[$general_settings['hitshipo_dhlp_country']]['currency'] : (isset($value[$default_country]) ? $value[$default_country]['currency'] : "");
	$general_settings['hitshipo_dhlp_woo_currency'] = get_option('woocommerce_currency');
?>

<style>
.notice{display:none;}
#multistepsform {
  width: 80%;
  margin: 50px auto;
  text-align: center;
  position: relative;
}
#multistepsform fieldset {
  background: white;
  text-align:left;
  border: 0 none;
  border-radius: 5px;
  <?php if (!$initial_setup) { ?>
  box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
  <?php } ?>
  padding: 20px 30px;
  box-sizing: border-box;
  position: relative;
}
<?php if (!$initial_setup) { ?>
#multistepsform fieldset:not(:first-of-type) {
  display: none;
}
<?php } ?>
#multistepsform input[type=text], #multistepsform input[type=password], #multistepsform input[type=number], #multistepsform input[type=email], 
#multistepsform textarea {
  padding: 5px;
  width: 95%;
}
#multistepsform input:focus,
#multistepsform textarea:focus {
  border-color: #679b9b;
  outline: none;
  color: #637373;
}
#multistepsform .action-button {
  width: 100px;
  background: #ffca00;
  font-weight: bold;
  color: #fff;
  transition: 150ms;
  border: 0 none;
  float:right;
  border-radius: 1px;
  cursor: pointer;
  padding: 10px 5px;
  margin: 10px 5px;
}
#multistepsform .action-button:hover,
#multistepsform .action-button:focus {
  box-shadow: 0 0 0 2px #f08a5d, 0 0 0 3px #ff976;
  color: #fff;
}
#multistepsform .fs-title {
  font-size: 15px;
  text-transform: uppercase;
  color: #2c3e50;
  margin-bottom: 10px;
}
#multistepsform .fs-subtitle {
  font-weight: normal;
  font-size: 13px;
  color: #666;
  margin-bottom: 20px;
}
#multistepsform #progressbar {
  margin-bottom: 30px;
  overflow: hidden;
  counter-reset: step;
}
#multistepsform #progressbar li {
  list-style-type: none;
  color: #ffca00;
  text-transform: uppercase;
  font-size: 9px;
  width: 16.5%;
  float: left;
  position: relative;
}
#multistepsform #progressbar li:before {
  content: counter(step);
  counter-increment: step;
  width: 20px;
  line-height: 20px;
  display: block;
  font-size: 10px;
  color: #fff;
  background: #ffca00;
  border-radius: 3px;
  margin: 0 auto 5px auto;
}
#multistepsform #progressbar li:after {
  content: "";
  width: 100%;
  height: 2px;
  background: #ffca00;
  position: absolute;
  left: -50%;
  top: 9px;
  z-index: -1;
}
#multistepsform #progressbar li:first-child:after {
  content: none;
}
#multistepsform #progressbar li.active {
  color: #bf2800;
}
#multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
  background: #bf2800;
  color: white;
}
.insetbox{
	/*box-shadow: inset 2px 2px 15px 10px #f4f4f4;*/
	padding: 10px;
	<?php if (!$initial_setup) { ?>
	height: 300px;
	overflow: scroll;
	<?php } ?>
}
		</style>
<div style="text-align:center;margin-top:20px;"><img src="<?php echo plugin_dir_url(__FILE__); ?>dhl_p.png" style="width:150px;"></div>

<?php if($success != ''){
	echo '<form id="multistepsform" method="post"><fieldset>
    <center><h2 class="fs-title" style="line-height:27px;">'. $success .'</h2>
	</center></form>';
}else{
	?>
<!-- multistep form -->
<form id="multistepsform" method="post">
  <?php if (!$initial_setup) { ?>
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active">Integration</li>
    <li>Setup</li>
    <li>Packing</li>
    <li>Rates</li>
    <li>Shipping Label</li>
    <li>HITShipo</li>
  </ul>
  <?php } ?>
  <?php if($error == ''){

  ?>
  <!-- fieldsets -->
	<fieldset>
		<center><h2 class="fs-title">DHL Parcel Account Information</h2></center>
		<table style="width:100%">
			<tr><td style="padding:10px;"><hr></td></tr>
		</table>
		<div class="insetbox">
		<center>
			<table style="padding-left:10px;padding-right:10px;">
				<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_test" <?php echo (isset($general_settings['hitshipo_dhlp_test']) && $general_settings['hitshipo_dhlp_test'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Test Mode</small></span></td>
				<!-- <td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_rates" <?php echo (isset($general_settings['hitshipo_dhlp_rates']) && $general_settings['hitshipo_dhlp_rates'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Live Shipping Rates.</small></span></td> -->
				<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_shippo_label_gen" <?php echo (isset($general_settings['hitshipo_dhlp_shippo_label_gen']) && $general_settings['hitshipo_dhlp_shippo_label_gen'] == 'yes') || ($initial_setup) ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Create Label automatically</small></span></td>
				<!-- <td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_developer_rate" <?php echo (isset($general_settings['hitshipo_dhlp_developer_rate']) && $general_settings['hitshipo_dhlp_developer_rate'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Debug (Front)</small></span></td> -->
			</table>
		</center>
		<table style="width:100%">
			<tr><td style="padding:10px;"><hr></td></tr>
		</table>
		<table style="width:100%;">
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('DHL Parcel Username','hitshipo_dhlp') ?>
					<input type="text" class="input-text regular-input" name="hitshipo_dhlp_site_id" value="<?php echo (isset($general_settings['hitshipo_dhlp_site_id'])) ? esc_html($general_settings['hitshipo_dhlp_site_id']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('DHL Parcel Password','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_site_pwd" value="<?php echo (isset($general_settings['hitshipo_dhlp_site_pwd'])) ? esc_html($general_settings['hitshipo_dhlp_site_pwd']) : ''; ?>">			
			</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('DHL Parcel Account number','hitshipo_dhlp') ?>
					<input type="text" class="input-text regular-input" name="hitshipo_dhlp_acc_no" value="<?php echo (isset($general_settings['hitshipo_dhlp_acc_no'])) ? esc_html($general_settings['hitshipo_dhlp_acc_no']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('DHL Parcel API Key','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_access_key" value="<?php echo (isset($general_settings['hitshipo_dhlp_access_key'])) ? esc_html($general_settings['hitshipo_dhlp_access_key']) : ''; ?>">			
			</td>
			</tr>
			<tr>
				<td style="padding:10px;">
				<?php _e('Weight Unit','hitshipo_dhlp') ?><br>
					<select name="hitshipo_dhlp_weight_unit" class="wc-enhanced-select" style="width:95%;padding:5px;">
						<!-- <option value="LB_IN" <?php echo (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] == 'LB_IN') ? 'Selected="true"' : ''; ?>> LB & IN </option> -->
						<option value="KG_CM" <?php echo (isset($general_settings['hitshipo_dhlp_weight_unit']) && $general_settings['hitshipo_dhlp_weight_unit'] == 'KG_CM') ? 'Selected="true"' : ''; ?>> KG & CM </option>
					</select>
				</td>
				<td style="padding:10px;">
					<?php _e('Change DHL Parcel currency','hitshipo_dhlp') ?>
					<select name="hitshipo_dhlp_currency" style="width:95%;padding:5px;">
							
						<?php foreach($currencys as  $currency)
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
			
			<?php if ($general_settings['hitshipo_dhlp_woo_currency'] != $general_settings['hitshipo_dhlp_currency'] ){
				?>
					<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
					<tr><td colspan="2" style="text-align:center;"><small><?php _e(' Your Website Currency is ','hitshipo_dhlp') ?> <b><?php echo esc_html($general_settings['hitshipo_dhlp_woo_currency']);?></b> and your DHL Parcel currency is <b><?php echo (isset($general_settings['hitshipo_dhlp_currency'])) ? esc_html($general_settings['hitshipo_dhlp_currency']) : '(Choose country)'; ?></b>. <?php echo ($general_settings['hitshipo_dhlp_woo_currency'] != $general_settings['hitshipo_dhlp_currency'] ) ? 'So you have to consider the converstion rate.' : '' ?></small>
						</td>
					</tr>
					
					<tr><td colspan="2" style="text-align:center;">
					<input type="checkbox" id="auto_con" name="hitshipo_dhlp_auto_con_rate" <?php echo (isset($general_settings['hitshipo_dhlp_auto_con_rate']) && $general_settings['hitshipo_dhlp_auto_con_rate'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><?php _e('Auto Currency Conversion ','hitshipo_dhlp') ?>
						
					</td>
					</tr>
					<tr>
						<td style="padding:10px;text-align:center;" colspan="2" class="con_rate" >
							<?php _e('Exchange Rate','hitshipo_dhlp') ?><font style="color:red;">*</font> <?php echo "( ".esc_html($general_settings['hitshipo_dhlp_woo_currency'])."->".esc_html($general_settings['hitshipo_dhlp_currency'])." )"; ?>
							<br><input type="text" style="width:240px;" name="hitshipo_dhlp_con_rate" value="<?php echo (isset($general_settings['hitshipo_dhlp_con_rate'])) ? esc_html($general_settings['hitshipo_dhlp_con_rate']) : ''; ?>">
							<br><small style="color:gray;"><?php _e('Enter conversion rate.','hitshipo_dhlp') ?></small>
						</td>
					</tr>
				<?php
			}
			?>
			
		</table>
		</div>
		<table style="width:100%">
			<tr><td style="padding:10px;"><hr></td></tr>
		</table>
		<?php if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] !=''){
		?>
		<input type="submit" name="save" class="action-button" style="width:auto;float:left;" value="Save Changes" />
		<?php
		}

		?>
		<?php if (!$initial_setup) { ?>
		<input type="button" name="next" class="next action-button" value="Next" />
		<?php } ?>
    </fieldset>
	<fieldset>
		<center><h2 class="fs-title">Shipping Address Information</h2></center>
		<table style="width:100%;">
			<tr><td style="padding:10px;"><hr></td></tr>
		</table>
		<div class="insetbox">
		<table style="width:100%;">
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('Shipper Name','hitshipo_dhlp') ?>
					<input type="text" name="hitshipo_dhlp_shipper_name" value="<?php echo (isset($general_settings['hitshipo_dhlp_shipper_name'])) ? esc_html($general_settings['hitshipo_dhlp_shipper_name']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('Company Name','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_company" value="<?php echo (isset($general_settings['hitshipo_dhlp_company'])) ? esc_html($general_settings['hitshipo_dhlp_company']) : ''; ?>">
				</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('Shipper Mobile / Contact Number','hitshipo_dhlp') ?>
					<input type="text" name="hitshipo_dhlp_mob_num" value="<?php echo (isset($general_settings['hitshipo_dhlp_mob_num'])) ? esc_html($general_settings['hitshipo_dhlp_mob_num']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('Email Address of the Shipper','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_email" value="<?php echo (isset($general_settings['hitshipo_dhlp_email'])) ? esc_html($general_settings['hitshipo_dhlp_email']) : ''; ?>">
				</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('Address Line 1','hitshipo_dhlp') ?>
					<input type="text" name="hitshipo_dhlp_address1" value="<?php echo (isset($general_settings['hitshipo_dhlp_address1'])) ? esc_html($general_settings['hitshipo_dhlp_address1']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('Address Line 2','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_address2" value="<?php echo (isset($general_settings['hitshipo_dhlp_address2'])) ? esc_html($general_settings['hitshipo_dhlp_address2']) : ''; ?>">
				</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('City of the Shipper from address','hitshipo_dhlp') ?>
					<input type="text" name="hitshipo_dhlp_city" value="<?php echo (isset($general_settings['hitshipo_dhlp_city'])) ? esc_html($general_settings['hitshipo_dhlp_city']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('State (Two digit ISO code accepted.)','hitshipo_dhlp') ?>
				<input type="text" name="hitshipo_dhlp_state" value="<?php echo (isset($general_settings['hitshipo_dhlp_state'])) ? esc_html($general_settings['hitshipo_dhlp_state']) : ''; ?>">
				</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('Postal/Zip Code','hitshipo_dhlp') ?>
					<input type="text" name="hitshipo_dhlp_zip" value="<?php echo (isset($general_settings['hitshipo_dhlp_zip'])) ? esc_html($general_settings['hitshipo_dhlp_zip']) : ''; ?>">
				</td>
				<td style="padding:10px;">
				<?php _e('Shipper Country','hitshipo_dhlp') ?>
				<select name="hitshipo_dhlp_country" class="wc-enhanced-select" style="width:95%;padding:5px;">
						<?php foreach($countires as $key => $value)
						{
							if(isset($general_settings['hitshipo_dhlp_country']) && ($general_settings['hitshipo_dhlp_country'] == $key))
							{
								echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
							}
							else
							{
								echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
							}
						} ?>
					</select>
				</td>
			</tr>
			
			<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		</table>
		<center><h2 class="fs-title">Are you gonna use Multi Vendor?</h2><br>
		<table style="padding-left:10px;padding-right:10px;">
			<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_v_enable" <?php echo (isset($general_settings['hitshipo_dhlp_v_enable']) && $general_settings['hitshipo_dhlp_v_enable'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Use Multi-Vendor.</small></span></td>
			<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_v_rates" <?php echo (isset($general_settings['hitshipo_dhlp_v_rates']) && $general_settings['hitshipo_dhlp_v_rates'] == 'yes') || ($initial_setup) ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Get rates from vendor address.</small></span></td>
			<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_v_labels" <?php echo (isset($general_settings['hitshipo_dhlp_v_labels']) && $general_settings['hitshipo_dhlp_v_labels'] == 'yes') || ($initial_setup) ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Create Label from vendor address.</small></span></td>
			<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_v_email" <?php echo (isset($general_settings['hitshipo_dhlp_v_email']) && $general_settings['hitshipo_dhlp_v_email'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Email the shipping labels to vendors.</small></span></td>
		</table>
		</center>
		<table style="width:100%">							
							<tr>
								<td style=" width: 50%;padding:10px;text-align:center;">
									<?php _e('Vendor role','hitshipo_dhlp') ?></h4><br>
									<select name="hitshipo_dhlp_v_roles[]" style="padding:5px;width:240px;">

										<?php foreach (get_editable_roles() as $role_name => $role_info){
											if(isset($general_settings['hitshipo_dhlp_v_roles']) && in_array($role_name, $general_settings['hitshipo_dhlp_v_roles'])){
												echo "<option value=".esc_html($role_name)." selected='true'>".esc_html($role_info['name'])."</option>";
											}else{
												echo "<option value=".esc_html($role_name).">".esc_html($role_info['name'])."</option>";	
											}
											
										}
									?>

									</select><br>
									<small style="color:gray;"> To this role users edit page, you can find the new<br>fields to enter the ship from address.</small>
									
								</td>
							</tr>
							
						</table>
					</div>
					<table style="width:100%">
						<tr><td style="padding:10px;"><hr></td></tr>
					</table>
		<?php if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] !=''){
		?>
		<input type="submit" name="save" class="action-button" style="width:auto;float:left;" value="Save Changes" />
		<?php
		}

		?>
		<?php if (!$initial_setup) { ?>
			<input type="button" name="next" class="next action-button" value="Next" />
			<input type="button" name="previous" class="previous action-button" value="Previous" />
		<?php } ?>
    </fieldset>
	<fieldset <?php echo ($initial_setup) ? 'style="display:none"' : ''?>>
		<center><h2 class="fs-title">Choose Packing ALGORITHM</h2></center>
		<table style="width:100%;">
			<tr><td style="padding:10px;"><hr></td></tr>
		</table>
		<div class="insetbox">
		<table style="width:100%">
	
			<tr>
				<td style=" width: 50%;padding:10px;">
					<?php _e('Select Package Type','hitshipo_dhlp') ?>
				</td>
				<td style="padding:10px;">
					<select name="hitshipo_dhlp_packing_type" style="padding:5px; width:95%;" id = "hitshipo_dhlp_packing_type" class="wc-enhanced-select" style="width:153px;" onchange="changepacktype(this)">
						<?php foreach($packing_type as $key => $value)
						{
							if(isset($general_settings['hitshipo_dhlp_packing_type']) && ($general_settings['hitshipo_dhlp_packing_type'] == $key))
							{
								echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
							}
							else
							{
								echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
							}
						} ?>
					</select>
				</td>
			</tr>
			<!-- <tr>
				<td style=" width: 50%;padding:10px;">
				<?php _e('What is the Maximum weight to one package? (Weight based shipping only)','hitshipo_dhlp') ?><font style="color:red;">*</font>
				</td>
				<td style="padding:10px;">
					<input type="number" name="hitshipo_dhlp_max_weight" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_max_weight'])) ? $general_settings['hitshipo_dhlp_max_weight'] : ''; ?>">
				</td>
			</tr> -->
		</table>
		<div id="box_pack" style="width: 100%;">
					<h4 style="font-size: 16px;">Box packing configuration</h4><p>( Saved boxes are used when package type is "BOX". Enter the box dimensions/weight based on selected weight/dimension unit on plugin. )</p>
					<table id="box_pack_t">
						<tr>
							<th style="padding:3px;"></th>
							<th style="padding:3px;"><?php _e('Name','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Length','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Width','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Height','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Box Weight','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Max Weight','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Enabled','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Package Type','hitshipo_dhlp') ?><font style="color:red;">*</font></th>
						</tr>
						<tbody id="box_pack_tbody">
							<?php

							$boxes = ( isset($general_settings['hitshipo_dhlp_boxes']) ) ? $general_settings['hitshipo_dhlp_boxes'] : $boxes;
								if (!empty($boxes)) {//echo '<pre>';print_r($general_settings['hitshipo_dhlp_boxes']);die();
									foreach ($boxes as $key => $box) {
										echo '<tr>
												<td class="check-column" style="padding:3px;"><input type="checkbox" /></td>
												<input type="hidden" size="1" name="boxes_id['.esc_html($key).']" value="'.esc_html($box["id"]).'"/>
												<td style="padding:3px;"><input type="text" size="25" name="boxes_name['.esc_html($key).']" value="'.esc_html($box["name"]).'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length['.esc_html($key).']" value="'.esc_html($box["length"]).'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width['.esc_html($key).']" value="'.esc_html($box["width"]).'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height['.esc_html($key).']" value="'.esc_html($box["height"]).'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight['.esc_html($key).']" value="'.esc_html($box["box_weight"]).'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight['.esc_html($key).']" value="'.esc_html($box["max_weight"]).'" /></td>';
												if ($box['enabled'] == true) {
													echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.esc_html($key).']" checked/></center></td>';
												}else {
													echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.esc_html($key).']" /></center></td>';
												}
												
										echo '<td style="padding:3px;"><select name="boxes_pack_type['.esc_html($key).']">';
											foreach ($package_type as $k => $v) {
												$selected = ($k==$box['pack_type']) ? "selected='true'" : '';
												echo '<option value="'.esc_html($k).'" ' .esc_html($selected). '>'.esc_html($v).'</option>';
											}
										echo '</select></td>
											</tr>';
									}
								}
							?>
							<tfoot>
							<tr>
								<th colspan="6">
									<a href="#" class="button button-secondary" id="add_box"><?php _e('Add Box','hitshipo_dhlp') ?></a>
									<a href="#" class="button button-secondary" id="remove_box"><?php _e('Remove selected box(es)','hitshipo_dhlp') ?></a>
								</th>
							</tr>
						</tfoot>
						</tbody>
					</table>
				</div>
			</div>
			<table style="width:100%;">
				<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
			</table>
		
		
	<?php if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] !=''){
	?>
		<input type="submit" name="save" class="action-button" style="width:auto;float:left;" value="Save Changes" />
	<?php }

	?>
	<?php if (!$initial_setup) { ?>
	<input type="button" name="next" class="next action-button" value="Next" />
	<input type="button" name="previous" class="previous action-button" value="Previous" />
	<?php } ?>
</fieldset>
<fieldset>
  <center><h2 class="fs-title">Rates</h2></center>
  	<table style="width:100%;">
		<tr><td style="padding:10px;"><hr></td></tr>
	</table>
	<div class="insetbox">
  	<!-- <table style="width:100%">
			<tr><td colspan="2" style="padding:10px;"><center><h2 class="fs-title">Do you wants to exclude countries?</h2></center></td></tr>
				
			<tr>
				<td colspan="2" style="text-align:center;padding:10px;">
					<?php _e('Exclude Countries','hitshipo_dhlp') ?><br>
					<select name="hitshipo_dhlp_exclude_countries[]" multiple="true" class="wc-enhanced-select" style="padding:5px;width:600px;">

					<?php
					$general_settings['hitshipo_dhlp_exclude_countries'] = empty($general_settings['hitshipo_dhlp_exclude_countries'])? array() : $general_settings['hitshipo_dhlp_exclude_countries'];
					foreach ($countires as $key => $county){
						if(isset($general_settings['hitshipo_dhlp_exclude_countries']) && in_array($key,$general_settings['hitshipo_dhlp_exclude_countries'])){
							echo "<option value=".esc_html($key)." selected='true'>".esc_html($county)."</option>";
						}else{
							echo "<option value=".esc_html($key).">".esc_html($county)."</option>";	
						}
						
					}
					?>

					</select>
				</td>
				<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
				
			</tr>
			
		</table> -->
			<center style="color: red;">Note: Currently Rating is not available. Selected services are only avilable to create label manually.</center>
				<center><h2 class="fs-title">Shipping Services</h2></center>
				<table style="width:100%;">
				
					<tr>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Carries','hitshipo_dhlp') ?></h3>
						</td>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Alternate Name for Carrier','hitshipo_dhlp') ?></h3>
						</td>
						
					</tr>
							<?php foreach($_carriers as $key => $value)
							{
								if($key == '101'){
									echo ' <tr><td colspan="4" style="padding:10px;"><hr></td></tr><tr ><td colspan="4" style="text-align:center;"><div style="padding:10px;border:1px solid gray;"><b><u>INTERNATIONAL SERVICES</u><br>
									This all are the services provided by DHL Parcel to ship internationally.<br>
									
								</b></div></td></tr> <tr><td colspan="4" style="padding:10px;"><hr></td></tr>';
								}else if($key == "220"){
									echo ' <tr><td colspan="4" style="padding:10px;"><hr></td></tr><tr ><td colspan="4" style="text-align:center;"><div style="padding:10px;border:1px solid gray;"><b><u>DOMESTIC SERVICES</u><br>
										This all are the services provided by DHL Parcel to ship domestic.<br>
									</b></div>
									</td></tr> <tr><td colspan="4" style="padding:10px;"><hr></td></tr>';
								}
								$ser_to_enable = ["220", "225", "240", "250", "260", "101", "102"];
								echo '	<tr>
										<td>
										<input type="checkbox" value="yes" name="hitshipo_dhlp_carrier['.esc_html($key).']" '. ((isset($general_settings['hitshipo_dhlp_carrier'][$key]) && $general_settings['hitshipo_dhlp_carrier'][$key] == 'yes') || ($initial_setup && in_array($key, $ser_to_enable)) ? 'checked="true"' : '') .' > <small>'.__($value,"hitshipo_dhlp").' - [ '.$key.' ]</small>
										</td>
										<td>
											<input type="text" name="hitshipo_dhlp_carrier_name['.esc_html($key).']" value="'.((isset($general_settings['hitshipo_dhlp_carrier_name'][$key])) ? __($general_settings['hitshipo_dhlp_carrier_name'][$key],"hitshipo_dhlp") : '').'">
										</td>
										</tr>';
							} ?>
							 
				</table>
			</div>
			<table style="width: 100%;">
				<tr><td style="padding:10px;"><hr></td></tr>
			</table>
				<?php if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] !=''){
				?>
				<input type="submit" name="save" class="action-button" style="width:auto;float:left;" value="Save Changes" />
				<?php
				}

				?>
			<?php if (!$initial_setup) { ?>
			<input type="button" name="next" class="next action-button" value="Next" />
  			<input type="button" name="previous" class="previous action-button" value="Previous" />
			<?php } ?>
 </fieldset>
 <fieldset>
 <center><h2 class="fs-title">Configure Shipping Label</h2></center>
 <table style="width:100%;">
	<tr><td style="padding:10px;"><hr></td></tr>
 </table>
 <div class="insetbox">
 	<center>
	  	<table style="padding-left:10px;padding-right:10px;">
			<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_ext_cover" <?php echo (isset($general_settings['hitshipo_dhlp_ext_cover']) && $general_settings['hitshipo_dhlp_ext_cover'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray">Extended Cover</small></span></td>
			<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_lunch" <?php echo (isset($general_settings['hitshipo_dhlp_lunch']) && $general_settings['hitshipo_dhlp_lunch'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray">Close For Lunch</small></span></td>
		</table>
	</center>
  <table style="width:100%">
  	<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
  	<tr>
  		<td style="padding:10px;" colspan="2">
			<?php _e('Select default Service for International shipments','hitshipo_dhlp') ?><font style="color:red;">* </font>
			<select name="hitshipo_dhlp_intl_srvc" style="width:95%;padding:5px;">
				<?php foreach($intl_carriers as $key => $value)
				{
					if(isset($general_settings['hitshipo_dhlp_intl_srvc']) && ($general_settings['hitshipo_dhlp_intl_srvc'] == $key))
					{
						echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
					}
					else
					{
						echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
					}
				} ?>
			</select>
		</td>
  	</tr>
  	<tr><td style="padding:10px;" colspan="2"><span style="color: red;">( Label for International shipments were generated with selected service if available between SRC and DST. Label generation for domestic shipments not available now. )</span></td></tr>
  	<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
	<tr>
		<td style=" width: 50%;padding:10px;vertical-align: baseline;">
			<?php _e('Extended Cover Units','hitshipo_dhlp') ?><font style="color:red;">*</font>
			<input type="number" name="hitshipo_dhlp_ext_cover_units" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_ext_cover_units'])) ? esc_html($general_settings['hitshipo_dhlp_ext_cover_units']) : ''; ?>"><br>
			<small style="color:gray;">Valid value 0 to 10. 1 unit equates to £1000 additional cover.</small>
		</td>
		<td style="padding: 10px;">
			<?php _e('Email address to sent Shipping label','hitshipo_dhlp') ?><font style="color:red;">*</font>
			<input type="text" name="hitshipo_dhlp_shippo_mail" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_shippo_mail'])) ? esc_html($general_settings['hitshipo_dhlp_shippo_mail']) : ''; ?>"><br>
			<small style="color:gray;"> While HITShipo created the shipping label, It will sent the label, invoice to the given email. If you don't need this thenleave it empty.</small>
		</td>
	</tr>
	<tr>
	  	<td style=" width: 50%;padding:10px;">
			<?php _e('Shipment Content','hitshipo_dhlp') ?><font style="color:red;">*</font>
			<input type="text" name="hitshipo_dhlp_shipment_content" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_shipment_content'])) ? esc_html($general_settings['hitshipo_dhlp_shipment_content']) : ''; ?>">
		</td>
		<td style="padding:10px;">
			<?php _e('Shipping Label Format (PDF)','hitshipo_dhlp') ?><font style="color:red;">*</font>
			<select name="hitshipo_dhlp_label_size" style="width:95%;padding:5px;">
				<?php foreach($printer_doc_size as $key => $value)
				{
					if(isset($general_settings['hitshipo_dhlp_label_size']) && ($general_settings['hitshipo_dhlp_label_size'] == $key))
					{
						echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
					}
					else
					{
						echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
					}
				} ?>
			</select>
		</td>
	</tr>
		
		<tr <?php echo ($initial_setup) ? 'style="display:none"' : ''?>>
			<td style=" width: 50%;padding:10px;">
				<?php _e(' Earliest Time','hitshipo_dhlp') ?><font style="color:red;">*</font><br/>
				<select name="hitshipo_dhlp_e_time" style="width:95%;padding:5px;">
					<?php foreach($col_time as $key => $value)
					{
						if(isset($general_settings['hitshipo_dhlp_e_time']) && ($general_settings['hitshipo_dhlp_e_time'] == $key))
						{
							echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
						}
						else
						{
							echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
						}
					} ?>
				</select><br>
			</td>
			<td style=" width: 50%;padding:10px;">
				<?php _e(' Latest Time','hitshipo_dhlp') ?><font style="color:red;">*</font><br/>
				<select name="hitshipo_dhlp_l_time" style="width:95%;padding:5px;">
					<?php foreach($col_time as $key => $value)
					{
						if(isset($general_settings['hitshipo_dhlp_l_time']) && ($general_settings['hitshipo_dhlp_l_time'] == $key))
						{
							echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
						}
						else
						{
							echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
						}
					} ?>
				</select><br>
			</td>
		</tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Shipment Commodity Code','hitshipo_dhlp') ?><font style="color:red;">*</font>
				<input type="text" name="hitshipo_dhlp_shipment_cc" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_shipment_cc'])) ? esc_html($general_settings['hitshipo_dhlp_shipment_cc']) : ''; ?>">
			</td>
			<td style="padding:10px;">
				<?php _e('Collection date','hitshipo_dhlp') ?><font style="color:red;">*</font><br/>
				<select name="hitshipo_dhlp_col_aftr" style="width:95%;padding:5px;">
					<?php foreach($col_aftr as $key => $value)
					{
						if(isset($general_settings['hitshipo_dhlp_col_aftr']) && ($general_settings['hitshipo_dhlp_col_aftr'] == $key))
						{
							echo "<option value=".esc_html($key)." selected='true'>".esc_html($value)."</option>";
						}
						else
						{
							echo "<option value=".esc_html($key).">".esc_html($value)."</option>";
						}
					} ?>
				</select><br>
			</td>
		</tr>
		</table>
	</div>
	<table style="width: 100%;">
		<tr><td style="padding:10px;"><hr></td></tr>
	</table>
		
		
		<?php if(isset($general_settings['hitshipo_dhlp_shippo_int_key']) && $general_settings['hitshipo_dhlp_shippo_int_key'] !=''){
		?>
			<input type="submit" name="save" class="action-button" style="width:auto;float:left;" value="Save Changes" />
		<?php
		}

		?>
		<?php if (!$initial_setup) { ?>
		<input type="button" name="next" class="next action-button" value="Next" />
		<input type="button" name="previous" class="previous action-button" value="Previous" />
		<?php } ?>
	
 </fieldset>
  <?php } 
?>
<fieldset>
    <center><h2 class="fs-title">LINK HITSHIPO</h2></center>
    <table style="width:100%;">
		<tr><td style="padding:10px;"><hr></td></tr>
	</table>
	<div class="insetbox">
	<center>
	<img src="<?php echo plugin_dir_url(__FILE__); ?>hdhl_p.png" style="width: 250px;height: 50px;">
	<h3 class="fs-subtitle">HITShipo is performs all the operations in its own server. So it won't affect your page speed or server usage.</h3>
	<?php 
		if(!isset($general_settings['hitshipo_dhlp_shippo_int_key']) || empty($general_settings['hitshipo_dhlp_shippo_int_key'])){
		?>
		<input type="radio" name="shipo_link_type" id="WITHOUT" value="WITHOUT" checked>I don't have HITShipo account  &nbsp; &nbsp; &nbsp;
		<input type="radio" name="shipo_link_type" id="WITH" value="WITH">I have HITShipo integration key
<br><hr>
		<table class="with_shipo_acc" style="width:100%;text-align:center;display: none;">
		<tr>
			<td style="width: 50%;padding:10px;">
				<?php _e('Enter Intergation Key', 'hitshipo_dhlp') ?><font style="color:red;">*</font><br>
				
				<input type="text" style="width:330px;" class="intergration" id="shipo_intergration"  name="hitshipo_dhlp_shippo_int_key" value="">
			</td>
		</tr>
	</table>
	<table class="without_shipo_acc" style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_track_audit" <?php echo (isset($general_settings['hitshipo_dhlp_track_audit']) && $general_settings['hitshipo_dhlp_track_audit'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Track shipments everyday & Update the order status with Audit shipments.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_daily_report" <?php echo (isset($general_settings['hitshipo_dhlp_daily_report']) && $general_settings['hitshipo_dhlp_daily_report'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Daily Report.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="hitshipo_dhlp_monthly_report" <?php echo (isset($general_settings['hitshipo_dhlp_monthly_report']) && $general_settings['hitshipo_dhlp_monthly_report'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Monthly Report.</small></span></td>
		</table>
	</center>
    <table class="without_shipo_acc" style="width:100%;text-align:center;">
	<tr><td style="padding:10px;"></td></tr>
	
	<tr>
		<td style=" width: 50%;padding:10px;">
			<?php _e('Email address to signup / check the registered email.','hitshipo_dhlp') ?><font style="color:red;">*</font><br>
			<input type="email" style="width:330px;" id="shipo_mail" placeholder="Enter email address" name="hitshipo_dhlp_shipo_signup" placeholder="" value="<?php echo (isset($general_settings['hitshipo_dhlp_shipo_signup'])) ? esc_html($general_settings['hitshipo_dhlp_shipo_signup']) : ''; ?>">
		</td>
	</tr>
	<tr>
		<td style=" width: 50%;padding:10px;">
			<?php _e('Enter Password','hitshipo_dhlp') ?><font style="color:red;">*</font><br>
			<input type="password" style="width:330px;" id="shipo_pass" placeholder="Enter password" name="hitshipo_dhlp_shipo_signup_pass" placeholder="" value="">
		</td>
	</tr>
	</table>
	</div>
	<table style="width: 100%;">
		<tr><td style="padding:10px;"><hr></td></tr>
	</table>

	<?php }else{
		?>
		<p style="font-size:14px;line-height:24px;">
			Site Linked Successfully. <br><br>
		It's great to have you here. Your account has been linked successfully with HITSHIPO. <br><br>
Make your customers happier by reacting faster and handling their service requests in a timely manner, meaning higher store reviews and more revenue.</p>
		
		<?php
		echo '</center></div>';
	}
	?>
	<?php echo '<center>' . $error . '</center>'; ?>
	
	<table style="width: 100%;">
		<tr><td style="padding:10px;"><hr></td></tr>
	</table>
	
	<?php if(!isset($general_settings['hitshipo_dhlp_shippo_int_key']) || empty($general_settings['hitshipo_dhlp_shippo_int_key'])){
	?>
		<input type="submit" name="save" class="action-button save_change" style="width:auto;" value="SAVE & START" />
	<?php	} else {	?>
		<input type="submit" name="save" class="action-button" style="width:auto;" value="Save Changes" />
		<?php	} if (!$initial_setup) { if (empty($error)) { ?>
		<input type="button" name="previous" class="previous action-button" value="Previous" />
	<?php }else { ?>
		<button type="button" style="padding:11px;" name="previous" class="previous action-button"  onclick="location.reload();">Previous</button>
	<?php }} ?>
    
  </fieldset>
<?php
} ?>
</form>
<center><a href="https://app.hitshipo.com/support" target="_blank" style="width:auto;margin-right :20px;" class="button button-primary">Trouble in configuration? / not working? Email us.</a>
<a href="https://meetings.hubspot.com/hitshipo" target="_blank" style="width:auto;" class="button button-primary">Looking for demo ? Book your slot with our expert</a></center>


<script type="text/javascript">
var current_fs, next_fs, previous_fs;
var left, opacity, scale;
var animating;
jQuery(".next").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = jQuery(this).parent();
  next_fs = jQuery(this).parent().next();
  jQuery("#progressbar li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
  next_fs.show();
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; 
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        scale = 1 - (1 - now) * 0.2;
        left = now * 50 + "%";
        opacity = 1 - now;
        current_fs.css({
          transform: "scale(" + scale + ")"});
        next_fs.css({ left: left, opacity: opacity });
      },
      duration: 0,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //easing: "easeInOutBack"
    }
  );
});

jQuery(".previous").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = jQuery(this).parent();
  previous_fs = jQuery(this).parent().prev();
  jQuery("#progressbar li")
    .eq(jQuery("fieldset").index(current_fs))
    .removeClass("active");

  previous_fs.show();
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        scale = 0.8 + (1 - now) * 0.2;
        left = (1 - now) * 50 + "%";
        opacity = 1 - now;
        current_fs.css({ left: left });
        previous_fs.css({
          transform: "scale(" + scale + ")",
          opacity: opacity
        });
      },
      duration: 0,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //easing: "easeInOutBack"
    }
  );
});

jQuery(".submit").click(function () {
  return false;
});
jQuery(document).ready(function(){
	var dhlp_curr = '<?php echo esc_html($general_settings['hitshipo_dhlp_currency']); ?>';
	var woo_curr = '<?php echo esc_html($general_settings['hitshipo_dhlp_woo_currency']); ?>';
	var box_type = jQuery('#hitshipo_dhlp_packing_type').val();
	// var box = document.getElementById("box_pack");

    if('#checkAll'){
    	jQuery('#checkAll').on('click',function(){
            jQuery('.dhlp_service').each(function(){
                this.checked = true;
            });
    	});
    }
    if('#uncheckAll'){
		jQuery('#uncheckAll').on('click',function(){
            jQuery('.dhlp_service').each(function(){
                this.checked = false;
            });
    	});
	}

	if (dhlp_curr != null && dhlp_curr == woo_curr) {
		jQuery('.con_rate').each(function(){
		jQuery('.con_rate').hide();
	    });
	}else{
		if(jQuery("#auto_con").prop('checked') == true){
			jQuery('.con_rate').hide();
		}else{
			jQuery('.con_rate').each(function(){
			jQuery('.con_rate').show();
		    });
		}
	}
	jQuery('.save_change').click(function() {
		var shipo_mail = jQuery('#shipo_mail').val();
		var shipo_intergration = jQuery('#shipo_intergration').val();
			
			var link_type = jQuery("input[name='shipo_link_type']:checked").val();
			if (link_type === 'WITHOUT') {
				if(shipo_mail == ''){
						alert('Enter HITShipo Email');
						return false;
					}
			} else {
				if(shipo_intergration == ''){
						alert('Enter HITShipo intergtraion Key');
						return false;
					}
			}
			
	});

	jQuery("#auto_con").change(function() {
	    if(this.checked) {
	        jQuery('.con_rate').hide();
	    }else{
	    	if (dhlp_curr != woo_curr) {
	    		jQuery('.con_rate').show();
	    	}
	    }
	});

	jQuery('#add_box').click( function() {
		var pack_type_options = '<option value="BOX">Box Pack</option><option value="YP" selected="selected" >Your Pack</option>';
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');
		var size = tbody.find('tr').size();
		var code = '<tr class="new">\
			<td  style="padding:3px;" class="check-column"><input type="checkbox" /></td>\
			<input type="hidden" size="1" name="boxes_id[' + size + ']" value="box_id_' + size + '"/>\
			<td style="padding:3px;"><input type="text" size="25" name="boxes_name[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled[' + size + ']" /></center></td>\
			<td style="padding:3px;"><select name="boxes_pack_type[' + size + ']" >' + pack_type_options + '</select></td>\
	        </tr>';
		tbody.append( code );
		return false;
	});

	jQuery('#remove_box').click(function() {
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');console.log(tbody);
		tbody.find('.check-column input:checked').each(function() {
			jQuery(this).closest('tr').remove().find('input').val('');
		});
		return false;
	});

	if (box_type != "box") {
		// box.style.display = "none";
		jQuery('#box_pack').hide();
	}

});

function changepacktype(selectbox){
	var box = document.getElementById("box_pack");
	var box_type = selectbox.value;
	if (box_type == "box") {
	    box.style.display = "block";
	  } else {
	    box.style.display = "none";
	  }
		// alert(box_type);
}
jQuery(document).ready(function() {
			jQuery("input[name='shipo_link_type']").change(function() {
			if (jQuery(this).val() == "WITHOUT") {
				jQuery(".without_shipo_acc").show();
				jQuery(".with_shipo_acc").hide();
			} else if (jQuery(this).val() == "WITH") {
				jQuery(".without_shipo_acc").hide();
				jQuery(".with_shipo_acc").show();
			}
		});
	});

</script>
