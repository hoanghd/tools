var swfu;
var $oSWF_settings;
var iQueuedFiles = 0;
var aFailedFiles = [];
var aQueuedFiles = [];

/* Make sure the css is loaded */
var bAdded = false;
for (var i in document.styleSheets)
{
	if(isset(document.styleSheets[i].href) && document.styleSheets[i].href.indexOf('progress.css') > (-1)) bAdded = true;

}
if (bAdded == false)
{
	var oCss = document.createElement('link');
	oCss.type = 'text/css';
	oCss.rel = 'stylesheet';
	oCss.href = oParams['sProgressCssFile'];
	oCss.media = 'screen';
	document.getElementsByTagName("head")[0].appendChild(oCss);
}

//When uploading a photo make sure that if View Photos is selected or Upload More, it needs to show
//the thumbnails, like when using the default way
window.aImagesUrl = [];

/**
 * fileDialogStart is fired after selectFile for selectFiles is called.
 * This event is fired immediately before the File Selection Dialog window is displayed.
 * However, the event may not execute until after the Dialog window is closed.
 */
function fileDialogStart()
{
	// $('#js_temp_mass_uploader').focus();
}

/**
 *Attempts to update the progress bar to mention thumbnail was created
 */
function thumbnailCreated(sFileId)
{	
	$('#js_file_'+sFileId+'_status').html('Thumbnail Created.');
}


function fileQueued(fObject)
{
	this.addPostParam(getParam('sGlobalTokenName') + "[security_token]", oCore["log.security_token"]);
	for (var sSetting in this.customSettings)
	{
		this.addPostParam(sSetting, this.customSettings[sSetting]);
	}
	if (typeof this.customSettings['flash_user_id'] == 'undefined')
	{
		alert('[2] Implemention incomplete, contact support');
	}
	if (typeof oParams['sMethod'] != undefined)
	{
		this.addPostParam('sMethod', oParams['sMethod'] == 'simple' ? 'simple' : 'massuploader');
	}
	this.addPostParam('sFileId', fObject.id);
	iQueuedFiles++;
	var oStats = this.getStats();

	aQueuedFiles.push(fObject.name);

	if (typeof this.atFileQueue == "function")
	{
		this.atFileQueue(fObject);
	}
}

function fileQueueError(fObject, errorConstant, sMessage)
{	
	if (errorConstant == -110)
	{
		//alert(this.settings.file_size_limit);
		var iLimit = (this.settings['file_size_limit'].replace(' B',''));
		iLimit = parseInt(iLimit);
		iLimit = iLimit / 1024
		iLimit = Math.floor(iLimit);
		alert(oTranslations['core.upload_failed_your_file_size_is_larger_then_our_limit_file_size'].replace('{size}', fObject.name + ' = ' + Math.floor(fObject.size / 1024) + 'KB').replace('{file_size}', iLimit + ' KB'));
		$('#js_form').append('<div style="display: none;"><input type="hidden" name="aFailed[\''+errorConstant+'\'][]" value = "'+fObject.name+'"></div>');

		aFailedFiles.push({
				"sConstant" : errorConstant,
				"sFileName": fObject.name
			});
	}
	else if( errorConstant == -100)
	{
		if (typeof oTranslations['core.more_queued_than_allowed'] != undefined)
		{
			alert(oTranslations['core.more_queued_than_allowed'].replace('{iQueueLimit}', this.settings.file_queue_limit));
		}
	}
	
}

function fileDialogComplete(iSelectedFiles, iQueuedFiles, iFilesInQueue)
{
	// $('#js_temp_mass_uploader').focus();
	
	// good place to call this.startUpload
	if (iQueuedFiles > 0 && ((typeof this.customSettings['bDelayUpload'] == 'undefined') || this.customSettings['bDelayUpload'] == false))
	{
		this.startUpload();
	}	
}
/**
 * @param fObject a file object
 * @param iCompleted bytes completed
 * @param iTotal total bytes
 */
function uploadProgress(fObject, iCompleted, iTotal)
{
	var iPercentage = Math.floor((iCompleted/iTotal) * 100);	
	
	$($oSWF_settings.object_holder).find('.swf_upload_progress:first').show().css('width', iPercentage + '%');
	$($oSWF_settings.object_holder).find('.swf_upload_text:first').html(fObject.name.substr(0, 20) + '... ('+iPercentage + '%)');
}

function uploadError(fObject, sErrorConstant, sMessage)
{
	// $('#js_file_'+fObject+'_status').html(sMessage);
	alert(sMessage);
}

/**
 * @param fObject a file object
 * @param sData anything that comes from the server
 * @param sResponse not sure...
 */
