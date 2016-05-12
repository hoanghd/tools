
var sGlobalAdHolder = 'js_sample_ad_form_728_90';

$Behavior.creatingAnAd = function()
{
	$('#js_upload_image_holder').hide();
	
	if (!$Core.exists('#js_image_holder')){
		return;
	}
	
	if ($Core.exists('#js_upload_image_holder_frame')){
		$('body').append($('#js_upload_image_holder_frame').html());
		$('#js_upload_image_holder_frame').remove();		
	}	

	var oLocation = $('#js_image_holder').offset();
	
	$('#js_upload_image_holder').css('left', oLocation.left + 'px');
	$('#js_upload_image_holder').css('top', oLocation.top + 'px');
	$('#js_upload_image_holder').show();
	
	$('#title').keyup(function()
	{
		$('.js_ad_title').html(htmlspecialchars(this.value));
		limitChars(this, 25, 'js_ad_title_form_limit');
	});
	
	$('#body_text').keyup(function()
	{
		$('.js_ad_body').html(htmlspecialchars(this.value));
		limitChars(this, 135, 'js_ad_body_text_form_limit');
	});	
	
	$('#js_ad_type_cpc').focus(function()
	{
		$('.js_ad_select').hide();
		$('#js_ad_cpc').show();
	});
	
	$('#js_ad_type_cpm').focus(function()
	{
		$('.js_ad_select').hide();
		$('#js_ad_cpm').show();
	});	
	
	$('#js_specific_date').focus(function()
	{
		$('#js_show_date').show();	
	});
	
	$('#js_specific_date_hide').focus(function()
	{
		$('#js_show_date').hide();	
	});	
	
	$('.js_create_ad').click(function()
	{
		$('.hide_sub_block').hide();
		$('#js_create_ad').show();
		$(this).parent().addClass('active');
		$('.js_upload_ad').parent().removeClass('active');
		$('#js_sample_ad_create_holder').show();
		$('#js_upload_image_holder').show();
		$('#type_id').val('2');
		
		return false;
	});
	
	$('.js_upload_ad').click(function()
	{
		$('.hide_sub_block').hide();
		$('#js_upload_ad').show();
		$(this).parent().addClass('active');
		$('.js_create_ad').parent().removeClass('active');		
		$('#js_sample_ad_create_holder').hide();
		$('#js_upload_image_holder').hide();
		$('#type_id').val('1');
		
		return false;
	});	

	$('#js_submit_button').click(function()
	{
		$('#html_code').val($('#' + sGlobalAdHolder).parent().html());	
		
		$('#js_custom_ad_form').ajaxCall('ad.checkAdForm');
		
		return false;
	});
	
	$('#location option').each(function()
	{
		if (this.value == '7')
		{
			this.selected = true;
		}
	});
	
	$('#js_ad_continue_form').click(function()
	{
		if (empty($('#js_upload_ad_size_find').val()))
		{
			alert(getPhrase('ad.select_an_ad_placement'));
			
			return false;	
		}
		
		$.ajaxCall('ad.getAdPrice', 'location=' + $('#location').val());
		
		return true;
	});
	
	$('#total_view').keyup(function()
	{
		$('#js_ad_cost').hide();
		$('#js_ad_cost_recalculate').show();	
	});
}

function blockPlacementCallback(iWidth, iHeight, sBlock)
{
	$('#js_upload_ad_size_find').val('' + iWidth + 'x' + iHeight + '');
	
	$('.js_ad_holder').hide();	
	
	$('#js_ad_position_selected').find('span').html('Block ' + sBlock + ' (' + iWidth + 'x' + iHeight + ')')
	$('#js_ad_position_selected').show();
	$('#js_ad_position_select').hide();
	
	sGlobalAdHolder = (iWidth > iHeight ? 'js_sample_ad_form_728_90' : 'js_sample_ad_form_160_600');
	sId = '#' + sGlobalAdHolder;
	
	$(sId).show();
	$(sId).css('width', iWidth + 'px');
	$(sId).css('height', iHeight + 'px');	
	
	if ($('#type_id').val() == '2'){
		var oLocation = $('#js_image_holder').offset();
	
		$('#js_upload_image_holder').css('left', oLocation.left + 'px');
		$('#js_upload_image_holder').css('top', oLocation.top + 'px');
		$('#js_upload_image_holder').show();			
	}	
}