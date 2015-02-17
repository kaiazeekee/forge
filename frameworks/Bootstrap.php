<?php
/**
 *                                                                   
 */
namespace foonster\forge;
/**
 * A twitter bootstrap abstraction class
 * 
 * 
 * @author  Nicolas Colbert
 * @copyright (c) 2002 Foonster Technology                                     
 *                                                                    
 */
class Bootstrap
{
    

    public function __construct()
    {
    
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        
    }   

    public function alert($message, $type = 'success')
    {
        if ($type != 'success' || $type != 'warning' || $type != 'danger') {
            $type = 'success';
        }
        $string = '<div class="alert alert-' . $type . ' alert-dismissible fade in flash" id="' . $type . 'Alert" role="alert">';
        $string .= '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
        $string .= '<div id="' . $type . 'AlertMessage">' . $message . '</div>';
        return $string;
    }

}
