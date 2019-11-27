<?php

ini_set('display_errors', 1);
$root = dirname(__DIR__);
require 'class.php';
//
session_start();
$json = json_decode(file_get_contents('php://input'), true);

$pdf = new pdfGenerator(['DUCK!', 'bad_word']);
$output = $pdf->connect($json);

$pdf->save_session();

ob_start('ob_gzhandler');
header('Content-type: application/json');

echo $output;
$buffer = ob_get_contents();
ob_end_clean();
echo $buffer;

exit();

?>
