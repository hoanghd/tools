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
 * @version 		$Id: service.class.php 67 2009-01-20 11:32:45Z Raymond_Benc $
 */
class Photo_Service_Api extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('photo');
		$this->_oApi = Phpfox::getService('api');	
	}
	
	/**
	 * Adds a photo to an album of the name of the application.
	 * If such album does not exist it creates it.
	 */
	public function addPhoto()
	{
		// Check permission
		if ($this->_oApi->isAllowed('photo.add_photo') == false)
		{
			return $this->_oApi->error('photo.add_photo', 'User did not allow to upload photos on their behalf.');
		}	
		
		$oFile = Phpfox::getLib('file');
		$oImage = Phpfox::getLib('image');
		
		$sType = 'png';
		switch($_FILES['photo']['type'])
		{
			case 'image/jpeg':
			case 'image/jpg':
				$sType = 'jpg';
				break;
			case 'image/gif':
				$sType = 'gif';
				break;
		}
		
		$sImagePath = Phpfox::getParam('photo.dir_photo') . uniqid() . '.' . $sType;
		
		move_uploaded_file($_FILES['photo']['tmp_name'], $sImagePath);
		
		$_FILES['photo']['tmp_name'] = $sImagePath;
		$_FILES['photo']['name'] = $this->_oApi->get('photo_name');
		
		$aImage = $oFile->load('photo', array(
			'jpg',
			'gif',
			'png'), (Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : (Phpfox::getUserParam('photo.photo_max_upload_size') / 1024))
		);
		
		$aImage['type_id'] = 1;
		$aErrors = Phpfox_Error::get();
		if (!empty($aErrors))
		{
			return $this->_oApi->error('photo.photo_add_photo_load', array_pop($aErrors));
		}

		$aReturnPhotos = array();
		if ($iId = Phpfox::getService('photo.process')->add(PHPFOX_APP_USER_ID, $aImage))
		{
			$sFileName = $oFile->upload('photo', Phpfox::getParam('photo.dir_photo'), $iId);
			$sPath = Phpfox::getParam('photo.dir_photo');
			$aErrors = Phpfox_Error::get();
			if (!empty($aErrors))
			{
				return $this->_oApi->error('photo.photo_add_photo_upload', array_pop($aErrors));
			}

			$sPhotoTitle = $this->_oApi->get('photo_name');
			if (empty($sPhotoTitle))
			{
				$sPhotoTitle = $this->_oApi->getAppName() . ' ' . rand(100,999);
			}
			// Update the image with the full path to where it is located.
			$aSize = getimagesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
			Phpfox::getService('photo.process')->update(Phpfox::getUserId(), $iId, array(
					'destination' => $sFileName,
					'title' => $sPhotoTitle,
					'width' => $aSize[0],
					'height' => $aSize[1],
					'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
				)
			);		
			
			$aReturnPhotos['original'] = Phpfox::getParam('photo.url_photo') . sprintf($sFileName, '');
			
			foreach (Phpfox::getParam('photo.photo_pic_sizes') as $iSize)
			{
				if ($oImage->createThumbnail($sPath . sprintf($sFileName, ''), $sPath . sprintf($sFileName, '_' . $iSize), $iSize, $iSize) === false)
				{
					continue;
				}
				
				$aReturnPhotos[$iSize . 'px'] = Phpfox::getParam('photo.url_photo') . sprintf($sFileName, '_' . $iSize);

				if (Phpfox::getParam('photo.enabled_watermark_on_photos'))
				{
					$oImage->addMark($sPath . sprintf($sFileName, '_' . $iSize));
				}
			}

			if (Phpfox::getParam('photo.enabled_watermark_on_photos'))
			{
				$oImage->addMark($sPath . sprintf($sFileName, ''));
			}
			if (Phpfox::isModule('feed'))
			{
				$iPrivacy = null;
				$iPrivacyComment = null;
				$iFeedId = Phpfox::getService('feed.process')->add('photo', $iId, $iPrivacy, $iPrivacyComment, 0);
			}
			
			return $aReturnPhotos;
		}
	
		return $this->_oApi->error('photo.add_photo_process', 'Could not add photo to process');
	
	}
	
	public function getPhotos()
	{
		if ((int) $this->_oApi->get('user_id') === 0)
		{
			$iUserId = $this->_oApi->getUserId();
		}
		else
		{
			$iUserId = $this->_oApi->get('user_id');
		}		
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->where('p.view_id = 0 AND p.group_id = 0 AND p.type_id = 0 AND p.privacy = 0 AND p.user_id = ' . (int) $iUserId)
			->execute('getSlaveField');
		
		$this->_oApi->setTotal($iCnt);
		
		$aRows = $this->database()->select('p.*')
			->from($this->_sTable, 'p')
			->where('p.view_id = 0 AND p.group_id = 0 AND p.type_id = 0 AND p.privacy = 0 AND p.user_id = ' . (int) $iUserId)
			->limit($this->_oApi->get('page'), 10, $iCnt)
			->execute('getSlaveRows');
	
		$aPhotos = array();
		foreach ($aRows as $iKey => $aRow)
		{
			$sImagePath = $aRow['destination'];
			
			$aPhotos[$iKey]['photo_100px'] = Phpfox::getLib('image.helper')->display(array(
					'path' => 'photo.url_photo',
					'server_id' => $aRow['server_id'],
					'file' => $aRow['destination'],
					'suffix' => '_100',
					'return_url' => true
				)
			);	
			
			$aPhotos[$iKey]['photo_240px'] = Phpfox::getLib('image.helper')->display(array(
					'path' => 'photo.url_photo',
					'server_id' => $aRow['server_id'],
					'file' => $aRow['destination'],
					'suffix' => '_240',
					'return_url' => true
				)
			);
			
			$aPhotos[$iKey]['photo_original'] = Phpfox::getLib('image.helper')->display(array(
					'path' => 'photo.url_photo',
					'server_id' => $aRow['server_id'],
					'file' => $aRow['destination'],
					'suffix' => '',
					'return_url' => true
				)
			);				
		}
		
		return $aPhotos;
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
		if ($sPlugin = Phpfox_Plugin::get('photo.service_api__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>