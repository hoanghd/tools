<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * phpFox Engine
 * All interactions with anything phpFox related is executed via this class.
 * It is the engine that runs phpFox and all of the other libraries and modules.
 * All methods, variables and constants are static.
 * 
 * All libraries are located within the folder: include/library/phpfox/
 * Example of connect to request library:
 * <code>
 * $oObject = Phpfox::getLib('request');
 * </code>
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: phpfox.class.php 3317 2011-10-19 12:35:46Z Miguel_Espinoza $
 */
final class Phpfox
{	
	/**
 	* Product Version : major.minor.maintenance [alphaX, betaX or rcX]
 	*/
	const VERSION = '3.0.0beta5';
	
	/**
	 * Product Code Name
	 * 
	 */	
	const CODE_NAME = 'Nebula';
	
	/**
	 * Browser agent used with API curl requests.
	 *
	 */
	const BROWSER_AGENT = 'phpFox';
	
	/**
	 * Product build number.
	 *
	 */
	const PRODUCT_BUILD = '1';
	
	/**
	 * phpFox API server.
	 *
	 */
	const PHPFOX_API = '';
	
	/**
	 * phpFox package ID.
	 *
	 */
	const PHPFOX_PACKAGE = '3';
	
	/**
	 * ARRAY of objects initiated. Used to keep a static history
	 * so we don't call the same class more then once.
	 *
	 * @var unknown_type
	 */
	private static $_aObject = array();
	
	/**
	 * ARRAY of libraries being loaded.
	 *
	 * @var array
	 */
	private static $_aLibs = array();
	
	/**
	 * ARRAY of custom pages the site admins have created.
	 *
	 * @var array
	 */
	private static $_aPages = array();
	
	/**
	 * Used to keep a static variable to see if we are within the AdminCP.
	 *
	 * @var bool
	 */
	private static $_bIsAdminCp = false;	
	
	/**
	 * History of any logs we save for debug purposes.
	 *
	 * @var array
	 */
	private static $_aLogs = array();	
	
	/**
	 * Get the current phpFox version.
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		if (defined('PHPFOX_INSTALLER'))
		{
			return self::VERSION;
		}
		
		return Phpfox::getParam('core.phpfox_version');
	}
	
	/**
	 * Get the current phpFox version ID.
	 *
	 * @return int
	 */
	public static function getId()
	{
		return self::getVersion();
	}
	
	/**
	 * Get the products code name.
	 *
	 * @return string
	 */
	public static function getCodeName()
	{
		return self::CODE_NAME;
	}
	
	/**
	 * Get the products build number.
	 *
	 * @return int
	 */
	public static function getBuild()
	{
		return self::PRODUCT_BUILD;
	}
	
	/**
	 * Get the clean numerical value of the phpFox version.
	 *
	 * @return int
	 */
	public static function getCleanVersion()
	{
		return str_replace('.', '', self::VERSION);
	}
	
	/**
	 * Check if a feature can be used based on the package the client
	 * has installed.
	 * 
	 * Example (STRING):
	 * <code>
	 * if (Phpfox::isPackage('1') { }
	 * </code>
	 * 
	 * Example (ARRAY):
	 * <code>
	 * if (Phpfox::isPackage(array('1', '2')) { }
	 * </code>
	 *
	 * @param mixed $mPackage STRING can be used to pass the package ID, or an ARRAY to pass multipl packages.
	 * @return unknown
	 */
	public static function isPackage($mPackage)
	{
		if (self::PHPFOX_PACKAGE == '[PHPFOX_PACKAGE_NAME]')
		{
			return false;
		}
		
		if (!is_array($mPackage))
		{
			$mPackage = array($mPackage);
		}
		
		return (in_array(self::PHPFOX_PACKAGE, $mPackage) ? true : false);
	}
	
	/**
	 * Provide "powered by" link.
	 *
	 * @param bool $bLink TRUE to include a link to phpFox.
	 * @param bool $bVersion TRUE to include the version being used.
	 * @return string Powered by phpFox string returned.
	 */
	public static function link($bLink = true, $bVersion = true)
	{
		return 'Powered By ' . ($bLink ? '' : '') . 'phpFox' . ($bLink ? '' : '') . ($bVersion ? ' Version ' . PhpFox::getVersion() : '');
	}
	
	/**
	 * Gets and creates an object for a class.
	 *
	 * @param string $sClass Class name.
	 * @param array $aParams Params to pass to the class.
	 * @return object Object created will be returned.
	 */
	public static function &getObject($sClass, $aParams = array())
	{		
		$sHash = md5($sClass . serialize($aParams));
		
		if (isset(self::$_aObject[$sHash]))
		{
			return self::$_aObject[$sHash];
		}	

		(PHPFOX_DEBUG ? Phpfox_Debug::start('object') : false);
		
		$sClass = str_replace(array('.', '-'), '_', $sClass);		
		
		if (!class_exists($sClass))
		{
			Phpfox_Error::trigger('Unable to call class: ' . $sClass, E_USER_ERROR);
		}		

		if ($aParams)
		{
			self::$_aObject[$sHash] = new $sClass($aParams);
		}
		else 
		{		
			self::$_aObject[$sHash] = new $sClass();
		}

		(PHPFOX_DEBUG ? Phpfox_Debug::end('object', array('name' => $sClass)) : false);
		
		if (method_exists(self::$_aObject[$sHash], 'getInstance'))
		{
			return self::$_aObject[$sHash]->getInstance();
		}				
		
		return self::$_aObject[$sHash];
	}
	
	/**
	 * @see Phpfox_Setting::getParam()
	 * @param string $sVar
	 * @return mixed
	 */
	public static function getParam($sVar)
	{
		return Phpfox::getLib('setting')->getParam($sVar);
	}
	
