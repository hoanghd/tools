<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: fbconnect.php 2143 2010-11-15 17:13:55Z Raymond_Benc $
 */

error_reporting(E_ALL);

// Make sure we are running PHP5
if (version_compare(phpversion(), '5', '<') === true)
{
	exit('phpFox 2.x requires PHP 5 or newer.');
}

if (!function_exists('json_decode'))
{
	exit('You are missing the PHP function <a href="http://php.net/manual/en/function.json-decode.php">json_decode</a>, which requires your PHP version to be greater then 5.2.0.');
}

$sContent = file_get_contents('https://graph.facebook.com/19292868552');

if (!empty($sContent))
{
	$oData = json_decode($sContent);
	if (isset($oData->id))
	{
		exit('You will be able to use the Facebook Connect plug-in on your server.');
	}
}

echo 'Unable to connect to Facebook via HTTPS<br /><br />Error Report:<br />';
$aWrappers = stream_get_wrappers();
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<br />";
echo 'http wrapper: ', in_array('http', $aWrappers) ? 'yes':'no', "<br />";
echo 'https wrapper: ', in_array('https', $aWrappers) ? 'yes':'no', "<br />";
echo 'wrappers:<pre>';
echo var_dump($aWrappers);
echo '</pre>';

?>