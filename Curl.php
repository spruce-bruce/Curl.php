<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Copyright (C) 2013 Aaron Bruce
 *
 * MIT License
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this 
 * software and associated documentation files (the "Software"), to deal in the Software 
 * without restriction, including without limitation the rights to use, copy, modify, 
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to 
 * permit persons to whom the Software is furnished to do so.
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS 
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER 
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class Curl{
    /**
     * Perform an http POST
     * @param  string  $url     POST url
     * @param  array $data      associative array of post vals
     * @param  boolean $options array of custom options to be passed to curl_setopt_array()
     * @return string           string returned from curl_exec()
     */
    public static function post($url, $data = false, $options = false){
        if(!$options){
            $options = array();
        }
        
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        if($data){
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        
        return self::do_curl($url, $options);
    }
    
    /**
     * Perform an http GET
     *
     * You can put your get vars in the url passed in or pass an
     * associative array and the get() method will add the contents
     * to the end of the url
     * 
     * @param  string  $url     GET url
     * @param  array $data      associative array of GET values
     * @param  array $options   array of custom options to be passed to curl_setopt_array()
     * @return string           string returned from curl_exec()
     */
    public static function get($url, $data = false, $options = false){
        if(!$options){
            $options = array();
        }
        if($data){
            $url .= "?";
            foreach($data as $key=>$val){
                $url .= (substr($url, -1) != "?") ? ("&") : ("");
                $url .= $key . "=" . $val;
            }
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        
        return self::do_curl($url, $options);
    }
    
    /**
     * @todo write this method!
     */
    public static function put($url, $data){
        
    }
    
    /**
     * @todo write this method!
     */
    public static function delete($url, $data){
        
    }
    
    private static function do_curl($url, $options = false){
        if(!$options){
            $options = array();
        }
        
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);
        
        if(!$res){
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            throw new Curl_Exception("Curl error number " . $errno . ". " . $error .".");
        }
        
        $headers = curl_getinfo($ch); 
        try{
            self::validate_http_code($headers['http_code']);
        }catch(Http_Code_Exception $e){
            $e->response = $res;
            $e->response_info = $headers;
            $e->http_code = $headers['http_code'];
            //rethrow exception
            throw $e;
        }
        
        curl_close($ch);
        return $res;
    }
    
    /**
     * Checks the http code of the result and throws exception for unsuccessful requests
     * 
     * This method currently only checks ranges (i.e. 2xx, 3xx, 4xx) instead of specific
     * http codes with one exception, the 401 code. As this class receives more and more
     * use the plan is to get this method handling all the http codes individually, but
     * I don't feel like writing that right now.
     * 
     * Exceptions are thrown by http response range. All exceptions thrown by this
     * method inherit from Http_Code_Exception.
     * 
     * 2xx - throws no exception
     * 3xx - throws Redirection_Exception
     * 4xx - throws Bad_Request_Exception
     * 5xx - throws Server_Error_Exception
     * 
     * http response codes documented here: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * 
     * @param type $http_code
     * @throws Http_Code_Exception
     * @throws Redirection_Exception
     * @throws Bad_Request_Exception
     * @throws Server_Error_Exception 
     */
    private static function validate_http_code($http_code){
        switch($http_code){
            case ($http_code >= 200 && $http_code < 300):
                //the 2xx range status code indicates success
                break;
            case ($http_code >= 300 & $http_code < 400):
                //the 3xx range status code indicates failure, but action can be taken to be successful
                throw new Redirection_Exception("Curl request failed. The response had an http code of {$http_code}");
                break;
            case 401:
                //401 unauthorized
                throw new Bad_Request_Exception("Curl request failed. The response returned {$http_code} Unauthorized");
                break;
            case ($http_code >= 400 && $http_code < 500):
                throw new Bad_request_Exception("Curl request failed. The response had an http code of {$http_code}");
                break;
            case ($http_code >= 500 && $http_code < 600):
                throw new Server_Error_Exception("Curl request failed. The response had an http code of {$http_code}");
                break;
        }
    }
}

class Curl_Exception extends Kohana_Exception{
    protected $message = "There has been an error with the Curl class.";
}

class Http_Code_Exception extends Curl_Exception{
    protected $message = "An http code other than 2xx was returned by the curl request";
    public $response_info;
    public $response;
    public $http_code;
}
class Redirection_Exception extends Http_Code_Exception{
    protected $message = "A 3xx range http code was returned by the curl request";
}
class Bad_Request_Exception extends Http_Code_Exception{
    protected $message = "A 4xx range http code was returned by the curl request";
}
class Server_Error_Exception extends Http_Code_Exception{
    protected $message = "A 5xx range http code was returned by the curl request";
}
