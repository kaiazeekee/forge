<?php

namespace foonster\forge\other\Authorizenet;
/**
 * This class is based on the AIM Developer Guide using Authorize.Net’s 
 * Advanced Integration Method API
 * 
 */

class Aim
{
    /**
     * @var string URL associated with the Authorize.net gateway
     */ 
    private $gateway = 'https://secure.authorize.net/gateway/transact.dll'; 
    /**
     * @var string The merchant API Login ID
     */ 
    private $id;
    /**
     * @var string The merchant assigned transaction key
     */ 
    private $transactionKey;
    /**
     * @var array variables that are assocaited with posting transaction
     */ 
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
     * 
     * @return [type] [description]
     */
    public function captureOnly()
    {

    }
    
    /**
     * issue credit
     */ 
    public function credit() 
    {

    }    

    /**
     * return the URL associated with the currently selected gateway
     * 
     * @return string
     */
    public function gateway()
    {
        return $this->gateway;
    }

    /**
     * post transaction to AuthorizeNet server]
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

    /**
     * set the logon credentials
     * 
     * @param string $id [The merchant API Login ID is provided in the Merchant Interface and
     * must be stored securely. The API Login ID and Transaction Key together 
     * provide the merchant the authentication required for access to the 
     * payment gateway.
     *                          
     * See the Merchant Integration Guide at {@link http://www.authorize.net/support/merchant/} for more information.]
     *                          
     * @param string $transactionKey [The merchant Transaction Key is provided in the Merchant Interface and 
     * must be stored securely. The API Login ID and Transaction Key together 
     * provide the merchant the authentication required for access to the 
     * payment gateway. 
     *                         
     * This field is not used for mobile devices. Use the mobileDeviceId field instead. See the Merchant Integration Guide at {@link http:// www.authorize.net/support/merchant/} for more information.]
     *
     * @return Aim
     */
    public function setCredentials($id = null, $transactionKey = null)
    {
        $this->id = $id;
        $this->transactionKey = $transactionKey;
        return $this;
    }

    /**
     * set which gateway transactions will be sent to
     *      
     * @param string $gateway [which gateway you want to use]
     * 
     *     PRODUCTION - transactions will be sent to the production server
     *     DEVELOPER - transactions will be sent to the test server.
     *
     * @return Aim
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
     * the variable array used to construct the POST string
     * 
     * @param mixed $var [string or array to be added t]
     * @param string $value [string or ignored]
     * @return Aim
     */
    public static function setVar($var = '', $value = '')
    {

        (is_object($var)) ? $var = (array) $var : false;

        if (is_array($var)) { 
            foreach ($var as $n => $v) {
                if (is_array($v)) {                 
                    $this->setVar($v);
                } else {
                    $this->vars[$var] = $value;                
                }
            }
        } else {
            $this->vars[$var] = $value;
        }
        return $this;
    }

    public function unlinkedCredit() 
    {

    }

    /**
     * 
     *
     */
    public function visaVerified()
    {

    }

    /**
     * void a transaction
     * @return string 
     */
    public function void()
    {

    }
}
