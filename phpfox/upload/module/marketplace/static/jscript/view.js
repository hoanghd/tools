
$Core.marketplace =
{
	aImage: {},

	loadImage: function(sSrc)
	{
		this.aImage[sSrc] = new Image();
		this.aImage[sSrc].src = sSrc;		
	}
}

$(function()
{
	$('.js_change_image').click(function()
	{	
		$('#js_current_image').attr('src', $Core.marketplace.aImage[this.href].src).attr('height', $Core.marketplace.aImage[this.href].height).attr('width', $Core.marketplace.aImage[this.href].width);
		$('#js_current_image').parent().attr('href', this.href.replace('_400', ''));
				
		return false;
	});
});