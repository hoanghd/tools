<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Quiz
 * @version 		$Id: callback.class.php 3358 2011-10-25 15:00:49Z Raymond_Benc $
 */
class Quiz_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('quiz');
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Quizzes',
			'link' => Phpfox::getLib('url')->makeUrl('quiz'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_quizzes.png'))
		);
	}	
	
	public function getProfileLink()
	{
		return 'profile.quiz';
	}

	public function getAjaxCommentVar()
	{
		return 'quiz.can_post_comment_on_quiz';
	}

	public function getCommentItemName()
	{
		return 'quiz';
	}
	
	public function getNewsFeed($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('quiz.service_callback_getnewsfeed_start')){eval($sPlugin);}
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');

		$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_added_a_new_quiz_a_href_question_url_question_a', array(
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'question_url' => $aRow['link'],
				'question' => Phpfox::getService('feed')->shortenTitle($aRow['content'])
			)
		);	
		
		$aRow['icon'] = 'module/quiz.png';
		$aRow['enable_like'] = true;

		return $aRow;
	}
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('quiz_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('quiz_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set('Unable to post a comment on this item due to privacy settings.');
			
			unset($aRow['comment_item_id']);
		}
		
		return $aRow;
	}

	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aQuiz = $this->database()->select('q.quiz_id, q.title_url, u.user_id, u.user_name')
			->from($this->_sTable, 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->where('q.quiz_id = ' . (int) $iId)
			->execute('getSlaveRow');

		if (!isset($aQuiz['quiz_id']))
		{
			return false;
		}

		if (Phpfox::getParam('core.is_personal_site'))
		{
			return Phpfox::getLib('url')->makeUrl('quiz', $aQuiz['title_url']);
		}

		if ($iChild > 0)
		{
			return Phpfox::getLib('url')->makeUrl($aQuiz['user_name'], array('quiz', $aQuiz['title_url'], 'comment' => $iChild, '#comment-view'));
		}		
		return Phpfox::getLib('url')->makeUrl($aQuiz['user_name'], array('quiz', $aQuiz['title_url']));
	}

	public function getCommentNewsFeed($aRow)
	{
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_quiz_a', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']
				)
			);
		}
		else 
		{
			if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
			{
				$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_quiz_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']				
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_name_s_a_a_href_title_link_quiz_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
						'item_user_link' =>	$oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
					)
				);
			}
		}		

		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);

		return $aRow;
	}
	
	public function processCommentModeration($sAction, $iId)
	{
		// Is this comment approved?
		if ($sAction == 'approve')
		{
			// Update the blog count
			Phpfox::getService('quiz.process')->updateCounter($iId);

			// Get the blogs details so we can add it to our news feed
			$aQuiz = $this->database()->select('q.quiz_id, q.user_id, q.title_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')
				->from($this->_sTable, 'q')
				->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'quiz\' AND c.item_id = q.quiz_id')
				->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
				->where('q.quiz_id = ' . (int) $iId)
				->execute('getSlaveRow');

			// Add to news feed
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_quiz', $aQuiz['quiz_id'], $aQuiz['text_parsed'], $aQuiz['comment_user_id'], $aQuiz['user_id'], $aQuiz['comment_id']) : null);

			// Send the user an email
			if (Phpfox::getParam('core.is_personal_site'))
			{
				$sLink = Phpfox::getLib('url')->makeUrl('quiz', $aQuiz['title_url']);
			}
			else
			{
				$sLink = Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'), array('quiz', $aQuiz['title_url']));
			}

			Phpfox::getLib('mail')->to($aQuiz['comment_user_id'])
				->subject(array('quiz.full_name_approved_your_comment_on_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'))))
				->message(array('quiz.full_name_approved_your_comment_on_site_title_message', array(
							'full_name' => Phpfox::getUserBy('full_name'),
							'site_title' => Phpfox::getParam('core.site_title'),
							'link' => $sLink
						)
					)
				)
				->notification('comment.approve_new_comment')
				->send();
		}
	}

	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		$aQuiz = $this->database()->select('q.quiz_id, u.full_name, u.user_id, u.gender, u.user_name, q.title')
			->from($this->_sTable, 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->where('q.quiz_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		$this->database()->updateCounter('quiz', 'total_comment', 'quiz_id', $aQuiz['quiz_id']);	
		
		// Send the user an email
		$sLink = Phpfox::permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aQuiz['user_id'],
				'item_id' => $aQuiz['quiz_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on one of your quiz "' . $aQuiz['title'] . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your quiz \"<a href=\"" . $sLink . "\">" . $aQuiz['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_quiz',
				'mass_id' => 'quiz',
				'mass_subject' => (Phpfox::getUserId() == $aQuiz['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aQuiz['gender'], 1) . ' quiz.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aQuiz['full_name'] . '\'s quiz.'),
				'mass_message' => (Phpfox::getUserId() == $aQuiz['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aQuiz['gender'], 1) . " quiz \"<a href=\"" . $sLink . "\">" . $aQuiz['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aQuiz['full_name'] . "'s quiz \"<a href=\"" . $sLink . "\">" . $aQuiz['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);	
	}
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('q.quiz_id, q.title, q.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('quiz'), 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->where('q.quiz_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('quiz', $aRow['quiz_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}	
	
	public function updateCommentText($aVals, $sText)
	{
		// (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_quiz', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
	}		

	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . Phpfox::getPhrase('quiz.on_name_s_quiz', array('name' => $sName)) . '</a>';
	}	
	
	/**
	 * Adds a track to quiz_track only if this user has not taken the quiz. Meaning
	 * if the user has taken the quiz then no new track is added.
	 * @param int $iId quiz id
	 */
	public function addTrack($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('quiz.component_service_callback_addtrack_start')) ? eval($sPlugin) : false);

		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('quiz_track'))
			->where('item_id = ' . (int)$iId . ' AND user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');

		if ($iCnt <= 0)
		{
			$this->database()->insert(Phpfox::getT('quiz_track'), array(
					'item_id' => (int) $iId,
					'user_id' => Phpfox::getUserBy('user_id'),
					'time_stamp' => PHPFOX_TIME
				)
			);
		}

		
		(($sPlugin = Phpfox_Plugin::get('quiz.component_service_callback_addtrack_end')) ? eval($sPlugin) : false);
	}

	public function getLatestTrackUsers($iId, $iUserId)
	{
		$aUsers = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('quiz_track'), 'track')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
			->where('track.item_id = ' . (int) $iId . ' AND track.user_id != ' . (int) $iUserId)
			->order('track.time_stamp DESC')
			->limit(0, 7)
			->execute('getSlaveRows');		
		
		if (count($aUsers))
		{
			// get their answers and percentage
			$aAnswers = $this->database()->select('*')
				->from(Phpfox::getT('quiz_answer'), 'qa')
				->join(Phpfox::getT('quiz_result'), 'qr', 'qr.answer_id = qa.answer_id AND qr.quiz_id = ' . (int)$iId . ' AND qr.user_id = ' . (int)$iUserId)
				
				->execute('getSlaveRows');
			
		}
		return (count($aUsers) ? $aUsers : false);
	}
	
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('quiz', 'total_comment', 'quiz_id', $iId, true);
	}
	
	public function getDashboardLinks()
	{
		return array(
			'submit' => array(
				'phrase' => Phpfox::getPhrase('quiz.create_a_quiz'),
				'link' => 'quiz.add',
				'image' => 'misc/chart_pie_add.png'
			),
			'edit' => array(
				'phrase' => Phpfox::getPhrase('quiz.manage_quizzes'),
				'link' => 'profile.quiz',
				'image' => 'misc/chart_pie_edit.png'
			)
		);
	}	

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aQuizzes = $this->database()
			->select('quiz_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aQuizzes as $aQuiz)
		{
			Phpfox::getService('quiz.process')->deleteQuiz($aQuiz['quiz_id'], $iUser);
		}
	}
	
	public function getItemView()
	{
		if (Phpfox::getLib('request')->get('req3') != '')
		{
			return true;
		}
	}
	
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('quiz.quizzes') => $aUser['activity_quiz']
		);
	}

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('quiz.quizzes'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('quiz'))
				->where('view_id = 0 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}
	
	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
	
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_liked_their_own_a_href_link_quiz_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_quiz_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;				
	}		

	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_likes_your_a_href_link_quiz_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('quiz', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('quiz', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function sendLikeEmail($iItemId)
	{
		return Phpfox::getPhrase('quiz.a_href_user_link_full_name_a_likes_your_a_href_link_quiz_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('quiz', array('redirect' => $iItemId))
				)
			);
	}

	public function getActivityPointField()
	{
		return array(
			Phpfox::getPhrase('quiz.quizzes') => 'activity_quiz'
		);
	}

	public function pendingApproval()
	{
		$aPending[] = array(
			'phrase' => Phpfox::getPhrase('quiz.quizzes'),
			'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('quiz'))->where('view_id = 1')->execute('getSlaveField'),
			'link' => Phpfox::getLib('url')->makeUrl('quiz', array('view' => 'approval'))
		);		
		
		return $aPending;
	}

	public function getSqlTitleField()
	{
		return array(
			array(
				'table' => 'quiz',
				'field' => 'title'
			),
			array(
				'table' => 'quiz_answer',
				'field' => 'answer'
			),
			array(
				'table' => 'quiz_question',
				'field' => 'question'
			)			
		);
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('q.quiz_id, q.title, q.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('quiz'), 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->where('q.quiz_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('quiz', $aRow['quiz_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	

	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('quiz_id, title, user_id')
			->from(Phpfox::getT('quiz'))
			->where('quiz_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['quiz_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'quiz\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'quiz', 'quiz_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('quiz', $aRow['quiz_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('quiz.full_name_liked_your_quiz_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('quiz.full_name_liked_your_quiz_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('quiz_like', $aRow['quiz_id'], $aRow['user_id']);				
		}
	}
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'quiz\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'quiz', 'quiz_id = ' . (int) $iItemId);	
	}	
	
	public function getNotificationApproved($aNotification)
	{
		$aRow = $this->database()->select('q.quiz_id, q.title, q.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('quiz'), 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->where('q.quiz_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['quiz_id']))
		{
			return false;
		}
		
		$sPhrase = 'Your quiz "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '" has been approved.';
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('quiz', $aRow['quiz_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
			'no_profile_image' => true
		);			
	}	
	
	public function getActivityFeed($aRow)
	{
		$aRow = $this->database()->select('q.*, l.like_id AS is_liked,' . Phpfox::getUserField())
			->from(Phpfox::getT('quiz'), 'q')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'quiz\' AND l.item_id = q.quiz_id AND l.user_id = ' . Phpfox::getUserId())
			->where('q.quiz_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['quiz_id']))
		{
			return false;
		}
		
		$aReturn = array(
			'feed_title' => $aRow['title'],
			'feed_info' => Phpfox::getPhrase('feed.created_a_quiz'),
			'feed_link' => Phpfox::permalink('quiz', $aRow['quiz_id'], $aRow['title']),
			'feed_content' => $aRow['description'],
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/quiz.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'quiz',
			'like_type_id' => 'quiz'
		);
		
		if (!empty($aRow['image_path']))
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['server_id'],
					'path' => 'quiz.url_image',
					'file' => $aRow['image_path'],
					'suffix' => '_' . Phpfox::getParam('quiz.quiz_max_image_pic_size'),
					'max_width' => Phpfox::getParam('quiz.quiz_max_image_pic_size'),
					'max_height' => Phpfox::getParam('quiz.quiz_max_image_pic_size')					
				)
			);			
			
			$aReturn['feed_image'] = $sImage;
			$aReturn['feed_custom_width'] = '78px';
		}
		
		return $aReturn;
	}	

	public function tabHasItems($iUser)
	{
		$iCount = $this->database()->select('COUNT(user_id)')
				->from($this->_sTable)
				->where('user_id = ' . (int)$iUser)
				->execute('getSlaveField');
		return $iCount > 0;
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_quiz']))
			{
				return false;
			}

			if (isset($aUser['total_quiz']) && (int) $aUser['total_quiz'] === 0)
			{
				return false;
			}
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.quizzes'),
			'url' => 'profile.quiz',
			'total' => (int) (isset($aUser['total_quiz']) ? $aUser['total_quiz'] : 0),
			'icon' => 'feed/quiz.png'
		);	
		
		return $aMenus;
	}	
	
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_quiz',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('quiz'))->where('view_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
		);	
	}		
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.quiz_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'quiz\' AS item_type_id, \'\' AS item_photo, \'\' AS item_photo_server')
			->from(Phpfox::getT('quiz'), 'item')
			->where('item.view_id = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch) . ' AND item.privacy = 0')
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('quiz', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = 'Quiz';
		
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => 'Quizzes'
		);
	}	
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'quiz.default_privacy_setting' => array(
				'phrase' => Phpfox::getPhrase('user.quizzes')								
			)
		);
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
		if ($sPlugin = Phpfox_Plugin::get('quiz.service_callback__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>