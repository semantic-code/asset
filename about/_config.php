<?php
//error_reporting(E_ALL); // 모든 에러 표시
//ini_set('display_errors', 1); // 에러를 브라우저에 표시

$path = '';
while (!file_exists($path .'common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path .'common.php';

//$sub_menu = '100100';
$bo_table = $location;
$board = get_board_db($bo_table);

if ($board) $target_table = $g5['write_prefix'] . $bo_table;
