<?php

function _verfy_ac($private_key, $params) {
    ksort($params); 
    $params_data = ""; 
    foreach($params as $key => $value){
        $params_data .= $key;
        $params_data .= $value;
    }   
    $params_data .= $private_key;
    return sha1($params_data);
}   

class UConnection{
	/**
	 * Contains the last HTTP status code returned. 
	 *
	 * @ignore
	 */
	public $http_code;
	/**
	 * Contains the last API call.
	 *
	 * @ignore
	 */
	public $url;
	/**
	 * Set up the API root URL.
	 *
	 * @ignore
	 */
	public $host;
	/**
	 * Set timeout default.
	 *
	 * @ignore
	 */
	public $timeout = 30;
	/**
	 * Set connect timeout.
	 *
	 * @ignore
	 */
	public $connecttimeout = 30;
	/**
	 * Verify SSL Cert.
	 *
	 * @ignore
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * Respons format.
	 *
	 * @ignore
	 */
	public $format = 'json';
	/**
	 * Decode returned json data.
	 *
	 * @ignore
	 */
	public $decode_json = TRUE;
	/**
	 * Contains the last HTTP headers returned.
	 *
	 * @ignore
	 */
	public $http_info;
	/**
	 * Set the useragnet.
	 *
	 * @ignore
	 */
	public $useragent = 'Sae T OAuth2 v0.1';

	/**
	 * print the debug info
	 *
	 * @ignore
	 */
	public $debug = false;

	/**
	 * boundary of multipart
	 * @ignore
	 */
	public static $boundary = '';

    function __construct($base_url) {
        $this->host = $base_url;
    }

    /**
     * GET wrappwer for oAuthRequest.
     *
     * @return mixed
     */
    function get($url, $parameters = array()) {
        $response = $this->oAuthRequest($url, 'GET', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);
        }
        return $response;
    }

    /**
     * POST wreapper for oAuthRequest.
     *
     * @return mixed
     */
    function post($url, $parameters = array(), $multi = false) {
        $response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);
        }
        return $response;
    }

    /**
     * DELTE wrapper for oAuthReqeust.
     *
     * @return mixed
     */
    function delete($url, $parameters = array()) {
        $response = $this->oAuthRequest($url, 'DELETE', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);
        }
        return $response;
    }

    /**
     * Format and sign an OAuth / API request
     *
     * @return string
     * @ignore
     */
    function oAuthRequest($url, $method, $parameters, $multi = false) {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = "{$this->host}{$url}";
        }

        switch ($method) {
        case 'GET':
            $url = $url . '?' . http_build_query($parameters);
            return $this->http($url, 'GET');
        default:
            $headers = array();
            if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
                $body = http_build_query($parameters);
            } else {
                $body = self::build_http_query_multi($parameters);
                $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
            }
            return $this->http($url, $method, $body, $headers);
        }
    }

    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    function http($url, $method, $postfields = NULL, $headers = array()) {
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                $this->postdata = $postfields;
            }
            break;
        case 'DELETE':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($postfields)) {
                $url = "{$url}?{$postfields}";
            }
        }

        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;

        if ($this->debug) {
            echo "=====http code======\r\n";
            var_dump($this->http_code);
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo '=====info====='."\r\n";
            print_r( curl_getinfo($ci) );

            echo '=====$response====='."\r\n";
            print_r( $response );
        }
        curl_close ($ci);
        return $response;
    }

    /**
     * Get the header info to store.
     *
     * @return int
     * @ignore
     */
    function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * @ignore
     */
    public static function build_http_query_multi($params) {
        if (!$params) return '';

        uksort($params, 'strcmp');

        $pairs = array();

        self::$boundary = $boundary = uniqid('------------------');
        $MPboundary = '--'.$boundary;
        $endMPboundary = $MPboundary. '--';
        $multipartbody = '';

        foreach ($params as $parameter => $value) {

            if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
                $url = ltrim( $value, '@' );
                $content = file_get_contents( $url );
                $array = explode( '?', basename( $url ) );
                $filename = $array[0];

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
                $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
                $multipartbody .= $content. "\r\n";
            } else {
                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
                $multipartbody .= $value."\r\n";
            }

        }

        $multipartbody .= $endMPboundary;
        return $multipartbody;
    }
}



class UcloudApiClient {

    function __construct( $base_url, $public_key, $private_key, $project_id)
    {
        $this->conn = new UConnection($base_url);
        $this->PublicKey = $public_key;
        $this->private_key = $private_key;

        if ($project_id !== "") {
            $this->project_id = $project_id;
        }
    }

    function get($api, $params){
        $params["PublicKey"] = $this->PublicKey;
        $params["Signature"] = _verfy_ac($this->private_key, $params);
        return $this->conn->get($api, $params);
    }

    function post($api, $params){
        $params["PublicKey"] = $this->PublicKey;
        $params["Signature"] = _verfy_ac($this->private_key, $params);
        return $this->conn->post($api, $params);
    }
} 

