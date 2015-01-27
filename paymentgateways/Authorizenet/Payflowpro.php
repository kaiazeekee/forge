<?php

namespace foonster\forge\paymentgateways\Authorizenet;
/**
 * This class is based on the AIM Developer Guide using Authorize.Net’s 
 * Advanced Integration Method API
 * 
 */

class Payflowpro
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
     * return the URL associated with the currently selected gateway
     * 
     * @return string
     */
    public function gateway()
    {
        return $this->gateway;
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
