<?php
$domain = array();
$not = array();
foreach(file('email.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $email){
	if(preg_match('/([^\@]+)$/', $email, $matches)){
		if(!in_array($matches[1], $not) && (in_array($matches[1], $domain) || checkMxPorts($matches[1]))){
			echo "{$email}\n";
			$domain[] = $matches[1];
			file_put_contents('ids.txt', $email . "\n", FILE_APPEND);
		} else {
			$not[] = $matches[1];
		}
	}
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


