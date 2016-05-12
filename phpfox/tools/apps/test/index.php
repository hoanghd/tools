<?php

// Enable error reporting to help us debug issues
error_reporting(E_ALL);

// Start PHP sessions
session_start();

require_once('./config.php');

function doPost($sMethod, $aPost = array())
{
	// Build the request string we are going to POST to the API server. We include some of the required params.
	$sPost = 'public_key=' . APP_PUBLIC_KEY . '&private_key=' . APP_PRIVATE_KEY . '&method=' . $sMethod . '&token=' . $_REQUEST['token'];
	foreach ($aPost as $sKey => $sValue)
	{
		$sPost .= '&' . $sKey . '=' . $sValue;
	}		
		
	// Start curl request.
	$hCurl = curl_init();		
		
	curl_setopt($hCurl, CURLOPT_URL, APP_URL . 'api.php');
	curl_setopt($hCurl, CURLOPT_HEADER, false);
	curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);			

	curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, false);
			
	curl_setopt($hCurl, CURLOPT_POST, true);
	curl_setopt($hCurl, CURLOPT_POSTFIELDS, $sPost);
		
	// Run the exec
	$sData = curl_exec($hCurl);
			
	// Close the curl connection
	curl_close($hCurl);	
	
	// print(APP_URL . 'api.php?' . $sPost);

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
		<h1>Test Application</h1>
<?php

echo '<h3>Getting Users Information [user.getUser]</h3>';
$mReturn = doPost('user.getUser');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

echo '<h3>Getting Friends [friend.getFriends]</h3>';
$mReturn = doPost('friend.getFriends');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

if (isset($mReturn->output[0]))
{
	echo '<h3>Checking if Friends with User #' . $mReturn->output[0]->user_id . ' [friend.isFriend]</h3>';
	$mReturn = doPost('friend.isFriend', array('friend_user_id' => $mReturn->output[0]->user_id));
 	echo '<pre>' . print_r($mReturn, true) . '</pre>';
}

echo '<h3>Updating Users Status [user.updateStatus]</h3>';
$mReturn = doPost('user.updateStatus', array('user_status' => 'This is a test status update from an application... [' . uniqid() . ']'));
echo '<pre>' . print_r($mReturn, true) . '</pre>';

echo '<h3>Getting Photos [photo.getPhotos]</h3>';
$mReturn = doPost('photo.getPhotos');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

echo '<h3>Getting New Notifications Count [notification.getNewCount]</h3>';
$mReturn = doPost('notification.getNewCount');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

echo '<h3>Getting Notifications [notification.get]</h3>';
$mReturn = doPost('notification.get');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

echo '<h3>Getting New Mail Count [mail.getNewCount]</h3>';
$mReturn = doPost('mail.getNewCount');
echo '<pre>' . print_r($mReturn, true) . '</pre>';

?>
	</body>
</html>