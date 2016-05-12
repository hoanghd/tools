var $Core = {};
var $Behavior = {};
var $Cache = {};
var $oEventHistory = {};
var $oStaticHistory = {};
var $bDocumentIsLoaded = false;

$.fn.message = function(sMessage, sType) 
{
	switch(sType)
	{
		case 'valid':
			sClass = 'valid_message';
			break;
		case 'error':
			sClass = 'error_message';
			break;
		case 'public':
			sClass = 'public_message';
			break;
	}
	this.html(this.html() + '<div class="' + sClass + '">' + sMessage + '</\div>');
	
	return this;
}

$.getParams = function(sUrl)
{
	var aArgs = sUrl.split('#');
	var aArgsFinal = aArgs[1].split('?');	
	var aFinal = aArgsFinal[1].split('&');
	
	var aUrlParams = Array();

	for (i = 0; i < aFinal.length; i++)
	{
		var aArg = aFinal[i].split('=');	
		
		aUrlParams[aArg[0]] = aArg[1];
	}
	
	return aUrlParams;
}

$.ajaxProcess = function(sMessage, sSize)
{
	sMessage = (sMessage ? sMessage : getPhrase('core.processing'));
	
	if (empty(sSize))
	{
		sSize = 'small';
	}	
	
	return '<span style="margin-left:4px; margin-right:4px; font-size:9pt; font-weight:normal;"><img src="' + eval('oJsImages.ajax_' + sSize + '') + '" class="v_middle" /> ' + (sMessage === 'no_message' ? '' : sMessage + '...') + '</span>';
}

$Behavior.imageHoverHolder = function()
{
	$('body').click(function(){
		$('.image_hover_menu_link').each(function() {
			if ($(this).hasClass('image_hover_active'))
			{
				$(this).removeClass('image_hover_active');
				$(this).parent().find('.image_hover_menu:first').hide();
				$(this).hide();
			}				
		});
	});
	
	$('.image_hover_holder').mouseover(function()
	{		
		if (!empty($(this).find('.image_hover_menu:first').html()))
		{
			$(this).addClass('image_hover_holder_hover').find('.image_hover_menu_link:first').show();
		}
	});
	
	$('.image_hover_holder').mouseout(function()
	{
		if (!$(this).find('.image_hover_menu_link').hasClass('image_hover_active'))
		{
			$(this).removeClass('image_hover_holder_hover').find('.image_hover_menu_link:first').hide();
		}
	});
	
	$('.image_hover_menu_link').click(function(){
		
		var oMenu = $(this).parent().find('.image_hover_menu:first');
		
		if ($(this).hasClass('image_hover_active'))
		{
			$(this).removeClass('image_hover_active');
			
			oMenu.hide();
			
			return false;
		}
		
		$('.image_hover_menu_link').each(function() {
			if ($(this).hasClass('image_hover_active'))
			{
				$(this).removeClass('image_hover_active');
				$(this).parent().find('.image_hover_menu:first').hide();
				$(this).hide();
			}				
		});
		
		$(this).addClass('image_hover_active');
		
		oMenu.show();
		
		return false;
	});
}

$Behavior.targetBlank = function()
{
	$('.targetBlank').click(function()
	{
		window.open($(this).get(0).href);
		return false;
	});
}

var bCacheIsHover = false;
$Behavior.dropDown = function()
{
	$('.sJsDropMenu').click(function()
	{
		$(this).blur();
		$('.dropContent').hide();
		$('.sub_menu_bar li a').removeClass('is_already_open');	
		
		if ($(this).hasClass('is_already_open'))
		{
			$(this).parent().find('.dropContent:first').hide();
			$(this).removeClass('is_already_open');
		}
		else
		{
			$(this).parent().find('.dropContent:first').show();	
			$(this).addClass('is_already_open');	
		}
			
		return false;
	});	
	
	$('.dropContent').mouseover(function(){
		bCacheIsHover = true;
	});
	
	$('.dropContent').mouseout(function(){
		bCacheIsHover = false;
	});	
	
	$('body').click(function()
	{
		if (!bCacheIsHover){		
			$('.dropContent').hide();
			$('.sub_menu_bar li a').each(function(){
				if ($(this).hasClass('is_already_open')){
					$(this).removeClass('is_already_open')
				}
			});
		}
	});
}

/**
 * Drop down auto jump
 */
$Behavior.goJump = function()
{
	$('.goJump').change(function()
	{
		// Empty value, do nothing
		if ($(this).get(0).value == "")
		{
			return false;
		}		
		
		// Is this a delete link? If it is make sure they confirm they want to delete the item
		if ($(this).get(0).value.search(/delete/i) != -1 && !confirm(getPhrase('core.are_you_sure')))
		{
			return false;
		}		
		
		// All set lets send them to the new page
		window.location.href = $(this).get(0).value;
	});
}

