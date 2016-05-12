<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: image.php 2637 2011-06-02 13:08:50Z Raymond_Benc $
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

// Require phpFox Init
require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'init.inc.php');

$sFile = PHPFOX_DIR . 'file' . PHPFOX_DS . 'pic' . PHPFOX_DS . 'photo' . PHPFOX_DS . $_GET['file'] . '.' . $_GET['ext'];

if (!isset($_SESSION['core']['image']['photo_' . md5($sFile)]) || !isset($_SESSION['phpfox']['image']['photo_' . md5($sFile)]))
{	
	$sName = $_GET['file'] . '.' . $_GET['ext'];
	$sName = preg_replace('/(.*)_(.*)\.(.*)/i', '\\1.\\3', $sName);
	$sName = preg_replace('/(.*)\.(.*)/i', '\\1%s.\\2', $sName);
	$bCantViewImage = false;

	$aPhoto = Phpfox::getService('photo')->getPhotoByDestination($sName);

	if ($aPhoto === false)
	{
		$bCantViewImage = true;	
	}
	else 
	{
		// User can view this item because they are allowed to view all hidden items
		if (Phpfox::getUserParam('photo.can_view_hidden_photos'))
		{
			
		}
		// Viewer can view this item since it belongs to them
		elseif (Phpfox::getUserId() && Phpfox::getUserId() == $aPhoto['user_id'])
		{
			
		}
		else 
		{		
			// If the album is only for friends lets make sure the user is friends with the album owner
			if ($aPhoto['privacy'] == 2 && !Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aPhoto['user_id']))
			{
				$bCantViewImage = true;		
			}
			// If the album is only allowed to be viewed by certain users make sure they are in the "white" list
			elseif ($aPhoto['privacy'] == 3 && !Phpfox::getService('privacy')->verify('photo_album', $aPhoto['album_id'], $aPhoto['user_id']))
			{				
				$bCantViewImage = true;			
			}
		}
		
		// Check if this is a password protected album
		if (!empty($aPhoto['album_password']) && $aPhoto['user_id'] != Phpfox::getUserId())
		{
			$bCantViewImage = true;			
		}
	}	
	
	if ($bCantViewImage)
	{
		header("HTTP/1.0 404 Not Found");
		$sText = "Visit our site to view this image.\n" . Phpfox::getParam('core.path');	
		$nW = 400;
		$nH = 50;
		$nLeft = 5;
		$nTop = 5;
		$hImg = imageCreate($nW, $nH);
		$nBgColor  = imageColorAllocate($hImg, 0, 0, 0);
		$nTxtColor = imageColorAllocate($hImg, 255, 255, 255);
		$aLines = explode("\n", $sText);
		foreach ($aLines as $sLine)
		{
			imageString($hImg, 5, $nLeft, $nTop, $sLine, $nTxtColor);
			$nTop += 17;
		}
			        
		ob_clean();
		header('Content-Type: image/jpeg');
		imagejpeg($hImg);        
		exit;
	}
}

if (file_exists($sFile))
{
	ob_clean();
	header('Content-Type: image/' . $_GET['ext']);
	echo file_get_contents($sFile);
	ob_end_flush();
	exit;
}
else 
{
	header("HTTP/1.0 404 Not Found");
	$sText = 'Not Found!';
	$nW = 100;
	$nH = 30;
	$nLeft = 5;
	$nTop = 5;
	$hImg = imageCreate($nW, $nH);
	$nBgColor  = imageColorAllocate($hImg, 0, 0, 0);
	$nTxtColor = imageColorAllocate($hImg, 255, 255, 255);
	imageString($hImg, 5, $nLeft, $nTop,  $sText, $nTxtColor);
		        
	ob_clean();
	header('Content-Type: image/jpeg');
	imagejpeg($hImg);        
	exit;
}

?>