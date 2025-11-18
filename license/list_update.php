<?php
$cfg = include_once('./_config.php');

$allow = array('w', 'bo_table', 'wr_id');
foreach ($allow as $k) if (isset($_GET[$k])) $$k = trim($_GET[$k]);

$board = $cfg['bo_table'];

if ($w === 'd') {
    $sql = "DELETE FROM {$g5['write_prefix']}{$bo_table} WHERE wr_id = {$wr_id} ";
    $delete = sql_query($sql);
    sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write - 1 WHERE bo_table = '{$bo_table}' ");

    if ($delete) {
        $File->delete_file($bo_table, $wr_id);
        goto_url("list.php");
    }
}
