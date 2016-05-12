
$Core.loadStaticFile(getParam('sJsStatic') + 'jscript/player/flowplayer/flowplayer.js');

$Core.player =
{
    aParams: {},
	
    load: function(aParams)
    {
		if (document.getElementById(aParams['id']) === null)
		{		
		    return false;
		}
		
		this.aParams = aParams;
			
		var sCall = '$f(\'' + this.aParams['id'] + '\', {src: \'' + getParam('sJsStatic') + 'jscript/player/flowplayer/flowplayer.swf\', wmode: \'transparent\', zIndex: -1},{';
		switch (this.aParams['type'])
		{
		    case 'video':
				sCall += 'clip: {';
				sCall += 'url: \'' + this.aParams['play'] + '\',';
				sCall += 'autoBuffering: true,';
				if (isset(this.aParams['auto']))
				{
				    sCall += 'autoPlay: ' + this.aParams['auto'];
				}
				sCall = rtrim(sCall, ',');
				sCall += '}';
				break;
			    case 'music':
				sCall += 'clip: {';
						
				if (!empty(this.aParams['play']))
				{
				    sCall += 'url: \'' + this.aParams['play'] + '\',';
				}
				if (isset(this.aParams['on_finish']))
				{
				    sCall += 'onFinish: ' + this.aParams['on_finish'] + ','
				}
				else
				{		    
				    if (this.aParams['playlist'] != undefined)
				    {
						sCall += 'onFinish: function(clip){\n';
						sCall += 'debug("Event onFinish triggered");';
						sCall += 'var aPlaylist = {\n';
						for (oPlay in this.aParams['playlist'])
						{
						    sCall += '"' + this.aParams['playlist'][oPlay] + '" : \n{"iNext" : ' + this.aParams['aNextSong'][oPlay] +', "sNextPath" : "' + this.aParams['playlist'][this.aParams['aNextSong'][oPlay]] + '"},';
						}

						sCall = rtrim( sCall, ',');
						sCall += '};';
						sCall += '\n$(".isSelected").removeClass("isSelected");';
						sCall += '\n$("#js_music_track_" + aPlaylist[clip.originalUrl]["iNext"]).addClass("isSelected");';
						sCall += '$f().unload();';
						sCall += '\n$Core.player.play("'+this.aParams['id']+'",aPlaylist[clip.originalUrl]["sNextPath"]);';
						sCall += '$.ajaxCall(\'music.play\', \'id=\' + aPlaylist[clip.originalUrl]["iNext"], \'GET\');';			
						sCall += '},';
				    }
						    
				}
				if (isset(this.aParams['on_start']))
				{
				    sCall += 'onStart: ' + this.aParams['on_start'] + ','
				}
				sCall = rtrim(sCall, ',');
				sCall += '},';
				sCall += 'plugins: {';
				sCall += 'controls: {fullscreen: false, height: 30, autoHide: false},';
				sCall += 'audio: {';
				sCall += 'url: \'' + getParam('sJsStatic') + 'jscript/player/flowplayer/flowplayer.audio.swf\'';
				sCall += '}';
				sCall += '}';	
			
				break;
			    default:
			    	p('Not a valid call.');
			    break;
		}
		sCall += '});';
		
		if (this.aParams['playlist'] != undefined)
		{
		    // Lets play the first item
		    for (var iSong in this.aParams['playlist'])
		    {
				if (iSong != undefined)
				{
				    sCall +='\n $Core.player.play("'+this.aParams['id']+'","'+this.aParams['playlist'][iSong]+'");';
				    sCall += '\n$.ajaxCall(\'music.play\',"id='+iSong +'", "GET");';
				    break;
				}
		    }
		    
		}
		//debug(sCall);
		eval(sCall);
		return this;
    },
	
    play: function(sId, sPath)
    {
		p('#' + sId + ' Playing song: ' + sPath);
			
		$('#' + sId).show();
			
		$f(sId).play(sPath);
			
		return false;
    }
}