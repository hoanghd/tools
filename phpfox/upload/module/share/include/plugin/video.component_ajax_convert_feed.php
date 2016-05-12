<?php
$aFeed = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iFeedId);

if (isset($aFeed[0]) && isset($aFeed[0]['feed_id']))
{
	if ($this->get('facebook_connection') == '1')
	{
		$this->call("FB.api('/me/feed', 'post', {link: '" . $aFeed[0]['feed_link'] . "', message: '" . str_replace('\'', '\\\'', html_entity_decode($aFeed[0]['feed_content'], null, 'UTF-8')) . "'}, function(response){});");		
	}

	if ($this->get('twitter_connection') == '1')
	{		
		Phpfox::getLib('twitter')->post(html_entity_decode($aFeed[0]['feed_content'], null, 'UTF-8') . ' ' . $aFeed[0]['feed_link']);
	}
}
?>