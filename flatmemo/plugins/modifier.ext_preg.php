<?php
function smarty_modifier_ext_preg($string='', $preg_pattern='')
{
    if (strcmp($string, '')==0 || strcmp($preg_pattern, '')==0) {
        return false;
    }
    if (preg_match($preg_pattern, $string)) {
        return true;
    } else {
        return false;
    }
}
