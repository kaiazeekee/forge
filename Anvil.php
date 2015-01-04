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
namespace foonster\forge;

class Anvil
{
    public $error;
    private $_vars = array();
    private $_benchmarks = array();
    private $_tag;
    private $_options = array();
    private $_attributes = array();
    private $_dbh; // the database handle.

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    /**
    *
    * @set undefined vars
    * @param string $index
    * @param mixed $value
    * @return void
    *
    */
    public function __set($index, $value)
    {
        $this->_vars[$index] = $value;
    }
    /**
    *
    * @get variables
    *
    * @param mixed $index
    *
    * @return mixed
    *
    */
    public function __get($index)
    {
        return $this->_vars[$index];
    }


    /**
     * [array_to_columns description]
     * @param  [array]  $aArray     [description]
     * @param  integer $nCols      [number of columns to return]
     * @param  string  $cDirection [description]
     * @return [array]              [description]
     */
    public function arrayToColumns ($aArray, $nCols = 2, $cDirection = 'vertical')
    {

        $aReturn = array();   
        $nRows = @ sizeof($aArray);
        for( $i=1; $i<= $nCols; $i++) { $aReturn[$i] = array(); }
        if ( $nRows > 0 ) {
            $nTemp = ceil( $nRows / $nCols );
            if ( $cDirection == 'vertical' ) {        
                $nCols = 1;
                $nLoop = 0;      
                foreach ( $aArray AS $nId => $aValue ) 
                {   
                    $nLoop++;               
                    if ( $nLoop <= $nTemp ) {
                        $aReturn[$nCols][] = $aValue;

                    } else {    
                        $nLoop = 1;   
                        $nCols++;
                        $aReturn[$nCols][] = $aValue;         
                    }                
                }            
            } else {        
                // horizontal    
                foreach ( $aArray AS $nId => $aValue ) {   
                    $nLoop++;               
                    if ( $nLoop <= $nCols ) {                        
                        $aReturn[$nLoop][] = $aValue;            
                    } else {
                        $nLoop = 0;   
                        $nLoop++;
                        $aReturn[$nLoop][] = $aValue;
                    }            
                }
            }       
        }              
        return $aReturn;
    } 
    
    /**
     * [attributeExists description]
     * @param  [type] $cAttribute [description]
     * @return [type]             [description]
     */
    public static function attributeExists($cAttribute)
    {
        $oObject = get_object_vars($this);

        return array_key_exists($attribute, $object_vars);
    }

  
    /**
     * [benchMark]
     * @param  string $cType [description]
     * @return [type]        [description]
     */
    public function benchMark($marker = null)
    {
        if ($marker == null) {
            $marker = 'Mark: ' . (sizeof($this->_benchmarks)+1);
        }

        $this->_benchmarks[] = 
        array( 
            'marker' => $marker,
            'time' => microtime());
    }


    /**
     * [benchMarkElapsedTime]
     * @return integer [number of seconds]
     */
    public function benchMarkElapsedTime()
    {
        $return = array();        
        if (sizeof($this->_benchmarks) > 0) {
            if (sizeof($this->_benchmarks) == 1) {
                // there is only one value
                list($su, $sw) = preg_split('/ /', $this->_benchmarks[0]['time']);
                list($eu, $ew) = preg_split('/ /', microtime());
                $return[$this->_benchmarks[0]['marker']] = round((($ew + $eu) - ($sw + $su)), 4);
            } else {
                foreach ($this->_benchmarks as $n => $mark) {
                    if ($n > 0) {
                        list($su, $sw) = preg_split('/ /', $this->_benchmarks[($n-1)]['time']);
                        list($eu, $ew) = preg_split('/ /', $mark['time']);
                        $return[$this->_benchmarks[($n)]['marker']] = round((($ew + $eu) - ($sw + $su)), 4);
                    } else {
                        $return[$this->_benchmarks[($n)]['marker']] = 0;
                    }
                }
            }
        } else {
            $return['null'] = 0;
        }    
        return $return;
    }


    /**
     * [benchMarkReset - reset the benchmark options]
     * @return none
     */
    public function benchMarkReset()
    {
        $_benchmarks = array();
    }

    /**
     * [bytes - return filesize in human readable format]
     * @param  integer $size [number of filesize]
     * @return string       [formatted string with approximate number of bytes]
     */
    public static function bytes($size)
    {
        $i=0;
        $iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        while (($size / 1024) > 1) {
            $size= $size / 1024;
            $i++;
        }

        return substr($size, 0, strpos($size, '.') + 4) . ' ' . $iec[$i];
    }

