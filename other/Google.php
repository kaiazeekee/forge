<?php
/******************************************************************************\
+------------------------------------------------------------------------------+
| Foonster Publishing Software                                                 |
| Copyright (c) 2002 Foonster Technology                                       |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
|                                                                              |
| OWNERSHIP. The Software and all modifications or enhancements to, or         |
| derivative works based on the Software, whether created by Foonster          |
| Technology or you, and all copyrights, patents, trade secrets, trademarks    |
| and other intellectual property rights protecting or pertaining to any       |
| aspect of the Software or any such modification, enhancement or derivative   |
| work are and shall remain the sole and exclusive property of Foonster        |
| Technology.                                                                  |
|                                                                              |
| LIMITED RIGHTS. Pursuant to this Agreement, you may: (a) use the Software    |
| on one website only, for purposes of running one website only. You must      |
| provide Foonster Technology with exact URL (Unique Resource Locator) of the  |
| website you install the Software to; (b) modify the Software and/or merge    |
| it into another program; c) transfer the Software and license to another     |
| party if the other party agrees to accept the terms and conditions of this   |
| Agreement.                                                                   |
|                                                                              |
| Except as expressly set forth in this Agreement, you have no right to use,   |
| make, sublicense, modify, transfer or copy either the original or any copies |
| of the Software or to permit anyone else to do so. You may not allow any     |
| third party to use or have access to the Software. It is illegal to copy the |
| Software and install that single program for simultaneous use on multiple    |
| machines.                                                                    |
|                                                                              |
| PROPRIETARY NOTICES. You may not remove, disable, modify, or tamper with     |
| any copyright, trademark or other proprietary notices and legends contained  |
| within the code of the Software.                                             |
|                                                                              |
| COPIES.  "CUSTOMER" will be entitled to make a reasonable number of          |
| machine-readable copies of the Software for backup or archival purposes.     |
|                                                                              |
| LICENSE RESTRICTIONS. "CUSTOMER" agrees that you will not itself, or through |
| any parent, subsidiary, affiliate, agent or other third party:               |
|(a) sell, lease, license or sub-license the Software or the Documentation;    |
|(b) decompile, disassemble, or reverse engineer the Software, the Database,   |
| in whole or in part; (c) write or develop any derivative software or any     |
| other software program based upon the Software or any Confidential           |
| Information, | except pursuant to authorized Use of Software, if any; (d) use|
| the Software to provide services on a 'service bureau' basis; or (e) provide,|
| disclose, | divulge or make available to, or permit use of the Software by   |
| any unauthorized third party without Foonster Technology's prior written     |
| consent.                                                                     |
|                                                                              |
+------------------------------------------------------------------------------+
\******************************************************************************/
namespace foonster\forge\other;

class Google
{

    private $mapApiKey = '';

    /**
     * [__construct]
     */
    public function __construct()
    {

    }

    /**
     * [__destruct]
     */
    public function __destruct()
    {
        
    }

    /**
    * [generateQrCode]
    *
    * url: http://www.foonster.com
    * email address: mailto:wherever@example.com
    * MECARD:N:Owen,Sean;ADR:76 9th Avenue, 4th Floor, New York, NY 10011;TEL:+12125551212;EMAIL:srowen@example.com;
    * sms:+15105550101?body=hello%20there
    * geo:40.71872,-73.98905,100
    *
    * @param  string  $cUrl     [the string to be encoded based on the above parameters]
    * @param  string  $alttext  [alt text associated with the image]
    * @param  integer $nSize    [the size of the returned image]
    * @param  integer $EC_level [the error correction level]
    * @param  integer $nMargin  [margin associated with image generation]
    * @return string            [anchor html tag]
    */
    function generateQrCode($cUrl, $alttext = 'QR Code', $nSize = '250', $EC_level = '1', $nMargin = '0')
    {

        $cUrl = urlencode( $cUrl );
        return '<img src="http://chart.apis.google.com/chart?chs=' . $nSize . 'x' . $nSize . '&cht=qr&chld='.$EC_level . '|' . $nMargin . '&chl=' . $cUrl . '" alt="' . $altText . '" widhtHeight="' . $nSize . '" widhtHeight="' . $nSize . '"/>';

    }

    /**
     * [mapAddressInfo] Use GoogleMaps API to determine Lon/Lat - this may require a license
     *                depending on how you are using the application.
     *                
     * @param  string $address [Address]
     * @return [object]        [full map information]
     */
    public function mapAddressInfo($address = '')
    {

        $map = $this->slurp('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=true');
        $array = json_decode($map, true);    
        return (object) $array;
    }  

    /**
     * [mapLonLat]    Use GoogleMaps API to determine Lon/Lat - this may require a license
     *                depending on how you are using the application.
     *                
     * @param  string $address [Address Line 1]
     * @param  string $city    [City]
     * @param  string $state   [State]
     * @param  string $zip     [Zip Code]
     * @param  string $country [Country]
     * @return [object]        [Lon & Lat Variables]
     */
    public function mapLonLat($address = '', $city = '', $state = '', $zip = '', $country = 'US')
    {

        $return = array('lon' => '', 'lat' => '');
        $input = array();
        !empty($address) ? $input[] = urlencode($address) : false;
        !empty($city) ? $input[] = urlencode($city) : false;
        !empty($state) ? $input[] = urlencode($state) : false;
        !empty($zip) ? $input[] = urlencode($zip) : false;
        !empty($country) ? $input[] = urlencode($country) : false;
        $map = $this->slurp('http://maps.googleapis.com/maps/api/geocode/json?address=' . implode(',+', $input) . '&sensor=true');

        echo 'http://maps.googleapis.com/maps/api/geocode/json?address=' . implode(',+', $input) . '&sensor=true';

        $array = json_decode($map, true);
        if ($array['status'] != 'ZERO_RESULTS') {
            $return['lat'] = $array['results'][0]['geometry']['location']['lat'];
            $return['lon'] = $array['results'][0]['geometry']['location']['lng'];
        }
        return (object) $return;
    }

    /**
     * [setMapApiKey]
     */
    public function setMapApiKey($string)
    {
        $this->mapApiKey = $string;
        return $this;
    }

}
