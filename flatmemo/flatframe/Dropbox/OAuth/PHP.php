<?php
 class Dropbox_OAuth_PHP extends Dropbox_OAuth
 {
     protected $oAuth;
     public function __construct($consumerKey, $consumerSecret)
     {
         if (!class_exists('OAuth')) {
             throw new Dropbox_Exception('The OAuth class could not be found! Did you install and enable the oauth extension?');
         }
         $this->OAuth = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
         $this->OAuth->enableDebug();
     }
     public function setToken($token, $token_secret = null)
     {
         parent::setToken($token, $token_secret);
         $this->OAuth->setToken($this->oauth_token, $this->oauth_token_secret);
     }
     public function fetch($uri, $arguments = array(), $method = 'GET', $httpHeaders = array())
     {
         try {
             $this->OAuth->fetch($uri, $arguments, $method, $httpHeaders);
             $result = $this->OAuth->getLastResponse();
             $lastResponseInfo = $this->OAuth->getLastResponseInfo();
             return array( 'httpStatus' => $lastResponseInfo['http_code'], 'body' => $result, );
         } catch (OAuthException $e) {
             $lastResponseInfo = $this->OAuth->getLastResponseInfo();
             switch ($lastResponseInfo['http_code']) { case 304: return array( 'httpStatus' => 304, 'body' => null, ); break; case 403: throw new Dropbox_Exception_Forbidden('Forbidden. This could mean a bad OAuth request, or a file or folder already existing at the target location.'); case 404: throw new Dropbox_Exception_NotFound('Resource at uri: ' . $uri . ' could not be found'); case 507: throw new Dropbox_Exception_OverQuota('This dropbox is full'); default: throw $e; }
         }
     }
     public function getRequestToken()
     {
         try {
             $tokens = $this->OAuth->getRequestToken(self::URI_REQUEST_TOKEN);
             $this->setToken($tokens['oauth_token'], $tokens['oauth_token_secret']);
             return $this->getToken();
         } catch (OAuthException $e) {
             throw new Dropbox_Exception_RequestToken('We were unable to fetch request tokens. This likely means that your consumer key and/or secret are incorrect.', 0, $e);
         }
     }
     public function getAccessToken()
     {
         $uri = self::URI_ACCESS_TOKEN;
         $tokens = $this->OAuth->getAccessToken($uri);
         $this->setToken($tokens['oauth_token'], $tokens['oauth_token_secret']);
         return $this->getToken();
     }
 }
