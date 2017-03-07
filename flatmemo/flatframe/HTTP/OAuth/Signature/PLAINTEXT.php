<?php
 require_once 'HTTP/OAuth/Signature/Common.php'; class HTTP_OAuth_Signature_PLAINTEXT extends HTTP_OAuth_Signature_Common
 {
     public function build($method, $url, array $params, $consumerSecret, $tokenSecret = '')
     {
         return $this->getKey($consumerSecret, $tokenSecret);
     }
 }
