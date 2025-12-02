<?php
//error_reporting(E_ALL); // 모든 에러 표시
//ini_set('display_errors', 1); // 에러를 브라우저에 표시

$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

$sub_menu = '750100';
$bo_table = 'landing_log';
$cf_bo_table = 'landing';
$page_title = '랜딩페이지 DB';

$target_table = $g5['write_prefix'] . $bo_table;
$board = get_board_db($bo_table);

