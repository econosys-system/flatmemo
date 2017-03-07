<?php
 class Dropbox_API
 {
     const ROOT_SANDBOX = 'sandbox';
     const ROOT_DROPBOX = 'dropbox';
     protected $oauth;
     protected $root;
     public function __construct(Dropbox_OAuth $oauth, $root = self::ROOT_DROPBOX)
     {
         $this->oauth = $oauth;
         $this->root = $root;
     }
     public function getToken($email, $password)
     {
         $data = $this->oauth->fetch('http://api.dropbox.com/0/token', array( 'email' => $email, 'password' => $password ), 'POST');
         $data = json_decode($data['body']);
         return array( 'token' => $data->token, 'token_secret' => $data->secret, );
     }
     public function getAccountInfo()
     {
         $data = $this->oauth->fetch('http://api.dropbox.com/0/account/info');
         return json_decode($data['body'], true);
     }
     public function createAccount($email, $first_name, $last_name, $password)
     {
         $result = $this->oauth->fetch('http://api.dropbox.com/0/account', array( 'email' => $email, 'first_name' => $first_name, 'last_name' => $last_name, 'password' => $password, ), 'POST');
         return $result['body']==='OK';
     }
     public function getFile($path = '', $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $result = $this->oauth->fetch('http://api-content.dropbox.com/0/files/' . $root . '/' . ltrim($path, '/'));
         return $result['body'];
     }
     public function putFile($path, $file, $root = null)
     {
         $directory = dirname($path);
         $filename = basename($path);
         if ($directory==='.') {
             $directory = '';
         }
         if (is_null($root)) {
             $root = $this->root;
         }
         if (is_string($file)) {
             $file = fopen($file, 'r');
         } elseif (!is_resource($file)) {
             throw new Dropbox_Exception('File must be a file-resource or a string');
         }
         $this->multipartFetch('http://api-content.dropbox.com/0/files/' . $root . '/' . trim($directory, '/'), $file, $filename);
         return true;
     }
     public function copy($from, $to, $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $response = $this->oauth->fetch('http://api.dropbox.com/0/fileops/copy', array('from_path' => $from, 'to_path' => $to, 'root' => $root));
         return json_decode($response['body'], true);
     }
     public function createFolder($path, $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $path = '/' . ltrim($path, '/');
         $response = $this->oauth->fetch('http://api.dropbox.com/0/fileops/create_folder', array('path' => $path, 'root' => $root), 'POST');
         return json_decode($response['body'], true);
     }
     public function delete($path, $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $response = $this->oauth->fetch('http://api.dropbox.com/0/fileops/delete', array('path' => $path, 'root' => $root));
         return json_decode($response['body']);
     }
     public function move($from, $to, $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $response = $this->oauth->fetch('http://api.dropbox.com/0/fileops/move', array('from_path' => $from, 'to_path' => $to, 'root' => $root));
         return json_decode($response['body'], true);
     }
     public function getLinks($path, $root = null)
     {
         throw new Dropbox_Exception('This API method is currently broken, and dropbox documentation about this is no longer online. Please ask Dropbox support if you really need this.');
     }
     public function getMetaData($path, $list = true, $hash = null, $fileLimit = null, $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $args = array( 'list' => $list, );
         if (!is_null($hash)) {
             $args['hash'] = $hash;
         }
         if (!is_null($fileLimit)) {
             $args['file_limit'] = $hash;
         }
         $response = $this->oauth->fetch('http://api.dropbox.com/0/metadata/' . $root . '/' . ltrim($path, '/'), $args);
         if ($response['httpStatus']==304) {
             return true;
         } else {
             return json_decode($response['body'], true);
         }
     }
     public function getThumbnail($path, $size = 'small', $root = null)
     {
         if (is_null($root)) {
             $root = $this->root;
         }
         $response = $this->oauth->fetch('http://api-content.dropbox.com/0/thumbnails/' . $root . '/' . ltrim($path, '/'), array('size' => $size));
         return $response['body'];
     }
     protected function multipartFetch($uri, $file, $filename)
     {
         $boundary = 'R50hrfBj5JYyfR3vF3wR96GPCC9Fd2q2pVMERvEaOE3D8LZTgLLbRpNwXek3';
         $headers = array( 'Content-Type' => 'multipart/form-data; boundary=' . $boundary, );
         $body="--" . $boundary . "\r\n";
         $body.="Content-Disposition: form-data; name=file; filename=".$filename."\r\n";
         $body.="Content-type: application/octet-stream\r\n";
         $body.="\r\n";
         $body.=stream_get_contents($file);
         $body.="\r\n";
         $body.="--" . $boundary . "--";
         $uri.='?file=' . $filename;
         return $this->oauth->fetch($uri, $body, 'POST', $headers);
     }
     public function is_exists($path)
     {
         date_default_timezone_set('Asia/Tokyo');
         $root = $this->root;
         $response = $this->oauth->fetch('http://api.dropbox.com/0/metadata/' . $root . '/' . ltrim($path, '/'));
         if ($response['httpStatus']==304) {
             return 1;
         } elseif ($response['httpStatus']==404) {
             return 0;
         } else {
             return 1;
         }
     }
     public function need_upload($dropbox_file, $local_file)
     {
         if (! $this->is_exists($dropbox_file)) {
             return 1;
         }
         $md = $this->getMetaData($dropbox_file);
         $dropbox_time = strtotime($md['modified']);
         $local_time = filemtime($local_file);
         if ($dropbox_time < $local_time) {
             return 1;
         } elseif (@$md['is_deleted']==1) {
             return 1;
         } else {
             return 0;
         }
     }
 }
