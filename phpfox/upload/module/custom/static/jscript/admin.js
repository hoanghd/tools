
$Core.custom =
{
	iDefault: 4,
		
	aOptions: null,
	
	sUrl: '',
	
	init: function(iDefault, aOptions)
	{
		this.iDefault = iDefault;
		
		if (!empty(aOptions))
		{
			this.aOptions = aOptions;
			
			var iCnt = 0;
			for (i in aOptions)
			{
				iCnt++;
			}			
		}				
		
		this.display();			
	},
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},
	
	display: function()
	{		
		var sForm = $('#js_sample_option').html();
		var sForms = '';
		for (i = 0; i < this.iDefault; i++)
		{			
			sForms += sForm;
		}
		$('#js_option_holder').html(sForms).show();
		//$('#tbl_add_custom_option').show();
		
		this.update();	
	},
	
	update: function()
	{
	    //return;
		var iCnt = 0;
		var aMatches;
		$('.js_option_holder').each(function()
		{
			iCnt++;
			//return;
			$(this).find('.js_option_count').html((iCnt - 1));			
			
			$(this).find('input').each(function()
			{
			    
				if ($Core.custom.aOptions !== null)
				{
					aMatches = $(this).attr('name').match(/val\[option\]\[(.*?)\]/i);
					if (isset(aMatches[1]) && isset($Core.custom.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]))
					{
						$(this).val($Core.custom.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]);
					}
				}
				
				//$(this).attr('name', $(this).attr('name').replace(/\[option\]\[([0-9]+)\]/, '[option][' + (iCnt-1) + ']'));
				$(this).attr('name', $(this).attr('name').replace('#', (iCnt-1)));//(/\[option\]\[([a-z0-9]+)\]/, '[option][' + (iCnt-1) + '][$1]'));
				//$(this).attr('name', $(this).attr('name').replace('[option]', '[option][' + (iCnt-1) + ']'));
				
				/*aMatches = $(this).attr('name').match(/val\[option\]\[(.*?)\]\[(.*?)\]/i);
				var aMatchLang = $(this).attr('name').match(/val\[option\]\[([a-z]{2})\]/i);
				var aMatchFeed = $(this).attr('name').match(/val\[feed]\[\]\[([a-z]{2})\]/i);
				if ($(this).attr('name').indexOf('[feed]') > 0)
				{
					$(this).attr('name', 'val[feed][' + (iCnt - 1) + '][' + aMatchFeed[1] + ']');
				}
				else if (aMatchLang)
				{
				    console.log('about to replace: ' + $(this).attr('name'));
				    $(this).attr('name',$(this).attr('name').replace(/val\[option\]\[([a-z]{2})\]/i, 'val[option][' + (iCnt - 1) + '][' + aMatchLang[1] + ']'));
				    console.log('Replaced to: ' + $(this).attr('name'));
				}
				else if (aMatches)
				{
					$(this).attr('name', $(this).attr('name').replace(/val\[option\]\[(.*?)\]\[(.*?)\]/i, 'val[option][' + (iCnt - 1) + '][' + aMatches[2] + ']'));
				}
				else
				{
					$(this).attr('name', $(this).attr('name').replace('[option]', '[option]' + '[' + (iCnt - 1) + ']'));
				}*/	
			});
			
			if ((iCnt - 1) > $Core.custom.iDefault)
			{
				$(this).find('.js_option_delete').html('<a href="#" onclick="return $Core.custom.remove(this);"><img src="' + getParam('sImagePath') + 'misc/delete.png" alt="" /></option>');				
			}
		});		
	},
	
	add: function()
	{		
	    
		$('#js_option_holder').append($('#js_sample_option').html());	
		
		this.update();		
	},
	
	remove: function(oObj)
	{
		$(oObj).parents('.js_option_holder').remove();		
		
		return false;
	},
	
	updateSort: function()
	{
		$('.sortable').removeClass('odd');
		$('.sortable').removeClass('first');
		$('.sortable li:first').addClass('first');		
		
		var iGroupCnt = 0;
		$('.sortable ul .group').each(function()
		{
			iGroupCnt++;
			$(this).find('input:first').val(iGroupCnt);
		});
		
		var iFieldCnt = 0;
		$('.sortable ul .field').each(function()
		{
			iFieldCnt++;
			$(this).find('input:first').val(iFieldCnt);
		});		
	},
	
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);	
		
		$('.dropContent').hide();		
		
		switch (sAction)
		{
			case 'edit':
				if (aParams['type'] == 'group')
				{
					window.location.href = this.sUrl + 'group/add/id_' + aParams['id'] + '/';
				}
				else
				{
					window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
				}
				break;
			case 'delete':
				if (confirm(oTranslations['core.are_you_sure']))
				{
					if (aParams['type'] == 'group')
					{
						window.location.href = this.sUrl + 'delete_' + aParams['id'] + '/';
					}
					else
					{
						$.ajaxCall('custom.deleteField', 'id=' + aParams['id']);
					}					
				}
				break;
			default:
				if (aParams['type'] == 'group')
				{
					$.ajaxCall('custom.toggleActiveGroup', 'id=' + aParams['id']);
				}
				else
				{
					$.ajaxCall('custom.toggleActiveField', 'id=' + aParams['id']);
				}				
				break;
		}
		
		return false;
	},
	
	addSort: function()
	{
		$('.sortable ul').sortable({
				axis: 'y',
				update: function(element, ui)
				{
					$Core.custom.updateSort();
				},
				opacity: 0.4
			}
		);		
	},
	
	toggleFieldActivity: function(iId)
	{
		if ($('#js_field_' + aParams['id']).html().match(/<del(.*?)>/i))
		{
			$('#js_field_' + aParams['id']).html($('#js_field_' + aParams['id']).html().replace(/<del(.*?)>/i, '').replace(/<\/del>/i, ''));
		}
		else
		{
			$('#js_field_' + aParams['id']).html('<del>' + $('#js_field_' + aParams['id']).html() + '</del>');
		}		
	},
	
	toggleGroupActivity: function(iId)
	{
		if ($('#js_group_' + aParams['id']).html().match(/<del>/i))
		{
			$('#js_group_' + aParams['id']).html($('#js_group_' + aParams['id']).html().replace('<del>', '').replace('</del>', ''));
		}
		else
		{
			$('#js_group_' + aParams['id']).html('<del>' + $('#js_group_' + aParams['id']).html() + '</del>');
		}
	},
	toggleShowFeed: function(iVal)
	{
		if (iVal == 1)
		{
			$('div.add_feed').each(function(){$(this).show()});
		}
		else
		{
			$('div.add_feed').each(function(){$(this).hide()});
		}
	}
}