function uploadSuccess(fObject, sData, sResponse)
{
	eval(sData);
	
	if (typeof thumbnailCreated == "function" && sData.indexOf('thumbnail created') >= 0)
	{
		thumbnailCreated(fObject.id);
	}
	
	iQueuedFiles--;

	if (typeof this.customSettings['sJSCallSuccess'] != 'undefined' && this.customSettings['sJSCallSuccess'] != '')
	{
		eval(this.customSettings['sJSCallSuccess'] + '(fObject);');
	}

	if (iQueuedFiles > 0)
	{
		debug ('Calling uploadStart from uploadSuccess because iQueuedFiles = ' + iQueuedFiles)
		this.startUpload();
	}
}

/**
 * This function is called when all the queued files have been uploaded.
 * if the customSetting sAjaxCall was defined it will be called here.
 */
function uploadComplete(fObject)
{
	if (window.aImagesUrl != undefined && iQueuedFiles < 1)
	{
		if (window.aImagesUrl.length == 0)
		{
		//d("No images need to be uploaded");
		}
		var sImages = "", sFailed = "";
		var iTotalImages = 0;
		for (var iKey in window.aImagesUrl)
		{
			iTotalImages++;
			sImages += "photos[]=" + window.aImagesUrl[iKey] + "&";
		}
		sImages = sImages.substr(0, (sImages.length - 1));
		
		sFailed = sFailed.substr(0, (sFailed.length - 1));
		if (typeof this.customSettings['sAjaxCall'] != 'undefined' && this.customSettings['sAjaxCall'] != '')
		{
			var sAjaxParams = 'js_disable_ajax_restart=true&' + sImages + '&' + sFailed;
			if (typeof oParams['sMethod'] != 'undefined')
			{
				sAjaxParams += '&' + (oParams['sMethod'] == 'simple' ? 'simple' : 'massuploader');
			}
			else
			{
			}
			
			if (isset(this.customSettings['sAjaxCallParams'])){
				sAjaxParams += this.customSettings['sAjaxCallParams'];
			}
			
			if (isset(this.customSettings['sAjaxCallAction'])){
				this.customSettings['sAjaxCallAction'](iTotalImages);
			}				
			
			$.ajaxCall(this.customSettings['sAjaxCall'], sAjaxParams);
		}
		if (typeof this.customSettings['sJSCallComplete'] != 'undefined' && this.customSettings['sJSCallComplete'] != '')
		{
			eval(this.customSettings['sJSCallComplete'] + '(fObject);');
		}
	}
	
	if (iQueuedFiles > 0)
	{
		// swf_upload_text_holder
		var sSubHtml = $($oSWF_settings.object_holder).find('.swf_upload_text_holder:first').html();
			
		$($oSWF_settings.object_holder).find('.swf_upload_text_holder:first').after('<div class="swf_upload_text_holder">' + sSubHtml + '</div>');		
		
		this.startUpload();
	}
}

var aFunction = function()
{	
	if (!isset($oSWF_settings))
	{
		p('Cannot load flash object. Missing variable: $oSWF_settings');
		
		return;
	}
	
	if ($('#' + $oSWF_settings.object_holder().toString()).length <= 0 )
	{
		p('Unable to find SWFU holder: ' + $oSWF_settings.object_holder().toString());
		return;		
	}
	
	if ($('#' + $oSWF_settings.div_holder().toString()).length <= 0 )
	{
		$('#' + $oSWF_settings.object_holder().toString()).find('.swf_upload_holder:first').remove();
		$('#' + $oSWF_settings.object_holder().toString()).prepend('<div class="swf_upload_holder"><div id="' + $oSWF_settings.div_holder().toString() + '"></div></div>');	
		p('Reseting SWFU object: ' + $oSWF_settings.object_holder().toString());
	}
	
	p('Loading SWFU object: ' + $oSWF_settings.object_holder().toString());
	/*
	if ($('#js_temp_mass_uploader').length <= 0)
	{
		$('body').append('<div style="display:none;"><input type="text" name="null" value="" id="js_temp_mass_uploader" /></div>');
	}	
	*/
	swfu = new SWFUpload({
		
		upload_url: oParams['sJsHome'],
		
		// Flash file settings
		file_types : "*.jpg;*.gif;*.png",
		file_types_description : oTranslations['photo.you_can_upload_a_jpg_gif_or_png_file'],
		file_upload_limit : 0,
		file_queue_limit : 0,

		// Event handler settings		
		swfupload_loaded_handler : $oSWF_settings.get_settings,
		file_dialog_start_handler: fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,

		//upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		
		// Button Settings
		button_image_url : oParams['sJsHome'] + "static/jscript/massuploader/buttonBackground.php",
		button_placeholder_id : $oSWF_settings.div_holder().toString(),
		button_width: 300,
		button_height: 30,
		button_window_mode: 'transparent',

		// Flash Settings
		flash_url : oParams['sJsHome'] + "static/jscript/massuploader/swfupload.swf",
		
		// Debug settings
		debug: false
	});
}

$Behavior.swfUploadLoader = function()
{
	if (typeof bDoNotLoad != 'undefined' && bDoNotLoad == true)
	{

	}
	else
	{
		aFunction();
	}
}