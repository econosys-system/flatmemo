<?php
 abstract class Dropbox_OAuth
 {
     public $authorizeCallbackUrl = null;
     const URI_REQUEST_TOKEN = 'http://api.dropbox.com/0/oauth/request_token';
     const URI_AUTHORIZE = 'https://www.dropbox.com/0/oauth/authorize';
     const URI_ACCESS_TOKEN = 'http://api.dropbox.com/0/oauth/access_token';
     protected $oauth_token = null;
     protected $oauth_token_secret = null;
     abstract public function __construct($consumerKey, $consumerSecret);
     public function setToken($token, $token_secret = null)
     {
         if (is_array($token)) {
             $this->oauth_token = $token['token'];
             $this->oauth_token_secret = $token['token_secret'];
         } else {
             $this->oauth_token = $token;
             $this->oauth_token_secret = $token_secret;
         }
     }
     public function getToken()
     {
         return array( 'token' => $this->oauth_token, 'token_secret' => $this->oauth_token_secret, );
     }
     public function getAuthorizeUrl($callBack = null)
     {
         $token = $this->getToken();
         $uri = self::URI_AUTHORIZE . '?oauth_token=' . $token['token'];
         if ($callBack) {
             $uri.='&oauth_callback=' . $callBack;
         }
         return $uri;
     }
     abstract public function fetch($uri, $arguments = array(), $method = 'GET', $httpHeaders = array());
     abstract public function getRequestToken();
     abstract public function getAccessToken();
 }
