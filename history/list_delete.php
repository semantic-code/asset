<?php
include_once('_config.php');

if($w === 'd'){
    $sql = "DELETE FROM {$g5['write_prefix']}{$bo_table} where wr_id = {$wr_id} ";
    $delete = sql_query($sql);

    if ($delete) goto_url("list.php?sca={$sca}");
}