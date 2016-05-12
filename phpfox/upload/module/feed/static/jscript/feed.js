var $sFormAjaxRequest = null;
var $bButtonSubmitActive = true;
var $ActivityFeedCompleted = {};
var $sCurrentSectionDefaultPhrase = null;
var $sCssHeight = '20px';	
var $sCustomPhrase = null;
var $sCurrentForm = null;
var $sStatusUpdateValue = null;
var $iReloadIteration = 0;

$Core.isInView = function(elem)
{
    if (!$Core.exists(elem)){
		return false;
	}
	
	var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

	return ((docViewTop < elemTop) && (docViewBottom > elemBottom));
}
	
$Core.resetActivityFeedForm = function()
{		
	$('.activity_feed_form_attach li a').removeClass('active');
	$('.activity_feed_form_attach li a:first').addClass('active');	
	$('.global_attachment_holder_section').hide();
	$('#global_attachment_status').show();		
	$('.global_attachment_holder_section textarea').val($('#global_attachment_status_value').html()).css({height: $sCssHeight});
		
	$('.activity_feed_form_button_status_info').hide();
	$('.activity_feed_form_button_status_info textarea').val('');	
	
	$Core.resetActivityFeedErrorMessage();

	$sFormAjaxRequest = $('.activity_feed_form_attach li a.active').find('.activity_feed_link_form_ajax').html();
		
	$Core.activityFeedProcess(false);
	
	$('.js_share_connection').val('0');
	$('.feed_share_on_item a').removeClass('active');
		
	$.each($ActivityFeedCompleted, function()
	{
		this(this);
	});
}

$Core.resetActivityFeedErrorMessage = function()
{
	$('#activity_feed_upload_error').hide();
	$('#activity_feed_upload_error_message').html('');	
}

$Core.resetActivityFeedError = function(sMsg)
{
	$('.activity_feed_form_share_process').hide();
	$('.activity_feed_form_button .button').removeClass('button_not_active');
	$bButtonSubmitActive = true;
	$('#activity_feed_upload_error').show();
	$('#activity_feed_upload_error_message').html(sMsg);
}
	
$Core.activityFeedProcess = function($bShow)
{
	if ($bShow)
	{
		$bButtonSubmitActive = false;
		$('.activity_feed_form_share_process').show();
		$('.activity_feed_form_button .button').addClass('button_not_active');
	}
	else
	{
		$bButtonSubmitActive = true;
		$('.activity_feed_form_share_process').hide();
		$('.activity_feed_form_button .button').removeClass('button_not_active');
		$('.egift_wrapper').hide();			
	}
}

$Core.addNewPollOption = function()
{
	$('.js_poll_feed_answer').append('<li><input type="text" name="val[answer][][answer]" value="" size="30" class="js_feed_poll_answer v_middle" /></li>');
	
	return false;
}

$(function()
{
	/**
	 * Lost focus on the text area so let us close it.
	 */
	$('body').click(function()
	{		
		$('.js_comment_feed_textarea').each(function()
		{
			if ($(this).hasClass('is_focus'))
			{
				$(this).removeClass('is_focus');
			}
			else
			{			
				if (empty($(this).val()))
				{
					$(this).removeClass('js_comment_feed_textarea_focus');
					$(this).val($('.js_comment_feed_value').html());
					if (!$(this).parents('.comment_mini:first').hasClass('feed_item_view'))
					{
						$(this).parents('.comment_mini:first').find('.comment_mini_textarea_holder').removeClass('comment_mini_content');
						$(this).parents('.comment_mini:first').find('.comment_mini_image').hide();					
					}
				}			
				
				$(this).parents('.comment_mini').find('.feed_comment_buttons_wrap').hide();
			}
		});		
	});	
});

