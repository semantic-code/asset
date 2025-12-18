<?php
//error_reporting(E_ALL); // 모든 에러 표시
//ini_set('display_errors', 1); // 에러를 브라우저에 표시

$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

$sub_menu = '500100';
$bo_table = $location ?? 'contact';
$page_title = '문의하기';

$board = get_board_db($bo_table);
if (!empty($board['bo_table'])) {
    $target_table = $g5['write_prefix'] . $bo_table;
} else {
    die('board  값이 없습니다.');
}
