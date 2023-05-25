<?php
/**
 * Load countries ISO
 *
 * @author Gauvain Van Ghele
 * @since  1.6
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}

/**
 * @author  Gauvain Van Ghele
 * @since   1.6.0
 *
 *  Array Of all countries ( example UK ): will be usefull later for Countries translations
 *   [GBR] => Array (
 *      [fr] => Royaume-Uni
 *      [en] => United Kingdom
 *      [num] => 826
 *      [code] => GB
 *   )
 *
 */
function seokey_helpers_get_full_countries(){
    $countries  = seokey_helpers_get_iso_countries();
    $codes      = seokey_helpers_get_codes_countries();
    foreach( $countries as $k => $v ){
        if( in_array( $k, array_keys( $codes ) ) ){
            $countries[$k]["code"] = $codes[$k];
        }else{
            // Guess & Create 2-letters code if it does not exist
            $countries[$k]["code"] = substr( $k, 0, -1 );
        }
    }
    unset($codes);
    return $countries;
}

/**
 * Iso countries 01/2023
 *
 * @author  Gauvain Van Ghele
 *
 * @since   1.6.0
 */
function seokey_helpers_get_iso_countries() {
    $countries = array (
        'AFG' => array
        (
            'fr' => 'Afghanistan',
            'en' => 'Afghanistan',
            'num' => 4,
        ),

        'ALB' => array
        (
            'fr' => 'Albanie',
            'en' => 'Albania',
            'num' => 8,
        ),

        'ATA' => array
        (
            'fr' => 'Antarctique',
            'en' => 'Antarctica',
            'num' => 10,
        ),

        'DZA' => array
        (
            'fr' => 'Algérie',
            'en' => 'Algeria',
            'num' => 12,
        ),

        'ASM' => array
        (
            'fr' => 'Samoa Américaines',
            'en' => 'American Samoa',
            'num' => 16,
        ),

        'AND' => array
        (
            'fr' => 'Andorre',
            'en' => 'Andorra',
            'num' => 20,
        ),

        'AGO' => array
        (
            'fr' => 'Angola',
            'en' => 'Angola',
            'num' => 24,
        ),

        'ATG' => array
        (
            'fr' => 'Antigua-et-Barbuda',
            'en' => 'Antigua and Barbuda',
            'num' => 28,
        ),

        'AZE' => array
        (
            'fr' => 'Azerbaïdjan',
            'en' => 'Azerbaijan',
            'num' => 31,
        ),

        'ARG' => array
        (
            'fr' => 'Argentine',
            'en' => 'Argentina',
            'num' => 32,
        ),

        'AUS' => array
        (
            'fr' => 'Australie',
            'en' => 'Australia',
            'num' => 36,
        ),

        'AUT' => array
        (
            'fr' => 'Autriche',
            'en' => 'Austria',
            'num' => 40,
        ),

        'BHS' => array
        (
            'fr' => 'Bahamas',
            'en' => 'Bahamas',
            'num' => 44,
        ),

        'BHR' => array
        (
            'fr' => 'Bahreïn',
            'en' => 'Bahrain',
            'num' => 48,
        ),

        'BGD' => array
        (
            'fr' => 'Bangladesh',
            'en' => 'Bangladesh',
            'num' => 50,
        ),

        'ARM' => array
        (
            'fr' => 'Arménie',
            'en' => 'Armenia',
            'num' => 51,
        ),

        'BRB' => array
        (
            'fr' => 'Barbade',
            'en' => 'Barbados',
            'num' => 52,
        ),

        'BEL' => array
        (
            'fr' => 'Belgique',
            'en' => 'Belgium',
            'num' => 56,
        ),

        'BMU' => array
        (
            'fr' => 'Bermudes',
            'en' => 'Bermuda',
            'num' => 60,
        ),

        'BTN' => array
        (
            'fr' => 'Bhoutan',
            'en' => 'Bhutan',
            'num' => 64,
        ),

        'BOL' => array
        (
            'fr' => 'Bolivie',
            'en' => 'Bolivia',
            'num' => 68,
        ),

        'BIH' => array
        (
            'fr' => 'Bosnie-Herzégovine',
            'en' => 'Bosnia and Herzegovina',
            'num' => 70,
        ),

        'BWA' => array
        (
            'fr' => 'Botswana',
            'en' => 'Botswana',
            'num' => 72,
        ),

        'BVT' => array
        (
            'fr' => 'Île Bouvet',
            'en' => 'Bouvet Island',
            'num' => 74,
        ),

        'BRA' => array
        (
            'fr' => 'Brésil',
            'en' => 'Brazil',
            'num' => 76,
        ),

        'BLZ' => array
        (
            'fr' => 'Belize',
            'en' => 'Belize',
            'num' => 84,
        ),

        'IOT' => array
        (
            'fr' => "Territoire Britannique de l'Océan Indien",
            'en' => 'British Indian Ocean Territory',
            'num' => 86,
        ),

        'SLB' => array
        (
            'fr' => 'Îles Salomon',
            'en' => 'Solomon Islands',
            'num' => 90,
        ),

        'VGB' => array
        (
            'fr' => 'Îles Vierges Britanniques',
            'en' => 'British Virgin Islands',
            'num' => 92,
        ),

        'BRN' => array
        (
            'fr' => 'Brunéi Darussalam',
            'en' => 'Brunei Darussalam',
            'num' => 96,
        ),

        'BGR' => array
        (
            'fr' => 'Bulgarie',
            'en' => 'Bulgaria',
            'num' => 100,
        ),

        'MMR' => array
        (
            'fr' => 'Myanmar',
            'en' => 'Myanmar',
            'num' => 104,
        ),

        'BDI' => array
        (
            'fr' => 'Burundi',
            'en' => 'Burundi',
            'num' => 108,
        ),

        'BLR' => array
        (
            'fr' => 'Bélarus',
            'en' => 'Belarus',
            'num' => 112,
        ),

        'KHM' => array
        (
            'fr' => 'Cambodge',
            'en' => 'Cambodia',
            'num' => 116,
        ),

        'CMR' => array
        (
            'fr' => 'Cameroun',
            'en' => 'Cameroon',
            'num' => 120,
        ),

        'CAN' => array
        (
            'fr' => 'Canada',
            'en' => 'Canada',
            'num' => 124,
        ),

        'CPV' => array
        (
            'fr' => 'Cap-vert',
            'en' => 'Cape Verde',
            'num' => 132,
        ),

        'CYM' => array
        (
            'fr' => 'Îles Caïmanes',
            'en' => 'Cayman Islands',
            'num' => 136,
        ),

        'CAF' => array
        (
            'fr' => 'République Centrafricaine',
            'en' => 'Central African',
            'num' => 140,
        ),

        'LKA' => array
        (
            'fr' => 'Sri Lanka',
            'en' => 'Sri Lanka',
            'num' => 144,
        ),

        'TCD' => array
        (
            'fr' => 'Tchad',
            'en' => 'Chad',
            'num' => 148,
        ),

        'CHL' => array
        (
            'fr' => 'Chili',
            'en' => 'Chile',
            'num' => 152,
        ),

        'CHN' => array
        (
            'fr' => 'Chine',
            'en' => 'China',
            'num' => 156,
        ),

        'TWN' => array
        (
            'fr' => 'Taïwan',
            'en' => 'Taiwan',
            'num' => 158,
        ),

        'CXR' => array
        (
            'fr' => 'Île Christmas',
            'en' => 'Christmas Island',
            'num' => 162,
        ),

        'CCK' => array
        (
            'fr' => 'Îles Cocos (Keeling)',
            'en' => 'Cocos (Keeling) Islands',
            'num' => 166,
        ),

        'COL' => array
        (
            'fr' => 'Colombie',
            'en' => 'Colombia',
            'num' => 170,
        ),

        'COM' => array
        (
            'fr' => 'Comores',
            'en' => 'Comoros',
            'num' => 174,
        ),

        'MYT' => array
        (
            'fr' => 'Mayotte',
            'en' => 'Mayotte',
            'num' => 175,
        ),

        'COG' => array
        (
            'fr' => 'République du Congo',
            'en' => 'Republic of the Congo',
            'num' => 178,
        ),

        'COD' => array
        (
            'fr' => 'République Démocratique du Congo',
            'en' => 'The Democratic Republic Of The Congo',
            'num' => 180,
        ),

        'COK' => array
        (
            'fr' => 'Îles Cook',
            'en' => 'Cook Islands',
            'num' => 184,
        ),

        'CRI' => array
        (
            'fr' => 'Costa Rica',
            'en' => 'Costa Rica',
            'num' => 188,
        ),

        'HRV' => array
        (
            'fr' => 'Croatie',
            'en' => 'Croatia',
            'num' => 191,
        ),

        'CUB' => array
        (
            'fr' => 'Cuba',
            'en' => 'Cuba',
            'num' => 192,
        ),

        'CYP' => array
        (
            'fr' => 'Chypre',
            'en' => 'Cyprus',
            'num' => 196,
        ),

        'CZE' => array
        (
            'fr' => 'République Tchèque',
            'en' => 'Czech Republic',
            'num' => 203,
        ),

        'BEN' => array
        (
            'fr' => 'Bénin',
            'en' => 'Benin',
            'num' => 204,
        ),

        'DNK' => array
        (
            'fr' => 'Danemark',
            'en' => 'Denmark',
            'num' => 208,
        ),

        'DMA' => array
        (
            'fr' => 'Dominique',
            'en' => 'Dominica',
            'num' => 212,
        ),

        'DOM' => array
        (
            'fr' => 'République Dominicaine',
            'en' => 'Dominican Republic',
            'num' => 214,
        ),

        'ECU' => array
        (
            'fr' => 'Équateur',
            'en' => 'Ecuador',
            'num' => 218,
        ),

        'SLV' => array
        (
            'fr' => 'El Salvador',
            'en' => 'El Salvador',
            'num' => 222,
        ),

        'GNQ' => array
        (
            'fr' => 'Guinée Équatoriale',
            'en' => 'Equatorial Guinea',
            'num' => 226,
        ),

        'ETH' => array
        (
            'fr' => 'Éthiopie',
            'en' => 'Ethiopia',
            'num' => 231,
        ),

        'ERI' => array
        (
            'fr' => 'Érythrée',
            'en' => 'Eritrea',
            'num' => 232,
        ),

        'EST' => array
        (
            'fr' => 'Estonie',
            'en' => 'Estonia',
            'num' => 233,
        ),

        'FRO' => array
        (
            'fr' => 'Îles Féroé',
            'en' => 'Faroe Islands',
            'num' => 234,
        ),

        'FLK' => array
        (
            'fr' => 'Îles (malvinas), Falkland',
            'en' => 'Falkland Islands',
            'num' => 238,
        ),

        'SGS' => array
        (
            'fr' => 'Géorgie du Sud et les Îles Sandwich du Sud',
            'en' => 'South Georgia and the South Sandwich Islands',
            'num' => 239,
        ),

        'FJI' => array
        (
            'fr' => 'Fidji',
            'en' => 'Fiji',
            'num' => 242,
        ),

        'FIN' => array
        (
            'fr' => 'Finlande',
            'en' => 'Finland',
            'num' => 246,
        ),

        'ALA' => array
        (
            'fr' => 'Îles Åland',
            'en' => 'Åland Islands',
            'num' => 248,
        ),

        'FRA' => array
        (
            'fr' => 'France',
            'en' => 'France',
            'num' => 250,
        ),

        'GUF' => array
        (
            'fr' => 'Guyane Française',
            'en' => 'French Guiana',
            'num' => 254,
        ),

        'PYF' => array
        (
            'fr' => 'Polynésie Française',
            'en' => 'French Polynesia',
            'num' => 258,
        ),

        'ATF' => array
        (
            'fr' => 'Terres Australes Françaises',
            'en' => 'French Southern Territories',
            'num' => 260,
        ),

        'DJI' => array
        (
            'fr' => 'Djibouti',
            'en' => 'Djibouti',
            'num' => 262,
        ),

        'GAB' => array
        (
            'fr' => 'Gabon',
            'en' => 'Gabon',
            'num' => 266,
        ),

        'GEO' => array
        (
            'fr' => 'Géorgie',
            'en' => 'Georgia',
            'num' => 268,
        ),

        'GMB' => array
        (
            'fr' => 'Gambie',
            'en' => 'Gambia',
            'num' => 270,
        ),

        'PSE' => array
        (
            'fr' => 'Territoire Palestinien Occupé',
            'en' => 'Occupied Palestinian Territory',
            'num' => 275,
        ),

        'DEU' => array
        (
            'fr' => 'Allemagne',
            'en' => 'Germany',
            'num' => 276,
        ),

        'GHA' => array
        (
            'fr' => 'Ghana',
            'en' => 'Ghana',
            'num' => 288,
        ),

        'GIB' => array
        (
            'fr' => 'Gibraltar',
            'en' => 'Gibraltar',
            'num' => 292,
        ),

        'KIR' => array
        (
            'fr' => 'Kiribati',
            'en' => 'Kiribati',
            'num' => 296,
        ),

        'GRC' => array
        (
            'fr' => 'Grèce',
            'en' => 'Greece',
            'num' => 300,
        ),

        'GRL' => array
        (
            'fr' => 'Groenland',
            'en' => 'Greenland',
            'num' => 304,
        ),

        'GRD' => array
        (
            'fr' => 'Grenade',
            'en' => 'Grenada',
            'num' => 308,
        ),

        'GLP' => array
        (
            'fr' => 'Guadeloupe',
            'en' => 'Guadeloupe',
            'num' => 312,
        ),

        'GUM' => array
        (
            'fr' => 'Guam',
            'en' => 'Guam',
            'num' => 316,
        ),

        'GTM' => array
        (
            'fr' => 'Guatemala',
            'en' => 'Guatemala',
            'num' => 320,
        ),

        'GIN' => array
        (
            'fr' => 'Guinée',
            'en' => 'Guinea',
            'num' => 324,
        ),

        'GUY' => array
        (
            'fr' => 'Guyana',
            'en' => 'Guyana',
            'num' => 328,
        ),

        'HTI' => array
        (
            'fr' => 'Haïti',
            'en' => 'Haiti',
            'num' => 332,
        ),

        'HMD' => array
        (
            'fr' => 'Îles Heard et Mcdonald',
            'en' => 'Heard Island and McDonald Islands',
            'num' => 334,
        ),

        'VAT' => array
        (
            'fr' => 'Saint-Siège (état de la Cité du Vatican)',
            'en' => 'Vatican City State',
            'num' => 336,
        ),

        'HND' => array
        (
            'fr' => 'Honduras',
            'en' => 'Honduras',
            'num' => 340,
        ),

        'HKG' => array
        (
            'fr' => 'Hong-Kong',
            'en' => 'Hong Kong',
            'num' => 344,
        ),

        'HUN' => array
        (
            'fr' => 'Hongrie',
            'en' => 'Hungary',
            'num' => 348,
        ),

        'ISL' => array
        (
            'fr' => 'Islande',
            'en' => 'Iceland',
            'num' => 352,
        ),

        'IND' => array
        (
            'fr' => 'Inde',
            'en' => 'India',
            'num' => 356,
        ),

        'IDN' => array
        (
            'fr' => 'Indonésie',
            'en' => 'Indonesia',
            'num' => 360,
        ),

        'IRN' => array
        (
            'fr' => "République Islamique d'Iran",
            'en' => 'Islamic Republic of Iran',
            'num' => 364,
        ),

        'IRQ' => array
        (
            'fr' => 'Iraq',
            'en' => 'Iraq',
            'num' => 368,
        ),

        'IRL' => array
        (
            'fr' => 'Irlande',
            'en' => 'Ireland',
            'num' => 372,
        ),

        'ISR' => array
        (
            'fr' => 'Israël',
            'en' => 'Israel',
            'num' => 376,
        ),

        'ITA' => array
        (
            'fr' => 'Italie',
            'en' => 'Italy',
            'num' => 380,
        ),

        'CIV' => array
        (
            'fr' => "Côte d'Ivoire",
            'en' => "Côte d'Ivoire",
            'num' => 384,
        ),

        'JAM' => array
        (
            'fr' => 'Jamaïque',
            'en' => 'Jamaica',
            'num' => 388,
        ),

        'JPN' => array
        (
            'fr' => 'Japon',
            'en' => 'Japan',
            'num' => 392,
        ),

        'KAZ' => array
        (
            'fr' => 'Kazakhstan',
            'en' => 'Kazakhstan',
            'num' => 398,
        ),

        'JOR' => array
        (
            'fr' => 'Jordanie',
            'en' => 'Jordan',
            'num' => 400,
        ),

        'KEN' => array
        (
            'fr' => 'Kenya',
            'en' => 'Kenya',
            'num' => 404,
        ),

        'PRK' => array
        (
            'fr' => 'République Populaire Démocratique de Corée',
            'en' => "Democratic People's Republic of Korea",
            'num' => 408,
        ),

        'KOR' => array
        (
            'fr' => 'République de Corée',
            'en' => 'Republic of Korea',
            'num' => 410,
        ),

        'KWT' => array
        (
            'fr' => 'Koweït',
            'en' => 'Kuwait',
            'num' => 414,
        ),

        'KGZ' => array
        (
            'fr' => 'Kirghizistan',
            'en' => 'Kyrgyzstan',
            'num' => 417,
        ),

        'LAO' => array
        (
            'fr' => 'République Démocratique Populaire Lao',
            'en' => "Lao People's Democratic Republic",
            'num' => 418,
        ),

        'LBN' => array
        (
            'fr' => 'Liban',
            'en' => 'Lebanon',
            'num' => 422,
        ),

        'LSO' => array
        (
            'fr' => 'Lesotho',
            'en' => 'Lesotho',
            'num' => 426,
        ),

        'LVA' => array
        (
            'fr' => 'Lettonie',
            'en' => 'Latvia',
            'num' => 428,
        ),

        'LBR' => array
        (
            'fr' => 'Libéria',
            'en' => 'Liberia',
            'num' => 430,
        ),

        'LBY' => array
        (
            'fr' => 'Jamahiriya Arabe Libyenne',
            'en' => 'Libyan Arab Jamahiriya',
            'num' => 434,
        ),

        'LIE' => array
        (
            'fr' => 'Liechtenstein',
            'en' => 'Liechtenstein',
            'num' => 438,
        ),

        'LTU' => array
        (
            'fr' => 'Lituanie',
            'en' => 'Lithuania',
            'num' => 440,
        ),

        'LUX' => array
        (
            'fr' => 'Luxembourg',
            'en' => 'Luxembourg',
            'num' => 442,
        ),

        'MAC' => array
        (
            'fr' => 'Macao',
            'en' => 'Macao',
            'num' => 446,
        ),

        'MDG' => array
        (
            'fr' => 'Madagascar',
            'en' => 'Madagascar',
            'num' => 450,
        ),

        'MWI' => array
        (
            'fr' => 'Malawi',
            'en' => 'Malawi',
            'num' => 454,
        ),

        'MYS' => array
        (
            'fr' => 'Malaisie',
            'en' => 'Malaysia',
            'num' => 458,
        ),

        'MDV' => array
        (
            'fr' => 'Maldives',
            'en' => 'Maldives',
            'num' => 462,
        ),

        'MLI' => array
        (
            'fr' => 'Mali',
            'en' => 'Mali',
            'num' => 466,
        ),

        'MLT' => array
        (
            'fr' => 'Malte',
            'en' => 'Malta',
            'num' => 470,
        ),

        'MTQ' => array
        (
            'fr' => 'Martinique',
            'en' => 'Martinique',
            'num' => 474,
        ),

        'MRT' => array
        (
            'fr' => 'Mauritanie',
            'en' => 'Mauritania',
            'num' => 478,
        ),

        'MUS' => array
        (
            'fr' => 'Maurice',
            'en' => 'Mauritius',
            'num' => 480,
        ),

        'MEX' => array
        (
            'fr' => 'Mexique',
            'en' => 'Mexico',
            'num' => 484,
        ),

        'MCO' => array
        (
            'fr' => 'Monaco',
            'en' => 'Monaco',
            'num' => 492,
        ),

        'MNG' => array
        (
            'fr' => 'Mongolie',
            'en' => 'Mongolia',
            'num' => 496,
        ),

        'MDA' => array
        (
            'fr' => 'République de Moldova',
            'en' => 'Republic of Moldova',
            'num' => 498,
        ),

        'MSR' => array
        (
            'fr' => 'Montserrat',
            'en' => 'Montserrat',
            'num' => 500,
        ),

        'MAR' => array
        (
            'fr' => 'Maroc',
            'en' => 'Morocco',
            'num' => 504,
        ),

        'MOZ' => array
        (
            'fr' => 'Mozambique',
            'en' => 'Mozambique',
            'num' => 508,
        ),

        'OMN' => array
        (
            'fr' => 'Oman',
            'en' => 'Oman',
            'num' => 512,
        ),

        'NAM' => array
        (
            'fr' => 'Namibie',
            'en' => 'Namibia',
            'num' => 516,
        ),

        'NRU' => array
        (
            'fr' => 'Nauru',
            'en' => 'Nauru',
            'num' => 520,
        ),

        'NPL' => array
        (
            'fr' => 'Népal',
            'en' => 'Nepal',
            'num' => 524,
        ),

        'NLD' => array
        (
            'fr' => 'Pays-Bas',
            'en' => 'Netherlands',
            'num' => 528,
        ),

        'ABW' => array
        (
            'fr' => 'Aruba',
            'en' => 'Aruba',
            'num' => 533,
        ),

        'NCL' => array
        (
            'fr' => 'Nouvelle-Calédonie',
            'en' => 'New Caledonia',
            'num' => 540,
        ),

        'VUT' => array
        (
            'fr' => 'Vanuatu',
            'en' => 'Vanuatu',
            'num' => 548,
        ),

        'NZL' => array
        (
            'fr' => 'Nouvelle-Zélande',
            'en' => 'New Zealand',
            'num' => 554,
        ),

        'NIC' => array
        (
            'fr' => 'Nicaragua',
            'en' => 'Nicaragua',
            'num' => 558,
        ),

        'NER' => array
        (
            'fr' => 'Niger',
            'en' => 'Niger',
            'num' => 562,
        ),

        'NGA' => array
        (
            'fr' => 'Nigéria',
            'en' => 'Nigeria',
            'num' => 566,
        ),

        'NIU' => array
        (
            'fr' => 'Niué',
            'en' => 'Niue',
            'num' => 570,
        ),

        'NFK' => array
        (
            'fr' => 'Île Norfolk',
            'en' => 'Norfolk Island',
            'num' => 574,
        ),

        'NOR' => array
        (
            'fr' => 'Norvège',
            'en' => 'Norway',
            'num' => 578,
        ),

        'MNP' => array
        (
            'fr' => 'Îles Mariannes du Nord',
            'en' => 'Northern Mariana Islands',
            'num' => 580,
        ),

        'UMI' => array
        (
            'fr' => 'Îles Mineures Éloignées des États-Unis',
            'en' => 'United States Minor Outlying Islands',
            'num' => 581,
        ),

        'FSM' => array
        (
            'fr' => 'États Fédérés de Micronésie',
            'en' => 'Federated States of Micronesia',
            'num' => 583,
        ),

        'MHL' => array
        (
            'fr' => 'Îles Marshall',
            'en' => 'Marshall Islands',
            'num' => 584,
        ),

        'PLW' => array
        (
            'fr' => 'Palaos',
            'en' => 'Palau',
            'num' => 585,
        ),

        'PAK' => array
        (
            'fr' => 'Pakistan',
            'en' => 'Pakistan',
            'num' => 586,
        ),

        'PAN' => array
        (
            'fr' => 'Panama',
            'en' => 'Panama',
            'num' => 591,
        ),

        'PNG' => array
        (
            'fr' => 'Papouasie-Nouvelle-Guinée',
            'en' => 'Papua New Guinea',
            'num' => 598,
        ),

        'PRY' => array
        (
            'fr' => 'Paraguay',
            'en' => 'Paraguay',
            'num' => 600,
        ),

        'PER' => array
        (
            'fr' => 'Pérou',
            'en' => 'Peru',
            'num' => 604,
        ),

        'PHL' => array
        (
            'fr' => 'Philippines',
            'en' => 'Philippines',
            'num' => 608,
        ),

        'PCN' => array
        (
            'fr' => 'Pitcairn',
            'en' => 'Pitcairn',
            'num' => 612,
        ),

        'POL' => array
        (
            'fr' => 'Pologne',
            'en' => 'Poland',
            'num' => 616,
        ),

        'PRT' => array
        (
            'fr' => 'Portugal',
            'en' => 'Portugal',
            'num' => 620,
        ),

        'GNB' => array
        (
            'fr' => 'Guinée-Bissau',
            'en' => 'Guinea-Bissau',
            'num' => 624,
        ),

        'TLS' => array
        (
            'fr' => 'Timor-Leste',
            'en' => 'Timor-Leste',
            'num' => 626,
        ),

        'PRI' => array
        (
            'fr' => 'Porto Rico',
            'en' => 'Puerto Rico',
            'num' => 630,
        ),

        'QAT' => array
        (
            'fr' => 'Qatar',
            'en' => 'Qatar',
            'num' => 634,
        ),

        'REU' => array
        (
            'fr' => 'Réunion',
            'en' => 'Réunion',
            'num' => 638,
        ),

        'ROU' => array
        (
            'fr' => 'Roumanie',
            'en' => 'Romania',
            'num' => 642,
        ),

        'RUS' => array
        (
            'fr' => 'Fédération de Russie',
            'en' => 'Russian Federation',
            'num' => 643,
        ),

        'RWA' => array
        (
            'fr' => 'Rwanda',
            'en' => 'Rwanda',
            'num' => 646,
        ),

        'SHN' => array
        (
            'fr' => 'Sainte-Hélène',
            'en' => 'Saint Helena',
            'num' => 654,
        ),

        'KNA' => array
        (
            'fr' => 'Saint-Kitts-et-Nevis',
            'en' => 'Saint Kitts and Nevis',
            'num' => 659,
        ),

        'AIA' => array
        (
            'fr' => 'Anguilla',
            'en' => 'Anguilla',
            'num' => 660,
        ),

        'LCA' => array
        (
            'fr' => 'Sainte-Lucie',
            'en' => 'Saint Lucia',
            'num' => 662,
        ),

        'SPM' => array
        (
            'fr' => 'Saint-Pierre-et-Miquelon',
            'en' => 'Saint-Pierre and Miquelon',
            'num' => 666,
        ),

        'VCT' => array
        (
            'fr' => 'Saint-Vincent-et-les Grenadines',
            'en' => 'Saint Vincent and the Grenadines',
            'num' => 670,
        ),

        'SMR' => array
        (
            'fr' => 'Saint-Marin',
            'en' => 'San Marino',
            'num' => 674,
        ),

        'STP' => array
        (
            'fr' => 'Sao Tomé-et-Principe',
            'en' => 'Sao Tome and Principe',
            'num' => 678,
        ),

        'SAU' => array
        (
            'fr' => 'Arabie Saoudite',
            'en' => 'Saudi Arabia',
            'num' => 682,
        ),

        'SEN' => array
        (
            'fr' => 'Sénégal',
            'en' => 'Senegal',
            'num' => 686,
        ),

        'SYC' => array
        (
            'fr' => 'Seychelles',
            'en' => 'Seychelles',
            'num' => 690,
        ),

        'SLE' => array
        (
            'fr' => 'Sierra Leone',
            'en' => 'Sierra Leone',
            'num' => 694,
        ),

        'SGP' => array
        (
            'fr' => 'Singapour',
            'en' => 'Singapore',
            'num' => 702,
        ),

        'SVK' => array
        (
            'fr' => 'Slovaquie',
            'en' => 'Slovakia',
            'num' => 703,
        ),

        'VNM' => array
        (
            'fr' => 'Viet Nam',
            'en' => 'Vietnam',
            'num' => 704,
        ),

        'SVN' => array
        (
            'fr' => 'Slovénie',
            'en' => 'Slovenia',
            'num' => 705,
        ),

        'SOM' => array
        (
            'fr' => 'Somalie',
            'en' => 'Somalia',
            'num' => 706,
        ),

        'ZAF' => array
        (
            'fr' => 'Afrique du Sud',
            'en' => 'South Africa',
            'num' => 710,
        ),

        'ZWE' => array
        (
            'fr' => 'Zimbabwe',
            'en' => 'Zimbabwe',
            'num' => 716,
        ),

        'ESP' => array
        (
            'fr' => 'Espagne',
            'en' => 'Spain',
            'num' => 724,
        ),

        'ESH' => array
        (
            'fr' => 'Sahara Occidental',
            'en' => 'Western Sahara',
            'num' => 732,
        ),

        'SDN' => array
        (
            'fr' => 'Soudan',
            'en' => 'Sudan',
            'num' => 736,
        ),

        'SUR' => array
        (
            'fr' => 'Suriname',
            'en' => 'Suriname',
            'num' => 740,
        ),

        'SJM' => array
        (
            'fr' => 'Svalbard etÎle Jan Mayen',
            'en' => 'Svalbard and Jan Mayen',
            'num' => 744,
        ),

        'SWZ' => array
        (
            'fr' => 'Swaziland',
            'en' => 'Swaziland',
            'num' => 748,
        ),

        'SWE' => array
        (
            'fr' => 'Suède',
            'en' => 'Sweden',
            'num' => 752,
        ),

        'CHE' => array
        (
            'fr' => 'Suisse',
            'en' => 'Switzerland',
            'num' => 756,
        ),

        'SYR' => array
        (
            'fr' => 'République Arabe Syrienne',
            'en' => 'Syrian Arab Republic',
            'num' => 760,
        ),

        'TJK' => array
        (
            'fr' => 'Tadjikistan',
            'en' => 'Tajikistan',
            'num' => 762,
        ),

        'THA' => array
        (
            'fr' => 'Thaïlande',
            'en' => 'Thailand',
            'num' => 764,
        ),

        'TGO' => array
        (
            'fr' => 'Togo',
            'en' => 'Togo',
            'num' => 768,
        ),

        'TKL' => array
        (
            'fr' => 'Tokelau',
            'en' => 'Tokelau',
            'num' => 772,
        ),

        'TON' => array
        (
            'fr' => 'Tonga',
            'en' => 'Tonga',
            'num' => 776,
        ),

        'TTO' => array
        (
            'fr' => 'Trinité-et-Tobago',
            'en' => 'Trinidad and Tobago',
            'num' => 780,
        ),

        'ARE' => array
        (
            'fr' => 'Émirats Arabes Unis',
            'en' => 'United Arab Emirates',
            'num' => 784,
        ),

        'TUN' => array
        (
            'fr' => 'Tunisie',
            'en' => 'Tunisia',
            'num' => 788,
        ),

        'TUR' => array
        (
            'fr' => 'Turquie',
            'en' => 'Turkey',
            'num' => 792,
        ),

        'TKM' => array
        (
            'fr' => 'Turkménistan',
            'en' => 'Turkmenistan',
            'num' => 795,
        ),

        'TCA' => array
        (
            'fr' => 'Îles Turks et Caïques',
            'en' => 'Turks and Caicos Islands',
            'num' => 796,
        ),

        'TUV' => array
        (
            'fr' => 'Tuvalu',
            'en' => 'Tuvalu',
            'num' => 798,
        ),

        'UGA' => array
        (
            'fr' => 'Ouganda',
            'en' => 'Uganda',
            'num' => 800,
        ),

        'UKR' => array
        (
            'fr' => 'Ukraine',
            'en' => 'Ukraine',
            'num' => 804,
        ),

        'MKD' => array
        (
            'fr' => "L'ex-République Yougoslave de Macédoine",
            'en' => 'The Former Yugoslav Republic of Macedonia',
            'num' => 807,
        ),

        'EGY' => array
        (
            'fr' => 'Égypte',
            'en' => 'Egypt',
            'num' => 818,
        ),

        'GBR' => array
        (
            'fr' => 'Royaume-Uni',
            'en' => 'United Kingdom',
            'num' => 826,
        ),

        'IMN' => array
        (
            'fr' => 'Île de Man',
            'en' => 'Isle of Man',
            'num' => 833,
        ),

        'TZA' => array
        (
            'fr' => 'République-Unie de Tanzanie',
            'en' => 'United Republic Of Tanzania',
            'num' => 834,
        ),

        'USA' => array
        (
            'fr' => 'États-Unis',
            'en' => 'United States',
            'num' => 840,
        ),

        'VIR' => array
        (
            'fr' => 'Îles Vierges des États-Unis',
            'en' => 'U.S. Virgin Islands',
            'num' => 850,
        ),

        'BFA' => array
        (
            'fr' => 'Burkina Faso',
            'en' => 'Burkina Faso',
            'num' => 854,
        ),

        'URY' => array
        (
            'fr' => 'Uruguay',
            'en' => 'Uruguay',
            'num' => 858,
        ),

        'UZB' => array
        (
            'fr' => 'Ouzbékistan',
            'en' => 'Uzbekistan',
            'num' => 860,
        ),

        'VEN' => array
        (
            'fr' => 'Venezuela',
            'en' => 'Venezuela',
            'num' => 862,
        ),

        'WLF' => array
        (
            'fr' => 'Wallis et Futuna',
            'en' => 'Wallis and Futuna',
            'num' => 876,
        ),

        'WSM' => array
        (
            'fr' => 'Samoa',
            'en' => 'Samoa',
            'num' => 882,
        ),

        'YEM' => array
        (
            'fr' => 'Yémen',
            'en' => 'Yemen',
            'num' => 887,
        ),

        'ZMB' => array
        (
            'fr' => 'Zambie',
            'en' => 'Zambia',
            'num' => 894,
        ),

        'BES' => array
        (
            'fr' => 'Bonaire, Saba, Saint-Eustache',
            'en' => 'Bonaire, Sint Eustatius and Saba',
            'num' => 535,
        ),

        'BLM' => array
        (
            'fr' => 'Saint-Barthélemy',
            'en' => 'Saint Barthélemy',
            'num' => 652,
        ),

        'CUW' => array
        (
            'fr' => 'Curaçao',
            'en' => 'Curaçao',
            'num' => 531,
        ),

        'GGY' => array
        (
            'fr' => 'Guernesey',
            'en' => 'Guernsey',
            'num' => 831,
        ),

        'JEY' => array
        (
            'fr' => 'Jersey',
            'en' => 'Jersey',
            'num' => 832,
        ),

        'MAF' => array
        (
            'fr' => 'Saint Martin ( Antilles françaises )',
            'en' => 'Saint Martin (French part)',
            'num' => 663,
        ),

        'MNE' => array
        (
            'fr' => 'Montenegro',
            'en' => 'Montenegro',
            'num' => 499,
        ),

        'SRB' => array
        (
            'fr' => 'Serbie',
            'en' => 'Serbia',
            'num' => 688,
        ),

        'SSD' => array
        (
            'fr' => 'Soudan du Sud',
            'en' => 'South Sudan',
            'num' => 728,
        ),

        'SXM' => array
        (
            'fr' => 'Saint-Martin (royaume des Pays-Bas)',
            'en' => 'Sint Maarten (Dutch part)',
            'num' => 534,
        ),

    );
    return $countries;
}