$Behavior.inlinePopup = function()
{
	$('.inlinePopup').click(function()
	{
		var $aParams = $.getParams($(this).get(0).href);
		var sParams = '&tb=true';
		for (sVar in $aParams)
		{			
			sParams += '&' + sVar + '=' + $aParams[sVar] + '';
		}
		sParams = sParams.substr(1, sParams.length);		
		
		tb_show($(this).get(0).title, $.ajaxBox($aParams['call'], sParams));		
		
		return false;
	});
}

$Behavior.blockClick = function()
{
	$('.block .menu ul li a').click(function()
	{
		$(this).parents('.block:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		
		if (this.href.match(/#/))
		{
			var aParts = explode('#', this.href);
			var aParams = explode('?', aParts[1]);
			var aParamParts = explode('&', aParams[1]);
			var aRequest = Array();
			for (i in aParamParts)
			{
				var aPart = explode('=', aParamParts[i]);
				
				aRequest[aPart[0]] = aPart[1];
			}			

			$('.js_block_click_lis_cache').remove();
			$(this).parents('.menu:first').find('ul').append('<li class="js_block_click_lis_cache" style="margin-top:2px;">' + $.ajaxProcess('no_message') + '</li>');
			$.ajaxCall(aParams[0], aParams[1] + '&js_block_click_lis_cache=true', 'GET');			
		}		
		
		return false;
	});
}

$Behavior.deleteLink = function()
{
	$('.delete_link').click(function()
	{
		if (confirm(getPhrase('core.are_you_sure')))
		{
			$aParams = $.getParams($(this).get(0).href);
			var sParams = '';
			for (sVar in $aParams)
			{			
				sParams += '&' + sVar + '=' + $aParams[sVar] + '';
			}
			sParams = sParams.substr(1, sParams.length);
				
			$.ajaxCall($aParams['call'], sParams);
		}
				
		return false;			
	});	
}

$Behavior.globalToolTip = function()
{
	if ($('#js_global_tooltip').length <= 0)
	{
		$('body').prepend('<div id="js_global_tooltip" style="display:none;"></div>');		
	}
	
	$('.js_hover_title').mouseover(function()
	{
		var offset = $(this).offset();					
		var sContent = '';
		
		$('#js_global_tooltip').css('display', 'block');	
		$('#js_global_tooltip').css('left', (offset.left - 10) + 'px');
		
		if ($(this).find('.js_hover_info').length > 0)
		{
			sContent = $(this).find('.js_hover_info').html();
		}
		else
		{
			var oParent = $(this).parent();			
			
			if (!empty(oParent.attr('title')))
			{
				oParent.data('title', oParent.attr('title')).removeAttr('title');
			}
			
			sContent = oParent.data('title');
		}
		
		$('#js_global_tooltip').html('<div id="js_global_tooltip_display">' + sContent + '</div>');
		$('#js_global_tooltip').css('top', (offset.top - ($('#js_global_tooltip_display').height() + 10)) + 'px');
	});
	
	$('.js_hover_title').mouseout(function()
	{		
		$('#js_global_tooltip').hide()
			.html('')
			.css('top', '0px')
			.css('left', '0px');
	});
}

$Behavior.clearTextareaValue = function()
{
	$('.js_comment_text_area #text').focus(function()
	{
		if ($(this).val() == $('#js_comment_write_phrase').html())
		{
			$(this).val('');
		}
	});
}

$Behavior.loadEditor = function()
{
	if ((!getParam('bWysiwyg') || typeof(bForceDefaultEditor) != 'undefined') && typeof(Editor) == 'object')
	{
		Editor.getEditors();
	}	
}

var sMoreFeedIds = {};
$Core.loadMoreFeeds = function(){
    $.ajaxCall('feed.appendMore', 'ids=' + sMoreFeedIds, 'GET');
    
    return false;
}

$Core.rebuildActivityFeedCount = function(iTotal, sIds){
	
	sMoreFeedIds = sIds;
	
    $('.activity_feed_updates_link').hide();    
    if (iTotal){
        $('#activity_feed_updates_link_holder').show();
        if (iTotal == 1){
            $('#activity_feed_updates_link_single').show();
        }
        else{
            $('#activity_feed_updates_link_plural').show();
            $('#js_new_update_view').html(iTotal);            
        }        
    }  
	else{
		$('#activity_feed_updates_link_holder').hide();
	}
}

$Core.reloadActivityFeed = function(){
	setTimeout("$.ajaxCall('feed.reloadActivityFeed', 'reload-ids=' + $Core.getCurrentFeedIds(), 'GET');", 2000);
}

$Core.getCurrentFeedIds = function()
{
	var sMoreFeedIds = '';
	$('.js_parent_feed_entry').each(function(){
		sMoreFeedIds += $(this).attr('id').replace('js_item_feed_', '') + ',';				
	});	
	
	return sMoreFeedIds;
}

$Core.processForm = function(sSelector, bReset)
{
	if (bReset === true)
	{
		$(sSelector).find('.button:first').removeClass('button_off').attr('disabled', false);
		$(sSelector).find('.table_clear_ajax').hide();		
	}
	else
	{
		$(sSelector).find('.button:first').addClass('button_off').attr('disabled', true);
		$(sSelector).find('.table_clear_ajax').show();
	}
}

$Core.exists = function(sSelector)
{
	return ($(sSelector).length > 0 ? true : false);
}

$Core.searchFriends = function($aParams)
{	
	if (typeof($Core.searchFriendsInput) == 'undefined'){
		return;
	}
	$Core.searchFriendsInput.init($aParams);
}

$Core.loadStaticFile = function($aFiles)
{
	$Core.loadStaticFiles($aFiles);	
}

$Core.loadStaticFiles = function($aFiles)
{	
	if (typeof($aFiles) == 'string')
	{
		$aFiles = new Array($aFiles);	
	}
	
	if (!$bDocumentIsLoaded)
	{
		if (!isset($Cache['post_static_files']))
		{
			$Cache['post_static_files'] = new Array();
		}
		
		$Cache['post_static_files'].push($aFiles);	
		
		return;
	}	
	
	$($aFiles).each(function($sKey, $sFile)
	{		
		if (!isset($oStaticHistory[$sFile]))
		{
			$oStaticHistory[$sFile] = true;
			if (substr($sFile, -3) == '.js')
			{
				$('head').append('<script type="text/javascript" src="' + $sFile + '?v=' + getParam('sStaticVersion') + '"></script>');
			}
			else if (substr($sFile, -4) == '.css')
			{
				$('head').append('<link rel="stylesheet" type="text/css" href="' + $sFile + '?v=' + getParam('sStaticVersion') + '" />');
			}
			else
			{
				eval($sFile);				
			}
		}
	});		
}

$Behavior.globalInit = function()
{
	// Confirm before deleting an item
	$('.sJsConfirm').click(function()
	{
		if (confirm(getPhrase('core.are_you_sure')))
		{
			return true;
		}
		return false;
	});
	
	$('#select_lang_pack').click(function()
	{
		tb_show(oTranslations['core.language_packages'], $.ajaxBox('language.select', 'height=300&amp;width=300'));
		
		return false;
	});	
	
	if (!oCore['core.is_admincp'])
	{
		if ($('#country_iso').length > 0 && !empty(oCore['core.country_iso']))
		{			
			if (empty($('#country_iso').val()))
			{
				$('#js_country_iso_option_' + oCore['core.country_iso']).attr('selected', true);			
			}	
		}
	}
	
    $('.js_item_active').click(function()
    {    	
    	$(this).parent().find('.js_item_active input').attr('checked', false);
    	if ($(this).hasClass('item_is_active'))
    	{
    		$(this).parent().find('.item_is_active input').attr('checked', true);
    	}
    	else
    	{
    		$(this).parent().find('.item_is_not_active input').attr('checked', true);
    	}
    }); 
    
    if ($('.moderate_link').length > 0)
	{    
    	$('.moderation_drop').click(function() 
    	{    		
    		if (parseInt($('.js_global_multi_total').html()) === 0)
    		{
    			return false;
    		}
    	
    		if ($(this).hasClass('is_clicked'))
    		{
    			$('.moderation_holder ul').hide();
    			$(this).removeClass('is_clicked');
    		}
    		else
    		{
    			$('.moderation_holder ul').show();
    			$('.moderation_holder ul').css({'margin-top': '-' + ($(this).height() + $('.moderation_holder ul').height() + 4) + 'px'});
    			$(this).addClass('is_clicked');    			
    		}
    	
    		return false;
    	});
    
    	var iEmptyModLinks = 0;
    	$('.moderate_link').each(function()
    	{    		
    		var sName = 'js_item_m_' + $(this).attr('rel') + '_' + $(this).attr('href').replace('#', '');
    		if (getCookie(sName))
			{
     			$(this).addClass('moderate_link_active');     		
			}
    		else
    		{
    			iEmptyModLinks++;
    		}
    	});
    	
    	if (iEmptyModLinks === 0)
    	{
    		$('.moderation_action_unselect').show();
    		$('.moderation_action_select').hide();
    	}
    }
    
    $('.moderation_process_action').click(function()
    {		
		if ($(this).attr('rel') == 'mail.moderation' && $(this).attr('href').replace('#', '') == 'move'){
			$Core.box('mail.listFolders', 400);
		}
		else{
			$('.moderation_process').show();
			$('#js_global_multi_form_holder').ajaxCall($(this).attr('rel'), 'action=' + $(this).attr('href').replace('#', ''));			
			$Core.moderationLinkClear();
		}    	
		
    	return false;
    });
    
    $('.moderation_clear_all').click(function()
    {
    	$Core.moderationLinkClear();

    	return false;
    });
    
    $('.moderation_action').click(function()
    {    	
    	var sType = $(this).attr('rel');
    	
    	$(this).hide();
    	
    	if (sType == 'select')
    	{
	    	$('.moderation_action_unselect').show();	    	
    	}
    	else
    	{
			$('.moderation_action_select').show();
    	}
    	
	    $('.moderate_link').each(function()
	    {
			$Core.moderationLinkClick(this, sType);
	    });    	
    	
    	return false;
    });
    
    $('.moderate_link').click(function()
    {
    	return $Core.moderationLinkClick(this);
    });
    
    $('.page_section_menu ul li a').click(function()
    {
		var sRel = $(this).attr('rel');
    	if (empty(sRel))
    	{
			return true;		
		}
    	$('.page_section_menu ul li').removeClass('active');
    	$('.page_section_menu_holder').hide();
    	$('#' + sRel).show();
    	$(this).parent().addClass('active');
    	
    	if ($('#page_section_menu_form').length > 0)
    	{
    		$('#page_section_menu_form').val(sRel);
    	}
    		
    	return false;
    });
    
    if ($('.js_date_picker').length > 0)
    {
		$('.js_date_picker').datepicker('destroy');
		$('.js_date_picker').datepicker(
		{			
			minDate: new Date(oParams['user.date_of_birth_start'], new Date().getMonth(), new Date().getDate()), 
			maxDate: new Date(oParams['user.date_of_birth_end'], new Date().getMonth(), new Date().getDate()), 
			onSelect: function(dateText, inst) 
			{
				var aParts = explode('/', dateText);
				var sMonth = ltrim(aParts[0], '0');
				var sDay = ltrim(aParts[1], '0');

				$(this).parents('.js_datepicker_holder:first').find('.js_datepicker_month').val(sMonth);
				$(this).parents('.js_datepicker_holder:first').find('.js_datepicker_day').val(sDay);
				$(this).parents('.js_datepicker_holder:first').find('.js_datepicker_year').val(aParts[2]);
			}
		});
    }
	
	$('#js_login_as_page').click(function(){
		$Core.box('pages.login', 500);
		return false;
	});
	
	$('.mobile_view_options').click(function(){
		$('#js_mobile_form_holder').toggle();
		
		return false;
	});

	if ($.browser.msie && parseInt($.browser.version, 10) < 8 && !getParam('bJsIsMobile')){
		$('#pem').append('<div class="update_internet_explorer">You seem to be using an older version of Internet Explorer. This site requires Internet Explorer 8 or higher. Update your browser <a href="http://www.microsoft.com/ie/" target="_blank">here</a> today to fully enjoy all the marvels of this site.</div>');
	}
}

$Core.pageSectionMenuShow = function(sId)
{
	$('.page_section_menu_holder').hide();
	$('.page_section_menu ul li').removeClass('active');
	$(sId).show();
	$('.page_section_menu ul li a').each(function()
	{
		if ($(this).attr('rel') == sId.replace('#', ''))
		{
			$(this).parent().addClass('active');
			
			return false;
		}
	});
}

$Core.moderationLinkClear = function()
{
	var aCookies = document.cookie.split(';');
	$(aCookies).each(function(sKey, sValue)
	{
		if (sValue.match(/js_item_m/i))
		{
			var aParts = explode('=', sValue);
			
			deleteCookie(trim(aParts[0].replace(getParam('sJsCookiePrefix'), '')));
		}
	});
	
	$('.moderate_link').removeClass('moderate_link_active');
	$('#js_global_multi_form_ids').html('');
	$('.js_global_multi_total').html('0');
	$('.moderation_drop').addClass('not_active');
	$('.moderation_holder ul').hide();
	$('.moderation_action_unselect').hide();
	$('.moderation_action_select').show();	
}

$Core.moderationLinkClick = function(oObj, sType)
{
	var sName = 'js_item_m_' + $(oObj).attr('rel') + '_' + $(oObj).attr('href').replace('#', '');
	var iTotalItems = parseInt($('.js_global_multi_total').html());
	
	if (($(oObj).hasClass('moderate_link_active') && sType != 'select') || sType == 'unselect')
	{
		$(oObj).removeClass('moderate_link_active');
		$('#js_global_multi_form_ids').find('.' + sName).remove();
		deleteCookie(sName);
		iTotalItems--;
	}
	else
	{
		if (!$(oObj).hasClass('moderate_link_active'))
		{
			$(oObj).addClass('moderate_link_active');
			$('#js_global_multi_form_ids').append('<div class="' + sName + '"><input type="hidden" name="item_moderate[]" value="' + $(oObj).attr('href').replace('#', '') + '" /></div>');
			setCookie(sName, $(oObj).attr('rel') + '_' + $(oObj).attr('href').replace('#', ''), 1);
			iTotalItems++;
		}
	}    
	
	$('.js_global_multi_total').html(iTotalItems);
	
	if (iTotalItems)
	{
		$('.moderation_drop').removeClass('not_active');
	}
	else
	{
		$('.moderation_drop').addClass('not_active');
	}
	
	return false;	
}

$Behavior.privacySettingDropDown = function()
{	
	$('body').click(function()
	{
		$('.privacy_setting_active').each(function()
		{
			if ($(this).hasClass('is_active'))
			{
				$(this).parent().find('.privacy_setting_holder').hide();			
				$(this).removeClass('is_active');			
			}			
		});	
	});	
	
	$('.privacy_setting_active').click(function()
	{		
		var $oParent = $(this).parent().find('.privacy_setting_holder');
		
		if ($(this).hasClass('is_active'))
		{
			$oParent.hide();
			$(this).removeClass('is_active');
		}
		else
		{
			$('.privacy_setting_active').each(function()
			{
				if ($(this).hasClass('is_active'))
				{
					$(this).parent().find('.privacy_setting_holder').hide();			
					$(this).removeClass('is_active');			
				}			
			});					
			$oParent.show();
			$(this).addClass('is_active');
		}
		
		$('#js_global_tooltip').hide()
			.html('')
			.css('top', '0px')
			.css('left', '0px');		
		
		return false;
	});
		
	$('.privacy_setting_holder ul li a').click(function()
	{		
		var $oParent = $(this).parents('.privacy_setting_div:first').find('.privacy_setting_active');
		var $sContent = $(this).html();
		
		if ($sContent.toLowerCase().indexOf('<span>') > -1)
		{
			var $aParts = explode('<span>', $sContent);
			if (!isset($aParts[1]))
			{
				$aParts = explode('<SPAN>', $sContent);	
			}
			
			$sContent = $aParts[0];
		}		
		
		$oParent.html('' + $sContent + '<span class="js_hover_info">' + $sContent + '</span>');
		
		$(this).parents('.privacy_setting_div:first').find('.privacy_setting_holder').hide();
		$oParent.removeClass('is_active');
		
		$(this).parents('.privacy_setting_div:first').find('input').val($(this).attr('rel'));
		
		$('.privacy_setting_holder ul li a').removeClass('is_active_image');
		$(this).addClass('is_active_image');	
		
		return false;
	});	
}

var cacheShadownInfo = false;
var shadow = null;
var minHeight = null;
$Core.resizeTextarea = function(oObj)
{
	if (cacheShadownInfo === false)
	{	
		var lineHeight = oObj.css('lineHeight');
		minHeight = oObj.height();		
		cacheShadownInfo = true;
        shadow = $('<div></div>').css(
        {
			position:   'absolute',
			top:        -10000,
			left:       -10000,
			width:      oObj.width(),
			fontSize:   oObj.css('fontSize'),
			fontFamily: oObj.css('fontFamily'),
            lineHeight: oObj.css('lineHeight'),
			resize:     'none'
		}).appendTo(document.body);            
	}        
                
	var val = oObj.val().replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/&/g, '&amp;')
		.replace(/\n/g, '<br/>');
                
		shadow.html(val);
		oObj.css('height', Math.max(shadow.height() + 20, minHeight));              
}

$Core.getObjectPosition = function(sId) 
{
	if ($('#' + sId).length <= 0)
	{
		return false;
	}
	
	var curleft = 0;
    var curtop = 0;
    var obj = document.getElementById(sId);
    if (obj.offsetParent) 
    {
    	do 
    	{
        	curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
    
	return {left: curleft, top: curtop};
}

$Core.getFriends = function(aParams)
{
	tb_show('', $.ajaxBox('friend.search', 'height=410&width=600&input=' + aParams['input'] + '&type=' + (isset(aParams['type']) ? aParams['type'] : '') + ''));
}

$Core.browseUsers = function(aParams)
{
	tb_show('', $.ajaxBox('user.browse', 'height=410&width=600&input=' + aParams['input'] + ''));
}

$Core.composeMessage = function(aParams)
{
	if (aParams === undefined)
	{
		aParams = new Array();
	}
	
	tb_show('', $.ajaxBox('mail.compose', 'height=300&width=500' + (!isset(aParams['user_id']) ? '' : '&id=' + aParams['user_id']) + '&no_remove_box=true'));
}

$Core.addAsFriend = function(iUserId)
{
	tb_show('', $.ajaxBox('friend.request', 'width=420&user_id=' + iUserId + ''));
	
	return false;
}

$Core.getParams = function(sHref)
{
	var aParams = new Array();
	var aUrlParts = explode('/', sHref);
	var iRequest = 0;
	for (i in aUrlParts)
	{
		if (empty(aUrlParts[i]))
		{
			continue;
		}
			
		aUrlParts[i] = aUrlParts[i].replace('#', '');
		if (aUrlParts[i].match(/_/i))
		{
			var aUrlParams = explode('_', aUrlParts[i]);
				
			aParams[aUrlParams[0]] = aUrlParams[1];
		}
		else
		{
			iRequest++;			

			aParams['req' + iRequest] = aUrlParts[i];		
		}	
	}	
	
	return aParams;	
}

$Core.getRequests = function(sHref, bReturnPath)
{
	var sParams = '';	
	var sUrlString = '';
	var sModuleName = oCore['core.section_module'];	
	
	switch (oCore['core.url_rewrite'])
	{
		case '1':
			var oReq = new RegExp("" + getParam('sJsHome') + "(.*?)$","i");
			var aMatches = oReq.exec(sHref);
			var aParts = explode('/', aMatches[1]);
									
			sUrlString = '/' + aMatches[1];			
					
			break;
		case '3':
			if (oCore['profile.is_user_profile'])
			{
				var aProfileMatches = sHref.match(/http:\/\/(.*?)\.(.*?)/i);
				sModuleName = aProfileMatches[1];
			}		
		
			var oReq = new RegExp("" + oParams['sJsHome'] + "(.*?)$","i");
			var aMatches = oReq.exec(sHref);
			
			sUrlString = sModuleName + '/' + (aMatches != null && isset(aMatches[1]) ? aMatches[1] : '');		
			break;
		default:
			var oReq = new RegExp("(.*?)=\/(.*?)$","i");
			var aMatches = oReq.exec(sHref);	
			if (aMatches !== null)
			{		
				var aParts = explode('/', aMatches[2]);		
				
				sUrlString = aMatches[2];
			}
					
			break;
	}	
	
	if (bReturnPath === true)
	{
		return '/' + ltrim(sUrlString, '/');
	}	
	
	return $Core.parseUrlString(sUrlString);
}

$Core.parseUrlString = function(sUrlString)
{
	var sParams = '';
	var aUrlParts = explode('/', sUrlString);
	var iRequest = 0;
	var iLoadCount = 0;
	
	for (i in aUrlParts)
	{
		if (empty(aUrlParts[i]) || aUrlParts[i] == '#')
		{
			continue;
		}		
		
		iLoadCount++;
		
		if (iLoadCount != 1 && aUrlParts[i].match(/_/i))
		{
			var aUrlParams = explode('_', aUrlParts[i]);
				
			sParams += '&' + aUrlParams[0] + '=' + aUrlParams[1];	
		}
		else
		{
			iRequest++;
			
			sParams += '&req' + iRequest + '=' + aUrlParts[i];						
		}	
	}	
	
	return sParams;
}

$Core.reverseUrl = function(sForm, aSkip)
{	
	var aForms = explode('&', sForm);	
	var sUrlParam = '';	
	for (i in aForms)
	{			
		var aFormParts = aForms[i].match(/(.*?)=(.*?)$/i);
		if (aFormParts !== null)
		{			
			if (isset(aSkip))
			{				
				if (in_array(aFormParts[1], aSkip))
				{					
					continue;
				}
			}
				
			sUrlParam += aFormParts[1] + '_' + encodeURIComponent(aFormParts[2]) + '/';
		}
	}		
		
	return sUrlParam;
}

$Core.getHashParam = function(sHref)
{
	var sParams = '';
	var aParams = $.getParams(sHref);
	
	for (var sKey in aParams)
	{
		sParams += '&' + sKey + '=' + aParams[sKey];
	}
	sParams = ltrim(sParams, '&');
	
	return sParams;
}

$Core.box = function($sRequest, $sWidth, $sParams)
{
	tb_show('', $.ajaxBox($sRequest, 'width=' + $sWidth + ($sParams ? '&' + $sParams : '')));	
	
	return false;
}

$Core.ajax = function(sCall, $oParams)
{
	var sParams = '&' + getParam('sGlobalTokenName') + '[ajax]=true&' + getParam('sGlobalTokenName') + '[call]=' + sCall;
	
	if (!sParams.match(/\[security_token\]/i))
	{
		sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];
	}
	
	if (isset($oParams['params']))
	{
		if (typeof($oParams['params']) == 'string')
		{
			sParams += $oParams['params'];
		}
		else		
		{
			$.each($oParams['params'], function($sKey, $sValue)
			{
				sParams += '&' + $sKey + '=' + encodeURIComponent($sValue) + '';
			});
		}		
	}
	
	$.ajax(
	{
		type: (isset($oParams['type']) ? $oParams['type'] : 'GET'),
		url: getParam('sJsStatic') + "ajax.php",
		dataType: 'html',
		data: sParams,
		success: $oParams['success']
	});	
}

$Core.popup = function(sUrl, aParams)
{
	oDate = new Date();
	iId = oDate.getTime();
	var sParams = '';
	var iCount = 0;
	var bCenter = false;
	for (i in aParams)
	{
		if (i == 'center')
		{
			bCenter = true;
			continue;
		}
		
		iCount++;
		if (iCount != 1)
		{
			sParams += ',';
		}	
		
		sParams += i + '=' + aParams[i];
	}
	
	if (bCenter === true)
	{
		sParams += ',left=' + (($(window).width() - aParams['width']) / 2) + ',top=' + (($(window).height() - aParams['height']) / 2) + '';
	}
	
	window.open(sUrl, iId, sParams);
}

$Core.ajaxMessage = function()
{
	$('#global_ajax_message').html(getPhrase('core.saving')).animate({opacity: 0.9}).slideDown();
}

/**
 * Used for the accordion effect on sections with many categories
 */
$Core.toggleCategory = function(sName, iId)
{
    $('.' + sName).toggle();
    $('#show_more_' + iId).toggle();
    $('#show_less_' + iId).toggle();    
}

if (substr(window.location.hash, 0, 2) == '#!')
{
	if (oCore['core.url_rewrite'] == '1')
	{
		var sUrl = trim(getParam('sJsHome'), '/');
	}
	else
	{
		var sUrl = getParam('sJsHome') + 'index.php?' + getParam('sGetMethod') + '=';
	}
	
	window.location = sUrl + window.location.hash.replace('#!', '');	 
}

$Core.page = function($aParams)
{
	if (isset($aParams['phrases']))
	{
		for (sKey in $aParams['phrases'])
		{
			if (!isset(oTranslations[sKey]))
			{
				oTranslations[sKey] = $aParams['phrases'][sKey];
			}
		}
	}
	
	$('.js_user_tool_tip_holder').remove();
	
	$('#js_user_profile_css').remove();
	
	if (isset($aParams['profilecss'])){
		$('body').append($aParams['profilecss']);
	}		
	
	if (!empty($aParams['files']))
	{
		$Core.loadStaticFiles($aParams['files']);
	}
	
	if (isset($aParams['customcss'])){
		var sCustomCss = '';
		$('#js_global_custom_css').remove();
		for (sKey in $aParams['customcss']){
			sCustomCss += $aParams['customcss'][sKey];
		}
		if (!empty(sCustomCss)){
			// $('body').append()
		}
	}
	
	document.title = $aParams['title'];
	
	$('#main_content_holder').html('' + $aParams['content'] + '');
	/*
	if (isset($aParams['ads']))
	{
		for (sKey in $aParams['ads']){
			$('#js_ad_space_content_' + sKey).html($aParams['ads'][sKey]);
		}		
	}
	*/
	$('body').css('cursor', 'auto');
	
	$oEventHistory[($Core.hasPushState() ? $Core.getRequests(window.location, true) : window.location.hash.replace('#!', ''))] = $aParams['content'];		
	
	$Core.loadInit();
	
	scroll(0,0);
}

$Core.updatePageHistory = function()
{	
	var $sLocation = window.location.hash.replace('#!', '');
	if (empty($sLocation))
	{
		$sLocation = '/';
	}
		
	$oEventHistory[$sLocation] = $('#main_content_holder').html();
}

var bAjaxLinkIsClicked = false;
var bCanByPassClick = false;
var sClickProfileName = '';
$Behavior.linkClickAll = function()
{	
	if ($.browser.msie && $.browser.version == '7.0')
	{
		return false;
	}
	
	if (!oCore['core.site_wide_ajax_browsing'])
	{
		return false;
	}
	
	$('a').click(function()
	{
		var $sLink = $(this).attr('href');
		
		if (!$sLink)
		{
			return;
		}				
		
		if (substr($sLink, 0, 7) != 'http://' || substr($sLink, -1) == '#')
		{
			return;
		}
		
		if ($(this).hasClass('photo_holder_image') && !getParam('bPhotoTheaterMode')){
			
		}
		else{
			if ($(this).hasClass('no_ajax_link') || $(this).hasClass('thickbox'))
			{
				return;
			}			
		}
		
		$('.js_box_image_holder_full').remove();
		
		var $aUrlParts = parse_url($sLink);
		
		if ($aUrlParts['host'] != getParam('sJsHostname'))
		{
			return;
		}
		
		if (!isset($aUrlParts['query']))
		{
			var sTempHost = $aUrlParts['scheme'] + '://' + $aUrlParts['host'] + $aUrlParts['path'];
			$aUrlParts['query']	= sTempHost.replace(getParam('sJsHome'), '/');
		}

		if (isset($aUrlParts['query']))
		{
			var aUrlParts = explode('/', $aUrlParts['query']);
			var sAdminPath = 'admincp';
			if (getParam('sAdminCPLocation') != ''){
				sAdminPath = getParam('sAdminCPLocation');
			}
			if (aUrlParts[1] == sAdminPath)
			{
				return;
			}
			
			if (aUrlParts[1] == 'user' && aUrlParts[2] == 'logout')
			{
				return;
			}			
		}
		
		if (bCanByPassClick === true && aUrlParts[1] != sClickProfileName){
			bCanByPassClick = false;
			return;
		}		
		
		if ($('#noteform').length > 0)
		{
 			$('#noteform').hide(); 
		}
		if ($('#js_photo_view_image').length > 0)
		{
 			$('#js_photo_view_image').imgAreaSelect({ hide: true });		
		}
		if ($('#user_profile_photo').length > 0)
		{
 			$('#user_profile_photo').imgAreaSelect({ hide: true });		
		}	
		
		$('.ajax_link_reset').hide(); 
		
		bAjaxLinkIsClicked = true;
		
		$('body').css('cursor', 'wait');
		
		$('.js_user_tool_tip_holder').hide();
		$('#js_global_tooltip').hide();
		
		$(this).addClass('is_built');
		
		$Core.addUrlPager(this);
	
		$.ajaxCall('core.page', 'ajax_page_display=true' + $Core.getRequests(this) + '&do=' + $Core.getRequests(this, true), 'GET');
					
		return false;
	});
}

$Core.loadInit = function()
{
	debug('$Core.loadInit() Loaded');		
	
	$('*').unbind();
	
	$.each($Behavior, function() 
	{		
		this(this);
	});	
}

$Core.init = function()
{	
	$bDocumentIsLoaded = true;
	
	$(document).ready(function()
	{	
		$.each($Behavior, function() 
		{
			this(this);
		});
	});    
	
    $('script,link').each(function()
	{			
		if (!empty(this.src))
		{
			var $sVar = this.src;
				
			if (this.src.indexOf('f=') !== -1)
			{
				var $aFiles = explode('f=', $sVar);
				var $aParts = explode('&v=', $aFiles[1]);
				var $aGetFiles = explode(',', $aParts[0]);
				$($aGetFiles).each(function($sKey, $sFile)
				{
					if (substr($sFile, 0, 7) == 'module/')
					{
						$oStaticHistory[getParam('sJsHome') + $sFile] = true;
					}
					else
					{
						$oStaticHistory[getParam('sJsStatic') + 'jscript/' + $sFile] = true;
					}
				});
				return;
			}				
		}
		else if (!empty(this.href))
		{
			var $sVar = this.href;	
			
			if (this.href.indexOf('f=') !== -1)
			{
				var $aFiles = explode('f=', $sVar);
				var $aParts = explode('&v=', $aFiles[1]);
				var $aGetFiles = explode(',', $aParts[0]);
				$($aGetFiles).each(function($sKey, $sFile)
				{
					$oStaticHistory[getParam('sJsHome') + $sFile] = true;
				});
				return;
			}
		}
		
		if (!empty($sVar))
		{
			var $aParts = explode('?', $sVar);				
			$oStaticHistory[$aParts[0]] = true;	
		}
	});
		
	if (isset($Cache['post_static_files']))
	{
		$($Cache['post_static_files']).each(function($sKey, $mValue)
		{
			$Core.loadStaticFiles($mValue);
		});
	}		
	
	if (oCore['core.site_wide_ajax_browsing'])
    {	
		if ($.browser.msie && $.browser.version == '7.0')
		{
			
		}
    	else
    	{
			if ($Core.hasPushState()){
				$oEventHistory[$Core.getRequests(window.location, true)] = $('#main_content_holder').html();	
				$(window).bind('popstate', function(event) {
					$Core.changeHistoryState({path: $Core.getRequests(window.location, true)});
				});				
			}			
			else{
				$.address.change(function(event)
				{				
					$Core.changeHistoryState(event);
				});				
			}
    	}
    }
}

$Core.hasPushState = function(){
	return (typeof(window.history.pushState) == 'function' ? true : false);
}

/**
 * Adds a hash to the URL string, which is used to emulate a AJAX page
 *
 * @param object oObject Is the anchor object (this)
 */
$Core.addUrlPager = function(oObject, bShort)
{	
	if ($Core.hasPushState()){
		window.history.pushState('', '', oObject.href);
	}
	else{
		window.location = '#!' + (bShort ? oObject.href : $Core.getRequests(oObject.href, true));	
	}
}

$Core.changeHistoryState = function(event){
				$('.js_box').each(function()
				{
					if (!$(this).hasClass('js_box_no_remove'))
					{
						var $sLink = $(this).find('.js_box_history:first').html();
						if (isset($aBoxHistory[$sLink]))
						{
							delete $aBoxHistory[$sLink];
						}					
						$(this).remove();
					}
				});
				
				if ($Core.hasPushState()){
					bAjaxLinkIsClicked = false;
				}				
				
				if (isset($oEventHistory[event.path]) && !bAjaxLinkIsClicked)
				{								
					$('#main_content_holder').html($oEventHistory[event.path].replace('$Core.loadInit();', '').replace('$Core.updatePageHistory();', ''));	

					$Core.loadInit();
	
					scroll(0,0);
				}
				else
				{				
					if (empty($oEventHistory))
					{	
						if (event.path == '/')
						{
							if (isset($oEventHistory[$Core.getRequests(window.location, true)]))
							{							
								$('#main_content_holder').html($oEventHistory[$Core.getRequests(window.location, true)].replace('$Core.loadInit();', '').replace('$Core.updatePageHistory();', ''));
								
								$Core.loadInit();
								
								scroll(0,0);
							}
							else
							{
								$oEventHistory[$Core.getRequests(window.location, true)] = $('#main_content_holder').html();						
							}
						}
					}
					else
					{
						if (event.path == '/')
						{
							if (isset($oEventHistory[$Core.getRequests(window.location, true)]))
							{	
								$('#main_content_holder').html($oEventHistory[$Core.getRequests(window.location, true)].replace('$Core.loadInit();', '').replace('$Core.updatePageHistory();', ''));
								
								$Core.loadInit();
								
								scroll(0,0);
							}
						}
					}
				}
				
				if (bAjaxLinkIsClicked)
				{
					bAjaxLinkIsClicked = false;
				}	
}