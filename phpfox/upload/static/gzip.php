<?php

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

define('PHPFOX_IS_GZIP', true);
define('PHPFOX_NO_SESSION', true);
define('PHPFOX_NO_USER_SESSION', true);
define('PHPFOX_NO_PLUGINS', true);

ob_start();

error_reporting(E_ALL);

if (file_exists(PHPFOX_DIR . 'include/setting/server.sett.php'))
{
	require(PHPFOX_DIR . 'include/setting/server.sett.php');
	require(PHPFOX_DIR . 'include/setting/common.sett.php');
}
require(PHPFOX_DIR . 'include/setting/constant.sett.php');

if (!isset($_GET['f']))
{
	exit('No file specified!');
}

$aAllowedTypes = array(
	'css',
	'js'
);

$sFile = $_GET['f'];
if (strstr($sFile, '?'))
{
	$aParts = explode('?', $sFile);
	$sFile = $aParts[0];
}

$sStylePath = '';
if (isset($_GET['s']))
{
	$sStylePath = $_GET['s'];
}

$sType = $_GET['t'];

if (!in_array($sType, $aAllowedTypes))
{
	exit('Not a valid type.');
}

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

$sCacheFile = PHPFOX_DIR_FILE . 'gzip' . PHPFOX_DS . md5('static_' . $sFile . $sType . $bSupportsGzip . (isset($_GET['v']) ? $_GET['v'] : '')) . '.php';

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
	if (preg_match('/,/i', $sFile))
	{
		$aFiles = explode(',', $sFile);
	
		$sContent = '';
		$sThemePath = null;
		foreach ($aFiles as $sFile)
		{			
			if (substr($sFile, 0, 2) == '..')
			{
				exit('Not a valid call.');
			}			
			
			if (!preg_match('/^(.*)\.' . $sType . '$/i', $sFile))
			{
				exit('Not a valid file type.');
			}				
			
			if ($sType == 'js' && !preg_match('/(module|theme)\/(.*)/', $sFile))
			{
				$sFile = str_replace($_CONF['core.path'], '', $_CONF['core.url_static_script']) . $sFile;
			}
			
			if ($sType == 'css' && !preg_match('/(module|theme)\/(.*)/', $sFile))
			{
				$sFile = $sStylePath . $sFile;
			}						
			
			if (file_exists(PHPFOX_DIR . $sFile))
			{				
				if ($sType == 'css' && $sThemePath === null)
				{
					if (preg_match('/(.*?)\/css\/(.*?)\.css/i', $sFile, $aMatches) && isset($aMatches[1]))
					{
						$sThemePath = $aMatches[1] . '/image/';						
					}
				}
				
				$sContent .= file_get_contents(PHPFOX_DIR . $sFile) . "\n\n";	
			}
			else 
			{				
				if (preg_match('/file\/static\/(.*)_(.*)/i', $sFile, $aMatches))
				{
					require_once(PHPFOX_DIR . 'include/init.inc.php');
					
					$oDb = Phpfox::getLib('database');
					$aCss = $oDb->select('tc.css_id, tc.css_data')
							->from(Phpfox::getT('theme_css'), 'tc')
							->where('style_id = ' . (int) $aMatches[1] . ' AND file_name = \'' . $oDb->escape($aMatches[2]) . '\'')
							->execute('getRow');

					$sFound = 'not_found';
					if (isset($aCss['css_id']))
					{
						$aCss['css_data'] = str_replace('../image/', $_CONF['core.path'] . Phpfox::getLib('template')->getStyle('image'), $aCss['css_data']);

						$oCache = Phpfox::getLib('cache', array(
										'storage' => 'file',
										'free' => true
									)
								);
						$sCssCustomCacheId = $oCache->set(PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . $aMatches[1] . '_' . $aMatches[2]);
						$oCache->save($sCssCustomCacheId, Phpfox::getLib('file.minimize')->css($aCss['css_data']));
						$sContent .= $aCss['css_data'];
						$sFound = 'found';
					}
					
					// Phpfox::getLib('file')->write(PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'static_' . PHPFOX_TIME, $sFile . ': ' . print_r($_SERVER, true));
				}				
			}
		}		
	}
	else 
	{
		if (substr($sFile, 0, 2) == '..')
		{
			exit('Not a valid call.');
		}
		
		if (!preg_match('/^(.*)\.' . $sType . '$/i', $sFile))
		{
			exit('Not a valid file type.');
		}		
		
		if ($sType == 'js' && !preg_match('/(module|theme)\/(.*)/', $sFile))
		{
			$sFile = str_replace($_CONF['core.path'], '', $_CONF['core.url_static_script']) . $sFile;
		}	
		
		if ($sType == 'css' && !preg_match('/(module|theme)\/(.*)/', $sFile))
		{
			$sFile = $sStylePath . $sFile;
		}			
		
		if (!file_exists(PHPFOX_DIR . $sFile))
		{
			exit("Invalid File");
		}
		
		$sContent = file_get_contents(PHPFOX_DIR . $sFile);
	}

	if ($sType == 'css')
	{
		$sContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sContent);
		$sContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sContent);
		$sContent = preg_replace('/\.\.\/image\//i', '../' . $sThemePath, $sContent);
	}	
	else 
	{
		/*
		if (file_exists(PHPFOX_DIR_LIB . 'jsmin/jsmin.class.php'))
		{
			require_once(PHPFOX_DIR_LIB . 'jsmin/jsmin.class.php');	
			$sContent = JSMin::minify($sContent);
		}
		*/
	}	

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

ob_clean();

header("Content-type: text/" . ($sType == 'css' ? 'css' : 'javascript'));
header("Vary: Accept-Encoding");  
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");	

if (isset($sGzipContent))
{
	header("Content-Encoding: " . $sEnc);
}

echo (isset($sGzipContent) ? $sGzipContent : $sContent);

?>