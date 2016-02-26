<?php

class TrashmailBlacklist {

    const CACHE_TIME = 10800; //3h

    protected $cacheType = 0;
    protected $verifySSL = false; //StartCom Class 1 not always in Certificate Store...
    protected $endpoint = 'https://v2.trashmail-blacklist.org';

    /**
     * enables caching for api calls.  
     * @param int $cacheType value of one of the constants of TrashmailBlacklistCache
     */
    public function setCaching($cacheType) {
        $this->cacheType = $cacheType;
    }

    /**
     * @param bool $ssl false, if ssl cert should not be verified
     */
    public function setSSLVerification($verifySSL) {
        $this->verifySSL = $verifySSL;
    }

    /**
     * returns true, if param is blacklisted
     * @param string $parameter
     */
    public function isBlacklisted($parameter) {
        if(filter_var($parameter, FILTER_VALIDATE_EMAIL)) {
            $domain = explode("@", $parameter)[1];
        } else {
            $domain = $parameter;
        }

        if(strpos($domain, '.') === FALSE) {
            trigger_error('Trashmail-Blacklist: "'.$parameter.'" is neither a valid domain nor a valid email address!', E_USER_WARNING);
        }

        $api = $this->apiCall($domain);
        if($api['api']['status'] == 'blacklisted') {
            return true;
        }
        return false;
    }

    public function apiCall($domain) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endpoint.'/check/json/'.$domain);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "trashmail-blacklist-php (curl, ".gethostname().")");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return ['api' => json_decode($result, true), 'http_code' => $info];
    }

}

class TrashmailBlacklistCache {

    const USE_REDIS = 1;
    const USE_DIR = 2;

}