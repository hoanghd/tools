<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 2874 2011-08-23 08:40:57Z Raymond_Benc $
 */

// Enable error reporting to help us debug issues
error_reporting(E_ALL);

require_once('./config.php');

if (isset($_POST['token']))
{
	$sToken = $_POST['token'];
}
else
{
	$oToken = json_decode(file_get_contents(APP_URL . 'token.php?key=' . $_GET['key']));

	if (!isset($oToken->token))
	{
		exit('Not able to create a token.');
	}
	else
	{
		$sToken = $oToken->token;
	}
}

define('APP_TOKEN', $sToken);

// Connect to the database using default PHP functions.
$hConnection = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS) or die(mysql_error());
mysql_select_db(SQL_NAME, $hConnection) or die(mysql_error());

// App class used to connect to the API server
class App
{
	/**
	 * Run any GET requests. We use self::post.
	 * @param string $sMethod Method name
	 * @param array $aParams Array of anything you need to pass to the API server.
	 * @return object
	 */
	public static function get($sMethod, $aParams = array())
	{
		return self::post($sMethod, $aParams);
	}
	
	/**
	 * Run all requests to the API server
	 * @param string $sMethod Method name
	 * @param array $aParams Array of anything you need to pass to the API server.
	 * @return object
	 */	
	public static function post($sMethod, $aPost = array())
	{
		// Build the request string we are going to POST to the API server. We include some of the required params.
		$sPost = 'token=' . APP_TOKEN . '&method=' . $sMethod;
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

		// Return the curl request and since we use JSON we decode it.
		return json_decode(trim($sData));		
	}
	
	/**
	 * Gets users information.
	 * @staticvar array $aUser
	 * @param int $iUserId User ID#
	 * @return object
	 */
	public static function getUser($iUserId)
	{
		static $aUser = array();
		
		if (!isset($aUser[$iUserId]))
		{
			$aUser[$iUserId] = App::get('user.getUser', array('user_id' => $iUserId));
		}
		
		return $aUser[$iUserId]->output;
	}
}

// Get information about the user that is logged in.
$aUser = App::get('user.getUser');

if (isset($_REQUEST['action']))
{
	switch ($_REQUEST['action'])
	{
		// Post a guestbook entry and store it in the database.
		case 'post-comment':			
			
			if (!isset($aUser->output->user_id))
			{
				echo 'Unable to retrieve your user ID#. Please report this issue.';		
				exit;
			}
			
			if (empty($_POST['comment']))
			{
				echo 'alert(\'Please add some text.\');';
				exit;
			}
			
			$sSql = '
				INSERT INTO gb_comments
				SET user_id = \'' . mysql_real_escape_string($aUser->output->user_id) . '\',
					comment_text = \'' . mysql_real_escape_string(stripslashes($_POST['comment'])) . '\',
					time_stamp = \'' . time() . '\'
			';
			mysql_query($sSql) or die(mysql_error());
			
			// Prepend the new comment using jQuery
			echo '$(\'#guestbook_comments\').prepend(\'' . getLatestMessages(mysql_insert_id()) . '\');';
			break;		
	}
	exit;
}

/**
 * Get all the guestbook entries
 * @param int $iId Optional comment ID in case we want to show a specific comment.
 * @return string HTML output of the guestbook entry
 */
function getLatestMessages($iId = 0)
{
	// Query the database for the entries
	$sSql = '
		SELECT *
		FROM gb_comments
		' . ($iId ? 'WHERE comment_id = ' . (int) $iId : '') . '
		ORDER BY time_stamp DESC
	';
	$hQuery = mysql_query($sSql);
	
	$sContent = '';
	while ($aRow = mysql_fetch_array($hQuery))
	{
		// Build the guestbook entry
		$oUser = App::getUser($aRow['user_id']);
		if (!isset($oUser->photo_50px_square))
		{
			continue;
		}
		
		$sContent .= '<div class="gb_holder">';
		$sContent .= '<div class="gb_image"><img src="' . App::getUser($aRow['user_id'])->photo_50px_square . '" width="50" height="50" /></div>';
		$sContent .= '<div class="gb_content">';
		$sContent .= '<div class="gb_user"><a href="' . App::getUser($aRow['user_id'])->profile_url . '" title="' . App::getUser($aRow['user_id'])->full_name . '" target="_parent">' . App::getUser($aRow['user_id'])->full_name . '</a></div>';
		$sContent .= '<div class="gb_text">'. htmlspecialchars($aRow['comment_text']) . '</div>';
		$sContent .= '<div class="gb_date">'. date('F j, Y, g:i a', $aRow['time_stamp']) . '</div>';
		$sContent .= '</div>';
		$sContent .= '</div>';
	}
	
	$sContent = str_replace(array("\n", "\t"), '', $sContent);
	if ($iId)
	{
		$sContent = str_replace("'", "\'", $sContent);
	}
	
	return $sContent;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
	<head>
		<title>Test Application - Guestbook</title>		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>static/style.php?app_id=<?php echo APP_ID; ?>" />
		<link rel="stylesheet" type="text/css" href="./style.css" />
		<script type="text/javascript">
			$(document).ready(function(){
				
				$('#guestbook_form').submit(function(){

						$.ajax({
							type: 'POST',
							url: './index.php?action=post-comment',
							dataType: 'html',	
							data: 'comment=' + encodeURIComponent($('#guestbook_textarea').val()),
							success: function(sReturn){
								eval(sReturn);
								$('#guestbook_textarea').val('');
								$('#crossdomain_frame').remove();
								$('body').append('<iframe id="crossdomain_frame" src="<?php echo APP_URL; ?>static/crossdomain.php?height=' + document.body.scrollHeight + '&nocache=' + Math.random() + '" height="0" width="0" frameborder="0"></iframe>');
							}				
						});			

						return false;
					});				
				
				$('body').append('<iframe id="crossdomain_frame" src="<?php echo APP_URL; ?>static/crossdomain.php?height=' + document.body.scrollHeight + '&nocache=' + Math.random() + '" height="0" width="0" frameborder="0"></iframe>');
			});		
		</script>
	</head>	
	<body>	
		<h1>Sign our Guestbook</h1>
		<div class="message">
			Please note this is a test application for developers and not a feature that will be part of our product.
		</div>
		<div id="guestbook_holder">
			<div id="guesbook_user_image">
				<a href="<?php echo $aUser->output->profile_url; ?>" title="<?php echo $aUser->output->full_name; ?>"><img src="<?php echo $aUser->output->photo_50px_square; ?>" width="50" height="50" /></a>
			</div>
			<div id="guestbook_content">
				<form method="post" action="./index.php" id="guestbook_form">
					<textarea cols="60" rows="4" name="comment" id="guestbook_textarea"></textarea>
					<div id="guestbook_submit">
						<div id="guestbook_user">
							Logged in as <?php echo $aUser->output->full_name; ?>
						</div>
						<input type="submit" value="Submit" class="button" />
					</div>
				</form>
			</div>
			
			<div id="guestbook_comments">
				<?php echo getLatestMessages(); ?>				
			</div>
			
		</div>
	</body>
</html>