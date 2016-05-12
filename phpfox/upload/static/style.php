<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ajax.php 2771 2011-07-30 19:34:11Z Raymond_Benc $
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

if (!isset($_GET['app_id']))
{
	exit('You need to pass your APP ID as "app_id=<YOUR_ID_HERE>".');
}

// Require phpFox Init
require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'init.inc.php');

$oTpl = Phpfox::getLib('template');

ob_clean();

$bIsContentCached = false;
$expiresOffset = 3600 * 24 * 10;

$bSupportsGzip = false;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
{
	$aEncodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

	if ((in_array('gzip', $aEncodings) || in_array('x-gzip', $aEncodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression') && !headers_sent()) 
	{
		$sEnc = in_array('x-gzip', $aEncodings) ? "x-gzip" : "gzip";
		$bSupportsGzip = true;
	}
}

$sCacheFile = PHPFOX_DIR_FILE . 'gzip' . PHPFOX_DS . md5('apps_' . $_GET['app_id'] . $bSupportsGzip . (isset($_GET['v']) ? $_GET['v'] : '')) . '.php';

header('Content-Type: text/css');
header("Vary: Accept-Encoding");  
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");	

if (file_exists($sCacheFile))
{
	if ($bSupportsGzip)
	{
		$sGzipContent = file_get_contents($sCacheFile);
	}
	else 
	{
		$sContent = file_get_contents($sCacheFile);
	}
}
else 
{
	$sThemePath =  str_replace(Phpfox::getParam('core.path'), '', $oTpl->getStyle('image'));
	
	$sContent = file_get_contents(str_replace(Phpfox::getParam('core.path'), PHPFOX_DIR, $oTpl->getStyle('css', 'layout.css')));
	$sContent .= file_get_contents(str_replace(Phpfox::getParam('core.path'), PHPFOX_DIR, $oTpl->getStyle('css', 'common.css')));
	$sContent .= file_get_contents(str_replace(Phpfox::getParam('core.path'), PHPFOX_DIR, $oTpl->getStyle('css', 'custom.css')));
	
	$sContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sContent);
	$sContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sContent);
	$sContent = preg_replace('/\.\.\/image\//i', '../' . $sThemePath, $sContent);	
	
	if ($bSupportsGzip)
	{
		if (function_exists('gzencode'))
		{			
			$sGzipContent = gzencode($sContent, 9, FORCE_GZIP);	
		}
		else
		{
			if (function_exists('gzcompress') && function_exists('crc32'))
			{		
				$size = strlen($sContent);
				$crc = crc32($sContent);
				$sGzipContent = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
				$sGzipContent .= substr(gzcompress($sContent, 9), 2, -4);
				$sGzipContent .= pack('V', $crc);
				$sGzipContent .= pack('V', $size);		
			}
		}			
	}

	$hFile = fopen($sCacheFile, 'w');
	fwrite($hFile, (isset($sGzipContent) ? $sGzipContent : $sContent));
	fclose($hFile);	
}

if (isset($sGzipContent))
{
	header("Content-Encoding: " . $sEnc);
}

echo (isset($sGzipContent) ? $sGzipContent : $sContent);

?>