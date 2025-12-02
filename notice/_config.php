<?php
//error_reporting(E_ALL); // 모든 에러 표시
//ini_set('display_errors', 1); // 에러를 브라우저에 표시

$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

$sub_menu = '500100';
$bo_table = 'notice';
$page_title = '공지사항';

$target_table = $g5['write_prefix'] . $bo_table;
$board = get_board_db($bo_table);
