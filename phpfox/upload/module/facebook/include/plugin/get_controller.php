<?php

if (Phpfox::getParam('facebook.enable_facebook_connect') && !Phpfox::isAdminPanel())
{
	if (Phpfox::isUser())
	{
		if (Phpfox::getUserBy('fb_user_id'))
		{
			$oTpl->setHeader(array(
					'<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>',							
					'<script type="text/javascript">
						$(function()
						{
			   				FB.init(
			   				{
				     			appId  : \'' . Phpfox::getParam('facebook.facebook_app_id') . '\',
				     			status : true,
				     			cookie : true,
				     			xfbml  : true 
			   				});
			   				
							FB.getLoginStatus(function(response) 
							{
			   					if (!response.session) 
			   					{
			   						window.location.href = \'' . Phpfox::getLib('url')->makeUrl('user.logout') . '\';
			   					}
							});
		   				});
					</script>')
				);
		}
		else
		{
			$oTpl->setHeader(array(
					'<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>',							
					'<script type="text/javascript">
						$(function()
						{
			   				FB.init(
			   				{
				     			appId  : \'' . Phpfox::getParam('facebook.facebook_app_id') . '\',
				     			status : true,
				     			cookie : true,
				     			xfbml  : true 
			   				});			   			
		   				});
					</script>')
				);			
		}
	}
	else 
	{
		if (Phpfox::getLib('request')->get('req1') == 'facebook' && Phpfox::getLib('request')->get('req2') == 'frame')
		{
			
		}
		elseif (Phpfox::getLib('request')->get('req1') == 'facebook' && Phpfox::getLib('request')->get('req2') == 'logout')
		{
			
		}		
		elseif (Phpfox::getLib('request')->get('req1') == 'facebook' && Phpfox::getLib('request')->get('req2') == 'account')
		{
			
		}
		elseif (!empty($_REQUEST['facebook-process-login']))
		{
			
		}
		else 
		{
			$oTpl->setHeader(array(
					'<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>',							
					'<script type="text/javascript">
						$(function()
						{
			   				FB.init(
			   				{
				     			appId  : \'' . Phpfox::getParam('facebook.facebook_app_id') . '\',
				     			status : true,
				     			cookie : true,
				     			xfbml  : true 
			   				});
		   				});						
		   				
		   				FB.Event.subscribe(\'auth.login\', function(response) 
		   				{
							if (response.session) 
							{
								$(\'body\').html(\'<div id="fb-root"></div><div id="facebook_connection">Connecting to Facebook. Please hold...</div>\');
								window.location.href = \'' . Phpfox::getLib('url')->makeUrl('facebook.frame') . '\';
							}			
		      			});		      		
						
					</script>')
				);
		}
	}
}
else
{
	if (Phpfox::isUser() && !Phpfox::isAdminPanel())
	{
		$oTpl->setHeader(array(
				'<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>',							
				'<script type="text/javascript">
					$(function()
					{
		   				FB.init(
		   				{
			     			appId  : \'' . Phpfox::getParam('facebook.facebook_app_id') . '\',
			     			status : true,
			     			cookie : true,
			     			xfbml  : true 
		   				});			   			
	   				});
				</script>')
			);	
	}
}
?>