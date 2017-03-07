<?php
 function smarty_modifier_mbtruncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
 {
     if ($length == 0) {
         return '';
     }
     if (mb_strlen($string) > $length) {
         $length -= mb_strlen($etc);
         if (!$break_words && !$middle) {
             $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
         }
         if (!$middle) {
             return mb_substr($string, 0, $length).$etc;
         } else {
             return mb_substr($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
         }
     } else {
         return $string;
     }
 }
