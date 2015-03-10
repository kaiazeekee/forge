<?php
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
     * 
     */
    private $products = array(); 
    /**
     * 
     */
    private $version = '3.1'; 

    /**
     * 
     */
    private $delim_char = '|'; 

    /**
     * 
     */
    private $delim_data = 'TRUE'; 

    /**
     * 
     */
    private $url = 'FALSE'; 

    /**
     * 
     */
    private $type = 'AUTH_CAPTURE'; 

    /**
     * 
     */
    private $method = 'CC'; 

    /**
     * 
     */
    private $relay_response = 'FALSE'; 

    /**
     * the variable holding the taxes
     */
    private $tax = 'Taxes|0.00';

    /**
     * 
     */
    private $freight = '';
    
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
     * add product to entity
     * 
     * @param string $id []
     * @param string $name [sku 28 character limit]
     * @param string $desc [description 254 character limit]
     * @param string $qty [number of items]
     * @param integer $price [price]
     * @param integer $taxable [1:taxable - 0: not taxable]
     * 
     * @return boolean
     */ 
    public function addProduct($id = '', $name = '', $desc = '', $qty = 0, $price = 0, $taxable = 0)
    {
        $add = true;
        
        // clean up the variables        
        $qty = self::scrubNumber($qty);
        $price = self::scrubNumber($price);

        // after cleaning ensure they are valid numbers
        (!is_numeric($qty) || $qty == 0) ? $add = false : false;
        (!is_numeric($price) || $price == 0) ? $add = false : false;

        // if still true then add to array
        if ($add) {
            $this->products['item' . $id] = array(
                'id' => 'item' . $id,
                'name' => substr(trim($name) , 0 , 28 ),
                'desc' => substr($desc, 0, 254),
                'qty' => $qty,
                'price' => $price,
                'taxable' => $taxable
            );
        }
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
     * 
     */ 
    public function buildQueryString()
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
        return rand();
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
     * generate the formatted post string.
     */ 
    public function generatePostString()
    {
        $string = '';
        $vars = $this->getVars();
        foreach ($vars as $k => $v) {
            !empty($v) ? $string .= "$k=" . urlencode($v) . "&" : false;
        }
        return $string;
    }

    /**
     * retrieve all the variables that will be used to send to the server.
     */ 
    public function getVars()
    {
        $array = $this->vars;
        $array['x_version'] = $this->version;
        $array['x_delim_char'] = $this->delim_char;
        $array['x_delim_data'] = $this->delim_data;
        $array['x_url'] = $this->url;
        $array['x_type'] = $this->type;
        $array['x_method'] = $this->method;
        $array['x_relay_response'] = $this->relay_response;
        $array['x_tax'] = $this->tax;
        $array['x_freight'] = $this->freight;
        return $array;
    }    

    /**
     * post the transaction
     * @return array
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
     * [priorAuthCapture]
     * @return [type] [description]
     */
    public function priorAuthCapture()
    {

    }

    /**
     * remove a product from the products array
     */ 
    public function removeProduct($id)
    {
        if (array_key_exists($id, $this->products)) {
            unset($this->products[$id]);
        }         
    }

    /**
     * scrub the string and ensure a number is returned.
     * 
     * return integer
     */ 
    private function scrubNumber($number = '')
    {
        if (!empty($number)) {
            return preg_replace("/[^0-9\.]/", '', number_format(preg_replace("/[^0-9\.]/", '', $number), 2));         
        }
    } 

    /**
     * ensure that all variables set follow the published requirements
     */ 
    private function setCleanVar($key, $value) {
        $key = strtolower(trim(preg_replace("/x_/", '', $key)));        
        if ($key != 'login' && 
            $key != 'password' &&
            $key != 'line_item' &&
            $key != 'tax' &&
            $key != 'freight') {
            $this->vars['x_' . $key] = $value;
        }
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
    }

    /**
     * set the taxes variable
     */ 
    public function setFreight($method = '', $amount = 0)
    {
        $amount = $this->scrubNumber($amount);

        if (!empty($method) && $amount > 0) {
            $this->freight = "Freight<|>$method<|>$amount";
        } else {
            $this->freight = '';
        }

        if (is_numeric($n)) {
            $this->tax = 'Taxes|' . $n;
        } else {
            $this->tax = 'Taxes|0.00';
        }
    }


    /**
     * set which gateway transactions will be sent to
     *      
     * @param string $gateway [which gateway you want to use]
     * 
     *     PRODUCTION - transactions will be sent to the production server
     *     DEVELOPER - transactions will be sent to the test server.
     *    
     */
    public static function setGateway($gateway = 'production')
    {
        if (strtolower(trim($gateway)) == 'developer') {
            $this->gateway = 'https://test.authorize.net/gateway/transact.dll';
            $this->setCredentials('6zz6m5N4Et', '9V9wUv6Yd92t27t5');                    
        } else {
            $this->gateway = 'https://secure.authorize.net/gateway/transact.dll';
        }
    }

    /**
     * set the taxes variable
     */ 
    public function setTax($n = 0)
    {
        $n = $this->scrubNumber($n);

        if (is_numeric($n)) {
            $this->tax = 'Taxes|' . $n;
        } else {
            $this->tax = 'Taxes|0.00';
        }
    }

    /**
     * the variable array used to construct the POST string
     * 
     * @param mixed $var [string or array to be added t]
     * @param string $value [string or ignored]
     * @return Aim
     */
    public function setVar($var = '', $value = '')
    {
        (is_object($var)) ? $var = (array) $var : false;        

        if (is_array($var)) { 
            foreach ($var as $n => $v) {
                if (is_array($v)) {                 
                    $this->setVar($v);
                } else {
                    $this->setCleanVar($n, $v);                
                }
            }
        } else {
            $this->setCleanVar($var, $value);
        }                
    } 

    /**
     * 
     */ 
    public function unlinkedCredit() 
    {

    }

    /**
     * ensure variable is build correctly.
     */ 
    private function variableCheck()
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
