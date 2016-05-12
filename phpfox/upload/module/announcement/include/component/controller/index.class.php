<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Component
 * @version 		$Id: index.class.php 1306 2009-12-09 05:05:18Z Raymond_Benc $
 */
class Announcement_Component_Controller_Index extends Phpfox_Component
{
/**
 * Class process method wnich is used to execute this component.
 */
    public function process()
    {
    	Phpfox::getUserParam('announcement.can_view_announcements', true);
    	
	$iId = $this->request()->getInt('id');
	if ($iId > 0)
	{
	/**
	 * @todo assess if we can use getAnnouncementById instead of getLatest here
	 */
	    $aAnnouncements = Phpfox::getService('announcement')->getLatest($iId);

	    if ($aAnnouncements === false)
	    {
		$this->url()->send('announcement.view', null, Phpfox::getPhrase('announcement.that_announcement_does_not_exist'));
	    }
	    $aAnnouncements = reset($aAnnouncements);
	    $sSubject = $aAnnouncements['subject_var'];
	    
	    if (Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncements['subject_var']))
	    {
		$aAnnouncements['subject_text'] = Phpfox::getPhrase($aAnnouncements['subject_var']);
	    }
	    else
	    {
		$aAnnouncements['subject_text']  = '';
	    }

	    if (isset($aAnnouncements['intro_var']) && Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncements['intro_var']))
	    {
		$aAnnouncements['intro_text'] = Phpfox::getPhrase($aAnnouncements['intro_var']);
	    }
	    else
	    {
		$aAnnouncements['intro_text']  = '';
	    }

	    if (Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncements['content_var']))
	    {
		$aAnnouncements['content_text'] = Phpfox::getPhrase($aAnnouncements['content_var']);
	    }
	    $this->template()->setBreadcrumb(Phpfox::getPhrase('announcement.announcements'), $this->url()->makeUrl('announcement.view'))
		->setBreadcrumb(Phpfox::getPhrase($sSubject), null, true)
		->setTitle(Phpfox::getPhrase($sSubject))
		->assign(array('aAnnouncements' => array($aAnnouncements)));

	}
	else
	{
	    $aAnnouncements = Phpfox::getService('announcement')->getLatest();
	    if (is_array($aAnnouncements))
	    {
		foreach ($aAnnouncements as &$aAnnouncement)
		{
		    if (Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncement['subject_var']))
		    {
			$aAnnouncement['subject_text'] = Phpfox::getPhrase($aAnnouncement['subject_var']);
		    }
		    else
		    {
			$aAnnouncement['subject_text']  = '';
		    }

		    if (isset($aAnnouncement['intro_var']) && Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncement['intro_var']))
		    {
			$aAnnouncement['intro_text'] = Phpfox::getPhrase($aAnnouncement['intro_var']);
		    }
		    else
		    {
			$aAnnouncement['intro_text']  = '';
		    }

		    if (Phpfox::getLib('phpfox.locale')->isPhrase($aAnnouncement['content_var']))
		    {
			$aAnnouncement['content_text'] = Phpfox::getPhrase($aAnnouncement['content_var']);
		    }

		}
	    }

	    //if (!is_array($aAnnouncements)) $aAnnouncements = array();

	    $this->template()->setBreadcrumb(Phpfox::getPhrase('announcement.announcements'), $this->url()->makeUrl('announcement.view'))
		->setTitle(Phpfox::getPhrase('announcement.announcements'))
		->assign(array('aAnnouncements' => ($aAnnouncements)));
	}

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
	(($sPlugin = Phpfox_Plugin::get('announcement.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}

?>