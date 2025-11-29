<?php
$path = '';
while (!file_exists($path.'common.php') && realpath($path) !== '/') {$path .= '../';}
include_once $path.'common.php';

$target_table = $g5['write_prefix'] . $bo_table;
$wr_num = get_next_num($target_table);

$set = array(
    "wr_num"      => $wr_num,
    "mb_id"       => "guest",
    "wr_name"     => "비회원",
    "wr_subject"  => "제목" . time(),
    "wr_content"  => "내용" . time(),
    "ca_name"     => $ca_name,
    "wr_datetime" => date("Y-m-d H:i:s"),
    "wr_last"     => date("Y-m-d H:i:s"),
    "wr_ip"       => $_SERVER['REMOTE_ADDR'],
    "wr_option"   => "html1",
);

$set+= array(
    "wr_page_code" => $page_code,
    "wr_field_1"   => $wr_field_1,
    "wr_field_2"   => $wr_field_2,
    "wr_field_3"   => $wr_field_3,
    "wr_field_4"   => $wr_field_4,
    "wr_field_5"   => $wr_field_5,
);
$set+= Query::get_empty_fields($target_table);

$sql = "INSERT INTO {$g5['write_prefix']}{$bo_table} SET\n". Query::Build_query($set);

/*
echo "<pre>";
print_r2($sql);
echo "</pre>";
exit;
*/

$insert = sql_query($sql);

$wr_id = sql_insert_id();
sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");

if($insert) alert("내용이 저장되었습니다.", G5_URL."/landing/?page_code={$page_code}");
