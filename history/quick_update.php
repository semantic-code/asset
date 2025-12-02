<?php
include_once('_config.php');

$wr_num = get_next_num($target_table);

$set = array(
    "wr_num"      => $wr_num,
    "ca_name"     => $ca_nameb ?? '',
    "wr_subject"  => "제목-".time(),
    "wr_content"  => $wr_content,
    "wr_sort"     => $wr_sort,
    "wr_year"     => $wr_year,
    "wr_month"    => $wr_month,
    "wr_datetime" => G5_TIME_YMDHIS,
);

$sql = "INSERT INTO {$target_table} SET\n" . Query::build_query($set);
$insert = sql_query($sql);

if ($insert) goto_url("list.php?sca={$ca_name}&year={$wr_year}&month={$wr_month}");

