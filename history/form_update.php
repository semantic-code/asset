<?php
include_once('_config.php');

$set = array(
    "ca_name"     => $ca_name ?? '',
    "wr_subject"  => "제목-".time(),
    "wr_content"  => $wr_content,
    "wr_sort"     => $wr_sort,
    "wr_year"     => $wr_year,
    "wr_month"    => $wr_month,
    "wr_datetime" => G5_TIME_YMDHIS,
);

$sql = "UPDATE {$g5['write_prefix']}{$bo_table} SET\n" . Query::build_query($set) . "\nWHERE wr_id = {$wr_id} ";
$update = sql_query($sql);

if ($update) goto_url("form.php?w=u&wr_id={$wr_id}");
