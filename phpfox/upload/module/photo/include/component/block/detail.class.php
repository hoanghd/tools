<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: detail.class.php 2921 2011-08-29 17:35:44Z Raymond_Benc $
 */
class Photo_Component_Block_Detail extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!$this->getParam('bIsValidImage'))
		{
			return false;
		}

		$aUser = $this->getParam('aUser');
		$aPhoto = $this->getParam('aPhoto');

		if ($aPhoto === null)
		{
			return false;
		}
		
		$sCategories = '';
		if (isset($aPhoto['categories']) && is_array($aPhoto['categories']))
		{
			foreach ($aPhoto['categories'] as $aCategory)
			{
				$sCategories .= $aCategory[0] . ',';
			}
			$sCategories = rtrim($sCategories, ',');
		}

		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('photo.image_details'),
				'aPhotoDetails' => array(
					'Added' => Phpfox::getTime(Phpfox::getParam('photo.photo_image_details_time_stamp'), $aPhoto['time_stamp']),
					Phpfox::getPhrase('photo.category') => $sCategories,
					Phpfox::getPhrase('photo.file_size') => Phpfox::getLib('file')->filesize($aPhoto['file_size']),
					Phpfox::getPhrase('photo.resolution') => $aPhoto['width'] . '×' . $aPhoto['height'],
					Phpfox::getPhrase('photo.comments') => $aPhoto['total_comment'],
					Phpfox::getPhrase('photo.views') => $aPhoto['total_view'],
					Phpfox::getPhrase('photo.rating') => round($aPhoto['total_rating']),
					Phpfox::getPhrase('photo.battle_wins') => round($aPhoto['total_battle']),
					Phpfox::getPhrase('photo.downloads') => $aPhoto['total_download']
				),
				'sUrlPath' => (preg_match("/\{file\/pic\/(.*)\/(.*)\.jpg\}/i", $aPhoto['destination'], $aMatches) ? Phpfox::getParam('core.path') . str_replace(array('{', '}'), '', $aMatches[0]) : (($aPhoto['server_id'] && Phpfox::getParam('core.allow_cdn')) ? Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('photo.url_photo') . sprintf($aPhoto['destination'], '_500')) : Phpfox::getParam('photo.url_photo') . sprintf($aPhoto['destination'], '_500')))
			)
		);

		return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_block_detail_clean')) ? eval($sPlugin) : false);

		$this->template()->clean(array(
				'aPhotoDetails',
				'sEmbedCode'
			)
		);
	}
}

?>