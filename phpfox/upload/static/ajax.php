<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ajax.php 3118 2011-09-16 10:51:04Z Raymond_Benc $
 */
ob_start();

/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);

/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(__FILE__)) . PHPFOX_DS);

if (isset($_GET['ajax_page_display']))
{
	define('PHPFOX_IS_AJAX_PAGE', true);
}
else 
{
	define('PHPFOX_IS_AJAX', true);
}

// Require phpFox Init
require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'init.inc.php');

if (!Phpfox::getService('ban')->check('ip', Phpfox::getIp()))
{
	exit();
}

if (isset($_GET['ajax_page_display']))
{	
	$oCache = Phpfox::getLib('cache');
	$oAjax = Phpfox::getLib('ajax');

	Phpfox::run();

	$aHeaderFiles = Phpfox::getLib('template')->getHeader(true);	
	
	if (Phpfox::getLib('template')->sDisplayLayout)
	{			
		Phpfox::getLib('template')->getLayout(Phpfox::getLib('template')->sDisplayLayout);
	}	
	
	$sJs = '';
	$sCustomCss = '';
	$sLoadFiles = '';		
	$sEchoData = '';
	foreach ($aHeaderFiles as $sHeaderFile)
	{
		if (preg_match('/js_user_profile_css/i', $sHeaderFile))
		{
			$sJs .= 'profilecss: \'' . $sHeaderFile . '\', ';
			
			continue;
		}
		
		if (preg_match('/<style(.*)>(.*)<\/style>/i', $sHeaderFile))
		{
			$sCustomCss .= '\'' . strip_tags($sHeaderFile) . '\',';
			
			continue;
		}
		
		$sHeaderFile = strip_tags($sHeaderFile);
		
		$sNew = preg_replace('/\s+/','',$sHeaderFile);
		if (empty($sNew))
		{
			continue;
		}
			
		$sLoadFiles .= '\'' . str_replace("'", "\'", $sHeaderFile) . '\',';
	}		
	$sLoadFiles = rtrim($sLoadFiles, ',');
	$sCustomCss = rtrim($sCustomCss, ',');

	$sContent = Phpfox::getLib('ajax')->getContent();
	
	$aPhrases = Phpfox::getLib('template')->getPhrases();	
	
	$sJs .= 'content: \'' . $sContent . '\', ';
	$sJs .= 'files: [' . $sLoadFiles . '], ';
	if (count($aPhrases))
	{
		$sJs .= 'phrases:  {';
		foreach ($aPhrases as $sKey => $sValue)
		{
			$sJs .= '\'' . $sKey . '\': \'' . str_replace("'", "\'", $sValue) . '\',';	
		}
		$sJs = rtrim($sJs, ',');
		$sJs .= '}, ';
	}
	$sJs .= 'title: \'' . str_replace("'", "\'", html_entity_decode(Phpfox::getLib('template')->getTitle(), null, 'UTF-8')) . '\'';
	
	if (!empty($sCustomCss))
	{
		$sJs .= ', customcss: [' . $sCustomCss . ']';
	}
	
	/*
	$aAds = array();
	for ($i = 1; $i <= 11; $i++)
	{
		$aAds[] = Phpfox::getService('ad')->getForBlock($i);
	}
	
	if (count($aAds))
	{
		$sJs .= ', ads: {';
		foreach ($aAds as $aAd)
		{
			if (!isset($aAd['ad_id']))
			{
				continue;
			}
			$sJs .= '\'' . $aAd['location'] . '\': \'' . str_replace("'", "\'", $aAd['html_code']) . '\',';
		}
		$sJs = rtrim($sJs, ',');
		$sJs .= '}';
	}
	 * 
	 */
	
	if (isset($_GET['js_mobile_version']) && $_GET['js_mobile_version'])
	{
		if (isset($_GET['req1']) && empty($_GET['req2']))
		{
			Phpfox::getLib('ajax')->call('$(\'#mobile_search\').show();');
		}
		else
		{
			Phpfox::getLib('ajax')->call('$(\'#mobile_search\').hide();');
		}
	}
	
	Phpfox::getLib('ajax')->call('$Core.page({' . $sJs . '});');
	
	if (PHPFOX_DEBUG)
	{
		Phpfox::getLib('ajax')->call('$(\'#js_main_debug_holder\').html(\'' . Phpfox_Debug::getDetails() . '\');');
	}
	
	echo Phpfox::getLib('ajax')->getData();	
}
else 
{
	$oAjax = Phpfox::getLib('ajax');
	$oAjax->process();
	echo $oAjax->getData();
	if (!isset($_REQUEST['height']) && !isset($_REQUEST['width']) && !isset($_REQUEST['no_page_update']))
	{
		// echo '$Core.updatePageHistory();';	
	}
}

ob_end_flush();
?>