    /**
     * [categoryTree description]
     * @param  [type] $cTable [description]
     * @return [type]         [description]
     */
    public function categoryTree( $cTable, $qualifier = '' )
    {
        $aCategories = array();

        !empty($qualifier) ? $qualifier = ' AND ' . $qualifier : false;

        $sql = $this->sqlRunQuery( "
            SELECT * FROM $cTable 
            WHERE 1 $qualifier 
            ORDER BY parnt_id , sortorder" );
        
        $records = $sql->fetchAll(\PDO::FETCH_OBJ);        
        
        foreach ($records as $n => $record) {
            empty($record->sortorder) ? $record->sortorder = 1 : false;
            $aCategories[$record->id] = (array) $record;
            $aCategories[$record->id]['directory'] = array();
        }

        foreach ($aCategories AS $c => $v) {       
            if ($v['parnt_id'] > 0) {               
                $aCategories[$v['parnt_id']]['directory'][] = $v;
            }
        }

        return $aCategories;
    }

    /**
     * [categoryDirectory description]
     * @param  [type] $aCategories [description]
     * @param  [type] $cCat        [description]
     * @param  string $cType       [description]
     * @return [type]              [description]
     */
    public function categoryDirectory( $aCategories , $cCat , $cType = 'name' )
    {

        $aReturn = array();     

        if ($cCat != null) {            
            $nParent = $aCategories[$cCat]['parnt_id'];
            ( $nParent == null ) ? $nParent = 0 : false;
            $aReturn[] = $aCategories[$cCat]['name'];
            while ($nParent > 0) {
                foreach ($aCategories AS $cCategory => $aValue) {
                    if (isset($aValue['id'])) {
                        if ($aValue['id'] == $nParent) {
                            $aReturn[] = $aValue['name'];                        
                            $nParent = $aValue['parnt_id'];
                        }
                    }
                }
            }
        }


        return json_encode(array_reverse( $aReturn ));
    }

    /**
     * [categoryTitle description]
     * @param  [type] $aCategories [description]
     * @param  [type] $cCat        [description]
     * @return [type]              [description]
     */
    public function categoryTitle( $aCategories , $cCat )
    {
        $aReturn = array();
        if ($cCat != null) {
            $nParent = $aCategories[$cCat]['parnt_id'];
            if ($nParent == null) { $nParent = 0; }
            $aReturn[] = $aCategories[$cCat]['name'];
            while ($nParent > 0) {
                foreach ($aCategories AS $cCategory => $aValue) {
                    if ($aValue['id'] == $nParent) {
                        $aReturn[] = $aValue['name'];
                        $nParent = $aValue['parnt_id'];
                    }
                }
            }
        }
        return array_reverse( $aReturn );
    }

    /**
     * [connectToDatabase description]
     * @param  string $database   [description]
     * @param  string $user       [description]
     * @param  string $password   [description]
     * @param  string $connection [description]
     * @return [type]             [description]
     */
    public function connectToDatabase($database = '', $user = '', $password = '', $connection = 'localhost')
    {
        try {
            $this->_dbh = new \PDO("mysql:host=" . $connection . ";dbname=" . $database, $user, $password);  
            $this->_dbh->setAttribute( \PDO::MYSQL_ATTR_FOUND_ROWS, true);   
            $this->_dbh->setAttribute (\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            $this->_dbh->setAttribute (\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
        } catch (\PDOException $e) {
            $this->error = "Error!: " . $e->getMessage() . "<br/>";
        } catch (\Exception $e) {
            $this->error = "Error!: " . $e->getMessage() . "<br/>";
        }
    }

    /**
     *  connectToDatabase : controls the overall connection to the database used
     *  by this clas..
     *  
     * 
     */
    public function changeDatabase($dbh)
    {
        $this->_dbh = $dbh;
    }

    /**
    *
    *
    *
    */
   
    public function formatDate($date, $type = 'mdY')
    {
        
        if ($type == 'expanded') {
            return date('l F j, Y', strtotime($date));
        } elseif ($type == 'fancy') {
            return date('l F j', strtotime($date)) . '<sup>' . date('S', strtotime($date)) . '</sup> ' . date('Y', strtotime($date));
        } elseif ($type == 'fancy_with_hours') {
            return date('l F j', strtotime($date)) . '<sup>' . date('S', strtotime($date)) . '</sup>' . date('G:i Y', strtotime($date));
        } elseif ($type == 'europe') {
            return date('d/m/Y', strtotime($date));
        } else {
            return date('m/d/Y', strtotime($date));
        }
    }


    /**
     * [csrf - create a random text string to use in csrf]
     * @return string []
     */
    public function csrf()
    {
        return self::encryptString(substr(self::pi(9999), rand(0,9900), rand(8,80)));
    }


    /**
     * [convertLbtoKg - convert lbs to kilograms]
     * @param  integer $pounds [number of pounds]
     * @return integer         [number in kilograms]
     */
    public static function convertLbtoKg($pounds)
    {
        return $pounds * 0.4535923;
    }


    /**
     * [curlPOST description]
     * @param  [type] $cUrl     [description]
     * @param  [type] $cFields  [description]
     * @param  [type] &$cResult [description]
     * @return [type]           [description]
     */
    public static function curlPOST($cUrl, $cFields, &$cResult)
    {

        $ch = curl_init();                  // URL of gateway for cURL to post to
        curl_setopt($ch, CURLOPT_URL, $this->_servername . $this->_url);
        curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $cPost);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            curl_setopt($ch, CURLOPT_CAINFO, 'C:\WINNT\curl-ca-bundle.crt');
        }
        $cResult = curl_exec($ch);

        if (!$cResult) {
            $cResult = curl_error($ch) . '::' . curl_errno($ch);
            curl_close($ch);

            return false;

        } else {
            return true;
        }
    }

    /**
    *
    *
    *
    */

    public static function dateDiff($nStart, $nEnd, $sel = 'Y')
    {

        $sY = 31536000;
        $sW = 604800;
        $sD = 86400;
        $sH = 3600;
        $sM = 60;
        $r = 0;

        $sel = strtolower(trim($sel));
        $nEnd = strtotime($nEnd);
        $nStart = strtotime($nStart);

        if ($nEnd < $nStart) {
            $nEnd = $nStart;
        }

        $t = ($nEnd - $nStart);

        if ($sel == 'y') { // years

            return ($t / $sY);
        } elseif ($sel == 'w') { // weeks

            return ($t / $sW);
        } elseif ($sel == 'd') { // days

            return ($t / $sD);
        } elseif ($sel == 'h') { // hours

            return ($t / $sH);
        } elseif ($sel == 'm') { // minutes

            return ($t / $sM);
        } else { // seconds

            return $t;
        }
    }

    /**
     * [decodeValue description]
     * @param  [type] $string [description]
     * @param  [type] $key    [description]
     * @return [type]         [description]
     */
    public function decodeValue($string, $key)
    {

        $result = '';

        for ($i=1; $i<=strlen($string); $i++) {

            $char = substr($string, $i-1, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }

        return $result;

    }

    /**
     * [directoryToArray description]
     * @param  [type]  $directory [description]
     * @param  string  $extension [description]
     * @param  boolean $full_path [description]
     * @return [type]             [description]
     */
    public function directoryToArray($directory, $extension = '', $full_path = true)
    {

        $array_items = array();

        if ($handle = opendir($directory)) {

            while (false !== ($file = readdir($handle))) {

                if ($file != '.' && $file != '..') {

                    if (is_dir($directory. '/' . $file)) {

                        $array_items = array_merge($array_items, directoryToArray($directory. '/' . $file, $extension, $full_path));
                    } else {

                        if (!$extension || (preg_match("/." . $extension . '/', $file))) {

                            if ($full_path) {

                                $array_items[] = $directory . '/' . $file;
                            } else {
                                $array_items[] = $file;
                            }
                        }
                    }
                }
            }

            closedir($handle);

            return $array_items;

        }

    }

    /**
     * [isJSON] - determine if string is a JSON string
     * @param  [string]  $string [string to be tested]
     * @return boolean   true = yes / false = no
     */
    public function isJSON($string)
    {
        if (!empty($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        } else {
            return 0;
        }
    }

    /**
     * [showGlobals] - echo out all the global variables.
     * @return none - echo's out all the global vars
     */
    public function showGlobals()
    {
        echo '<hr /><strong>SESSION</strong><hr />';
        self::dumpVariable($_SESSION);        
        echo '<hr /><strong>GET</strong><hr />';
        self::dumpVariable($_GET);
        echo '<hr /><strong>POST</strong><hr />';
        self::dumpVariable($_POST);
        echo '<hr /><strong>REQUEST</strong><hr />';
        self::dumpVariable($_REQUEST);
        echo '<hr /><strong>SERVER</strong><hr />';
        self::dumpVariable($_SERVER);
        echo '<hr /><strong>COOKIE</strong><hr />';
        self::dumpVariable($_COOKIE);        
        echo '<hr /><strong>CONNECTION</strong><hr />';
        self::dumpVariable($this);
        self::bytes(memory_get_peak_usage(true));
    }

    /**
     * [dumpVariable] - dump variable to HTML
     * @param  [string] $var [the variable to be dumped to the screen in HTML]
     * @return [string]      [the returned screen.]
     */
    public static function dumpVariable($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }


    /**
     * [encodeValue] - simple encoding method
     * @param  [string] $string [string to encode]
     * @param  [string] $key    [key for encoding]
     * @return [string]         [returned string]
     */
    public static function encodeValue($string, $key)
    {
        $result = '';
        for ($i=1; $i<=strlen($string); $i++) {
            $char = substr($string, $i-1, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result.=$char;
        }
        return $result;
    }

    /**
     * [encodeXMLString]
     * @param  [string] $string [valid XML string to be encoded]
     * @return [string]         [HTML encoded string]
     */
    public static function encodeXMLString(&$string)
    {
        str_replace(array('&', '"', "'", '<', '>'), array ('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $string);
    }

    /**
     * [encryptString]         [one-way encryption of a string]
     * @param  string $cString [the string to be encoded]
     * @param  string $cMethod [the method to use when encoding]
     * @param  string $cSalt   [the salt to use, if applicable.]
     * @return string          [then encrypted string]
     */
    public static function encryptString($cString, $cSalt = 'th3ra1ninsp@1ns@ysMainly1nth3pl#3n',  $cMethod = 'SHA512')
    {

        $cMethod = 'CRYPT_' . $cMethod;
        $nLoop = intval(date('Hms', strtotime($cSalt)));
        $cString = trim($cString);

        if ($cMethod == 'CRYPT_MD5') {
            return md5($cString);
        } elseif ($cMethod == 'CRYPT_STND_DES') {
            return str_replace(substr($cSalt, 0, 2), '', crypt($cString, substr($cSalt, 0, 2)));
        } elseif ($cMethod == 'CRYPT_EXT_DES') {
            $cSalt = $this->scrubVar($cSalt);

            return str_replace('_F6..' . substr($cSalt, 0, 4), '', crypt($cString, '_F6..' . substr($cSalt, 0, 4)));
        } elseif ($cMethod == 'CRYPT_BLOWFISH') {
            $cSalt = $this->scrubVar($cSalt);

            return str_replace('$2a$06$' . $cSalt .'$', '', crypt($cString, '$2a$06$' . $cSalt .'$'));

        } elseif ($cMethod == 'CRYPT_SHA256') {
            return str_replace('$5$rounds=' . $nLoop . '$' . substr($cSalt, 0, 16) . '$', '', crypt($cString, '$5$rounds=' . $nLoop . '$' . substr($cSalt, 0, 16) . '$'));

        } else {
            return str_replace('$6$rounds=' . $nLoop . '$' . substr($cSalt, 0, 16) . '$', '', crypt($cString, '$6$rounds=' . $nLoop . '$' . substr($cSalt, 0, 16) . '$'));
        }

    }

    
    /**
     * [extractDomainName]
     * @param  [string] $cString [a string containing a domain name]
     * @return [string]          [the domain name found]
     */
    public static function extractDomainName($cString)
    {
        $aUrl = parse_url($cString);
        return preg_replace('/^(?:.+?\.)+(.+?\.(?:co\.uk|com|net|edu|gov|org))(\:[0-9]{2,5})?\/*.*$/is', '$1', $aUrl['host']);
    }

    /**
     * [formatPhoneNumber] - take raw string and format according to appropriate country code
     * @param  [string] $cString [string containing number]
     * @param  [string] $cType   [country to use as template]
     * @return [string]          [final string]
     */
    public static function formatPhoneNumber($cString, $cType = 'US')
    {
        $cString = preg_replace("/[^0-9a-zA-Z]/", '', $cString);
        if ($cType == 'US') {
            $strArea = substr($cString, 0, 3);
            $strPrefix = substr($cString, 3, 3);
            $strNumber = substr($cString, 6, 4);
            $strElse = substr($cString, 10);
            return "(".$strArea.") ".$strPrefix."-".$strNumber." ".$strElse;
        }
    }


    /**
     * [getAge] - compute the age in years base on today vs date.
     *
     *  This function does not suffer the rounding issue found in other solutions when 
     *  you start hitting the 70+ range.
     * 
     * @param  [string] $cDOB [string representing birthday]
     * @return [integer]      [number of years between the two dates.]
     */
    public static function getAge($cDOB)
    {

        if ($cDOB != null && ($cDOB != '0000-00-00' || $cDOB != '0000-00-00 00:00:00')) {
            $cDOB = strtolower(preg_replace("/[^0-9\/\-\.]/", '', trim(date('m/d/Y', strtotime($cDOB)))));
            if (strlen($cDOB) == 10 || strlen($cDOB) == 9 || strlen($cDOB) == 8) {
                $ddiff = date("d") - date("d", strtotime($cDOB));
                $mdiff = date("m") - date("m", strtotime($cDOB));
                $ydiff = date("Y") - date("Y", strtotime($cDOB));
                if ($mdiff < 0) {
                    $ydiff--;
                } elseif ($mdiff==0) {
                    if ($ddiff < 0) {
                        $ydiff--;
                    }
                } else {
                }
                return $ydiff;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * [getFileExtension] - extract the file extension from a path name.
     * @param  [string] $name [The path name to be evaluated]
     * @return [string]       [The file extension]
     */
    public function getFileExtension($name)
    {
        $ext = strrchr($name, '.');
        if ($ext !== false) {
            $name = substr($name, 0, -strlen($ext));
        }

        return $ext;
    }

    /**
     * [getIpAddress] - get the IP address of incoming traffic
     * @return [string] [The IP Address]
     */
    public static function getIpAddress()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            return getenv('HTTP_CLIENT_IP');

        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');

        } elseif (getenv('HTTP_X_FORWARDED')) {
            return getenv('HTTP_X_FORWARDED');

        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            return getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            return getenv('HTTP_FORWARDED');
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * [googleLonLat] Use GoogleMaps API to determine Lon/Lat - this may require a license
     *                depending on how you are using the application.
     *                
     * @param  string $address [Address Line 1]
     * @param  string $city    [City]
     * @param  string $state   [State]
     * @param  string $zip     [Zip Code]
     * @param  string $country [Country]
     * @return [object]        [Lon & Lat Variables]
     */
    public function googleLonLat($address = '', $city = '', $state = '', $zip = '', $country = 'US')
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
     * [googleLonLat] Use GoogleMaps API to determine Lon/Lat - this may require a license
     *                depending on how you are using the application.
     *                
     * @param  string $address [Address]
     * @return [object]        [full map information]
     */
    public function googleMapInfo($address = '')
    {

        $map = $this->slurp('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=true');

        $array = json_decode($map, true);
    
        return (object) $array;
    }    

    /**
     * [getTimeStamp description]
     * @param  string $cFormat [description]
     * @return [type]          [description]
     */
    public static function getTimeStamp($cFormat = 'MYSQL')
    {

        $cFormat = strtoupper(trim($cFormat));

        $nTime = (time() - date('Z'));

        if ($cFormat == 'ISO8601' || $cFormat == 'ATOM' || $cFormat == 'W3C') {
            return date("Y-m-d", $nTime).'T'.date("H:i:sO", $nTime);

        } elseif ($cFormat == 'COOKIE' || $cFormat == 'RFC822' || $cFormat == 'RFC1123') {
            return date("D, d M Y H:i:s", $nTime).' UTC';

        } elseif ($cFormat == 'RFC850' || $cFormat == 'RFC1036') {
            return date("l, d-M-y H:i:s", $nTime).' UTC';

        } elseif ($cFormat == 'RFC2822') {
            return date("D, d M Y H:i:s O", $nTime);

        } elseif ($cFormat == 'RSS') {
            return date("D, d M Y H:i:s", $nTime).' UTC';

        } elseif ($cFormat == 'EPOCH') {
            return time();

        } else {
            return date("Y-m-d H:i:s", (time()-date('Z')));

        }

    }

    /**
     * [isRecord description]
     * @param  [type]  $nRecord [description]
     * @return boolean          [description]
     */
    public static function isRecord($nRecord = null)
    {
        if (!empty($nRecord) && $nRecord > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [isStringHard - determine if string is hardend to requested level.]
     * @param  [string]  $cString  [the string to be tested]
     * @param  integer $nLen     [the minimum length required for the string]
     * @param  boolean $lSpecial [require special characters.]
     * @return boolean           [true/false if tests fail.]
     */
    public function isStringHard ($cString, $nLen = 8, $lSpecial = true)
    {
        if (strlen($cString) >= $nLen) {
            if (preg_match('/[A-Z]/', $cString)) {
                if (preg_match('/[a-z]/', $cString)) {
                    if (preg_match('/[0-9]/', $cString)) {
                        if ($lSpecial) {
                            if (preg_match("/\~\@\#\%\^\&\*\(\)\-\_\=\+\[\]\{\}\\\|\:\"\,\.\<\>\/\?/", $cString)) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

  /**
   * [isValidEmailAddress - test for valid email address provided.]
   * @param  string  $email     [string to be validated]
   * @param  boolean $lDNSCheck [if true, perform DNS check to ensure domain is valid.]
   * @return boolean           [true/false if tests fail.]
   */
    public function isValidEmailAddress($email, $lDNSCheck = false)
    {

        $email = strtolower(trim($email));

        if (preg_match('/[\x00-\x1F\x7F-\xFF]/', $email)) {
            return false;
        }

        if (!preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
            return false;
        }

        // Split it into sections to make life easier
        $email_array = explode("@", $email);

        $local_array = explode(".", $email_array[0]);

        // CHECK LOCAL ARRAY

        foreach ($local_array as $local_part) {
            if (!preg_match('/^(([A-Za-z0-9!#$%&\'*+\/=?^_`{|}~-]+)|("[^"]+"))$/', $local_part)) {
                return false;
            }
        }

        if (!preg_match('/^\[?[0-9\.]+\]?$/', $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name

            $domain_array = explode('.', $email_array[1]);

            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }

            foreach ($domain_array as $domain_part) {

                if (!preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/', $domain_part)) {
                    return false;
                }
            }

            if ($lDNSCheck) {
                if (checkdnsrr($email_array[1])) {
                    return true;

                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

    /**
     * [isRobot description]
     * @param  [type]  $cAgent  [description]
     * @param  [type]  $cRobots [description]
     * @return boolean          [description]
     */
    public static function isRobot ($cAgent = null, $cRobots = null)
    {
        $cRobots = array(
            'ABCdatos\sBotLink',            
            'YodaoBot'
           );

        empty($cAgent) ? $cAgent = $_SERVER['HTTP_USER_AGENT'] : false ;

        $cImplode = implode('|', $aCrawlers);

        if (preg_match("/$cImplode/i", $cAgent)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * [lpad] - pad the left side of a string with a character.
     * @param  [integer] $length  [the total length of the string]
     * @param  [string] $string   [the string to pad]
     * @param  string $character  [the character to pad with]
     * @return [string]           [the returned string]
     */
    public static function lpad($length, $string, $character = ' ')
    {
        $length = ($length - strlen($string));
        for ($counter = 1; $counter <= $length; $counter++) {
            $string = $character . $string;
        }
        return $string;
    }

    /**
     * [lwcase] - convert a string to lower-case letters
     * @param  [string] $cString [the string to convert]
     * @return [string]          [the converted string]
     */
    public static function lwcase($cString)
    {
        return strtolower(trim($cString));
    }

    /**
     * [mkdirs] - create the directory path if not present.
     * @param  string  $dir       [directory path]
     * @param  integer $mode      [numeric representation of permissions]
     * @param  boolean $recursive [create directories recursively]
     * @return [boolea]           [returns true/false on success]
     */
    public static function mkdirs($dir, $mode = 0766, $recursive = true)
    {
        if (is_null($dir) || $dir === '') {
            return false;
        }
        if (is_dir($dir) || $dir === "/") {
            return true;
        }
        if (self::mkdirs(dirname($dir), $mode, $recursive)) {
            $old_umask = umask(0);
            return @ mkdir($dir, $mode);
            umask($old_umask);
        }
        return false;
    }

    /**
     * [objectToArray] - convert an object to an array
     * @param  [object] $obj [the incoming object]
     * @return [array]       [the outgoing array]
     */
    public function objectToArray($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * [pagination description]
     * @param  integer $record  [description]
     * @param  integer $records [description]
     * @param  integer $perpage [description]
     * @param  integer $range   [description]
     * @return [type]           [description]
     */
    public function pagination($record = 0, $records = 0, $perpage = 24, $range = 2)
    {

        !is_numeric($record) ? $record = 0 : false;

        ($record < 0) ? $record = 0 : false;

        ($record > $records) ? $record = $records : false;

        $aPaginate = array();

        $aPaginate['total'] = ceil($records / $perpage);

        $aPaginate['page'] = ceil(($record + 1) / $perpage);

        ($aPaginate['page'] > $aPaginate['total']) ? $aPaginate['page'] = $aPaginate['total'] : false;

        $aPaginate['current'] = (($aPaginate['page'] * $perpage) - $perpage);

        ($aPaginate['current'] < 0) ? $aPaginate['current'] = 0 : false;

        $aPaginate['first'] = 0;

        $aPaginate['last'] = (($aPaginate['total'] * $perpage) - $perpage);

    // next

        $aPaginate['next'] = ($aPaginate['current'] + $perpage);
        ($aPaginate['next'] > $aPaginate['last']) ? $aPaginate['next'] = $aPaginate['last'] : false;

    // prev

        $aPaginate['prev'] = ($aPaginate['current'] - $perpage);
        ($aPaginate['prev'] < $aPaginate['first']) ? $aPaginate['prev'] = $aPaginate['first'] : false;

    // range

        $First = ($aPaginate['current'] + 1);

        ($First > $records) ? $First = $records : false;

        $Top = ($aPaginate['current'] + $perpage);

        ($Top > $records) ? $Top = $records : false;

        $aPaginate['range'] = number_format($First, 0)  . ' to ' . number_format($Top, 0) . ' of ' . number_format($records, 0);

        $aPaginate['pages'] = array();

    // the items before

        for ($i = $range; $i >= 1; $i--) {
            if (($aPaginate['current'] - ($i * $perpage)) >= 0) {
                $aPaginate['pages'][] = array(
                'cnt' => ($aPaginate['current'] - ($i * $perpage)),
                'pg' => ceil((($aPaginate['current'] - ($i * $perpage)) + 1) / $perpage)
                );
            }
        }

        $aPaginate['pages'][] = array(
            'cnt' => $aPaginate['current'],
            'pg' => $aPaginate['page']
        );

        for ($i = 1; $i <= $range; $i++) {
            if (($aPaginate['current'] + ($i * $perpage)) <= $aPaginate['last']) {
                $aPaginate['pages'][] = array(
                'cnt' => ($aPaginate['current'] + ($i * $perpage)),
                'pg' => ceil((($aPaginate['current'] + ($i * $perpage)) + 1) / $perpage)
                );
            }
        }

        return (object) $aPaginate;

    }

    /**
     * [pi]
     * @return integer calculated to 10,000 digits
     */
    public static function pi($length = 24)
    {
        $pi = '3.141592653589793238462643383279502884197169399375105820974944592307816406286208998628034825342117067982148086513282306647093844609550582231725359408128481117450284102701938521105559644622948954930381964428810975665933446128475648233786783165271201909145648566923460348610454326648213393607260249141273724587006606315588174881520920962829254091715364367892590360011330530548820466521384146951941511609433057270365759591953092186117381932611793105118548074462379962749567351885752724891227938183011949129833673362440656643086021394946395224737190702179860943702770539217176293176752384674818467669405132000568127145263560827785771342757789609173637178721468440901224953430146549585371050792279689258923542019956112129021960864034418159813629774771309960518707211349999998372978049951059731732816096318595024459455346908302642522308253344685035261931188171010003137838752886587533208381420617177669147303598253490428755468731159562863882353787593751957781857780532171226806613001927876611195909216420198938095257201065485863278865936153381827968230301952035301852968995773622599413891249721775283479131515574857242454150695950829533116861727855889075098381754637464939319255060400927701671139009848824012858361603563707660104710181942955596198946767837449448255379774726847104047534646208046684259069491293313677028989152104752162056966024058038150193511253382430035587640247496473263914199272604269922796782354781636009341721641219924586315030286182974555706749838505494588586926995690927210797509302955321165344987202755960236480665499119881834797753566369807426542527862551818417574672890977772793800081647060016145249192173217214772350141441973568548161361157352552133475741849468438523323907394143334547762416862518983569485562099219222184272550254256887671790494601653466804988627232791786085784383827967976681454100953883786360950680064225125205117392984896084128488626945604241965285022210661186306744278622039194945047123713786960956364371917287467764657573962413890865832645995813390478027590099465764078951269468398352595709825822620522489407726719478268482601476990902640136394437455305068203496252451749399651431429809190659250937221696461515709858387410597885959772975498930161753928468138268683868942774155991855925245953959431049972524680845987273644695848653836736222626099124608051243884390451244136549762780797715691435997700129616089441694868555848406353422072225828488648158456028506016842739452267467678895252138522549954666727823986456596116354886230577456498035593634568174324112515076069479451096596094025228879710893145669136867228748940560101503308617928680920874760917824938589009714909675985261365549781893129784821682998948722658804857564014270477555132379641451523746234364542858444795265867821051141354735739523113427166102135969536231442952484937187110145765403590279934403742007310578539062198387447808478489683321445713868751943506430218453191048481005370614680674919278191197939952061419663428754440643745123718192179998391015919561814675142691239748940907186494231961567945208095146550225231603881930142093762137855956638937787083039069792077346722182562599661501421503068038447734549202605414665925201497442850732518666002132434088190710486331734649651453905796268561005508106658796998163574736384052571459102897064140110971206280439039759515677157700420337869936007230558763176359421873125147120532928191826186125867321579198414848829164470609575270695722091756711672291098169091528017350671274858322287183520935396572512108357915136988209144421006751033467110314126711136990865851639831501970165151168517143765761835155650884909989859982387345528331635507647918535893226185489632132933089857064204675259070915481416549859461637180270981994309924488957571282890592323326097299712084433573265489382391193259746366730583604142813883032038249037589852437441702913276561809377344403070746921120191302033038019762110110044929321516084244485963766983895228684783123552658213144957685726243344189303968642624341077322697802807318915441101044682325271620105265227211166039666557309254711055785376346682065310989652691862056476931257058635662018558100729360659876486117910453348850346113657686753249441668039626579787718556084552965412665408530614344431858676975145661406800700237877659134401712749470420562230538994561314071127000407854733269939081454664645880797270826683063432858785698305235808933065757406795457163775254202114955761581400250126228594130216471550979259230990796547376125517656751357517829666454779174501129961489030463994713296210734043751895735961458901938971311179042978285647503203198691514028708085990480109412147221317947647772622414254854540332157185306142288137585043063321751829798662237172159160771669254748738986654949450114654062843366393790039769265672146385306736096571209180763832716641627488880078692560290228472104031721186082041900042296617119637792133757511495950156604963186294726547364252308177036751590673502350728354056704038674351362222477158915049530984448933309634087807693259939780541934144737744184263129860809988868741326047215695162396586457302163159819319516735381297416772947867242292465436680098067692823828068996400482435403701416314965897940924323789690706977942236250822168895738379862300159377647165122893578601588161755782973523344604281512627203734314653197777416031990665541876397929334419521541341899485444734567383162499341913181480927777103863877343177207545654532207770921201905166096280490926360197598828161332316663652861932668633606273567630354477628035045077723554710585954870279081435624014517180624643626794561275318134078330336254232783944975382437205835311477119926063813346776879695970309833913077109870408591337464144282277263465947047458784778720192771528073176790770715721344473060570073349243693113835049316312840425121925651798069411352801314701304781643788518529092854520116583934196562134914341595625865865570552690496520985803385072242648293972858478316305777756068887644624824685792603953527734803048029005876075825104747091643961362676044925627420420832085661190625454337213153595845068772460290161876679524061634252257719542916299193064553779914037340432875262888963995879475729174642635745525407909145135711136941091193932519107602082520261879853188770584297259167781314969900901921169717372784768472686084900337702424291651300500516832336435038951702989392233451722013812806965011784408745196012122859937162313017114448464090389064495444006198690754851602632750529834918740786680881833851022833450850486082503930213321971551843063545500766828294930413776552793975175461395398468339363830474611996653858153842056853386218672523340283087112328278921250771262946322956398989893582116745627010218356462201349671518819097303811980049734072396103685406643193950979019069963955245300545058068550195673022921913933918568034490398205955100226353536192041994745538593810234395544959778377902374216172711172364343543947822181852862408514006660443325888569867054315470696574745855033232334210730154594051655379068662733379958511562578432298827372319898757141595781119635833005940873068121602876496286744604774649159950549737425626901049037781986835938146574126804925648798556145372347867330390468838343634655379498641927056387293174872332083760112302991136793862708943879936201629515413371424892830722012690147546684765357616477379467520049075715552781965362132392640616013635815590742202020318727760527721900556148425551879253034351398442532234157623361064250639049750086562710953591946589751413103482276930624743536325691607815478181152843667957061108615331504452127473924544945423682886061340841486377670096120715124914043027253860764823634143346235189757664521641376796903149501910857598442391986291642193994907236234646844117394032659184044378051333894525742399508296591228508555821572503107125701266830240292952522011872676756220415420516184163484756516999811614101002996078386909291603028840026910414079288621507842451670908700069928212066041837180653556725253256753286129104248776182582976515795984703562226293486003415872298053498965022629174878820273420922224533985626476691490556284250391275771028402799806636582548892648802545661017296702664076559042909945681506526530537182941270336931378517860904070866711496558343434769338578171138645587367812301458768712660348913909562009939361031029161615288138437909904231747336394804575931493140529763475748119356709110137751721008031559024853090669203767192203322909433467685142214477379393751703443661991040337511173547191855046449026365512816228824462575916333039107225383742182140883508657391771509682887478265699599574490661758344137522397096834080053559849175417381883999446974867626551658276584835884531427756879002909517028352971634456212964043523117600665101241200659755851276178583829204197484423608007193045761893234922927965019875187212726750798125547095890455635792122103334669749923563025494780249011419521238281530911407907386025152274299581807247162591668545133312394804947079119153267343028244186041426363954800044800267049624820179289647669758318327131425170296923488962766844032326092752496035799646925650493681836090032380929345958897069536534940603402166544375589004563288225054525564056448246515187547119621844396582533754388569094113031509526179378002974120766514793942590298969594699556576121865619673378623625612521632086286922210327488921865436480229678070576561514463204692790682120738837781423356282360896320806822246801224826117718589638140918390367367222088832151375560037279839400415297002878307667094447456013455641725437090697939612257142989467154357846878861444581231459357198492252847160504922124247014121478057345510500801908699603302763478708108175450119307141223390866393833952942578690507643100638351983438934159613185434754649556978103829309716465143840700707360411237359984345225161050702705623526601276484830840761183013052793205427462865403603674532865105706587488225698157936789766974220575059683440869735020141020672358502007245225632651341055924019027421624843914035998953539459094407046912091409387001264560016237428802109276457931065792295524988727584610126483699989225695968815920560010165525637567';
        return substr($pi, 0, $length);   
    }

    /**
     * [properCase description]
     * @param  [type] $cString [description]
     * @return [type]          [description]
     */
    public static function properCase($cString)
    {
        $aWords = explode(' ', trim($cString));
        foreach ($aWords as $cKey => $cValue) {
            $aWords[$cKey] = ucwords(strtolower(trim($cValue)));
        }
        return implode(' ', $aWords);

    }

    /**
     * [randomString description]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public static function randomString($length = 32)
    {
        return substr(md5(rand(0, 9999) . uniqid('', 1)), 0, $length);
    }

 
    /**
     * [redirect description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function redirect($value)
    {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            header("Refresh: 0; URL=$value");
        } else {
            header("Location: $value");
        }
        exit;

    }

    /**
     * [recursiveRemoveDirectory description]
     * @param  [type]  $directory [description]
     * @param  boolean $empty     [description]
     * @return [type]             [description]
     */
    public function recursiveRemoveDirectory($directory, $empty = false)
    {

        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }

        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif (is_readable($directory)) {
            $handle = opendir($directory);
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    $path = $directory.'/'.$item;
                    if (is_dir($path)) {
                        $this->recursiveRemoveDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($handle);
            if ($empty == false) {
                if (@!rmdir($directory)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * [isHash description]
     * @param  [type]  $var [description]
     * @return boolean      [description]
     */
    public static function isHash($var)
    {
        return is_array($var) && array_diff_key($var, array_keys(array_keys($var)));
    }

    /**
     * [replaceUTF8Chars description]
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public function replaceUTF8Chars ($string)
    {

        $search = array(chr(0xe2) . chr(0x80) . chr(0x98),
        chr(0xe2) . chr(0x80) . chr(0x99),
        chr(0xe2) . chr(0x80) . chr(0x9c),
        chr(0xe2) . chr(0x80) . chr(0x9d),
        chr(0xe2) . chr(0x80) . chr(0x93),
        chr(0xe2) . chr(0x80) . chr(0x94));

        $replace = array('&lsquo;', '&rsquo;', '&ldquo;','&rdquo;', '&ndash;','&mdash;');

        $string = str_replace($search, $replace, $string);
        $string = str_replace(chr(130), ',', $string);    // baseline single quote
        $string = str_replace(chr(131), 'NLG', $string);  // florin
        $string = str_replace(chr(132), '"', $string);    // baseline double quote
        $string = str_replace(chr(133), '...', $string);  // ellipsis
        $string = str_replace(chr(134), '**', $string);   // dagger (a second footnote)
        $string = str_replace(chr(135), '***', $string);  // double dagger (a third footnote)
        $string = str_replace(chr(136), '^', $string);    // circumflex accent
        $string = str_replace(chr(137), 'o/oo', $string); // permile
        $string = str_replace(chr(138), 'Sh', $string);   // S Hacek
        $string = str_replace(chr(139), '<', $string);    // left single guillemet
        $string = str_replace(chr(140), 'OE', $string);   // OE ligature
        $string = str_replace(chr(145), "'", $string);    // left single quote
        $string = str_replace(chr(146), "'", $string);    // right single quote
        $string = str_replace(chr(147), '"', $string);    // left double quote
        $string = str_replace(chr(148), '"', $string);    // right double quote
        $string = str_replace(chr(149), '-', $string);    // bullet
        $string = str_replace(chr(150), '-', $string);    // endash
        $string = str_replace(chr(151), '--', $string);   // emdash
        $string = str_replace(chr(152), '~', $string);    // tilde accent
        $string = str_replace(chr(153), '(TM)', $string); // trademark ligature
        $string = str_replace(chr(154), 'sh', $string);   // s Hacek
        $string = str_replace(chr(155), '>', $string);    // right single guillemet
        $string = str_replace(chr(156), 'oe', $string);   // oe ligature
        $string = str_replace(chr(159), 'Y', $string);    // Y Dieresis

        return $string;

    }

    
    /**
     * [resizeImage] - this function uses the gdimage library to resize an image.
     * 
     * @param  string  $cInput   - file path for the source image
     * @param  string  $cOutput  - the output path.
     * @param  integer $nH       [description]
     * @param  integer $nW       [description]
     * @param  string  $xType    [description]
     * @param  integer $nQuality [description]
     * @return - none
     */
    public function resizeImage($cInput, $cOutput, $nH = 240, $nW = 320, $xType = 'normal', $nQuality = 100)
    {
      
        if (function_exists('imagecreatefromgif')) {
   
            $src_img = '';
            $nH == $nW ? $xType = 'square' : false;
            $cOutput == null ? $cOutput = $cInput : false;
            $cType = strtolower(substr(stripslashes($cInput), strrpos(stripslashes($cInput), '.')));

            if ($cType == '.gif' || $cType == 'image/gif') {
                $src_img = imagecreatefromgif($cInput); /* Attempt to open */
                $cType = 'image/gif';
            } elseif ($cType == '.png' || $cType == 'image/png' || $cType == 'image/x-png') {
                $src_img = imagecreatefrompng($cInput); /* Attempt to open */
                $cType = 'image/x-png';
            } elseif ($cType == '.bmp' || $cType == 'image/bmp') {
                $src_img = imagecreatefrombmp($cInput); /* Attempt to open */
                $cType = 'image/bmp';
            } elseif ($cType == '.jpg' || $cType == '.jpeg' || $cType == 'image/jpg' || $cType == 'image/jpeg' || $cType == 'image/pjpeg') {
                $src_img = imagecreatefromjpeg($cInput); /* Attempt to open */
                $cType = 'image/jpeg';
            } else {
      
            }

            if (!$src_img) {
                $src_img = imagecreatefromgif(FOONSTER_PATH . '/images/widget.gif'); /* Attempt to open */
                $cType = 'image/gif';
            } else {

                $tmp_img;
                list($width, $height) = getimagesize($cInput);
          
                if ($xType == 'square' && $width != $height) {
                
                    $biggestSide = '';
                    $cropPercent = .5;
                    $cropWidth   = 0;
                    $cropHeight  = 0;
                    $c1 = array();
                
                    if ($width > $height) {
                        $biggestSide = $width;
                        $cropWidth   = round($biggestSide*$cropPercent);
                        $cropHeight  = round($biggestSide*$cropPercent);
                        $c1 = array("x"=>($width-$cropWidth)/2, "y"=>($height-$cropHeight)/2);
                    } else {
                        $biggestSide = $height;
                        $cropWidth   = round($biggestSide*$cropPercent);
                        $cropHeight  = round($biggestSide*$cropPercent);
                        $c1 = array("x"=>($width-$cropWidth)/2, "y"=>($height-$cropHeight)/7);
                    }
                
                    $thumbSize = $nH;

                    if ($cType == 'image/gif') {
                        $tmp_img = imagecreate($thumbSize, $thumbSize);
                        imagecolortransparent($tmp_img, imagecolorallocate($tmp_img, 0, 0, 0));
                        imagecopyresized($tmp_img, $src_img, 0, 0, $c1['x'], $c1['y'], $thumbSize, $thumbSize, $cropWidth, $cropHeight);
                    } elseif ($cType == 'image/x-png') {
                    
                        $tmp_img = imagecreatetruecolor($thumbSize, $thumbSize);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $c1['x'], $c1['y'], $thumbSize, $thumbSize, $cropWidth, $cropHeight);
                    
                    } elseif ($cType == 'image/bmp') {
                    
                        $tmp_img = imagecreatetruecolor($thumbSize, $thumbSize);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $c1['x'], $c1['y'], $thumbSize, $thumbSize, $cropWidth, $cropHeight);

                    } elseif ($cType == 'image/jpeg') {
            
                        $tmp_img = imagecreatetruecolor($thumbSize, $thumbSize);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $c1['x'], $c1['y'], $thumbSize, $thumbSize, $cropWidth, $cropHeight);
    
                    } else {
                        $tmp_img = imagecreatetruecolor($thumbSize, $thumbSize);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $c1['x'], $c1['y'], $thumbSize, $thumbSize, $cropWidth, $cropHeight);
                    }

                    imagedestroy($src_img);
                    $src_img = $tmp_img;
                } else {
                    $ow = imagesx($src_img);
                    $oh = imagesy($src_img);
                    if ($nH == 0 && $nW == 0) {
                        $nH = $oh;
                        $nW = $ow;
                    }
                    if ($nH == 0) {
                        $nH = $nW;
                    }
                    if ($nW == 0) {
                        $nW = $nH;
                    }
                    if ($nH > $oh && $nW > $ow) {
                        $width  = $ow;
                        $height = $oh;
                    } else {

                        if ($nW && ($ow < $oh)) {
                            $nW = ($nH / $oh) * $ow;
                        } else {
                            $nH = ($nW / $ow) * $oh;
                        }
                
                        $width  = $nW;
                
                        $height = $nH;
                    }
     
                    if ($cType == 'image/gif') {
                        $tmp_img = imagecreate($width, $height);
                        imagecolortransparent($tmp_img, imagecolorallocate($tmp_img, 0, 0, 0));
                        imagecopyresized($tmp_img, $src_img, 0, 0, $off_w, $off_h, $width, $height, $ow, $oh);
                    } elseif ($cType == 'image/x-png') {
                        $tmp_img = imagecreatetruecolor($width, $height);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $off_w, $off_h, $width, $height, $ow, $oh);
                    } elseif ($cType == 'image/bmp') {
                        $tmp_img = imagecreatetruecolor($width, $height);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $off_w, $off_h, $width, $height, $ow, $oh);
                    } elseif ($cType == 'image/jpeg') {
                        $tmp_img = imagecreatetruecolor($width, $height);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $off_w, $off_h, $width, $height, $ow, $oh);
                    } else {
                        $tmp_img = imagecreatetruecolor($width, $height);
                        imagecopyresampled($tmp_img, $src_img, 0, 0, $off_w, $off_h, $width, $height, $ow, $oh);
                    }
             
                    imagedestroy($src_img);
                
                    $src_img = $tmp_img;
      
                }
      
            }
        
            // set the output
           
            if ($cType == 'image/gif') {
                imageGIF($src_img, $cOutput);
            } elseif ($cType == 'image/x-png') {
                imagePNG($src_img, $cOutput);
            } elseif ($cType == 'image/bmp') {
                imageJPEG($src_img, $cOutput, $nQuality);
            } elseif ($cType == 'image/jpeg') {
                imageJPEG($src_img, $cOutput, $nQuality);
            } else {
                imageJPEG($src_img, $cOutput, $nQuality);
            }
        }
    }

    /**
     * [rmDirectory description]
     * @param  [type]  $directory [description]
     * @param  boolean $empty     [description]
     * @return [type]             [description]
     */
    public function rmDirectory($directory, $empty = false)
    {

        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }

        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif (is_readable($directory)) {

            $handle = opendir($directory);

            while (false !== ($item = readdir($handle))) {

                if ($item != '.' && $item != '..') {

                    $path = $directory.'/'.$item;

                    if (is_dir($path)) {
                        self::rmDirectory($path);
                    } else {

                        unlink($path);
                    }

                }

            }
            closedir($handle);
            if ($empty == false) {
                if (@ !rmdir($directory)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * [sql]
     * @param  string $sql    [the query to run]
     * @param  array  $params [the parameters to bind to the query string.]
     * @return mixed
     */
    public function sql($sql, $params = array())
    {
        try {
            $sth = $this->_dbh->prepare($sql);
            if (!$sth) {
                echo "\nPDO::errorInfo():\n";
                print_r($dbh->errorInfo());
            }
            $sth->execute($params);

            if ($sth->rowCount() == 1) {
                return $sth->fetch(\PDO::FETCH_OBJ);
            } else {
                return $sth;
            }
        } catch ( \PDOException $e) {
            return $e->getCode() . ':' . $e->getMessage();
        } catch ( \Exception $e ) {
            return $e->getCode() . ':' . $e->getMessage();
        }
    }    

    /**
     * [runSql]
     * @param  string $sql    [the query to run]
     * @param  array  $params [the parameters to bind to the query string.]
     * @return mixed
     */
    public function runSql($sql, $params = array())
    {
        try {
            $sth = $this->_dbh->prepare($sql);
            if (!$sth) {
                echo "\nPDO::errorInfo():\n";
                print_r($dbh->errorInfo());
            }
            $sth->execute($params);

            if ($sth->rowCount() == 1) {
                return $sth->fetch(\PDO::FETCH_OBJ);
            } else {
                return $sth;
            }
        } catch ( \PDOException $e) {
            return $e->getCode() . ':' . $e->getMessage();
        } catch ( \Exception $e ) {
            return $e->getCode() . ':' . $e->getMessage();
        }
    }

    /**
     * [scrubVar description]
     * @param  [type] $value     [description]
     * @param  string $cType     [description]
     * @param  string $cWordFile [description]
     * @return [type]            [description]
     */
    public static function scrubVar($value, $cType = 'BASIC', $cWordFile = '')
    {

        $cType = strtoupper(trim($cType));

        if ($cType == 'ALPHA') {
            return preg_replace('/[^A-Za-z\s]/', '', $value);
        } elseif ($cType == 'TOKEN') {
            return preg_replace('%[^A-Za-z0-9\\\-\_\/]%', '', $value);
        } elseif ($cType == 'ALPHA_NUM') {
            return preg_replace('/[^A-Za-z0-9]/', '', $value);            
        } elseif ($cType == 'SIMPLE') {
            $cPattern = '[^A-Za-z0-9\s\-\_\.\ \,\@\!\#\$\%\^\&\*\(\)\[\]\{\}\?]';
            return preg_replace("/$cPattern/", '', $value);
        } elseif ($cType == 'EMAIL') {
            $cPattern = '/(;|\||`|>|<|&|^|"|'."\t|\n|\r|'".'|{|}|[|]|\)|\()/i';
            return preg_replace($cPattern, '', $value);
        } elseif ($cType == 'HYPERLINK') {
            // match protocol://address/path/file.extension?some=variable&another=asf%
            $value = preg_replace("/\s([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i", '', $value);
            // match www.something.domain/path/file.extension?some=variable&another=asf%
            return preg_replace("/\s(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i", '', $value);
        } elseif ($cType == 'WHOLE_NUM') {
            return preg_replace('/[^0-9]/', '', $value);
        } elseif ($cType == 'FLOAT') {
            return preg_replace('/[^0-9\-\+\.]/', '', $value);
        } elseif ($cType == 'FORMAT_NUM') {
            return preg_replace('/[^0-9\.\,\-]/', '', $value);
        } elseif ($cType == 'SQL_INJECT') {
            $cPattern = '[^A-Za-z0-9\s\-\_\.\ \,\@\!\#\$\%\^\&\*\(\)\[\]\{\}\?]';

            $aRestrictedWords = array(
                '/\bcmd\b/i',
                '/\badmin\b/i',
                '/\bhaving\b/i',
                '/\broot\b/i',
                '/\bexec\b/i',
                '/\bdelete\b/i',
                '/\bCOLLATE\b/i',
                '/\bupdate\b/i',
                '/\bunion\b/i',
                '/\binsert\b/i',
                '/\bdrop\b/i',
                '/\bhttp\b/i',
                '/\bhttps\b/i',
                '/\b--\b/i'
               );

            return preg_replace("/$cPattern/", '', preg_replace($aRestrictedWords, '', $value));

        } elseif ($cType == 'REMOVE_SPACES') {
            return preg_replace("/\s/", '', trim($value));
        } elseif ($cType == 'REMOVE_DOUBLESPACE') {
            return preg_replace("/\s+/", ' ', trim($value));
        } else {
            $cPattern = '[^A-Za-z0-9\s\-\_\.\ \,\@\!\#\$\%\^\&\*\(\)\[\]\{\}\?]';
            return preg_replace("/$cPattern/", '', strip_tags(trim($value)));
        }

    }

    
    /**
     * [slurp]
     * @param  [type]  $f        [description]
     * @param  array   $output   [description]
     * @param  integer $lDynamic [description]
     * @return [type]            [description]
     */
    public function slurp($f, $output = array(), $lDynamic = 1)
    {

        $output = (object) $output;
        $cReturn = '';
        if (file_exists($f)) {
            ob_start();
            if (strtolower(substr(stripslashes($f), strrpos(stripslashes($f), '.'))) == '.php' && $lDynamic) {
                include $f;
                $cReturn = ob_get_contents();
            } else {
                $retval = readfile($f);
                if (false !== $retval) { // no readfile error
                    $cReturn = ob_get_contents();
                }
            }
            ob_end_clean();
        } else {
            if (substr(trim(strtolower($f)), 0, 4) == 'http') {
                $cReturn = file_get_contents($f);
            }
        }

        return $cReturn;
    }

    /**
     * [splitString description]
     * @param  [type] $cString [description]
     * @return [type]          [description]
     */
    public function splitString($cString)
    {
        $cString = preg_replace("/([\r\n])([\r\n])[\s]+/si", '', $cString); // remove line breaks replace with paragraph
        $aArray = preg_split("/\s+/", $cString);
        $aFinal = array();
        $cTemp = '';
        foreach ($aArray as $cLabel => $cContent) {
            if ((strlen($cContent) + strlen($cTemp)) > $nLen) {
                $aFinal[] = $cTemp;
                $cTemp = $cContent.' ';
            } else {
                $cTemp .= $cContent.' ';
            }
        }
        strlen($cTemp) > 0 ? $aFinal[] = $cTemp : false;
        return($aFinal);
    }

    /**
     * [sqlAddRecord description]
     * @param  [type] $table     [description]
     * @param  [type] $variables [description]
     * @return [type]            [description]
     */
    public function sqlAddRecord($table, $variables)
    {
    
        is_object($variables) ? $variables = (array) $variables : false;

        $sql = $this->sqlBuildQuery($table, 'insert', $variables);
        $fields = $this->sqlSetVars($sql, $variables);
        $sth = $this->sqlRunQuery($sql, $fields);

        $err = $sth->errorInfo();
        if ($err[1] > 0) {
            $this->error = $err[2];
            return 0;
        } else {
            return $this->sqlInsertId();
        }
    }

    /**
     * [sqlDump description]
     * @return [type] [description]
     */
    public function sqlDump()
    {
        $this->dumpVariable($this->_dbh->errorInfo());
    }

    /**
     * [sqlErrorInfo description]
     * @return [type] [description]
     */
    public function sqlErrorInfo()
    {
        return $this->_dbh->errorInfo();
    }

    /**
     * [sqlInsertId description]
     * @return [type] [description]
     */
    public function sqlInsertId()
    {
        return $this->_dbh->lastInsertId();
    }

    /**
     * [sqlRunQuery description]
     * @param  [type] $sql    [description]
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function sqlRunQuery($sql, $params = array())
    {
    
        try {
            $sth = $this->_dbh->prepare($sql);
            if (!$sth) {
                echo "\nPDO::errorInfo():\n";
                print_r($dbh->errorInfo());
            }
            $sth->execute($params);

            return $sth;
        } catch ( \PDOException $e) {
            return $e->getCode() . ':' . $e->getMessage();
        } catch ( \Exception $e ) {
            return $e->getCode() . ':' . $e->getMessage();
        }
    }

    /**
     * [sqlSetVars description]
     * @param  [type] $sql    [description]
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function sqlSetVars($sql, $params = array())
    {
        $vars = array();
        // this is faster and SQL is not harmed by the double space.
        $sql = str_replace(',', ', ', $sql);
        preg_match_all("/:(.*?)\s/", $sql, $matches);
        foreach ($matches[1] as $value) {
            $value = str_replace(',', '', $value);
            array_key_exists($value, $params) ? $vars[ $value ] = $params[ $value ] : false;
        }

        return $vars;
    }

    /**
     * [sqlBuildQuery]
     * @param  string $type        [description]
     * @param  [type] $table       [description]
     * @param  array  $variables   [description]
     * @param  array  $constraints [description]
     * @param  string $limit       [description]
     * @return [type]              [description]
     */
    public function sqlBuildQuery($table, $type = 'INSERT', $variables = array(), $constraints = array(), $limit = '1')
    {

        $sql = '';
        $fields = $values = $updates = $columns = array();
        $type = strtoupper(trim($type));
        $sth = $this->_dbh->prepare('DESCRIBE ' . $table);

        $sth->execute();

        $limit > 0 ? $limit = " LIMIT $limit" : $limit = '';

        $tableinfo = $sth->fetchAll(\PDO::FETCH_ASSOC);
        // add field names by name
        foreach ($tableinfo as $key => $array) {
            strtoupper($array['Key']) == 'PRI' ? $primary = $array['Field'] : false;
            $columns[$array['Field']] = $array;
        }

        if ($type == 'SELECT') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && !empty($variables[ $array['Field'] ])) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            $sql = "SELECT FROM $table WHERE " . implode(' AND ', $updates) . "$limit";
        } elseif ($type == 'DELETE') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && !empty( $variables[ $array['Field'] ] )) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            $sql = "DELETE FROM $table WHERE " . implode(' AND ', $updates) . "$limit";
        } elseif ($type == 'UPDATE') {
            foreach ($columns as $key => $array) {
                if (array_key_exists($array['Field'], $variables) && $primary != $array['Field']) {
                    $updates[] = $array['Field'] . " = :$array[Field]";
                }
            }
            if (sizeof($constraints) > 0) {
                foreach ($constraints as $key => $array) {
                    $where[] = $key . " = :$key";
                }
                $sql = "UPDATE $table SET " . implode(',', $updates) . " WHERE " . implode(' AND ', $where) . "$limit";
            } else {
                $sql = "UPDATE $table SET " . implode(',', $updates) . " WHERE " . $primary . " = :" . $primary . "$limit";
            }
        } else {
            // check to see all null value no fields are accounted for.
            foreach ($columns as $key => $array) {
                if ($array['Field'] != $primary) {
                    if (!array_key_exists($array['Field'], $variables)) {
                        if ($array['Null'] == 'NO') {
                            if (!in_array($array['Field'], $fields)) {
                                $fields[] = $array['Field'];
                                $values[] = "'$array[Default]'";
                            }
                        }
                    } else {
                        $fields[] = $array['Field'];
                        $values[] = ":$array[Field]";
                    }
                }
            }
            $sql = "INSERT INTO $table ( " . implode(' , ', $fields) . ' ) VALUES ( ' . implode(' , ', $values) . " );";
        }
        
        return $sql;
    }

    /**
    * [updaterecord description]
    * @param  [type] $table     [description]
    * @param  array  $variables [description]
    * @param  [type] $error     [description]
    * @return [type]            [description]
    */
    public function sqlUpdateRecord($table, $variables)
    {
     
        is_object($variables) ? $variables = (array) $variables : false;

        $sql = $this->sqlBuildQuery($table, 'update', $variables);
        $fields = $this->sqlSetVars($sql, $variables);
        $sth = $this->sqlRunQuery($sql, $fields);
        $err = $sth->errorInfo();

        if ($err[1] > 0) {
            $this->error = $err[2];
            return 0;
        } else {
            if ($err[1] == 0) {
                return 1;
            } else {
                return $sth->rowCount();
            }
        }
    }

    /**
     * [stripFileExtension description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function stripFileExtension($value)
    {
        $ext = strrchr($value, '.');
        if ($ext !== false) {
            $value = substr($value, 0, -strlen($ext));
        }
        return $value;

    }

    /**
     * [stripWhiteSpace - remove extra white-space from string.]
     * @param  string $cStr [string to be modified]
     * @return string       string with white space removed.
     */
    public static function stripWhiteSpace($cStr)
    {
        return preg_replace("/\s/", ' ', $cStr);
    }

    /**
     * [substrWord description]
     * @param  [type]  $cString [description]
     * @param  integer $nLen    [description]
     * @return [type]           [description]
     */
    public static function substrWord($cString, $nLen = 250)
    {

        $aArray = str_word_count(strip_tags($this->stripWhiteSpace($cString)), 1);

        $aSlice = array_slice($aArray, 0, $nLen);

        if (sizeof($aArray) > $nLen) {
            return implode(' ', $aSlice) . ' ...';

        } else {
            return implode(' ', $aSlice);

        }
    }


    /**
     * [swapText description]
     * @param  [type] $cText    [description]
     * @param  [type] $cReplace [description]
     * @param  [type] $cWith    [description]
     * @return [type]           [description]
     */
    public function swapText ($cText, $cReplace, $cWith)
    {

        $cText = str_replace('&lt;@' . strtoupper($cReplace) . '@&gt;', $cWith, $cText);
        $cText = str_replace('<@' . strtoupper($cReplace) . '@>', $cWith, $cText);
        return $cText;

    }

    /**
     * [ucase] : convert string to uppercase.
     * @param  string $cString
     * @return string
     */
    public static function ucase($cString)
    {
        return strtoupper(trim($cString));
    }

    /**
     * [uploadFile description]
     * @param  string $file   [target to file to test for file upload]
     * @param  string $target [target where to save file]
     * @return boolean 
     */
    public function uploadFile($file, $target)
    {
        if (is_uploaded_file($file['tmp_name'])) {
            if (move_uploaded_file($file['tmp_name'], $target)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * [userTime description]
     * @param  [type] $cDate [description]
     * @return [type]        [description]
     */
    public static function userTime($cDate)
    {
        if (!empty($cDate) && $cDate != '0000-00-00 00:00:00') {
            return date('Y-m-d H:i:s', (strtotime($cDate) + ((substr(date('c'), -6, 3) * 60) * 60))) . ' ' . date('T');

        } else {
            return '';
        }
    }

    /**
     * [validateCreditCard - test if provided card number is valid.]
     * @param  string $cardnumber [string to be tested]
     * @param  string $cardname   [string identifying card type to be tested]
     * @param  string $error      [error code if error detected]
     * @return  boolean           [true/false for valid card number]
     */
    public function validateCreditCard ($cardnumber, $cardname, &$error)
    {

        $cardnumber = preg_replace('/[^0-9]/', '', $cardnumber);

        if ($cardnumber == '4007000000027') {
            return true;

        }

        $cards = array (
            array ('name' => 'Foonster',
                   'length' => '16',
                   'prefixes' => '71,73,78',
                   'checkdigit' => true
          ),
            array ('name' => 'American Express',
                   'length' => '15',
                   'prefixes' => '34,37',
                   'checkdigit' => true
          ),
            array ('name' => 'AMEX',
                   'length' => '15',
                    'prefixes' => '34,37',
                    'checkdigit' => true
          ),
            array ('name' => 'Carte Blanche',
                    'length' => '14',
                    'prefixes' => '300,301,302,303,304,305,36,38',
                    'checkdigit' => true
          ),
            array ('name' => 'Diners',
                    'length' => '14',
                    'prefixes' => '300,301,302,303,304,305,36,38',
                    'checkdigit' => true
          ),
            array ('name' => 'Diners Club',
                    'length' => '14',
                    'prefixes' => '300,301,302,303,304,305,36,38',
                    'checkdigit' => true
          ),
            array ('name' => 'Discover',
                    'length' => '16',
                    'prefixes' => '6011',
                    'checkdigit' => true
          ),
            array ('name' => 'Disc',
                'length' => '16',
                'prefixes' => '6011',
                'checkdigit' => true
          ),
            array ('name' => 'Enroute',
                'length' => '15',
                'prefixes' => '2014,2149',
                'checkdigit' => true
          ),
            array ('name' => 'JCB',
                'length' => '15,16',
                'prefixes' => '3,1800,2131',
                'checkdigit' => true
          ),
            array ('name' => 'Maestro',
                'length' => '16',
                'prefixes' => '5020,6',
                'checkdigit' => true
          ),
            array ('name' => 'MasterCard',
                'length' => '16',
                'prefixes' => '51,52,53,54,55',
                'checkdigit' => true
          ),
            array ('name' => 'MC',
                'length' => '16',
                'prefixes' => '51,52,53,54,55',
                'checkdigit' => true
          ),
            array ('name' => 'Solo',
                'length' => '16,18,19',
                'prefixes' => '6334,6767',
                'checkdigit' => true
          ),
            array ('name' => 'Switch',
                'length' => '16,18,19',
                'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759',
                'checkdigit' => true
          ),
            array ('name' => 'Visa',
                'length' => '13,16',
                'prefixes' => '4',
                'checkdigit' => true
          ),
            array ('name' => 'Visa Electron',
                'length' => '16',
                'prefixes' => '417500,4917,4913',
                'checkdigit' => true
          )
           );

        $ccErrorNo = 0;
        $ccErrors [0] = "Unknown card type";
        $ccErrors [1] = "No card number provided";
        $ccErrors [2] = "Credit card number has invalid format";
        $ccErrors [3] = "Credit card number is invalid";
        $ccErrors [4] = "Credit card number is wrong length";
        $ccErrors [5] = "Credit card number prefix invalid";

        // Establish card type
        $cardType = -1;
        for ($i=0; $i<sizeof($cards); $i++) {

            // See if it is this card (ignoring the case of the string)
            if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
                $cardType = $i;
                break;
            }

        }

        // If card type not found, report an error
        if ($cardType == -1) {

            $errornumber = 0;
            $error = $ccErrors [$errornumber];

            return false;

        }

        // Ensure that the user has provided a credit card number
        if (strlen($cardnumber) == 0) {

            $errornumber = 1;
            $error = $ccErrors [$errornumber];

            return false;

        }

        // Remove any spaces from the credit card number

        $cardNo = str_replace(' ', '', $cardnumber);

        // Check that the number is numeric and of the right sort of length.

        if (!preg_match('/^[0-9]{13,19}$/i', $cardNo)) {

            $errornumber = 2;
            $error = $ccErrors [$errornumber];

            return false;

        }

        // Now check the modulus 10 check digit - if required
        if ($cards[$cardType]['checkdigit']) {

            $checksum = 0;  // running checksum total
            $mychar = "";   // next char to process
            $j = 1;         // takes value of 1 or 2

            // Process each digit one by one starting at the right
            for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {

                // Extract the next digit and multiply by 1 or 2 on alternative digits.
                $calc = $cardNo{$i} * $j;

                // If the result is in two digits add 1 to the checksum total
                if ($calc > 9) {

                    $checksum = $checksum + 1;

                    $calc = $calc - 10;

                }

                // Add the units element to the checksum total

                $checksum = $checksum + $calc;

                // Switch the value of j

                ($j == 1) ? $j = 2 : $j = 1;
            }

            // All done - if checksum is divisible by 10, it is a valid modulus 10.
            // If not, report an error.

            if ($checksum % 10 != 0) {

                $errornumber = 3;

                $error = $ccErrors [$errornumber]." $checksum";

                return false;

            }
        }

        // The following are the card-specific checks we undertake.

        // Load an array with the valid prefixes for this card
        $prefix = explode(',', $cards[$cardType]['prefixes']);

        // Now see if any of them match what we have in the card number
        $PrefixValid = false;
        for ($i=0; $i<sizeof($prefix); $i++) {
            $exp = '/^' . $prefix[$i].'/';

            if (preg_match($exp, $cardNo)) {
                $PrefixValid = true;
                break;

            }
        }

         // If it isn't a valid prefix there's no point at looking at the length
        if (!$PrefixValid) {

            $errornumber = 5;
            $error = $ccErrors [$errornumber];

            return false;

        }

        // See if the length is valid for this card
        $LengthValid = false;
        $lengths = explode(',', $cards[$cardType]['length']);
        for ($j=0; $j<sizeof($lengths); $j++) {

            if (strlen($cardNo) == $lengths[$j]) {
                $LengthValid = true;
                break;
            }
        }

        // See if all is OK by seeing if the length was valid.
        if (!$LengthValid) {
            $errornumber = 4;
            $error = $ccErrors [$errornumber];
            return false;
        }
        // The credit card is in the required format.
        return true;

    }

    /**
    *
    *
    *
    *          NAME: writeToFile
    *  DATE CREATED: 09/18/2004
    * DATE MODIFIED: 09/18/2004
    *        USAGE : $obj->writeToFile ()
    *      PURPOSE : Write a text string to the specified path.
    *        RETURNS: boolean true / false
    *      COMMENTS :
    *
    *                   $cContent = the data that is to be written to the file.
    *
    *                   $cPath    = the file path that is to be written.
    *
    *                   $cMode    = the file mode that is to be used.
    *
    *                               'r'  Open for reading only; place the file pointer at
    *                               the beginning of the file.
    *
    *                               'r+' Open for reading and writing; place the file
    *                               pointer at the beginning of the file.
    *
    *                               'w'  Open for writing only; place the file pointer at
    *                               the beginning of the file and truncate the file to zero length. If the file does not
    *                               exist, attempt to create it.
    *
    *                               'w+' Open for reading and writing; place the file pointer
    *                               at the beginning of the file and truncate the file to zero length. If the file does not
    *                               exist, attempt to create it.
    *
    *                               'a'  Open for writing only; place the file pointer at the
    *                               end of the file. If the file does not exist, attempt to create it.
    *
    *                               'a+' Open for reading and writing; place the file pointer
    *                               at the end of the file. If the file does not exist, attempt to create it.
    *
    *                               'x'  Create and open for writing only; place the file
    *                               pointer at the beginning of the file. If the file already exists, the fopen() call will
    *                               fail by returning FALSE and generating an error of level E_WARNING. If the file does not
    *                               exist, attempt to create it. This is equivalent to specifying O_EXCL|O_CREAT flags for the
    *                               underlying open(2) system call.
    *
    *                               'x+' Create and open for reading and writing; otherwise it
    *                               has the same behavior as 'x'.
    *
    *                               'c'  Open the file for writing only. If the file does
    *                               not exist, it is created. If it exists, it is neither truncated (as opposed to 'w'),
    *                               nor the call to this function fails (as is the case with 'x'). The file pointer is
    *                               positioned on the beginning of the file. This may be useful if it's desired to get an
    *                               advisory lock (see flock()) before attempting to modify the file, as using 'w' could
    *                               truncate the file before the lock was obtained (if truncation is desired,
    *                               ftruncate() can be used after the lock is requested).
    *
    *                               'c+' Open the file for reading and writing; otherwise it has the same behavior as 'c'.
    */
    /**
     * [writeToFile description]
     * @param  string  $cContent    [description]
     * @param  string  $cPath       [description]
     * @param  string  $cMode       [description]
     * @param  integer $nPermission [description]
     * @return [type]               [description]
     */
    public static function writeToFile($cContent = '', $cPath = '', $cMode = 'w+', $nPermission = 0777)
    {    
        self::mkdirs(dirname($cPath));
        if ($fp = @ fopen(trim($cPath), $cMode)) {
            @ fwrite($fp, $cContent);
            @ fclose($fp);
            @ chmod(dirname($cPath), $nPermission);
            return true;

        } else {
            return false;
        }
    }

    /**
     * [xmlDataDump description]
     * @param  [type] $aArray  [description]
     * @param  [type] $cOutput [description]
     * @return [type] [description]
     */
    public function xmlDataDump($aArray, &$cOutput)
    {

        if (is_array($aArray)) {
            foreach ($aArray as $c => $v) {
                if (is_array($v)) {
                    if ($lChildren) {
                        converttoXML($v);
                    } else {
                        $cOutput .= $this->ucase("<$c>") . $v . $this->ucase("</$c>\n");
                    }
                } else {
                    $cOutput .= $this->ucase("<$c>") . $v . $this->ucase("</$c>\n");
                }
            }
        }
    }
}
