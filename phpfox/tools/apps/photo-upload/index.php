<?php

// Enable error reporting to help us debug issues
error_reporting(E_ALL);

// Start PHP sessions
session_start();

require_once('./config.php');

function doPost($sMethod, $aPost = array())
{	
	$aPost['public_key'] = APP_PUBLIC_KEY;
	$aPost['private_key'] = APP_PRIVATE_KEY;
	$aPost['method'] = $sMethod;
	$aPost['token'] = $_REQUEST['token'];
		
	// Start curl request.
	$hCurl = curl_init();		
		
	curl_setopt($hCurl, CURLOPT_URL, APP_URL . 'api.php');
	curl_setopt($hCurl, CURLOPT_HEADER, false);
	curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);			

	curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, false);
			
	curl_setopt($hCurl, CURLOPT_POST, true);
	curl_setopt($hCurl, CURLOPT_POSTFIELDS, $aPost);
		
	// Run the exec
	$sData = curl_exec($hCurl);
			
	// Close the curl connection
	curl_close($hCurl);		

	// Return the curl request and since we use JSON we decode it.
	return json_decode(trim($sData));		
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
	<head>
		<title>Test Application</title>		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>static/style.php?public_key=<?php echo APP_PUBLIC_KEY; ?>" />
		<script type="text/javascript">
			$(document).ready(function(){				
				$('body').append('<iframe id="crossdomain_frame" src="<?php echo APP_URL; ?>static/crossdomain.php?height=' + document.body.scrollHeight + '&nocache=' + Math.random() + '" height="0" width="0" frameborder="0"></iframe>');
			});		
		</script>
	</head>
	<body>
		<h1>Test Application - Uploading Photos</h1>
		<div class="extra_info p_bottom_15">
			This is a test application for uploading photos. When you select and upload an image using the API method it will get added to your wall.
		</div>
		<form action="index.php" method="post" enctype="multipart/form-data">
			<div><input type="hidden" name="token" value="<?php echo $_REQUEST['token']; ?>" />
			<div class="table">
				<div class="table_left">
					Select Image:
				</div>
				<div class="table_right">
					<input type="file" name="file" />
				</div>				
			</div>	
			<div class="table_clear">
				<input type="submit" value="Upload" class="button" />
			</div>				
		</form>
			<?php
				if (isset($_FILES['file']))
				{					
					if (!file_exists($_FILES['file']['tmp_name']))
					{
						echo '<div class="error_message">File was not uploaded properly.</div>';
					}	
					else
					{
						$mReturn = doPost('photo.addPhoto', array('photo' => '@' . $_FILES['file']['tmp_name'] . ';type=' . $_FILES['file']['type'], 'photo_name' => basename($_FILES['file']['name'])));
						?>
						<div class="message">Image successfully uploaded and sent to the API server.</div>
						<h3>API Server Output</h3>
						<?php
						echo '<pre>';
						print_r($mReturn);
						echo '</pre>';
						if (isset($mReturn->output->original))
						{
							echo '<h3>Photo Output</h3>';
							foreach ($mReturn->output as $sKey => $sImage)
							{
								if ($sKey == 'original')
								{
									continue;
								}
								echo '<img src="' . $sImage . '" /><br /><br />';
							}
						}
						
					}
				}			
			?>		
	</body>
</html>