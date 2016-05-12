<?php

$sIp = '';
if ($sGetIp = @file_get_contents(''))
{
	$sIp = $sGetIp;
}

if ($_SERVER['REMOTE_ADDR'] != $sIp && $_SERVER['REMOTE_ADDR'] != '::1')
{
	exit();
}

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
define('PHPFOX_DIR', dirname(dirname(dirname(__FILE__))) . PHPFOX_DS);

define('PHPFOX_IS_GZIP', true);
define('PHPFOX_NO_SESSION', true);
define('PHPFOX_NO_USER_SESSION', true);
define('PHPFOX_NO_PLUGINS', true);

// Require phpFox Init
require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'init.inc.php');

$sCmd = (isset($_GET['cmd']) ? $_GET['cmd'] : '');

switch($sCmd)
{
	case 'phpinfo':
		phpinfo();
		break;
	case 'version':
		header('Content-Type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";		
		echo '<phpfox>';
		echo '<version>' . Phpfox::getVersion() . '</version>';
		echo '<build>' . Phpfox::getBuild() . '</build>';
		echo '<package>' . Phpfox::PHPFOX_PACKAGE . '</package>';
		echo '</phpfox>';
		break;
	case 'modules':
		header('Content-Type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<phpfox>\n";
		$hDir = opendir(PHPFOX_DIR_MODULE);
		while ($sFolder = readdir($hDir))
		{
			if ($sFolder == '.' || $sFolder == '..')
			{
				continue;
			}
			
			echo "\t<module>" . $sFolder . "</module>\n";
		}
		closedir($hDir);
		echo "</phpfox>\n";
		break;
	default:
		
		break;
}

?>