	/**
	 * Fine and load a library class and make sure it exists.
	 *
	 * @param string $sClass Library class name.
	 * @return bool TRUE if library has loaded, FALSE if not.
	 */
	public static function getLibClass($sClass)
	{
		if (isset(self::$_aLibs[$sClass]))
		{
			return true;
		}

		self::$_aLibs[$sClass] = md5($sClass);		
		
		$sClass = str_replace('.', PHPFOX_DS, $sClass);
		$sFile = PHPFOX_DIR_LIB . $sClass . '.class.php';
		
		if (file_exists($sFile))
		{			
			require($sFile);
			return true;
		}		
		
		$aParts = explode(PHPFOX_DS, $sClass);		
		if (isset($aParts[1]))
		{
			$sSubClassFile = PHPFOX_DIR_LIB . $sClass . PHPFOX_DS . $aParts[1] . '.class.php';			
			if (file_exists($sSubClassFile))
			{
				require($sSubClassFile);
				return true;
			}
		}		
	
		Phpfox_Error::trigger('Unable to load class: ' . $sClass, E_USER_ERROR);
		
		return false;
	}
	
	/**
	 * Get a phpFox library. This includes the class file and creates the object for you.
	 * 
	 * Example usage:
	 * <code>
	 * Phpfox::getLib('url')->makeUrl('test');
	 * </code>
	 * In the example we called the URL library found in the folder: include/library/phpfox/url/url.class.php
	 * then created an object for it so we could directly call the method "makeUrl".
	 *
	 * @param string $sClass Library class name.
	 * @param array $aParams ARRAY of params you can pass to the library.
	 * @return object Object of the library class is returned.
	 */
	public static function &getLib($sClass, $aParams = array())
	{		
		if ((substr($sClass, 0, 7) != 'phpfox.') || ($sClass == 'phpfox.api' || $sClass == 'phpfox.process'))
		{
			$sClass = 'phpfox.' . $sClass;
		}
		
		$sHash = md5($sClass . serialize($aParams));
		
		if (isset(self::$_aObject[$sHash]))
		{	
			return self::$_aObject[$sHash];
		}		
		
		Phpfox::getLibClass($sClass);		

		$sClass = str_replace('phpfox.phpfox.', 'phpfox.', $sClass);		

		self::$_aObject[$sHash] = Phpfox::getObject($sClass, $aParams);
		
		return self::$_aObject[$sHash];
	}
	
	/**
	 * @see Phpfox_Module::isModule()
	 * @param string $sModule
	 * @return bool
	 */
	public static function isModule($sModule)
	{
		return Phpfox::getLib('module')->isModule($sModule);
	}
	
	/**
	 * @see Phpfox_Module::getComponent()
	 * @param string $sClass
	 * @param array $aParams
	 * @return object
	 */
	public static function getBlock($sClass, $aParams = array(), $bTemplateParams = false)
	{		
		return Phpfox::getLib('module')->getComponent($sClass, $aParams, 'block', $bTemplateParams);
	}	
	
	/**
	 * @see Phpfox_Module::callback()
	 * @param string $sCall
	 * @return mixed
	 */
	public static function callback($sCall)
	{
		if (func_num_args() > 1)
		{
			$aParams = func_get_args();			
			
			return Phpfox::getLib('module')->callback($sCall, $aParams);
		}
		
		return Phpfox::getLib('module')->callback($sCall);
	}
	
	/**
	 * @see Phpfox_Module::massCallback()
	 * @param string $sMethod
	 * @return mixed
	 */
	public static function massCallback($sMethod)
	{
		if (func_num_args() > 1)
		{
			$aParams = func_get_args();			
			
			return Phpfox::getLib('module')->massCallback($sMethod, $aParams);
		}
		
		return Phpfox::getLib('module')->massCallback($sMethod);		
	}
	
	/**
	 * @see Phpfox_Module::hasCallback()
	 * @param string $sModule
	 * @param string $sMethod
	 * @return bool
	 */
	public static function hasCallback($sModule, $sMethod)
	{
		return Phpfox::getLib('module')->hasCallback($sModule, $sMethod);
	}
	
	/**
	 * @see Phpfox_Module::getComponent()
	 * @param string $sClass Class name.
	 * @param array $aParams ARRAY of params you can pass to the component.
	 * @param string $sType Type of component (block or controller).
	 * @return object We return the object of the component class.
	 */
	public static function getComponent($sClass, $aParams = array(), $sType = 'block', $bTemplateParams = false)
	{		
		return Phpfox::getLib('module')->getComponent($sClass, $aParams, $sType, $bTemplateParams);
	}
	
	/**
	 * @see Phpfox_Module::getComponentSetting()
	 * @param int $iUserId
	 * @param string $sVarName
	 * @param mixed $mDefaultValue
	 * @return mixed
	 */
	public static function getComponentSetting($iUserId, $sVarName, $mDefaultValue)
	{
		return Phpfox::getLib('module')->getComponentSetting($iUserId, $sVarName, $mDefaultValue);
	}
	
	/**
	 * Returns the token name for forms
	 */
	public static function getTokenName()
	{
		return 'core';	
	}
	
	/**
	 * @see Phpfox_Module::getService()
	 * @param string $sClass
	 * @param array $aParams
	 * @return object
	 */
	public static function getService($sClass, $aParams = array())
	{
		return Phpfox::getLib('module')->getService($sClass, $aParams);
	}
	
	/**
	 * Builds a database table prefix.
	 *
	 * @param string $sTable Database table name.
	 * @return string Returns the table name with the clients prefix.
	 */
	public static function getT($sTable)
	{
        return Phpfox::getParam(array('db', 'prefix')) . $sTable;
	}
	
