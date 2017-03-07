<?php
 interface HTTP_OAuth_Store_Consumer_Interface
 {
     public function setRequestToken($token, $tokenSecret, $providerName, $sessionID);
     public function getRequestToken($providerName, $sessionID);
     public function getAccessToken($consumerUserID, $providerName);
     public function setAccessToken(HTTP_OAuth_Store_Data $data);
     public function removeAccessToken(HTTP_OAuth_Store_Data $data);
 }
