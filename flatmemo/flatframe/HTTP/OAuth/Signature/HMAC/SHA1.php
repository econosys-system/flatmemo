<?php
 require_once 'HTTP/OAuth/Signature/Common.php'; class HTTP_OAuth_Signature_HMAC_SHA1 extends HTTP_OAuth_Signature_Common
 {
     public function build($method, $url, array $params, $consumerSecret, $tokenSecret = '')
     {
         return base64_encode(hash_hmac('sha1', $this->getBase($method, $url, $params), $this->getKey($consumerSecret, $tokenSecret), true));
     }
 }
