<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		PhpFox
 * @version 		$Id: phpfox.php 1298 2009-12-05 16:19:23Z Raymond_Benc $
 * 
 * Requires: mysqladmin, mysqldump
 * Must be executed via command line as a root user. MySQL user must have the rights to create a database.
 * 
 */

if (PHP_SAPI != 'cli')
{
	exit("Run via command line as root user.\n");
}

error_reporting(E_ALL);

@set_time_limit(0);

define('COLOR_RED', "\033[01;31m");
define('COLOR_GREEN', "\033[01;32m");
define('COLOR_NORMAL', "\033[00m");
define('PHPFOX', true);

$_SERVER['REMOTE_ADDR'] = '';
$_SERVER['HTTP_HOST'] = '';

if (trim(cmd('whoami')) != 'root')
{
	exit("You have to be root.\n");
}

$sOriginalPath = get('What is the full path to your phpFox site?');
$sOriginalPath = trim($sOriginalPath);
$sOriginalPath = rtrim($sOriginalPath, '/');
$sOriginalPath = $sOriginalPath . '/';

if (!file_exists($sOriginalPath . 'include/setting/server.sett.php'))
{
	exit("phpFox cannot be found here.\n");
}

$sFinalPath = get('Where would you like it cloned?');
$sFinalPath = trim($sFinalPath);
$sFinalPath = rtrim($sFinalPath, '/');
$sFinalPath = $sFinalPath . '/';

chdir($sOriginalPath);

p('Cloning source files...');

cmd('cp -r -p . ' . $sFinalPath);

p('Done!');

$sDatabase = get('Provide a name for your cloned MySQL database?');
$sDatabase = trim($sDatabase);

$sUsername = get('MySQL Username: ');
$sUsername = trim($sUsername);

$sPassword = getSilent('MySQL Password: ');
$sPassword = trim($sPassword);

chdir($sFinalPath);

require_once($sFinalPath . 'include/setting/server.sett.php');

p('Creating database...');

cmd("mysqladmin create {$sDatabase} -u {$sUsername} --password=\"{$sPassword}\" && mysqldump -u {$sUsername} --password=\"{$sPassword}\" {$_CONF['db']['name']} | mysql -u {$sUsername} --password=\"{$sPassword}\" -h localhost {$sDatabase}");

p('Done!');

$sFullPath = get('What is the full URL to your cloned site?');
$sFullPath = trim($sFullPath);
$aParsedUrl = parse_url($sFullPath);

p('Updating server setting file...');

// Get the settings content
$sContent = file_get_contents($sFinalPath . 'include/setting/server.sett.php');			

$sRewrite = '2';
if (file_exists('./.htaccess'))
{
	$sHtaccess = file_get_contents('./.htaccess');
	if (preg_match('/index\.php/i', $sHtaccess))
	{
		$sHtaccess = preg_replace('/RewriteBase (.*)/i', 'RewriteBase ' . $aParsedUrl['path'], $sHtaccess);
	}
	
	if (($hHtaccess = @fopen($sFinalPath . '.htaccess', 'w')))
	{
		$sRewrite = '1';
		fwrite($hHtaccess, $sHtaccess);
		fclose($hHtaccess);
	}	
}
		
$aFind = array(
	"/\\\$_CONF\['db'\]\['name'\] = (.*?);/i",
	"/\\\$_CONF\['core.host'\] = (.*?);/i",
	"/\\\$_CONF\['core.folder'\] = (.*?);/i",
	"/\\\$_CONF\['core.url_rewrite'\] = (.*?);/i",
);
		
$aReplace = array(
	"\\\$_CONF['db']['name'] = '{$sDatabase}';",
	"\\\$_CONF['core.host'] = '{$aParsedUrl['host']}';",
	"\\\$_CONF['core.folder'] = '{$aParsedUrl['path']}';",
	"\\\$_CONF['core.url_rewrite'] = '{$sRewrite}';"
);
		
$sContent = preg_replace($aFind, $aReplace, $sContent);

if ($hServerConf = @fopen($sFinalPath . 'include/setting/server.sett.php', 'w'))
{
	fwrite($hServerConf, $sContent);
	fclose($hServerConf);
}

p('Done!');

p('Removing cache folder...');
cmd('rm -rf ' . $sFinalPath . 'file/cache/');
cmd('mkdir ' . $sFinalPath . 'file/cache/');
cmd('chmod 0777 ' . $sFinalPath . 'file/cache/');
p('Done!');

function cmd($sCmd)
{
	return shell_exec($sCmd);
}

function p($mStr)
{
	if (!is_array($mStr))
	{
		$mStr = array($mStr);
	}
		
	foreach ($mStr as $sStr)
	{
		$bAddLineBreak = (((substr($sStr, -3) != '...') ? true : false));
			
		if ($sStr == 'Done!')
		{
			echo (PHP_SAPI == 'cli' ? COLOR_GREEN : '<span style="color:green;">');
		}
		echo (!$bAddLineBreak ? " -> " : ' '). $sStr . ($bAddLineBreak ? "\n" : '');
		if ($sStr == 'Done!')
		{
			echo (PHP_SAPI == 'cli' ? COLOR_NORMAL : '</span>');
		}
	}
}

function get($sStr)
{
	fwrite(STDOUT, COLOR_GREEN . $sStr . COLOR_NORMAL . ' ');
		
	return trim(fgets(STDIN));
}

function getSilent($sStr) 
{
    $sCmd = "/usr/bin/env bash -c 'echo OK'";
    if (rtrim(shell_exec($sCmd)) !== 'OK') 
    {
    	exit("Can't invoke bash.\n");      	
    }
    $sCmd = "/usr/bin/env bash -c 'read -s -p \""
      . COLOR_GREEN . addslashes($sStr) . COLOR_NORMAL
      . "\" mypassword && echo \$mypassword'";
    $sPassword = rtrim(shell_exec($sCmd));
    echo "\n";
    return $sPassword;  
}

?>