$(function()
{	
	$('.js_drop_down').click(function()
	{		
		eleOffset = $(this).offset();
		
		aParams = $.getParams(this.href);
		
		$('#js_cache_menu').remove();
		
		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');
		
		$('#js_cache_menu .link_menu li a').each(function()
		{			
			if (this.hash == '#active' && (($('#js_field_' + aParams['id']).html() && $('#js_field_' + aParams['id']).html().match(/<del>/i)) || ($('#js_group_' + aParams['id']).html() && $('#js_group_' + aParams['id']).html().match(/<del>/i))))
			{
				$(this).html('Set to Active');
			}
			
			this.href = '#?id=' + aParams['id'] + '&type=' + aParams['type'] + '';			
		});
		
		$('.dropContent').show();		
		
		$('.dropContent').mouseover(function()
		{
			$('.dropContent').show(); 
			
			return false;
		});
		
		$('.dropContent').mouseout(function()
		{
			$('.dropContent').hide(); 
			$('.sJsDropMenu').removeClass('is_already_open');			
		});
		
		return false;
	});		
	
	$('.var_type').change(function()
	{
		$('#js_multi_select').hide();
		
		switch (this.value)
		{
			case 'select':
			case 'multiselect':
			case 'radio':
			case 'checkbox':
				$('#tbl_option_holder').show();	
				$('#tbl_add_custom_option').show();
				break;
			default:
				$('#tbl_option_holder').hide();
				$('#tbl_add_custom_option').hide();
				break;
		}
	});
	
	$('.js_add_custom_option').click(function()
	{    
		$Core.custom.add();
		
		return false;
	});
	
	$('#js_create_new_group').click(function()
	{
		$('#js_field_holder').hide();
		$('#js_group_holder').show();
		
		return false;
	});
	
	$('#js_cancel_new_group').click(function()
	{
		$('#js_group_holder').hide();
		$('#js_field_holder').show();		
		
		return false;
	});	
	
	$('.js_delete_current_option').click(function()
	{
		if (confirm(oTranslations['custom.are_you_sure_you_want_to_delete_this_custom_option']))
		{
			aParams = $.getParams(this.href);
			
			$.ajaxCall('custom.deleteOption', 'id=' + aParams['id']);
		}
		
		return false;
	});
	$('.js_custom_change_group').click(function()
	{		
		$(this).parents('ul:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('.js_custom_groups').hide();
		$('.js_custom_group_' + this.id.replace('group_', '')).show();				
		
		return false;
	});
});