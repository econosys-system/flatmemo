<?php
 function smarty_modifier_fsize($file_name, $format = '', $precision = 1)
 {
     if (! is_file($file_name)) {
         return 'no data';
     }
     $size = filesize($file_name);
     $sizes = array();
     $sizes["TBytes"] = 1099511627776;
     $sizes["GBytes"] = 1073741824;
     $sizes["MBytes"] = 1048576;
     $sizes["KBytes"] = 1024;
     $sizes["Bytes"] = 1;
     foreach ($sizes as $unit => $bytes) {
         if ($size > $bytes || $unit == strtoupper($format)) {
             return number_format($size / $bytes, $precision)." ".$unit;
         }
     }
 }
