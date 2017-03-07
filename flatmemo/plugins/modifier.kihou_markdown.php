<?php

function smarty_modifier_kihou_markdown( $string='' )
{
    if ($string=='') {
        return $string;
    }

    $parser = new \cebe\markdown\GithubMarkdown();
    return $parser->parse($string);
}
