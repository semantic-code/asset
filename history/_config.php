<?php
error_reporting(E_ALL); // 모든 에러 표시
ini_set('display_errors', 1); // 에러를 브라우저에 표시

$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

// 기본 게시판 정보
$bo_table = 'history';
$board = get_board_db($bo_table);
// 원본 글 테이블
$target_table = $g5['write_prefix'] . $bo_table;

// 기본 정보
$sub_menu = '500100';
$page_title = '연혁';
