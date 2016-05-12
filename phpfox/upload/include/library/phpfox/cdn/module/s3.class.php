<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class to work with Amazons S3 CDN
 * 
 * @link http://aws.amazon.com/s3/
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: s3.class.php 2651 2011-06-02 15:09:34Z Raymond_Benc $
 */
class Phpfox_Cdn_Module_S3 extends Phpfox_Cdn_Abstract
{
	/**
	 * Object for the amazon s3 class
	 *
	 * @var object
	 */
	private $_oObject = null;
	
	/**
	 * Name of the bucket we plan to store all the uploaded data
	 *
	 * @var string
	 */
	private $_sBucket = null;
	
	/**
	 * Bool value if the bucket has already been created or not.
	 *
	 * @var bool
	 */
	private $_bHasBucket = false;
	
	/**
	 * Bool value if we were able to upload the file or not.
	 *
	 * @var false
	 */
	private $_bIsUploaded = false;
	
	/**
	 * Loads the amazons3 library developed by another group.
	 *
	 */
	public function __construct()
	{
		require_once(PHPFOX_DIR_LIB . 'amazons3/S3.php');
		
		$this->_oObject = new S3(Phpfox::getParam('core.amazon_access_key'), Phpfox::getParam('core.amazon_secret_key'));
		$this->_sBucket = Phpfox::getParam('core.amazon_bucket');		
	}
	
	/**
	 * Uploads the file to amazons server.
	 *
	 * @param string $sFile Full path to where the file is located.
	 * @param string $sName Optional name of the file once it is uploaded. By default we just use the original file name.
	 * @return bool We only return a bool false if we were not able to upload the item.
	 */
	public function put($sFile, $sName = null)
	{
		if (!Phpfox::getParam('core.amazon_bucket_created'))
		{
			$sBucketName = Phpfox::getParam('core.amazon_bucket');
			if (empty($sBucketName))
			{
				$this->_sBucket = md5(Phpfox::getParam('core.path'));
			}
			
			if (!$this->_oObject->putBucket($this->_sBucket, S3::ACL_PUBLIC_READ))
			{
				return Phpfox_Error::set('Unable to create Amazon bucket: ' . $sBucketName);	
			}
			
			Phpfox::getLib('database')->update(Phpfox::getT('setting'), array(
					'value_actual' => '1'
				), 'var_name = \'amazon_bucket_created\''
			);
			
			Phpfox::getLib('database')->update(Phpfox::getT('setting'), array(
					'value_actual' => '' . $this->_sBucket . ''
				), 'var_name = \'amazon_bucket\''
			);			
			
			Phpfox::getLib('cache')->remove();
		}
		
		if (empty($sName))
		{
			$sName = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $sFile));
		}

		if ($this->_oObject->putObjectFile($sFile, $this->_sBucket, $sName, S3::ACL_PUBLIC_READ))
		{
			$this->_bIsUploaded = true;
	
			return true;
		}	
		
		return false;
	}
	
	public function remove($sFile)
	{
		$sName = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $sFile));
		
		$this->_oObject->deleteObject($this->_sBucket, $sName);
	}	
	
	/**
	 * Returns a server ID. We always run "1" (int) if the file was uploaded to the amazon server, if not return "0" (int) so we display the local file instead.
	 *
	 * @return string
	 */
	public function getServerId()
	{
		return '1';
		// return ($this->_bIsUploaded ? '1' : '0');
	}
	
	/**
	 * Get the full URL to the amazon bucket with support to load a CDN CNAME URL.
	 *
	 * @param string $sPath Path to the fule we are going to display.
	 * @return string Full path to the file on amazons server.
	 */
	public function getUrl($sPath)
	{
		$sUrl = Phpfox::getParam('core.cdn_cname');
		if (!empty($sUrl))
		{
			$sUrl = rtrim($sUrl, '/') . '/';
		}
	
		if ($this->_oObject->getObjectInfo(Phpfox::getParam('core.amazon_bucket'), str_replace(Phpfox::getParam('core.path'), '', $sPath)) === false)
		{
			return $sPath;			
		}

		return str_replace(Phpfox::getParam('core.path'), (empty($sUrl) ? ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' && Phpfox::getParam('core.cdn_amazon_https')) ? 'https://' : 'http://') . Phpfox::getParam('core.amazon_bucket') . '.s3.amazonaws.com/' : $sUrl), $sPath);
	}
}

?>