$Core.forceLoadOnFeed = function()
{
	if ($iReloadIteration >= 2){
		return;
	}
	
	$iReloadIteration++;
	$('#feed_view_more_loader').show();
	$('.global_view_more').hide();

	setTimeout("$.ajaxCall('feed.viewMore', $('#js_feed_pass_info').html().replace('&amp;', '&'), 'GET');", 1000);
}

	$Behavior.activityFeedProcess = function()
	{				
		if (!$Core.exists('#js_feed_content')){
			$iReloadIteration = 0;
			return;
		}
		
		if ($Core.exists('.global_view_more')){
			if ($Core.isInView('.global_view_more')){
				$Core.forceLoadOnFeed();
			}

			$(window).scroll(function(){
				if ($Core.isInView('.global_view_more')){
					$Core.forceLoadOnFeed();
				}			
			});							
		}		
		
		$sFormAjaxRequest = $('.activity_feed_form_attach li a.active').find('.activity_feed_link_form_ajax').html();
		
		$('#global_attachment_status textarea').keydown(function(){$Core.resizeTextarea($(this));});
		$('.activity_feed_form_button_status_info textarea').keydown(function(){$Core.resizeTextarea($(this));});
		
		$('#global_attachment_status textarea').focus(function()
		{
			if ($(this).val() == $('#global_attachment_status_value').html())
			{
				$(this).val('');
				$(this).css({height: '50px'});
				$('.activity_feed_form_button').show();
				$(this).addClass('focus');
				$('.activity_feed_form_button_status_info textarea').addClass('focus');
			}
		});
		
		$('.activity_feed_form_button_status_info textarea').focus(function()
		{		
			var $sDefaultValue = $(this).val();
			var $bIsDefault = true;			
			
			$('.activity_feed_extra_info').each(function()
			{
				if ($(this).html() == $sDefaultValue)
				{
					$bIsDefault = false;	
					
					return false;
				}
			});
			
			if (($('#global_attachment_status textarea').val() == $('#global_attachment_status_value').html() && empty($sDefaultValue)) || !$bIsDefault)
			{
				$(this).val('');
				$(this).css({height: '50px'});
				
				$(this).addClass('focus');
				$('#global_attachment_status textarea').addClass('focus');				
			}
		});
		
		$('#js_activity_feed_form').submit(function()
		{		
			if ($sCurrentForm == 'global_attachment_status'){
				var oStatusUpdateTextareaFilled = $('#global_attachment_status textarea');
				
				if ($sStatusUpdateValue == oStatusUpdateTextareaFilled.val()){
					oStatusUpdateTextareaFilled.val('');
				}
			}
			else{
				var oCustomTextareaFilled = $('.activity_feed_form_button_status_info textarea');
			
				if ($sCustomPhrase == oCustomTextareaFilled.val()){
					oCustomTextareaFilled.val('');				
				}				
			}			
			
			if ($bButtonSubmitActive === false)
			{
				return false;
			}
			
			$Core.activityFeedProcess(true);
			
			if ($sFormAjaxRequest === null)
			{
				return true;
			}
			
			$('.js_no_feed_to_show').remove();
			
			$(this).ajaxCall($sFormAjaxRequest);
			
			return false;
		});
		
		$('.activity_feed_form_attach li a').click(function()
		{			
			$('#activity_feed_upload_error').hide();
			
			$('.global_attachment_holder_section').hide();
			$('.activity_feed_form_attach li a').removeClass('active');
			$(this).addClass('active');
			
			if ($(this).find('.activity_feed_link_form').length > 0)
			{
				$('#js_activity_feed_form').attr('action', $(this).find('.activity_feed_link_form').html()).attr('target', 'js_activity_feed_iframe_loader');
				$sFormAjaxRequest = null;
				if (empty($('.activity_feed_form_iframe').html()))
				{
					$('.activity_feed_form_iframe').html('<iframe id="js_activity_feed_iframe_loader" name="js_activity_feed_iframe_loader" height="200" width="500" frameborder="1" style="display:none;"></iframe>');
				}				
			}
			else
			{
				$sFormAjaxRequest = $(this).find('.activity_feed_link_form_ajax').html();	
			}			
			
			$sCurrentForm = $(this).attr('rel');
			
			$('#' + $(this).attr('rel')).show();
			$('.activity_feed_form_holder_attach').show();
			$('.activity_feed_form_button').show();			
			
			var $oStatusUpdateTextarea = $('#global_attachment_status textarea');
			var $sStatusUpdateTextarea = $oStatusUpdateTextarea.val();
			$sStatusUpdateValue = $('#global_attachment_status_value').html();
			
			var $oCustomTextarea = $('.activity_feed_form_button_status_info textarea');
			var $sCustomTextarea = $oCustomTextarea.val();
			
			$sCustomPhrase = $(this).find('.activity_feed_extra_info').html();
			
			var $bHasDefaultValue = false;
			$('.activity_feed_extra_info').each(function()
			{
				if ($(this).html() == $sCustomTextarea)
				{
					$bHasDefaultValue = true;	
						
					return false;
				}
			});				
			
			if ($(this).attr('rel') != 'global_attachment_status')
			{
				$('.activity_feed_form_button_status_info').show();				
				
				if ((empty($sCustomTextarea) && ($sStatusUpdateTextarea == $sStatusUpdateValue 
					|| empty($sStatusUpdateTextarea))) 
					|| ($sStatusUpdateTextarea == $sStatusUpdateValue && $bHasDefaultValue)
					|| (!$bButtonSubmitActive && $bHasDefaultValue)
				)
				{
					$oCustomTextarea.val($sCustomPhrase).css({height: $sCssHeight});
				}
				else if ($sStatusUpdateTextarea != $sStatusUpdateValue && $bButtonSubmitActive && !empty($sStatusUpdateTextarea))
				{
					$oCustomTextarea.val($sStatusUpdateTextarea);
				}								
				
				$('.activity_feed_form_button .button').addClass('button_not_active');
				$bButtonSubmitActive = false;				
			}
			else
			{
				$('.activity_feed_form_button_status_info').hide();
				$('.activity_feed_form_button .button').removeClass('button_not_active');
				
				if (!$bHasDefaultValue && !empty($sCustomTextarea))
				{
					$oStatusUpdateTextarea.val($sCustomTextarea);
				}
				else if ($bHasDefaultValue && empty($sStatusUpdateTextarea))
				{
					$oStatusUpdateTextarea.val($sStatusUpdateValue).css({height: $sCssHeight});
				}				
							
				$bButtonSubmitActive = true;
			}
			
			if ($(this).hasClass('no_text_input'))
			{
				$('.activity_feed_form_button_status_info').hide();
			}		
				
			return false;
		});		
	}

