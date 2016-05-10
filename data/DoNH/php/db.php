<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/autoload.php';

$db = new \Basic\Database();
$db->connect('localhost', 'null-vnecoo', 'vnecoo', 'admin@123');

?>