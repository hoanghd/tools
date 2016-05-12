<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: phpfox.php 1535 2010-03-29 07:54:39Z Raymond_Benc $
 */

// Make sure we are running PHP5
if (version_compare(phpversion(), '5', '<') === true)
{
	exit('phpFox 2.x requires PHP 5 or newer.');
}

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
define('PHPFOX_DIR', dirname(__FILE__) . PHPFOX_DS);

define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));

// Require phpFox Init
require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'init.inc.php');

$oReq = Phpfox::getLib('request');
$oDb = Phpfox::getLib('database');

$sCmd = (isset($_GET['cmd']) ? $_GET['cmd'] : '');

$aCommands = array(
	'find-username-from-email' => 'Find Username From Email',
	'verify-email' => 'Auto Verify Email',
	'make-admin-from-email' => 'Make an Admin From Email',
);

switch ($sCmd)
{
	case 'make-admin-from-email':
		if (($sEmail = $oReq->get('email')) && !empty($sEmail))
		{
			$aRow = $oDb->select('user_id')
				->from(Phpfox::getT('user'))
				->where('email = \'' . $oDb->escape($sEmail) . '\'')
				->execute('getRow');
			if (isset($aRow['user_id']))
			{
				$oDb->update(Phpfox::getT('user'), array('user_group_id' => '1'), 'user_id = ' . $aRow['user_id']);
			}
			else 
			{
				echo '<div>Invalid!</div>';
			}
		}
		echo '<form method="post" action="phpfox.php?cmd=make-admin-from-email">';
		echo '<div>Email: <input type="text" name="email" value="" /> <input type="submit" value="Submit" /></div>';
		echo '</form>';
		break;		
	case 'find-username-from-email':
		if (($sEmail = $oReq->get('email')) && !empty($sEmail))
		{
			$aRow = $oDb->select('user_name')
				->from(Phpfox::getT('user'))
				->where('email = \'' . $oDb->escape($sEmail) . '\'')
				->execute('getRow');
			if (isset($aRow['user_name']))
			{
				echo '<div>Username: ' . $aRow['user_name'] . '</div>';
			}
			else 
			{
				echo '<div>Invalid!</div>';
			}
		}
		echo '<form method="post" action="phpfox.php?cmd=find-username-from-email">';
		echo '<div>Email: <input type="text" name="email" value="" /> <input type="submit" value="Submit" /></div>';
		echo '</form>';
		break;
	case 'verify-email':
		if (($sEmail = $oReq->get('email')) && !empty($sEmail))
		{
			$aRow = $oDb->select('user_id, user_name')
				->from(Phpfox::getT('user'))
				->where('email = \'' . $oDb->escape($sEmail) . '\'')
				->execute('getRow');
			if (isset($aRow['user_name']))
			{
				echo '<div>Done!</div>';
				$oDb->update(Phpfox::getT('user'), array('status_id' => '0'), 'user_id = ' . $aRow['user_id']);
			}
			else 
			{
				echo '<div>Invalid!</div>';
			}
		}
		echo '<form method="post" action="phpfox.php?cmd=verify-email">';
		echo '<div>Email: <input type="text" name="email" value="" /> <input type="submit" value="Submit" /></div>';
		echo '</form>';
		break;
	default:
		echo '<ul>';
		foreach ($aCommands as $sLink => $sPhrase)
		{
			echo '<li><a href="phpfox.php?cmd=' . $sLink . '">' . $sPhrase . '</a></li>';
		}
		echo '</ul>';
		break;
}

ob_end_flush();

?>