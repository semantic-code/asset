<?php
$cfg = include_once('./_config.php');

$allow = array('w', 'bo_table', 'wr_id');
foreach ($allow as $k) if (isset($_GET[$k])) $$k = trim($_GET[$k]);

$board = $cfg['bo_table'];

if($mode === 'update_ca_name'){
    $set = array("ca_name" => $ca_name);
    $sql = "UPDATE {$target_table} SET\n".$Query->build_query($set)."\nWHERE wr_id = '{$wr_id}' ";
    $update = sql_query($sql);
    if($update) {
        $arr_result = array("state" => "success_update_ca_name");
    } else {
        $arr_result = array("state" => "fail_update_ca_name");
    }
} elseif ($mode === 'update_memo'){
    $set = array("wr_memo" => $memo);
    $sql = "UPDATE {$target_table} SET\n".$Query->build_query($set)."\nWHERE wr_id = {$wr_id} ";
    $update = sql_query($sql);
    if($update) {
        $arr_result = array("state" => "success_update_memo");
    } else {
        $arr_result = array("state" => "fail_update_memo");
    }
} elseif ($mode === 'delete'){
    $sql = "DELETE FROM {$target_table} WHERE wr_id = '{$wr_id}' ";
    $delete = sql_query($sql);
    $count = sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write - 1 WHERE bo_table = '{$bo_table}' ");
    if($delete && $count){
        $arr_result = array("state" => "success_delete");
    } else {
        $arr_result = array("state" => "fail_delete");
    }
}

die(json_encode($arr_result));