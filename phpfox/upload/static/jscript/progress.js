
var oProgressBar = {};

$Core.loadStaticFile(getParam('sProgressCssFile'));

/**
 * Function is called when the upload is complete.
 */
function completeProgress()
{		
	// Check if we have a plug-in
	if (function_exists('plugin_completeProgress'))
	{
		plugin_completeProgress();
	}	

	$('.js_uploader_files_cache').remove();
	$('.js_uploader_files_input').attr('disabled', false);
	$('#js_progress_outer').hide();
	
	$Core.loadInit();
}

function startProcess(bForm, bForceImage)
{
	iUploaded = 0;
	iExtFailed = 0;
	$('.js_uploader_files_input').each(function()
	{
		if (!empty(this.value))
		{
			iUploaded++;
			
			if (isset(oProgressBar['valid_file_ext']))
			{
				sExt = this.value.split('.').pop().toLowerCase();				
				if ($.inArray(sExt, oProgressBar['valid_file_ext']) == -1)
				{
					iExtFailed++;
				}
			}
		}
	});
	
	if (iExtFailed > 0)
	{
		sExts = '';
		for (iExt in oProgressBar['valid_file_ext'])
		{
			if (iExt > 0)
			{
				sExts += ', ';
			}
			sExts += oProgressBar['valid_file_ext'][iExt];
		}
		alert(oTranslations['core.not_a_valid_file_extension_we_only_allow_ext'].replace('{ext}', sExts));
		
		return false;
	}
	
	if (!bForceImage && !iUploaded)
	{
		return bForm;
	}
	
	if (!iUploaded)
	{	
		alert(oTranslations['core.select_a_file_to_upload']);
		
		return false;
	}	
	
	$('.error_message').remove();
	$('#js_progress_cache_holder').remove();	
	
	if (bForm)
	{		
		$(oProgressBar['holder']).hide();
		$(oProgressBar['holder']).parent().prepend('<div id="js_progress_cache_holder" class="t_center" style="height:' + $(oProgressBar['holder']).innerHeight()  + 'px;">' + $.ajaxProcess(oTranslations['core.uploading'], 'large') + '</div>');
		
		return true;
	}
	
	return false;
}

function getProgress(sProgressKey)
{
	// $.ajaxCall('core.progress', 'progress_key=' + sProgressKey);
}

function startProgress(sProgressKey)
{	
	iUploaded = 0;
	$('.js_uploader_files_input').each(function()
	{
		if (!empty(this.value))
		{
			iUploaded++;
		}
	});
	
	if (!iUploaded)
	{	
		alert(oTranslations['core.select_a_file_to_upload']);
		
		return false;
	}
	
	if (function_exists('plugin_startProgress'))
	{
		plugin_startProgress(sProgressKey);
	}
	$('#js_progress_outer').show();

	$('.js_uploader_files').each(function()
	{
		$(this).addClass('js_uploader_files_cache').hide();	
	});
	
	sInput = '';
	for (i = 1; i <= oProgressBar['total']; i++)
	{
		sInput += '<div class="js_uploader_files"><input type="file" name="' + oProgressBar['file_id'] + '" size="30" class="js_uploader_files_input" disabled="disabled" onchange="addMoreToProgressBar();" /></div>' + "\n";
	}	
	$('#js_uploader_files_outer').append(sInput);
    
	// setTimeout('getProgress(\'' + sProgressKey + '\')', 1000);
		
	return true;
}

var iNewInputBars = 0;

function addMoreToProgressBar()
{
	iNewInputBars++;	

	if ((iNewInputBars + oProgressBar['total']) > oProgressBar['max_upload'])
	{
		iNewInputBars--;
		
		return false;
	}
	
	$('.js_uploader_files_input').each(function()
	{
		if (empty(this.value))
		{
			iNewInputBars--;
			$(this).parent().remove();
		}
	});	
	
	$('#js_uploader_files_outer').append('<div class="js_uploader_files" id="js_new_add_input_' + iNewInputBars + '"><input type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" onchange="addMoreToProgressBar();" /></div>' + "\n");
	
	return false;
}

function removeMoreToProgressBar(iId)
{
	iNewInputBars--;
	
	$('#js_new_add_input_' + iId).remove();
	
	return false;
}

$Core.progressBarInit = function()
{
	if ($(oProgressBar['uploader']).length > 0)
	{
		$(oProgressBar['progress_id']).html('<div id="js_progress_outer" style="width:300px;"><div id="js_progress_inner"><span id="js_progress_percent_value">0</span>/100%</div></div>');
		
		sInput = '<div id="js_uploader_files_outer">';
		for (i = 1; i <= oProgressBar['total']; i++)
		{
			sInput += '<div class="js_uploader_files"><input type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" onchange="addMoreToProgressBar();" /></div>' + "\n";
		}
		sInput += '</div>';
		
		var iDivHeight = $(oProgressBar['holder']).innerHeight();	
		// $(oProgressBar['holder']).hide().after('<div id="js_progress_cache_loader" style="height:' + (iDivHeight <= 0 ? '200' : iDivHeight)  + 'px;">' + $.ajaxProcess('Loading', 'large') + '</div>');
		
		$(oProgressBar['holder']).after('<div id="js_progress_cache_loader" style="height:' + (iDivHeight <= 0 ? '200' : iDivHeight)  + 'px; display:none;"></div>');
		
		sInput += '<iframe id="' + oProgressBar['frame_id'] + '" name="' + oProgressBar['frame_id'] + '" height="500" width="500" frameborder="1" style="display:none;"></iframe>';
		
		$(oProgressBar['uploader']).html(sInput);
		
		$.ajaxCall('user.checkSpaceUsage', 'holder=' + oProgressBar['holder'].replace('#', ''), 'GET');
	}
}