/**
 * ISO 3 & 2 countries 01/2023
 *
 * @author  Gauvain Van Ghele
 *
 * @since   1.6.0
 */
function seokey_helpers_get_codes_countries() {
    $codes = array (
        "AFG" =>"AF",
        "ALB" =>"AL",
        "ATA" =>"AQ",
        "DZA" =>"DZ",
        "ASM" =>"AS",
        "AND" =>"AD",
        "AGO" =>"AO",
        "ATG" =>"AG",
        "AZE" =>"AZ",
        "ARG" =>"AR",
        "AUS" =>"AU",
        "AUT" =>"AT",
        "BHS" =>"BS",
        "BHR" =>"BH",
        "BGD" =>"BD",
        "ARM" =>"AM",
        "BRB" =>"BB",
        "BEL" =>"BE",
        "BMU" =>"BM",
        "BTN" =>"BT",
        "BOL" =>"BO",
        "BIH" =>"BA",
        "BWA" =>"BW",
        "BVT" =>"BV",
        "BRA" =>"BR",
        "BLZ" =>"BZ",
        "IOT" =>"IO",
        "SLB" =>"SB",
        "VGB" =>"VG",
        "BRN" =>"BN",
        "BGR" =>"BG",
        "MMR" =>"MM",
        "BDI" =>"BI",
        "BLR" =>"BY",
        "KHM" =>"KH",
        "CMR" =>"CM",
        "CAN" =>"CA",
        "CPV" =>"CV",
        "CYM" =>"KY",
        "CAF" =>"CF",
        "LKA" =>"LK",
        "TCD" =>"TD",
        "CHL" =>"CL",
        "CHN" =>"CN",
        "TWN" =>"TW",
        "CXR" =>"CX",
        "CCK" =>"CC",
        "COL" =>"CO",
        "COM" =>"KM",
        "MYT" =>"YT",
        "COG" =>"CG",
        "COD" =>"CD",
        "COK" =>"CK",
        "CRI" =>"CR",
        "HRV" =>"HR",
        "CUB" =>"CU",
        "CYP" =>"CY",
        "CZE" =>"CZ",
        "BEN" =>"BJ",
        "DNK" =>"DK",
        "DMA" =>"DM",
        "DOM" =>"DO",
        "ECU" =>"EC",
        "SLV" =>"SV",
        "GNQ" =>"GQ",
        "ETH" =>"ET",
        "ERI" =>"ER",
        "EST" =>"EE",
        "FRO" =>"FO",
        "FLK" =>"FK",
        "SGS" =>"GS",
        "FJI" =>"FJ",
        "FIN" =>"FI",
        "ALA" =>"AX",
        "FRA" =>"FR",
        "GUF" =>"GF",
        "PYF" =>"PF",
        "ATF" =>"TF",
        "DJI" =>"DJ",
        "GAB" =>"GA",
        "GEO" =>"GE",
        "GMB" =>"GM",
        "PSE" =>"PS",
        "DEU" =>"DE",
        "GHA" =>"GH",
        "GIB" =>"GI",
        "KIR" =>"KI",
        "GRC" =>"GR",
        "GRL" =>"GL",
        "GRD" =>"GD",
        "GLP" =>"GP",
        "GUM" =>"GU",
        "GTM" =>"GT",
        "GIN" =>"GN",
        "GUY" =>"GY",
        "HTI" =>"HT",
        "HMD" =>"HM",
        "VAT" =>"VA",
        "HND" =>"HN",
        "HKG" =>"HK",
        "HUN" =>"HU",
        "ISL" =>"IS",
        "IND" =>"IN",
        "IDN" =>"ID",
        "IRN" =>"IR",
        "IRQ" =>"IQ",
        "IRL" =>"IE",
        "ISR" =>"IL",
        "ITA" =>"IT",
        "CIV" =>"CI",
        "JAM" =>"JM",
        "JPN" =>"JP",
        "KAZ" =>"KZ",
        "JOR" =>"JO",
        "KEN" =>"KE",
        "PRK" =>"KP",
        "KOR" =>"KR",
        "KWT" =>"KW",
        "KGZ" =>"KG",
        "LAO" =>"LA",
        "LBN" =>"LB",
        "LSO" =>"LS",
        "LVA" =>"LV",
        "LBR" =>"LR",
        "LBY" =>"LY",
        "LIE" =>"LI",
        "LTU" =>"LT",
        "LUX" =>"LU",
        "MAC" =>"MO",
        "MDG" =>"MG",
        "MWI" =>"MW",
        "MYS" =>"MY",
        "MDV" =>"MV",
        "MLI" =>"ML",
        "MLT" =>"MT",
        "MTQ" =>"MQ",
        "MRT" =>"MR",
        "MUS" =>"MU",
        "MEX" =>"MX",
        "MCO" =>"MC",
        "MNG" =>"MN",
        "MDA" =>"MD",
        "MSR" =>"MS",
        "MAR" =>"MA",
        "MOZ" =>"MZ",
        "OMN" =>"OM",
        "NAM" =>"NA",
        "NRU" =>"NR",
        "NPL" =>"NP",
        "NLD" =>"NL",
        "ABW" =>"AW",
        "NCL" =>"NC",
        "VUT" =>"VU",
        "NZL" =>"NZ",
        "NIC" =>"NI",
        "NER" =>"NE",
        "NGA" =>"NG",
        "NIU" =>"NU",
        "NFK" =>"NF",
        "NOR" =>"NO",
        "MNP" =>"MP",
        "UMI" =>"UM",
        "FSM" =>"FM",
        "MHL" =>"MH",
        "PLW" =>"PW",
        "PAK" =>"PK",
        "PAN" =>"PA",
        "PNG" =>"PG",
        "PRY" =>"PY",
        "PER" =>"PE",
        "PHL" =>"PH",
        "PCN" =>"PN",
        "POL" =>"PL",
        "PRT" =>"PT",
        "GNB" =>"GW",
        "TLS" =>"TL",
        "PRI" =>"PR",
        "QAT" =>"QA",
        "REU" =>"RE",
        "ROU" =>"RO",
        "RUS" =>"RU",
        "RWA" =>"RW",
        "SHN" =>"SH",
        "KNA" =>"KN",
        "AIA" =>"AI",
        "LCA" =>"LC",
        "SPM" =>"PM",
        "VCT" =>"VC",
        "SMR" =>"SM",
        "STP" =>"ST",
        "SAU" =>"SA",
        "SEN" =>"SN",
        "SYC" =>"SC",
        "SLE" =>"SL",
        "SGP" =>"SG",
        "SVK" =>"SK",
        "VNM" =>"VN",
        "SVN" =>"SI",
        "SOM" =>"SO",
        "ZAF" =>"ZA",
        "ZWE" =>"ZW",
        "ESP" =>"ES",
        "ESH" =>"EH",
        "SDN" =>"SD",
        "SUR" =>"SR",
        "SJM" =>"SJ",
        "SWZ" =>"SZ",
        "SWE" =>"SE",
        "CHE" =>"CH",
        "SYR" =>"SY",
        "TJK" =>"TJ",
        "THA" =>"TH",
        "TGO" =>"TG",
        "TKL" =>"TK",
        "TON" =>"TO",
        "TTO" =>"TT",
        "ARE" =>"AE",
        "TUN" =>"TN",
        "TUR" =>"TR",
        "TKM" =>"TM",
        "TCA" =>"TC",
        "TUV" =>"TV",
        "UGA" =>"UG",
        "UKR" =>"UA",
        "MKD" =>"MK",
        "EGY" =>"EG",
        "GBR" =>"GB",
        "IMN" =>"IM",
        "TZA" =>"TZ",
        "USA" =>"US",
        "VIR" =>"VI",
        "BFA" =>"BF",
        "URY" =>"UY",
        "UZB" =>"UZ",
        "VEN" =>"VE",
        "WLF" =>"WF",
        "WSM" =>"WS",
        "YEM" =>"YE",
        "ZMB" =>"ZM",
        "BES" =>"BQ",
        "BLM" =>"BL",
        "CUW" =>"CW",
        "GGY" =>"GG",
        "JEY" =>"JE",
        "MAF" =>"MF",
        "MNE" =>"ME",
        "SRB" =>"RS",
        "SSD" =>"SS",
        "SXM" =>"SX"
    );
    return $codes;
}