<?php
$cmdDir = '../gitlab/cmd/';
$confPath = '../gitlab/config/';

$json = file_get_contents('php://input');
$commit = @json_decode($json, true);

if(!empty($commit) && isset($commit['project']) && 
    isset($commit['project']['name']) && 
    isset($commit['object_kind']) && 
    ($commit['object_kind'] == 'push')){
    
    $name = $commit['project']['name'];
    
    if(file_exists("{$confPath}{$name}.txt")){
       $gitPath = realpath(__DIR__ . "/test/" . $name);
       $cmdPath = $cmdDir. $name . '_';
       
       if(count(glob($cmdPath . '*'))<2){
            $params = array(
               'git_path' => $gitPath,
               'username' => $commit['user_name']
            );
            
            file_put_contents($cmdPath . md5(uniqid()) . '.log', json_encode($params));
       }
    }
}
?>