<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Used to import data from a archive like a ZIP or tar.gz. This class however
 * is not being used and is currently on hold to be removed or replaced by a new routine.
 * Thus no further documentation was created for this specific class.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: import.class.php 1666 2010-07-07 08:17:00Z Raymond_Benc $
 */
class Phpfox_Archive_Import
{
	private $_aArchives = array();
	
	private $_aSupported = array(
		'xml',
		'zip',
		'tar.gz'
	);
	
	private $_aTypes = array(
		'application/x-gzip' => 'tar.gz',
		'application/x-zip' => 'zip',
		'application/zip' => 'zip',
		'application/x-zip-compressed' => 'zip',
		'text/xml' => 'xml'
	);
	
	public function __construct()
	{	
	}
	
	public function set($aArchives)
	{		
        foreach ($aArchives as $sArchive)
		{
			if (!in_array($sArchive, $this->_aSupported))
			{
				continue;
			}
			
			if (!class_exists('ZipArchive') && ($sArchive == 'zip' || $sArchive == 'tar.gz') && strtolower(PHP_OS) != 'linux')
			{
				continue;
			}
			
			$this->_aArchives[] = $sArchive;
		}
		
		return $this;
	}
	
	public function getSupported()
	{
		$iTotal = count($this->_aArchives);
		$sSupported = '';
		
		$iCnt = 0;
		foreach ($this->_aArchives as $sArchive)
		{
			$iCnt++;
			$sSupported .= (count($this->_aArchives) > 1 ? ($iCnt == $iTotal ? ' ' . Phpfox::getPhrase('admincp.or') . ' ' : ', ') : '') . $sArchive;
		}
		
		return $sSupported;
	}	
	
	public function process($aFile)
	{
		return Phpfox_Error::set('Unable to import data using the current routine. Use the manual method of importing data.');
		
		if (!preg_match('/^(.*?)\.zip$/i', $aFile['name']))
		{
			return Phpfox_Error::set('Not a valid ZIP package.');
		}
		
		$sExt = 'zip';

		$sLocation = PHPFOX_DIR_CACHE  . md5(PHPFOX_TIME . uniqid() . $aFile['name']) . PHPFOX_DS;
		
		mkdir($sLocation);
		
		Phpfox::getLib('archive', $sExt)->extract($aFile['tmp_name'], $sLocation);
		
		$aFiles = Phpfox::getLib('file')->getAllFiles($sLocation);
		foreach ($aFiles as $sFile)
		{
			$sNewFile = str_replace($sLocation, '', $sFile);
			$aParts = explode(PHPFOX_DS, $sNewFile);
			unset($aParts[(count($aParts) - 1)]);
			$sDirPath = implode(PHPFOX_DS, $aParts);
			
			Phpfox::getLib('ftp')->mkdir(PHPFOX_DIR . $sDirPath, true);	
			Phpfox::getLib('ftp')->put($sFile, PHPFOX_DIR . $sNewFile);			
		}
		
		Phpfox::getLib('file')->delete_directory($sLocation);		
		
		return true;		
	}
}

?>
