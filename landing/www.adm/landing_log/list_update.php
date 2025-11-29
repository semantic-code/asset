<?php
include_once '_config.php';

$target_table = $g5['write_prefix'] . $bo_table;

if ($mode === 'update_category') {
    $set = array("ca_name" => $ca_name);
    $sql = "UPDATE {$target_table} SET\n" . Query::build_query($set) . "\nWHERE wr_id = '{$wr_id}' ";
    $update = sql_query($sql);

    if ($update) {
        $arr_result = array("state" => "success_update_category");
    } else {
        $arr_result = array("state" => "fail_update_category");
    }
} elseif ($mode === 'update_memo') {
    $set = array("wr_memo" => $wr_memo);
    $sql = "UPDATE {$target_table} SET\n" . Query::build_query($set) . "\nWHERE wr_id = '{$wr_id}' ";
    $update = sql_query($sql);

    if ($update) {
        $arr_result = array("state" => "success_update_memo");
    } else {
        $arr_result = array("state" => "fail_update_memo");
    }
} elseif ($mode === 'delete') {
    $sql = "DELETE FROM {$target_table} WHERE wr_id = '{$wr_id}' ";
    $delete = sql_query($sql);

    if ($delete) {
        $arr_result = array("state" => "success_delete");
    } else {
        $arr_result = array("state" => "fail_delete");
    }
}

die(json_encode($arr_result));