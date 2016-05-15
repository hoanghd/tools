<?php
require './PHPMailer/autoload.php';

$title = "Cơ hội nhận được Iphone 6! Tham gia ngay kẽo lỡ.";
$content = renderInternal('iphone6');

$curr = (int)file_get_contents("./curr.dat");
foreach(file('emails.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $i=>$email){
	file_put_contents("./curr.dat", $i);
	if($i>$curr && isValid($email)){
		echo "================================\n";
		echo "{$i} -> {$email}\n";
		echo "================================\n";
		sendMail($email, $title, $content);
		sleep(rand(11, 20));
	}
}

function isValid($email){
	$not = array('facebook.com', 'taembe.com', 'trangcall.com', 'google.com', 'friend.com' ,'eic.com.vn', 'daiphuc.com.vn', 'jp.com.vn', 'lhc.vn');
	
	if(preg_match('/([^\@]+)$/', $email, $matches)){
		if(!in_array($matches[1], $not) && checkMxPorts($matches[1])){
			return true;
		}
	}
	return false;
}

function checkMxPorts($domain)
{
	$records=dns_get_record($domain, DNS_MX);
	if($records===false || empty($records))
		return false;
	usort($records,'mxSort');
	foreach($records as $record)
	{
		$handle=fsockopen($record['target'],25);
		if($handle!==false)
		{
			fclose($handle);
			return true;
		}
	}
	return false;
}
function mxSort($a, $b)
{
	return $a['pri']-$b['pri'];
}

function renderInternal($_viewFile_, $_data_=null)
{
	// we use special variable names here to avoid conflict when extracting data
	if(is_array($_data_))
		extract($_data_, EXTR_PREFIX_SAME, 'data');
	else
		$data=$_data_;
		
	ob_start();
		ob_implicit_flush(false);
		require('./views/' . $_viewFile_ . '.php');
		return ob_get_clean();
    }
	
function sendMail($sendTo, $subject, $body){
	$mail = new PHPMailer;
	
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 2;
	$mail->Debugoutput = 'html';
	$mail->CharSet = "utf-8";
	
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 587; 
	
	$list = array('big34562345657', 
				  'big34562345658', 
				  'big34562345659', 
				  'big34562345610');
	$id = 'big34562345657'; 
	$mail->Username = $id.'@gmail.com';
	$mail->Password = '2Congaquay';
	$mail->setFrom($id.'@gmail.com', 'BigSale');
	
	$mail->addAddress($sendTo);
	
	$mail->Subject = $subject;
	$mail->msgHTML($body);
	
	return $mail->send();
}