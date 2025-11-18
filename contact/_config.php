<?php
/**
 * wr_sort 유형, wr_tel 전화번호, wr_memo 간단메모 추가
**/
$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

$sub_menu = '500100';
$bo_table = 'contact';
$page_title = '문의하기';

$loc = $bo_table;
$target_table = $g5['write_prefix'] . $bo_table;
$board = get_board_db($bo_table);

return array(
    'sub_menu'         => $sub_menu,
    'bo_table'         => $bo_table,
    'board'            => $board,
    'target_table'     => $target_table,
    'loc'              => $loc,
    'bo_upload_count'  => $board['bo_upload_count'],
    'bo_use_category'  => $board['bo_use_category'],
    'bo_category_list' => $board['bo_category_list'],
    'page_title'       => $page_title
);
