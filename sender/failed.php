<?php
require './PhpImap/__autoload.php';

$mailbox = new PhpImap\Mailbox('{imap.gmail.com:993/imap/ssl}INBOX', 'big34562345659@gmail.com', '2Congaquay', __DIR__);

$ids = $mailbox->searchMailbox('ALL');
if(!empty($ids)){
	foreach($ids as $id){
		$mail = $mailbox->getMail($id);
		echo "{$mail->toString}\n";
		file_put_contents("./failed.dat", $mail->toString . "\n", FILE_APPEND );
	}
	
}