	/**
	 * @see User_Service_Auth::getUserId()
	 * @return int
	 */
	public static function getUserId()
	{
		if (isset($_REQUEST['custom_pages_post_as_page']) && (int) $_REQUEST['custom_pages_post_as_page'] > 0)
		{
			$aPage = Phpfox::getLib('database')->getRow('
				SELECT p.page_id, p.user_id AS owner_user_id, u.user_id
				FROM ' . Phpfox::getT('pages') . ' AS p
				JOIN ' . Phpfox::getT('user') . ' AS u ON(u.profile_page_id = p.page_id)
				WHERE p.page_id = ' . (int) $_REQUEST['custom_pages_post_as_page'] . '
			');
			
			$iActualUserId = Phpfox::getService('user.auth')->getUserId();
			
			if (isset($aPage['page_id']))
			{
				$bPass = false;
				if ($aPage['owner_user_id'] == $iActualUserId)
				{
					$bPass = true;
				}
				
				if (!$bPass)
				{
					$aAdmin = Phpfox::getLib('database')->getRow('
						SELECT page_id
						FROM ' . Phpfox::getT('pages_admin') . '
						WHERE page_id = ' . (int) $aPage['page_id'] . ' AND user_id = ' . (int) $iActualUserId . '
					');
					
					if (isset($aAdmin['page_id']))
					{
						$bPass = true;
					}
				}
				
				if ($bPass)
				{
					return $aPage['user_id'];
				}
			}
		}
		
		if (defined('PHPFOX_APP_USER_ID'))
		{			
			return PHPFOX_APP_USER_ID;
		}
		
		return Phpfox::getService('user.auth')->getUserId();
	}
	
	/**
	 * @see User_Service_Auth::getUserBy()
	 * @return string
	 */	
	public static function getUserBy($sVar = null)
	{		
		return Phpfox::getService('user.auth')->getUserBy($sVar);
	}
	
	/**
	 * @see Phpfox_Request::isMobile()
	 * @return bool
	 */
	public static function isMobile()
	{
		return Phpfox::getLib('request')->isMobile();
	}
	
	/**
	 * @see Phpfox_Request::getIp()
	 * @return string
	 */
	public static function getIp($bReturnNum = false)
	{
		return Phpfox::getLib('request')->getIp($bReturnNum);
	}
	
	/**
	 * Checks to see if the user that is logged in has been marked as a spammer.
	 *
	 * @return bool TRUE is a spammer, FALSE if not a spammer.
	 */
	public static function isSpammer()
	{
		if (Phpfox::getUserParam('core.is_spam_free'))
		{
			return false;
		}
		
		if (!Phpfox::getParam('core.enable_spam_check'))
		{
			return false;
		}
		
		if (Phpfox::isUser() && Phpfox::getUserBy('total_spam') > Phpfox::getParam('core.auto_deny_items'))
		{			
			return true;
		}

		return false;	
	}
	
	/**
	 * Get all the user fields when joining with the user database table.
	 *
	 * @param string $sAlias Table alias. User table alias by default is "u".
	 * @param string $sPrefix Prefix for each of the fields.
	 * @return string Returns SQL SELECT for user fields.
	 */
	public static function getUserField($sAlias = 'u', $sPrefix = '')
	{
		static $aValues = array();
		
		// Create hash
		$sHash = md5($sAlias . $sPrefix);
		
		// Have we already cached it? We do not want to run an extra foreach() for nothing.
		if (isset($aValues[$sHash]))
		{
			return $aValues[$sHash];
		}
		
		$aFields = Phpfox::getService('user')->getUserFields();
		
		$aValues[$sHash] = '';
		foreach ($aFields as $sField)
		{
			$aValues[$sHash] .= ", {$sAlias}.{$sField}";	
			
			if ($sAlias == 'u' && $sField == 'server_id')
			{
				// $sPrefix = 'user_' . (empty($sPrefix) ? '' : $sPrefix);
				
				$aValues[$sHash] .= " AS user_{$sPrefix}{$sField}";	
				
				// unset($sPrefix);
				
				continue;
			}
			
			if (!empty($sPrefix))
			{
				$aValues[$sHash] .= " AS {$sPrefix}{$sField}";	
			}
		}
		$aValues[$sHash] = ltrim($aValues[$sHash], ',');
		
		return $aValues[$sHash];
	}
	
	/**
	 * @see Phpfox_Date::getTimeZone()
	 * @param bool $bDst
	 * @return string
	 */
	public static function getTimeZone($bDst = true)
	{		
		return Phpfox::getLib('date')->getTimeZone($bDst);	
	}
	
	/**
	 * Gets a time stamp, Works similar to PHP date() function.
	 * We also take into account locale and time zone settings.
	 *
	 * @see date()
	 * @param string $sStamp Time stamp format.
	 * @param int $iTime UNIX epoch time stamp.
	 * @return string Time stamp value based on locale.
	 */
	public static function getTime($sStamp = null, $iTime = PHPFOX_TIME)
	{
		static $sUserOffSet;
		
		if (!$sUserOffSet)
		{
			$sUserOffSet = Phpfox::getTimeZone();
		}
		if (!preg_match('/z[0-9]+/i', $sUserOffSet, $aMatch))
		{
			// try to find it in the cache
			$aTZ = Phpfox::getService('core')->getTimeZones();
			$sTz = array_search($sUserOffSet, $aTZ);
			if ($sTz !== false)
			{
				$sUserOffSet = $sTz;
			}
		}
		if (substr($sUserOffSet,0,1) == 'z' && PHPFOX_USE_DATE_TIME)
		{
			// we are using DateTime
			// get the offset to use based on the time zone index code
			if (!isset($aTZ))
			{
				$aTZ = Phpfox::getService('core')->getTimeZones();
			}
			if (isset($aTZ[$sUserOffSet]))
			{
				$oTZ = new DateTimeZone($aTZ[$sUserOffSet]);
				$oDateTime = new DateTime(null, $oTZ);
				$oDateTime->setTimestamp($iTime);
				$sUserOffSet = $aTZ[$sUserOffSet];
				if ($sStamp !== null)
				{
					$iNewTime = $oDateTime->format($sStamp);
					$bSet = true;
				}
			}
		}	

		if ($sStamp === null)
		{
			return  (!empty($sUserOffSet) ? (substr($sUserOffSet, 0, 1) == '-' ? ($iTime - (substr($sUserOffSet, 1) * 3600)) : ($sUserOffSet * 3600) + $iTime) : $iTime);
		}
		else if (!isset($bSet))
		{
			$iNewTime = (!empty($sUserOffSet) ? date($sStamp, (substr($sUserOffSet, 0, 1) == '-' ? ($iTime - (substr($sUserOffSet, 1) * 3600)) : ($sUserOffSet * 3600) + $iTime)) : date($sStamp, $iTime));
			
		}
		$aFind = array(
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
			'Sunday',
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		);
		
		$aReplace = array(			
			Phpfox::getPhrase('core.monday'),
			Phpfox::getPhrase('core.tuesday'),
			Phpfox::getPhrase('core.wednesday'),
			Phpfox::getPhrase('core.thursday'),
			Phpfox::getPhrase('core.friday'),
			Phpfox::getPhrase('core.saturday'),
			Phpfox::getPhrase('core.sunday'),
			Phpfox::getPhrase('core.january'),
			Phpfox::getPhrase('core.february'),
			Phpfox::getPhrase('core.march'),
			Phpfox::getPhrase('core.april'),
			Phpfox::getPhrase('core.may'),
			Phpfox::getPhrase('core.june'),
			Phpfox::getPhrase('core.july'),
			Phpfox::getPhrase('core.august'),
			Phpfox::getPhrase('core.september'),
			Phpfox::getPhrase('core.october'),
			Phpfox::getPhrase('core.november'),
			Phpfox::getPhrase('core.december')
		);		
		
		$iNewTime = str_replace('Mon', 'Monday', $iNewTime);
		$iNewTime = str_replace('Tue', 'Tuesday', $iNewTime);
		$iNewTime = str_replace('Wed', 'Wednesday', $iNewTime);
		$iNewTime = str_replace('Thu', 'Thursday', $iNewTime);
		$iNewTime = str_replace('Fri', 'Friday', $iNewTime);
		$iNewTime = str_replace('Sat', 'Saturday', $iNewTime);
		$iNewTime = str_replace('Sun', 'Sunday', $iNewTime);
		$iNewTime = str_replace('Jan', 'January',$iNewTime);
		$iNewTime = str_replace('Feb', 'February',$iNewTime);
		$iNewTime = str_replace('Mar', 'March',$iNewTime);
		$iNewTime = str_replace('Apr', 'April',$iNewTime);
		$iNewTime = str_replace('May', 'May',$iNewTime);
		$iNewTime = str_replace('Jun', 'June',$iNewTime);
		$iNewTime = str_replace('Jul', 'July',$iNewTime);
		$iNewTime = str_replace('Aug', 'August',$iNewTime);
		$iNewTime = str_replace('Sep', 'September',$iNewTime);
		$iNewTime = str_replace('Oct', 'October',$iNewTime);
		$iNewTime = str_replace('Nov', 'November',$iNewTime);
		$iNewTime = str_replace('Dec', 'December',$iNewTime);
		
		$iNewTime = str_replace('Mondayday', 'Monday', $iNewTime);
		$iNewTime = str_replace('Tuesdaysday', 'Tuesday', $iNewTime);		
		$iNewTime = str_replace('Wednesdaynesday', 'Wednesday', $iNewTime);
		$iNewTime = str_replace('Thursdayrsday', 'Thursday', $iNewTime);
		$iNewTime = str_replace('Fridayday', 'Friday', $iNewTime);
		$iNewTime = str_replace('Saturdayurday', 'Saturday', $iNewTime);
		$iNewTime = str_replace('Sundayday', 'Sunday', $iNewTime);
		$iNewTime = str_replace('Januaryuary', 'January',$iNewTime);
		$iNewTime = str_replace('Februaryruary', 'February',$iNewTime);
		$iNewTime = str_replace('Marchch', 'March',$iNewTime);
		$iNewTime = str_replace('Aprilil', 'April',$iNewTime);
		$iNewTime = str_replace('Junee', 'June',$iNewTime);
		$iNewTime = str_replace('Julyy', 'July',$iNewTime);
		$iNewTime = str_replace('Augustust', 'August',$iNewTime);
		$iNewTime = str_replace('Septembertember', 'September',$iNewTime);
		$iNewTime = str_replace('Octoberober', 'October',$iNewTime);
		$iNewTime = str_replace('Novemberember', 'November',$iNewTime);
		$iNewTime = str_replace('Decemberember', 'December',$iNewTime);		
		
		$iNewTime = str_replace($aFind, $aReplace, $iNewTime);
		$iNewTime = str_replace('PM', Phpfox::getPhrase('core.pm'), $iNewTime);
		$iNewTime = str_replace('AM', Phpfox::getPhrase('core.am'), $iNewTime);
		
		return $iNewTime;
	}
	
	/**
	 * Used to see if a user is logged in or not. By passing the first argument as TRUE
	 * we can also do an auto redirect to guide the user to login first before using a 
	 * feature.
	 *
	 * @param bool $bRedirect User will be redirected to the login page if they are not logged int.
	 * @return bool If the 1st argument is FALSE, it will return a BOOL TRUE if the user is logged in, otherwise FALSE.
	 */
	public static function isUser($bRedirect = false)
	{
		if (defined('PHPFOX_APP_USER_ID'))
		{
			return true;
		}
		$bIsUser =  Phpfox::getService('user.auth')->isUser();
		
		if ($bRedirect && !$bIsUser)
		{
			if (PHPFOX_IS_AJAX || PHPFOX_IS_AJAX_PAGE)
			{
				return Phpfox::getLib('ajax')->isUser();
			}
			else 
			{
				// Create a session so we know where we plan to redirect the user after they login
				Phpfox::getLib('session')->set('redirect', Phpfox::getLib('url')->getFullUrl(true));
				
				Phpfox::getLib('url')->send('user.login');
			}
		}
		
		return $bIsUser;
	}
	
	/**
	 * Used to see if a user is an Admin. By passing the first argument as TRUE
	 * we can also do an auto redirect to guide the user to login first before using a 
	 * feature in the AdminCP.
	 *
	 * @param bool $bRedirect User will be redirected to the AdminCP login page if they are not logged int.
	 * @return bool If the 1st argument is FALSE, it will return a BOOL TRUE if the user is logged in, otherwise FALSE.
	 */	
	public static function isAdmin($bRedirect = false)
	{	
		if (!Phpfox::isUser($bRedirect))
		{
			return false;
		}
		
		if (!Phpfox::getUserParam('admincp.has_admin_access', $bRedirect))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Creates a URL for an item that is connected with a users profile.
	 *
	 * @deprecated 2.0.0beta1
	 * @param string $sUrl URL
	 * @param array $mParams URL params.
	 * @param string $sUserName Users vanity user name.
	 * @return string Items URL now connected with a persons profile.
	 */
	public static function itemUrl($sUrl, $mParams, $sUserName)
	{
		$bUserProfileUrl = true;	
		
		if ($bUserProfileUrl)
		{
			return self::getLib('phpfox.url')->makeUrl($sUserName, array_merge(array($sUrl), (is_array($mParams) ? $mParams : array($mParams))));
		}
		
		return self::getLib('phpfox.url')->makeUrl($sUrl, $mParams);
	}
	
	/**
	 * @see User_Service_Group_Setting_Setting::getGroupParam()
	 * @param int $iGroupId
	 * @param string $sName
	 * @return mixed
	 */
	public static function getUserGroupParam($iGroupId, $sName)
	{
		return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sName);
	}
	
	/**
	 * Get a user group setting.
	 *
	 * @see User_Service_Group_Setting_Setting::getParam()
	 * @param string $sName User group param name.
	 * @param bool $bRedirect TRUE will redirect the user to a subscribtion page if they do not have access to the param.
	 * @param mixed $sJsCall NULL will do nothing, however a STRING JavaScript code will run the code instead of a redirection.
	 * @return unknown
	 */
	public static function getUserParam($sName, $bRedirect = false, $sJsCall = null)
	{		
		if (defined('PHPFOX_INSTALLER'))
		{
			return true;
		}
		
		// Is this an array
		if (is_array($sName))
		{
			// Get the array key
			$sKey = array_keys($sName);
			
			// Get the setting value
			$sValue = Phpfox::getService('user.group.setting')->getParam($sKey[0]);

			// Do the evil eval to get our new value
			eval('$bPass = (' . $sValue . ' ' . $sName[$sKey[0]][0] . ' ' . $sName[$sKey[0]][1] . ');');	
		}
		else 
		{
			$bPass = (Phpfox::getService('user.group.setting')->getParam($sName) ? true : false);
		}
		
		if ($bRedirect)
		{
			if (PHPFOX_IS_AJAX && !$bPass)
			{
				if (!Phpfox::isUser())
				{
					// Are we using thickbox?
					if (Phpfox::getLib('request')->get('tb'))
					{
						Phpfox::getBlock('user.login-ajax');	
					}
					else 
					{				
						// If we passed an AJAX call we execute it
						if ($sJsCall !== null)
						{
							echo $sJsCall;
						}
						echo "tb_show('" . Phpfox::getPhrase('user.login_title') . "', \$.ajaxBox('user.login', 'height=250&width=400'));";
					}				
				}
				else 
				{
					// Are we using thickbox?
					if (Phpfox::getLib('request')->get('tb'))
					{
						Phpfox::getBlock('subscribe.message');
					}
					else 
					{
						// If we passed an AJAX call we execute it
						if ($sJsCall !== null)
						{
							echo $sJsCall;
						}						
						echo "tb_show('" . Phpfox::getPhrase('subscribe.membership_info') . "', \$.ajaxBox('subscribe.message', 'height=150&width=450'));";
					}
				}
				exit;				
			}
			else 
			{
				if (!$bPass)
				{
					if (!Phpfox::isUser())
					{
						// Create a session so we know where we plan to redirect the user after they login
						Phpfox::getLib('session')->set('redirect', Phpfox::getLib('url')->getFullUrl(true));
			
						// Okay thats it lets send them away so they can login
						Phpfox::getLib('url')->send('user.login');
					}	
					else 
					{				
						Phpfox::getLib('url')->send('subscribe');
					}
				}
				return true;
			}
		}
		else 
		{
			if (is_array($sName))
			{
				return $bPass;	
			}
			else
			{			
				return Phpfox::getService('user.group.setting')->getParam($sName);
			}			
		}
	}	

	/**
	 * Check to see if we are in the AdminCP or not.
	 *
	 * @return TRUE if we are, FALSE if we are not.
	 */
	public static function isAdminPanel()
	{
		return (self::$_bIsAdminCp ? true : false);
	}
	
	/**
	 * Check to see if items are to be displayed in a public manner or within a persons profile.
	 *
	 * @return bool TRUE will display items in public and FALSE will display on a users profile.
	 * @deprecated 3.0.0Beta1
	 */
	public static function isPublicView()
	{
		return (Phpfox::getParam('core.item_view_area') == 'public' ? true : false);
	}
	
	/**
	 * Starts the phpFox engine. Used to get and display the pages controller.
	 *
	 */
	public static function run()
	{		
		$oTpl = Phpfox::getLib('template');
		$aLocale = Phpfox::getLib('locale')->getLang();
		$oReq = Phpfox::getLib('request');
		$oModule = Phpfox::getLib('module');		
		
		(($sPlugin = Phpfox_Plugin::get('run_start')) ? eval($sPlugin) : false);
		
		// Load module blocks
		$oModule->loadBlocks();
		
		if (Phpfox::isMobile() && $oReq->get('req1') == 'go-to-full-site')
		{
			Phpfox::getLib('session')->set('mobilestatus', 'true');
			Phpfox::getLib('url')->send('phpfox_full_site');
		}
		
		if (!Phpfox::getParam('core.branding'))
		{
			$oTpl->setHeader(array('<meta name="author" content="phpFox" />'));
		}
		
		if (strtolower(Phpfox::getLib('request')->get('req1')) == Phpfox::getParam('admincp.admin_cp'))
		{
			self::$_bIsAdminCp = true;
		}				
	
		if (!Phpfox::getService('ban')->check('ip', Phpfox::getIp()))
		{
			$oModule->setController('ban.message');
		}		
		else 
		{		
			if (!self::$_bIsAdminCp 
				&& Phpfox::getParam('core.site_is_offline') 
				&& !Phpfox::getUserParam('core.can_view_site_offline')
			)
			{
				if ((Phpfox::getLib('request')->get('req1') == 'user' 
					&& 
					(
						Phpfox::getLib('request')->get('req2') == 'login' ||
						Phpfox::getLib('request')->get('req2') == 'logout'
					))
					|| Phpfox::getLib('request')->get('req1') == 'contact'				
					|| Phpfox::getLib('request')->get('req1') == 'captcha'				
				)
				{
					$oModule->setController();
				}
				else 
				{
					$oModule->setController('core.offline');
				}
			}
			else 
			{
				$oModule->setController();
			}		
		}
	
		if (!PHPFOX_IS_AJAX_PAGE)
		{
				if (Phpfox::isMobile())
				{
					$oTpl->setMobileHeader(array(
							'<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />'						
						)
					);	
				}
			
				$oTpl->setImage(array(
						'ajax_small' => 'ajax/small.gif',
						'ajax_large' => 'ajax/large.gif',
						'loading_animation' => 'misc/loading_animation.gif',
						'close' => 'misc/close.gif',
						'move' => 'misc/move.png',
						'calendar' => 'jquery/calendar.gif'
					)
				);			
				
				$oTpl->setHeader(array(
							'<meta http-equiv="Content-Type" content="text/html; charset=' . $aLocale['charset'] . '" />',
							'<meta name="keywords" content="' . Phpfox::getLib('locale')->convert(Phpfox::getParam('core.keywords')) . '" />',				
							'<meta name="description" content="' . Phpfox::getLib('locale')->convert(Phpfox::getParam('core.description')) . '" />',								
							'<meta name="robots" content="index,follow" />',				
							'<meta http-equiv="imagetoolbar" content="no" />',						
							'<meta http-equiv="cache-control" content="no-cache" />',
							'<meta http-equiv="expires" content="-1" />',
							'<meta http-equiv="pragma" content="no-cache" />',						
							'<link rel="shortcut icon" type="image/x-icon" href="' . Phpfox::getParam('core.path') . 'favicon.ico" />'						
						)
					)
					->setHeader('cache', array(
							'layout.css' => 'style_css',
							'common.css' => 'style_css',
							'thickbox.css' => 'style_css',
							'jquery.css' => 'style_css',
							'pager.css' => 'style_css',
							'jquery/jquery.js' => 'static_script'							
						)
					);
				
					if (!Phpfox::isMobile())
					{
						$oTpl->setHeader('cache', array('jquery/ui.js' => 'static_script'));
					}
				
					if (!defined('PHPFOX_IS_AD_PREVIEW') && !defined('PHPFOX_IN_DESIGN_MODE') && !defined('PHPFOX_INSTALLER') && Phpfox::getParam('core.site_wide_ajax_browsing'))
					{
						$oTpl->setHeader('cache', array('jquery/plugin/jquery.address.js' => 'static_script'));
					}				
				
					$oTpl->setHeader('cache', array(
							'common.js' => 'static_script',
							'main.js' => 'static_script',
							'ajax.js' => 'static_script',
							'thickbox/thickbox.js' => 'static_script',
							'search.js' => 'module_friend'					
						)
					);		
		
				if (PHPFOX_DEBUG)
				{
					$oTpl->setHeader('cache', array('debug.css' => 'style_css'));
				}		
				
				if (!Phpfox::isMobile() && Phpfox::isUser() && Phpfox::getParam('user.enable_user_tooltip'))
				{
					$oTpl->setHeader('cache', array(
							'user_info.js' => 'static_script'
						)
					);			
				}
				
				if ($aLocale['direction'] == 'rtl')
				{
					$oTpl->setHeader('cache', array(
							'rtl.css' => 'style_css'
						)
					);
				}
		}
			
		
		(($sPlugin = Phpfox_Plugin::get('get_controller')) ? eval($sPlugin) : false);

		$oModule->getController();
		
		if (!Phpfox::isUser() && !defined('PHPFOX_DONT_SAVE_PAGE'))
		{
			Phpfox::getLib('session')->set('redirect', Phpfox::getLib('url')->getFullUrl(true));
		}
	
		if (!defined('PHPFOX_NO_CSRF'))
		{			
			Phpfox::getService('log.session')->verifyToken();	
		}
		
		(($sPlugin = Phpfox_Plugin::get('run')) ? eval($sPlugin) : false);
	
		if (!self::isAdminPanel())
		{			
				if (!Phpfox::isMobile() && !PHPFOX_IS_AJAX_PAGE && Phpfox::isModule('rss') && !defined('PHPFOX_IS_USER_PROFILE'))
				{
					$aFeeds = Phpfox::getService('rss')->getLinks();
					if (is_array($aFeeds) && count($aFeeds))
					{
						foreach ($aFeeds as $sLink => $sPhrase)
						{
							$oTpl->setHeader('<link rel="alternate" type="application/rss+xml" title="' . $sPhrase . '" href="' . $sLink . '" />');
						}
					}
				}
				
				$aPageLastLogin = ((Phpfox::isModule('pages') && Phpfox::getUserBy('profile_page_id')) ? Phpfox::getService('pages')->getLastLogin() : false);
				
				$oTpl->assign(array(
						'aMenus' => $oTpl->getMenu('main'),
						'aRightMenus' => $oTpl->getMenu('main_right'),
						'aAppMenus' => $oTpl->getMenu('explore'),
						'aSubMenus' => $oTpl->getMenu(),
						'aFooterMenu' => $oTpl->getMenu('footer'),
						'aBlocks1' => ($oTpl->bIsSample ? true : Phpfox::getLib('module')->getModuleBlocks(1)),
						'aBlocks3' => ($oTpl->bIsSample ? true : Phpfox::getLib('module')->getModuleBlocks(3)),								
						'aAdBlocks1' => ($oTpl->bIsSample ? true : (Phpfox::isModule('ad') ? Phpfox::getService('ad')->getForBlock(1) : null)),
						'aAdBlocks3' => ($oTpl->bIsSample ? true : (Phpfox::isModule('ad') ? Phpfox::getService('ad')->getForBlock(3) : null)),								
						'bIsUsersProfilePage' => (defined('PHPFOX_IS_USER_PROFILE') ? true : false),
						'sStyleLogo' => $oTpl->getStyleLogo(),
						'aStyleInUse' => $oTpl->getStyleInUse(),
						'sGlobalUserFullName' => (Phpfox::isUser() ? Phpfox::getUserBy('full_name') : null),
						'aGlobalUser' => (Phpfox::isUser() ? Phpfox::getUserBy(null) : array()),
						'sFullControllerName' => str_replace('.', '_', Phpfox::getLib('module')->getFullControllerName()),
						'iGlobalProfilePageId' => Phpfox::getUserBy('profile_page_id'),
						'aGlobalProfilePageLogin' => $aPageLastLogin,
						'aInstalledApps' => (Phpfox::isUser() ? Phpfox::getService('apps')->getInstalledApps() : array())
					)
				);			
				
				if (isset($aPageLastLogin['style_id']) && $aPageLastLogin['style_id'] > 0)
				{
					Phpfox::getLib('template')->testStyle($aPageLastLogin['style_id']);
				}
				
				if (Phpfox::isModule('captcha'))
				{
				    $sCaptchaHeader = Phpfox::getParam('captcha.recaptcha_header');
				
				    if (strlen(preg_replace('/\s\s+/', '', $sCaptchaHeader)) > 0)
				    {
					    $oTpl->setHeader(array($sCaptchaHeader));
				    }
				}
				
				if (!PHPFOX_IS_AJAX_PAGE && !defined('PHPFOX_SKIP_IM'))
				{
					if (!defined('PHPFOX_IN_DESIGN_MODE'))
					{
						if (Phpfox::isUser() 
							&& Phpfox::isModule('subscribe')
							&& Phpfox::getParam('subscribe.subscribe_is_required_on_sign_up') 
							&& Phpfox::getUserBy('user_group_id') == '2' 
							&& (int) Phpfox::getUserBy('subscribe_id') > 0
						)
						{
				
						}
						else 
						{
							if (Phpfox::isModule('notification') && Phpfox::isUser() && Phpfox::getParam('notification.notify_on_new_request'))
							{
								$oTpl->setHeader('cache', array('update.js' => 'module_notification'));
							}
							
							if (!Phpfox::isMobile() && Phpfox::isModule('im') && Phpfox::isUser() && Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::getParam('im.enable_im_in_footer_bar') && !$oTpl->bIsSample)
							{
								$oTpl->setPhrase(array(
											'im.conversations',
											'im.more_conversations',
											'im.are_you_sure'
										)
									)
									->setImage(array(
											'misc_minimize' => 'misc/minimize.gif'
										)
									)
									->setHeader('cache', array(
										'im.js' => 'module_im',
										'im.css' => 'style_css',								
										// 'player/' . Phpfox::getParam('core.default_music_player') . '/core.js' => 'static_script',
									)
								)
								->setHeader(array(
										'<script type="text/javascript">oCore[\'im.is_hidden\'] = \'' . Phpfox::getUserBy('im_hide') . '\';</script>',
										// '<script type="text/javascript">$(function() { $Core.player.load({id: \'js_im_player\', type: \'music\'}); });</script>'
									)
								);
							}
						}
					}										
				}
			
		}
		
		if (!PHPFOX_IS_AJAX_PAGE && ($sHeaderFile = $oTpl->getHeaderFile()))
		{
        	require_once($sHeaderFile);
		}
		
		list($aBreadCrumbs, $aBreadCrumbTitle) = $oTpl->getBreadCrumb();

		$oTpl->assign(array(
				'aErrors' => (Phpfox_Error::getDisplay() ? Phpfox_Error::get() : array()),
				'sPublicMessage' => Phpfox::getMessage(),
				'sLocaleDirection' => $aLocale['direction'],
				'sLocaleCode' => $aLocale['language_code'],
				'sLocaleFlagId' => $aLocale['image'],
				'sLocaleName' => $aLocale['title'],
				'aRequests' => Phpfox::getLib('request')->getRequests(),
				'aBreadCrumbs' => $aBreadCrumbs,
				'aBreadCrumbTitle' => $aBreadCrumbTitle,
				'sCopyright' => '&copy; ' . Phpfox::getPhrase('core.copyright') . ' ' . Phpfox::getParam('core.site_copyright')
			)
		);		

		Phpfox::clearMessage();		
		
		unset($_SESSION['phpfox']['image']);		
	
		if (Phpfox::getParam('core.cron'))
		{
			require_once(PHPFOX_DIR_CRON . 'exec.php');
		}

		if (!PHPFOX_IS_AJAX_PAGE && $oTpl->sDisplayLayout)
		{			
			$oTpl->getLayout($oTpl->sDisplayLayout);
		}

		if (Phpfox::getParam('language.cache_phrases'))
		{
			Phpfox::getLib('locale')->cache();
		}		
		
		if (!PHPFOX_IS_AJAX_PAGE && Phpfox::getParam('core.phpfox_is_hosted'))
		{
			$iTotalMembersOnline = Phpfox::getService('log.session')->getOnlineMembers();
			if ($iTotalMembersOnline > Phpfox::getParam('core.phpfox_max_users_online') && $iTotalMembersOnline > (int) Phpfox::getParam('core.phpfox_total_users_online_mark'))
			{
				$oDb = Phpfox::getLib('database');
				$oDb->update(Phpfox::getT('setting'), array('value_actual' => (int) $iTotalMembersOnline), 'var_name = \'phpfox_total_users_online_mark\'');
				
				$sPastHistory = Phpfox::getParam('core.phpfox_total_users_online_history');
				$aPastHistory = array();
				if (!empty($sPastHistory) && Phpfox::getLib('parse.format')->isSerialized($sPastHistory))
				{
					$aPastHistory = unserialize($sPastHistory);	
				}
				$oDb->update(Phpfox::getT('setting'), array('value_actual' => serialize(array_merge($aPastHistory, array(array('time_stamp' => PHPFOX_TIME, 'total' => (int) $iTotalMembersOnline))))), 'var_name = \'phpfox_total_users_online_history\'');
				
				Phpfox::getLib('cache')->remove('setting');
				
				ob_clean();				
			}
		}		
		
		// Use GZIP to output the data if we can		
		if (Phpfox::getParam('core.use_gzip') && !PHPFOX_IS_AJAX_PAGE)
		{						
			$sContent = ob_get_contents();
			
			ob_clean();
	
			if (function_exists('gzencode'))
			{			
				$sGzipContent = gzencode($sContent, Phpfox::getParam('core.gzip_level'), FORCE_GZIP);
			}
			else
			{
				if (function_exists('gzcompress') && function_exists('crc32'))
				{		
					$iSize = strlen($sContent);
					$iCrc = crc32($sContent);
					$sGzipContent = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
					$sGzipContent .= substr(gzcompress($sContent, Phpfox::getParam('core.gzip_level')), 2, -4);
					$sGzipContent .= pack('V', $iCrc);
					$sGzipContent .= pack('V', $iSize);		
				}		
			}

			if (isset($sGzipContent))
			{				
				header("Content-Encoding: " . (in_array('x-gzip', Phpfox::getParam('core.gzip_encodings')) ? "x-gzip" : "gzip"));
			}			
			
			echo (isset($sGzipContent) ? $sGzipContent : $sContent);			
		}	
	}
	
	/**
	 * @see Phpfox_Local::getPhrase()
	 * @param string $sParam
	 * @param array $aParams
	 * @param bool $bNoDebug
	 * @param string $sDefault
	 * @param string $sLang
	 * @return string
	 */
	public static function getPhrase($sParam, $aParams = array(), $bNoDebug = false, $sDefault = null, $sLang = '')
	{
		return Phpfox::getLib('locale')->getPhrase($sParam, $aParams, $bNoDebug, $sDefault, $sLang);
	}
	
	/**
	 * @see Phpfox_Locale::translate()
	 * @param string $sParam
	 * @param string $sPrefix
	 * @return string
	 */	
	public static function getPhraseT($sParam, $sPrefix)
	{
		return Phpfox::getLib('locale')->translate($sParam, $sPrefix);
	}	
	
	/**
	 * Add a public message which can be used later on to display information to a user.
	 * Message gets stored in a $_SESSION so the message can be viewed after page reload in case
	 * it is used with a HTML form.
	 *
	 * @see Phpfox_Session::set()
	 * @param string $sMsg Message we plan to display to the user
	 */
	public static function addMessage($sMsg)
	{
		Phpfox::getLib('session')->set('message', $sMsg);
	}

	/**
	 * Get the public message we setup earlier
	 *
	 * @see Phpfox_Session::get()
	 * @return string|void Return the public message, or return nothing if no public message is set
	 */
	public static function getMessage()
	{
		return Phpfox::getLib('session')->get('message');		
	}
	
	/**
	 * Clear the public message we set earlier
	 *
	 * @see Phpfox_Session::remove()
	 */
	public static function clearMessage()
	{
		Phpfox::getLib('session')->remove('message');
	}	
	
	/**
	 * Set a cookie with PHP setcookie()
	 *
	 * @see setcookie()
	 * @param string $sName The name of the cookie. 
	 * @param string $sValue The value of the cookie.
	 * @param int $iExpire The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
	 */
	public static function setCookie($sName, $sValue, $iExpire = 0)
	{
		$sName = Phpfox::getParam('core.session_prefix') . $sName;

		setcookie($sName, $sValue, (($iExpire != 0 || $iExpire != -1) ? $iExpire : (PHPFOX_TIME + (60*60*24*$iExpire))), Phpfox::getParam('core.cookie_path'), Phpfox::getParam('core.cookie_domain'));
	}
	
	/**
	 * Gets a cookie set by the method self::setCookie().
	 *
	 * @param string $sName Name of the cookie.
	 * @return string Value of the cookie.
	 */
	public static function getCookie($sName)
	{
		$sName = Phpfox::getParam('core.session_prefix') . $sName;

		return (isset($_COOKIE[$sName]) ? $_COOKIE[$sName] : '');
	}
	
	/**
	 * Start a new log.
	 *
	 * @param string $sLog Message to the log.
	 */
	public static function startLog($sLog = null)
	{
		self::$_aLogs[] = array();
		
		if ($sLog !== null)
		{
			self::log($sLog);
		}
	}
	
	/**
	 * Log a message.
	 *
	 * @param string $sLog Message to the log.
	 */
	public static function log($sLog)
	{
		self::$_aLogs[] = $sLog;
	}
	
	/**
	 * End the log and get it.
	 *
	 * @return array Returns the log.
	 */
	public static function endLog()
	{
		return self::$_aLogs;
	}
	
	/**
	 * Permalink for items.
	 *
	 * @return	string	Returns the full URL of the link.
	 */
	public static function permalink($sLink, $iId, $sTitle = null, $bRedirect = false, $sMessage = null, $aExtra = array())
	{		
		return Phpfox::getLib('url')->permalink($sLink, $iId, $sTitle, $bRedirect, $sMessage, $aExtra);
	}
}

?>