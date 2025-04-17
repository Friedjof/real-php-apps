<?php

if (!defined('CMSIMPLE_VERSION') || preg_match('#/convert_h1split/index.php#i',$_SERVER['SCRIPT_NAME'])) 
{
    die('no direct access');
}

if(!defined('PLUGINLOADER')) {
	die('Plugin '. basename(dirname(__FILE__)) . ' requires a newer version of the Pluginloader. No direct access.');
}


// convert content to h1only splitting

if ($adm && isset($_GET['convert_content_to_h1only']) && !strstr(file_get_contents('./content/content.php'),'class="_level1_page_"'))
{
	copy($pth['file']['content'], $pth['folder']['content'] . 'SIK_content_4-to-5_' . date("Ymd_His") . '.php');
	copy($pth['file']['pagedata'], $pth['folder']['content'] . 'SIK_pagedata_4-to-5_' . date("Ymd_His") . '.php');
	
	$contemp = file_get_contents('./content/content.php');

	// create CMSimple 5 headings
	if($cf['menu']['levels'] == '6')
	{
		// clean h1-6 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		$contemp = preg_replace('/<h2.*?>/i','<h2>',$contemp);
		$contemp = preg_replace('/<h3.*?>/i','<h3>',$contemp);
		$contemp = preg_replace('/<h4.*?>/i','<h4>',$contemp);
		$contemp = preg_replace('/<h5.*?>/i','<h5>',$contemp);
		$contemp = preg_replace('/<h6.*?>/i','<h6>',$contemp);

		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
		$contemp = str_ireplace('<h2>','<h1 class="_level2_page_">',$contemp);
		$contemp = str_ireplace('</h2>','</h1>',$contemp);
		$contemp = str_ireplace('<h3>','<h1 class="_level3_page_">',$contemp);
		$contemp = str_ireplace('</h3>','</h1>',$contemp);
		$contemp = str_ireplace('<h4>','<h1 class="_level4_page_">',$contemp);
		$contemp = str_ireplace('</h4>','</h1>',$contemp);
		$contemp = str_ireplace('<h5>','<h1 class="_level5_page_">',$contemp);
		$contemp = str_ireplace('</h5>','</h1>',$contemp);
		$contemp = str_ireplace('<h6>','<h1 class="_level6_page_">',$contemp);
		$contemp = str_ireplace('</h6>','</h1>',$contemp);
	}
	
	if($cf['menu']['levels'] == '5')
	{
		// clean h1-5 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		$contemp = preg_replace('/<h2.*?>/i','<h2>',$contemp);
		$contemp = preg_replace('/<h3.*?>/i','<h3>',$contemp);
		$contemp = preg_replace('/<h4.*?>/i','<h4>',$contemp);
		$contemp = preg_replace('/<h5.*?>/i','<h5>',$contemp);

		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
		$contemp = str_ireplace('<h2>','<h1 class="_level2_page_">',$contemp);
		$contemp = str_ireplace('</h2>','</h1>',$contemp);
		$contemp = str_ireplace('<h3>','<h1 class="_level3_page_">',$contemp);
		$contemp = str_ireplace('</h3>','</h1>',$contemp);
		$contemp = str_ireplace('<h4>','<h1 class="_level4_page_">',$contemp);
		$contemp = str_ireplace('</h4>','</h1>',$contemp);
		$contemp = str_ireplace('<h5>','<h1 class="_level5_page_">',$contemp);
		$contemp = str_ireplace('</h5>','</h1>',$contemp);
	}
	
	if($cf['menu']['levels'] == '4')
	{
		// clean h1-4 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		$contemp = preg_replace('/<h2.*?>/i','<h2>',$contemp);
		$contemp = preg_replace('/<h3.*?>/i','<h3>',$contemp);
		$contemp = preg_replace('/<h4.*?>/i','<h4>',$contemp);

		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
		$contemp = str_ireplace('<h2>','<h1 class="_level2_page_">',$contemp);
		$contemp = str_ireplace('</h2>','</h1>',$contemp);
		$contemp = str_ireplace('<h3>','<h1 class="_level3_page_">',$contemp);
		$contemp = str_ireplace('</h3>','</h1>',$contemp);
		$contemp = str_ireplace('<h4>','<h1 class="_level4_page_">',$contemp);
		$contemp = str_ireplace('</h4>','</h1>',$contemp);
	}
	
	if($cf['menu']['levels'] == '3')
	{
		// clean h1-3 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		$contemp = preg_replace('/<h2.*?>/i','<h2>',$contemp);
		$contemp = preg_replace('/<h3.*?>/i','<h3>',$contemp);

		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
		$contemp = str_ireplace('<h2>','<h1 class="_level2_page_">',$contemp);
		$contemp = str_ireplace('</h2>','</h1>',$contemp);
		$contemp = str_ireplace('<h3>','<h1 class="_level3_page_">',$contemp);
		$contemp = str_ireplace('</h3>','</h1>',$contemp);
	}
	
	if($cf['menu']['levels'] == '2')
	{
		// clean h1-2 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		$contemp = preg_replace('/<h2.*?>/i','<h2>',$contemp);

		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
		$contemp = str_ireplace('<h2>','<h1 class="_level2_page_">',$contemp);
		$contemp = str_ireplace('</h2>','</h1>',$contemp);
	}
	
	if($cf['menu']['levels'] == '1')
	{
		// clean h1 tags
		$contemp = preg_replace('/<h1.*?>/i','<h1>',$contemp);
		
		$contemp = str_ireplace('<h1>','<h1 class="_level1_page_">',$contemp);
	}
	
	$content4 = $contemp;
	
	
	// write content.php
	$h1only_convertContentHandle = './content/content.php';
	
	$content4handle = fopen($h1only_convertContentHandle, "w+");
	fwrite($content4handle,$content4);
	fclose($content4handle);
	
	header('Location: ./?converted_content_to_h1only');
}



// plugin output

if($adm && $cf['use']['h1only_pagesplitting'] == 'true' && !strstr(file_get_contents('./content/content.php'),'class="_level1_page_"'))
{
	$o.= '<div class="cch1s_container">';

	$o.= '
<h1>' . $plugin_tx['convert_content']['headline_plugin'] . '</h1>
<p>' . $plugin_tx['convert_content']['text_config_changed'] . '</p>
<div style="background: #fff; border: 2px solid; padding: 0 12px;">
<p>' . $plugin_tx['convert_content']['text_config_changed_back1'] . '</p>
<p>' . $plugin_tx['convert_content']['text_config_changed_back2'] . '</p>
<!-- <p style="text-align: right;"><a href="./?file=config&action=array"><b>Zur CMS Konfiguration &raquo;</b></a></p> -->
</div>
';

	$o.= '<p>' . $plugin_tx['convert_content']['text_plugin_description'] . '</p>
<p class="cmsimplecore_warning" style="text-align: center; padding: 12px;"><b>' . $plugin_tx['convert_content']['text_usage_hint'] . '<br>
' . $plugin_tx['convert_content']['text_backup_hint'] . '</b></p>
';
	$o.= '<p style="text-align: right;"><br><a href="./?convert_content_to_h1only"><b>' . $plugin_tx['convert_content']['text_convert_content'] . ' h1only_pagesplitting &raquo;</b></a></p>';
	$o.= '</div>';
}

if(isset($_GET['converted_content_to_h1only']))
{
	$o.= '<p class="cch1s_done">' . $plugin_tx['convert_content']['text_conversion_complete'] . '</p>';
}

?>