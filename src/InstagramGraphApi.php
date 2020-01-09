<?php
namespace Nkcx\InstagramGraphApi;

class InstagramGraphApi {

    const API_URL = 'https://graph.instagram.com/';
    const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
    const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
    const API_LONG_LIVE_TOKEN_URL = 'https://graph.instagram.com/access_token';
    const API_REFRESH_TOKEN_URL = 'https://graph.instagram.com/refresh_access_token';
    private $_appid;
    private $_apikey;
    private $_apisecret;
    private $_accesstoken;
    private $_callbackurl;

    public function __construct($config) {
        if (true === is_array($config)) {
            // if you want to access user data
            $this->_apikey = $config['apiKey'];
            $this->_apisecret = $config['apiSecret'];
            $this->_callbackurl = $config['apiCallback'];
        } else if (true === is_string($config)) {
            // if you only want to access public data
            $this->setApiKey($config);
        } else {
            throw new \Exception("Error: __construct() - Configuration data is missing.");
        }
    }

    public function getLoginUrl($scope = array('user_profile')) {
        if (is_array($scope) && count(array_intersect($scope, ['user_profile','user_media'])) === count($scope)) {
          return self::API_OAUTH_URL . '?app_id=' . $this->getApiKey() . '&redirect_uri=' . $this->getApiCallback() . '&scope=' . implode(',', $scope) . '&response_type=code';
        } else {
          throw new \Exception("Error: getLoginUrl() - The parameter isn't an array or invalid scope permissions used.");
        }
    }

    public function getOAuthToken($code, $token = false) {
        $apiData = array(
          'grant_type'      => 'authorization_code',
          'app_id'       => $this->getApiKey(),
          'app_secret'   => $this->getApiSecret(),
          'redirect_uri'    => $this->getApiCallback(),
          'code'            => $code
        );

        $result = $this->_makeOAuthCall($apiData);
        $this->_accesstoken = $data->access_token;

        $apiData = array(
            'grant_type' => 'ig_exchange_token',
            'client_secret' => $this->_apisecret,
        );
        $long_live_token = $this->_callapi('access_token', true, $apiData);

        return (false === $token) ? $long_live_token : $long_live_token->access_token;
    }

    public function refreshAccessToken($token)
    {
        if ($token && $token != '') {
            $this->_accesstoken = $token;
        } else {
            throw new \Exception("Error: refreshAccessToken() |  This method requires an authenticated users access token.");
        }
        $result = $this->_callapi('refresh_access_token', true, array('grant_type'=>'ig_refresh_token'));
        return $result;
    }

    public function getMedia($token, $media_id = null)
    {
        if ($token && $token != '') {
            $this->_accesstoken = $token;
        } else {
            throw new \Exception("Error: getMedia() |  This method requires an authenticated users access token.");
        }
        $result = [];
        $apiData = ["fields"=>"id,media_type,media_url,permalink,thumbnail_url,timestamp,username"];
        if ($media) {
            $result = $this->_callapi($media_id, true, $apiData);
            if ()
        } else {
            $result = $this->_callapi('me/media', true, $apiData);
        }
        return $result;
    }

    protected function _callapi($function, $auth = false, $params = null, $method = 'GET') {
        if (false === $auth) {
          // if the call doesn't requires authentication
          $authMethod = '?app_id=' . $this->getApiKey();
        } else {
          // if the call needs an authenticated user
          if (true === isset($this->_accesstoken)) {
            $authMethod = '?access_token=' . $this->_accesstoken;
          } else {
            throw new \Exception("Error: _callapi() | $function - This method requires an authenticated users access token.");
          }
        }

        if (isset($params) && is_array($params)) {
          $paramString = '&' . http_build_query($params);
        } else {
          $paramString = null;
        }

        $apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);

        $headerData = array('Accept: application/json');
        // if (true === $this->_signedheader && 'GET' !== $method) {
        //   $headerData[] = 'X-Insta-Forwarded-For: ' . $this->_signHeader();
        // }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiCall);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ('POST' === $method) {
          curl_setopt($ch, CURLOPT_POST, count($params));
          curl_setopt($ch, CURLOPT_POSTFIELDS, ltrim($paramString, '&'));
        } else if ('DELETE' === $method) {
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $jsonData = curl_exec($ch);
        if (false === $jsonData) {
          throw new \Exception("Error: _makeCall() - cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($jsonData);
    }

    private function _makeOAuthCall($apiData) {
        $apiHost = self::API_OAUTH_TOKEN_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiHost);
        curl_setopt($ch, CURLOPT_POST, count($apiData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $jsonData = curl_exec($ch);
        if (false === $jsonData) {
            throw new \Exception("Error: _makeOAuthCall() - cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($jsonData);
    }

    protected function getApiCallback()
    {
        return $this->_callbackurl;
    }

    protected function getApiKey()
    {
        return $this->_apikey;
    }
}
?>
