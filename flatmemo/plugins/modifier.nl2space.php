<?php

function smarty_modifier_nl2space($string)
{
    return str_replace(array("\r\n","\n","\r"), " ", $string);
}
