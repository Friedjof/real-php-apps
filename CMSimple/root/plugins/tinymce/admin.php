<?php

/*
============================================================
CMSimple Plugin TinyMCE4
============================================================
Version:    TinyMce4 v2.3
Released:   2017/04/29
============================================================
utf-8 check: äöü
*/

initvar(basename(dirname(__FILE__)));

function getTinyMCE4Inits()
{
    $options = array();
    foreach (glob(dirname(__FILE__) . '/inits/init_*.js') as $file)
    {
        $options[] = substr(basename($file, '.js'), 5);
    }
    return $options;
}

if ($tinymce && $adm)
{
    $post     = (filter_input_array(INPUT_POST));
    $get      = filter_input_array(INPUT_GET);
    $options  = getTinyMCE4Inits();
    $location = "$sn?$plugin";

    $o.='
<h4>Plugin TinyMCE4 for CMSimple</h4>
<hr>
<p><b>Plugin-Version:</b> 2.5.1</p>
<p><b>TinyMCE-Version:</b> 4.9.11</p>
<p><b>Released:</b> 2022-05-01</p>
<p><b>Programming:</b> <a href="http://www.zeichenkombinat.de/">Martin Damken</a></p>
<p><b>Developement and Finishing:</b> <a href="http://www.ge-webdesign.de/">Gert Ebersbach</a></p>
<hr>
';

    $o .= '<p style="text-align: right;"><a href="http://www.tinymce.com/wiki.php/Configuration">TinyMce-Website &raquo;</a></p>';
}
?>