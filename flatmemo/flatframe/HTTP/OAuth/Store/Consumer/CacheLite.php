<?php
 require_once 'HTTP/OAuth/Store/Data.php'; require_once 'HTTP/OAuth/Store/Consumer/Interface.php'; require_once 'Cache/Lite.php'; class HTTP_OAuth_Store_Consumer_CacheLite implements HTTP_OAuth_Store_Consumer_Interface
 {
     const TYPE_REQUEST = 'requestTokens';
     const TYPE_ACCESS = 'accessTokens';
     const REQUEST_TOKEN_LIFETIME = 300;
     protected $cache = null;
     protected $options = array();
     protected $defaultOptions = array( 'cacheDir' => '/tmp', 'lifeTime' => 300, 'hashedDirectoryLevel' => 2 );
     public function __construct(array $options = array())
     {
         $this->options = array_merge($this->defaultOptions, $options);
         $this->cache = new Cache_Lite($this->options);
     }
     public function setRequestToken($token, $tokenSecret, $providerName, $sessionID)
     {
         $this->setOptions(self::TYPE_REQUEST, self::REQUEST_TOKEN_LIFETIME);
         $data = array( 'token' => $token, 'tokenSecret' => $tokenSecret, 'providerName' => $providerName, 'sessionID' => $sessionID );
         return $this->cache->save(serialize($data), $this->getRequestTokenKey($providerName, $sessionID));
     }
     public function getRequestToken($providerName, $sessionID)
     {
         $this->setOptions(self::TYPE_REQUEST, self::REQUEST_TOKEN_LIFETIME);
         $result = $this->cache->get($this->getRequestTokenKey($providerName, $sessionID));
         return unserialize($result);
     }
     protected function getRequestTokenKey($providerName, $sessionID)
     {
         return md5($providerName . ':' . $sessionID);
     }
     public function getAccessToken($consumerUserID, $providerName)
     {
         $this->setOptions(self::TYPE_ACCESS);
         $result = $this->cache->get($this->getAccessTokenKey($consumerUserID, $providerName));
         return unserialize($result);
     }
     public function setAccessToken(HTTP_OAuth_Store_Data $data)
     {
         $this->setOptions(self::TYPE_ACCESS);
         $key = $this->getAccessTokenKey($data->consumerUserID, $data->providerName);
         return $this->cache->save(serialize($data), $key);
     }
     public function removeAccessToken(HTTP_OAuth_Store_Data $data)
     {
         $this->setOptions(self::TYPE_ACCESS);
         $key = $this->getAccessTokenKey($data->consumerUserID, $data->providerName);
         return $this->cache->remove($key);
     }
     protected function getAccessTokenKey($consumerUserID, $providerName)
     {
         return md5($consumerUserID . ':' . $providerName);
     }
     protected function setOptions($key, $expire = null)
     {
         $cacheDir = $this->options['cacheDir'] . '/oauth/';
         $cacheDir .= rtrim($key, '/') . '/';
         $this->ensureDirectoryExists($cacheDir);
         $this->cache->setOption('cacheDir', $cacheDir);
         $this->cache->setOption('lifeTime', $expire);
     }
     protected function ensureDirectoryExists($dir)
     {
         if (!file_exists($dir)) {
             mkdir($dir, 0777, true);
         }
     }
 }
