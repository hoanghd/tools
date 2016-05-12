<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: link.class.php 3120 2011-09-19 09:25:17Z Raymond_Benc $
 */
class Link_Service_Link extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('link');	
	}
	
	public function getLink($sUrl)
	{
		if (substr($sUrl, 0, 7) != 'http://')
		{
			$sUrl = 'http://' . $sUrl;
		}
			
		$aParts = parse_url($sUrl);	
		
		if (!isset($aParts['host']))
		{
			return Phpfox_Error::set('Not a valid link.');
		}
		
		$aReturn = array();
		
		$sCacheId = $this->cache()->set('api_embed_ly');
		$aSites = array();		
		if (!($aSites = $this->cache()->get($sCacheId)))
		{
			$aSites[] = '#http://.*myspace.com/video/.*#i';
			$oOutput = json_decode(Phpfox::getLib('request')->send('http://api.embed.ly/1/services/php', array(), 'GET', $_SERVER['HTTP_USER_AGENT']));
			if (!is_array($oOutput))
			{
				return Phpfox_Error::set('Unable to build api.embed.ly array of sites.');
			}
			foreach ($oOutput as $aOutput)
			{
				foreach ($aOutput->regex as $sRegex)
				{
					$aSites[] = $sRegex;
				}
			}

			$this->cache()->save($sCacheId, $aSites);
		}
		
		foreach ($aSites as $sSiteRegex)
		{		
			if (preg_match($sSiteRegex, $sUrl))
			{
				$oVideo = json_decode(Phpfox::getLib('request')->send('http://api.embed.ly/1/oembed?format=json&maxwidth=400&url=' . urlencode($sUrl), array(), 'GET', $_SERVER['HTTP_USER_AGENT']));
	
				if (isset($oVideo->provider_url))
				{
					$aReturn = array(
						'link' => $sUrl,
						'title' => strip_tags($oVideo->title),
						'description' => strip_tags($oVideo->description),
						'default_image' => $oVideo->thumbnail_url,
						'embed_code' => ($oVideo->type == 'video' ? $oVideo->html : '')
					);
					
					return $aReturn;
				}
			}
		}
		
		$sContent = Phpfox::getLib('request')->send($sUrl, array(), 'GET', $_SERVER['HTTP_USER_AGENT']);
		if( function_exists('mb_convert_encoding') )
      	{
      		$sContent = mb_convert_encoding($sContent, 'HTML-ENTITIES', "UTF-8");
      	}		
      	      	
      	$aReturn['link'] = $sUrl;
		
		Phpfox_Error::skip(true);
		$oDoc = new DOMDocument();
		$oDoc->loadHTML($sContent);
		Phpfox_Error::skip(false);
		
		if (($oTitle = $oDoc->getElementsByTagName('title')->item(0)) && !empty($oTitle->nodeValue))
		{
			$aReturn['title'] = strip_tags($oTitle->nodeValue);
		}
		
		if (empty($aReturn['title']))
		{
			return Phpfox_Error::set('Not a valid link. Unable to find a title.');
		}
		
		$oXpath = new DOMXPath($oDoc);	
		$oMeta = $oXpath->query("//meta[@name='description']")->item(0);
		if (method_exists($oMeta, 'getAttribute'))
		{
			$sMeta = $oMeta->getAttribute('content');
			if (!empty($sMeta))
			{
				$aReturn['description'] = strip_tags($sMeta);
			}
		}
		
		$aImages = array();		
		$oMeta = $oXpath->query("//meta[@property='og:image']")->item(0);
		if (method_exists($oMeta, 'getAttribute'))
		{			
			$aReturn['default_image'] = strip_tags($oMeta->getAttribute('content'));
			$aImages[] = strip_tags($oMeta->getAttribute('content'));
		}		
		
		$oMeta = $oXpath->query("//link[@rel='image_src']")->item(0);
		if (method_exists($oMeta, 'getAttribute'))
		{			
			if (empty($aReturn['default_image']))
			{
				$aReturn['default_image'] = strip_tags($oMeta->getAttribute('href'));
			}
			$aImages[] = strip_tags($oMeta->getAttribute('href'));
		}			
		
		if (!isset($aReturn['default_image']))
		{
			$oImages = $oDoc->getElementsByTagName('img');
			$iIteration = 0;
			foreach ($oImages as $oImage)
			{
				$sImageSrc = $oImage->getAttribute('src');
				
				if (substr($sImageSrc, 0, 7) != 'http://' && substr($sImageSrc, 0, 1) != '/')
				{
					continue;	
				}
				
				if (substr($sImageSrc, 0, 2) == '//')
				{
					continue;
				}
				
				$iIteration++;		
				
				if (substr($sImageSrc, 0, 1) == '/')
				{					
					$sImageSrc = 'http://' . $aParts['host'] . $sImageSrc;
				}			
				
				if ($iIteration === 1 && empty($aReturn['default_image']))
				{
					$aReturn['default_image'] = strip_tags($sImageSrc);
				}
				
				if ($iIteration > 10)
				{
					break;
				}
				
				$aImages[] = strip_tags($sImageSrc);
			}
		}
		
		if (count($aImages))
		{
			$aReturn['images'] = $aImages;
		}
		
		$oLink = $oXpath->query("//link[@type='text/xml+oembed']")->item(0);
		if (method_exists($oLink, 'getAttribute'))
		{	
			$aXml = Phpfox::getLib('xml.parser')->parse(Phpfox::getLib('request')->send($oLink->getAttribute('href'), array(), 'GET', $_SERVER['HTTP_USER_AGENT']));			
			if (isset($aXml['html']))
			{
				$aReturn['embed_code'] = $aXml['html'];	
			}
		}				
		
		return $aReturn;
	}
	
	public function getEmbedCode($iId, $bIsPopUp = false)
	{
		$aLinkEmbed = $this->database()->select('embed_code')	
			->from(Phpfox::getT('link_embed'))
			->where('link_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		$iWidth = 640;
		$iHeight = 390;
		if (!$bIsPopUp)
		{
			$iWidth = 480;
			$iHeight = 295;
		}
		
		$aLinkEmbed['embed_code'] = preg_replace('/width=\"(.*?)\"/i', 'width="' . $iWidth . '"', $aLinkEmbed['embed_code']);
		$aLinkEmbed['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="' . $iHeight . '"', $aLinkEmbed['embed_code']);		
		$aLinkEmbed['embed_code'] = preg_replace_callback('/<object(.*?)>(.*?)<\/object>/is', array($this, '_embedWmode'), $aLinkEmbed['embed_code']);
			
		return $aLinkEmbed['embed_code'];
	}
	
	public function getLinkById($iId)
	{
		$aLink = $this->database()->select('l.*, u.user_name')
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aLink['link_id']))
		{
			return false;
		}
		
		return $aLink;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('link.service_link__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
    private function _embedWmode($aMatches)
    {
    	return '<object ' . $aMatches[1] . '><param name="wmode" value="transparent"></param>' . str_replace('<embed ', '<embed  wmode="transparent" ', $aMatches[2]) . '</object>';
    }	
}

?>