$Behavior.activityFeedLoader = function()
{			
	/**
	 * Click on adding a new comment link.
	 */
	$('.js_feed_entry_add_comment').click(function()
	{			
		$('.js_comment_feed_textarea').each(function()
		{
			if ($(this).val() == $('.js_comment_feed_value').html())
			{
				$(this).removeClass('js_comment_feed_textarea_focus');
				$(this).val($('.js_comment_feed_value').html());
			}			
				
			$(this).parents('.comment_mini').find('.feed_comment_buttons_wrap').hide();
		});				
			
		$(this).parents('.js_parent_feed_entry:first').find('.comment_mini_content_holder').show();
		$(this).parents('.js_parent_feed_entry:first').find('.feed_comment_buttons_wrap').show();
			
		if ($(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').val() == $('.js_comment_feed_value').html())
		{
			$(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').val('');
		}		
		$(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea').focus().addClass('js_comment_feed_textarea_focus');
		$(this).parents('.js_parent_feed_entry:first').find('.comment_mini_textarea_holder').addClass('comment_mini_content');
		$(this).parents('.js_parent_feed_entry:first').find('.js_feed_comment_form').find('.comment_mini_image').show();
			
		var iTotalComments = 0;
		$(this).parents('.js_parent_feed_entry:first').find('.js_mini_feed_comment').each(function()
		{
			iTotalComments++;
		});
			
		if (iTotalComments > 2)
		{
			$.scrollTo($(this).parents('.js_parent_feed_entry:first').find('.js_comment_feed_textarea_browse:first'), 340);
		}
			
		return false;
	});	
	
	/**
	 * Comment textarea on focus.
	 */
	$('.js_comment_feed_textarea').click(function()
	{
		$Core.commentFeedTextareaClick(this);
	});		
	
	$('.js_comment_feed_form').submit(function()
	{			
		if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) 
		{
			eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
		}		
		
		// $(this).parents('.js_parent_feed_entry:first').find('.js_feed_add_comment_button:first').hide(); 		
		$(this).parents('.js_parent_feed_entry:first').find('.js_feed_comment_process_form:first').show(); 
		$(this).parents('.js_parent_feed_entry:first').find('.js_feed_comment_process_form').show();
		$(this).ajaxCall('comment.add'); 
			
		return false;		
	});
}

$Core.commentFeedTextareaClick = function($oObj)
{
	$($oObj).keydown(function()
	{
		$Core.resizeTextarea($(this));
	});
		
	if ($($oObj).val() == $('.js_comment_feed_value').html())
	{
		$($oObj).val('');
	}
					
	$($oObj).addClass('js_comment_feed_textarea_focus').addClass('is_focus');
	$($oObj).parents('.comment_mini').find('.feed_comment_buttons_wrap').show();
			
	$($oObj).parents('.js_parent_feed_entry:first').find('.comment_mini_textarea_holder').addClass('comment_mini_content');
	$($oObj).parents('.js_parent_feed_entry:first').find('.js_feed_comment_form').find('.comment_mini_image').show();	
}
						
$Behavior.activityFeedAttachLink = function()
{	
	$('#js_global_attach_link').click(function()
	{	
		$Core.activityFeedProcess(true);
		
		$Core.ajax('link.preview', 
		{		
			params: 
			{				
				'no_page_update': '1',
				value: $('#js_global_attach_value').val()
			},
			success: function($sOutput)
			{
				$('#js_global_attachment_link_cancel').show();
				
				if (substr($sOutput, 0, 1) == '{'){					
					var $oOutput = $.parseJSON($sOutput);
					$Core.resetActivityFeedError($oOutput['error']);
					$bButtonSubmitActive = false;
					$('.activity_feed_form_button .button').addClass('button_not_active');
				}
				else{
					$Core.activityFeedProcess(false);

					$('#js_preview_link_attachment').html($sOutput);
					$('#global_attachment_link_holder').hide();				
				}
			}
		});
	});
}

$ActivityFeedCompleted.link = function()
{
	$bButtonSubmitActive = true;
	
	$('#global_attachment_link_holder').show();	
	$('.activity_feed_form_button .button').removeClass('button_not_active');	
	$('#js_preview_link_attachment').html('');			
	$('#js_global_attach_value').val('http://');
}

$ActivityFeedCompleted.photo = function()
{
	$bButtonSubmitActive = true;
	
	$('#global_attachment_photo_file_input').val('');
}