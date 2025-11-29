<?php
$path = '';
while (!file_exists($path.'_common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'_common.php';

if($wr_ld_page === 'basic'){
    $arr_result = array('state' => 'blocked', 'msg' => '해당 아이디는 사용할 수 없습니다.');
    die(json_encode($arr_result));
}

$sql = "SELECT COUNT(*) cnt FROM {$g5['write_prefix']}{$bo_table} WHERE (1) AND wr_page_code = '{$wr_page_code}' ";
$row = sql_fetch($sql);

if($row['cnt'] > 0){
    $arr_result = array('state' => 'fail', 'msg' => '이미 사용 중인 아이디 입니다.');
}else{
    $arr_result = array('state' => 'success', 'msg' => '사용 가능한 아이디 입니다.');
}

die(json_encode($arr_result));