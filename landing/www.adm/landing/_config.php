<?php
$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

// 기본 게시판 정보
$bo_table = 'landing';
$board = get_board_db($bo_table);

// 원본 글 테이블
$target_table = $g5['write_prefix'] . $bo_table;
