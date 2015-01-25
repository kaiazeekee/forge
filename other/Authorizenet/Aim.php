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
namespace foonster\forge\other\Authorizenet;

/**
 * This class is based on the DPM Developer Guide using Authorize.Net’s 
 * Direct Post Method API
 */

class Aim
{

    private $gateway = 'https://secure.authorize.net/gateway/transact.dll'; 
    private $id;
    private $transactionKey;
    private $vars = array();

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
     * [authCapture description]
     * @return [type] [description]
     */
    public function authCapture()
    {

    }

    /**
     * [authOnly description]
     * @return [type] [description]
     */
    public function authOnly()
    {

    }

    /**
     * [captureOnly description]
     * @return [type] [description]
     */
    public function captureOnly()
    {

    }
    
    public function credit() 
    {

    }    

    /**
     * [gateway - return the contents of the gateway variable.]
     * 
     * @return string [contents of private gateway variable]
     */
    public function gateway()
    {
        return $this->gateway;
    }

    /**
     * [httpPost - post transaction to AuthorizeNet server]
     * @return [type] [description]
     */
    public function httpPost()
    {

        $ch = curl_init($this->gateway); // URL of gateway for cURL to post to
        @ curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response    
        @ curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        @ curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->postString, "& " )); // use HTTP POST to send form data
        
        if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
            @ curl_setopt($ch, CURLOPT_CAINFO, 'C:\WINNT\curl-ca-bundle.crt');   
        }
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);     
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
   
        $response = curl_exec($ch); //execute post and get results
        
        if (empty($response)) {
       
            // some kind of an error happened
      
            $cError = curl_error($ch);      
            $aReturn['authorized'] = 0;        
            $aReturn['authcode'] = substr( $aResponse[3] , 9 );    
            $aReturn['response_code'] = 'CC Processing Error ['.substr( $aResponse[0] , 7 ).'] '.substr( $aResponse[2] , 8 );
        
            return $aReturn;
        
        } else {
      
            $aResponse = explode('|',$mResponse);      
            $info = curl_getinfo($ch);      
            curl_close($ch); // close cURL handler

            if (empty($info['http_code'])) {         
                die("No HTTP code was returned").'<br>';
            } 
    
        } 
        curl_close($ch); // close cURL handler 
    }    

    /**
     * [priorAuthCapture description]
     * @return [type] [description]
     */
    public function priorAuthCapture()
    {

    }
    

    public function unlinkedCredit() 
    {

    }

    public function resetVars()
    {
        self::vars = array();
    }

    /**
     * [setCredentials] - set the logon credentials
     * 
     * @param string $id       
     *                        [The merchant API Login ID is provided in the Merchant Interface and
     *                          must be stored securely. The API Login ID and Transaction Key together 
     *                          provide the merchant the authentication required for access to the 
     *                          payment gateway.
     *                          
     *                          See the Merchant Integration
     *                          Guide at http://www.authorize.net/support/merchant/ for more information.]
     *                          
     * @param string $transactionKey 
     * 
     *                         [The merchant Transaction Key is provided in the Merchant Interface and 
     *                         must be stored securely. The API Login ID and Transaction Key together 
     *                         provide the merchant the authentication required for access to the 
     *                         payment gateway. 
     *                         
     *                         This field is not used for mobile devices. Use the mobileDeviceId field instead.
     *                         
     *                         See the Merchant Integration Guide at http:// www.authorize.net/support/ merchant/ for more information.]
     *
     * @return self - function available for chaining
     */
    public function setCredentials($id = null, $transactionKey = null)
    {
        $this->id = $id;
        $this->transactionKey = $transactionKey;
        return $this;
    }

    /**
     * [setGateway] - 
     * @param string $gateway [the gateway url to send transactions]
     *
     * @return self - function available for chaining
     */
    public static function setGateway($gateway = 'production')
    {
        if (strtolower(trim($gateway)) == 'developer') {
            $this->gateway = 'https://test.authorize.net/gateway/transact.dll';
        } else {
            $this->gateway = 'https://secure.authorize.net/gateway/transact.dll';
        }
        return $this;
    }

    /**
     * [setVar - the variable array used to construct the POST string.]
     * @param string $var   [string or array]
     * @param string $value [string or ignored]
     */
    public static function setVar($var = '', $value = '')
    {

        (is_object($var)) ? $var = (array) $var : false;

        if (is_array($var)) { 
            foreach ($var as $n => $v) {
                $this->setVar($v);
            } else {
                $this->vars[$var] = $value;                
            }
        } else {
            $this->vars[$var] = $value;
        }
        return $this;
    }

    public function visaVerified()
    {

    }

    /**
     * [void - void transaction]
     * @return [type] [description]
     */
    public function void()
    {

    }
}
