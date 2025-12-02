<?php
include_once('_config.php');

if ($w === '') {
    $wr_num = get_next_num($target_table);

    $set = array(
        'wr_num'      => $wr_num,
        'ca_name'     => $ca_name ?? '',
        'wr_subject'  => $wr_subject ?? '제목-내용없음-' . time(),
        'wr_content'  => $wr_content ?? '내용-내용없음-' . time(),
        'wr_datetime' => date("Y-m-d H:i:s"),
        'wr_last'     => date("Y-m-d H:i:s"),
        'wr_ip'       => $_SERVER['REMOTE_ADDR'],
        'wr_option'   => 'html1',
    );
    $set+= array(
        'wr_year'  => $wr_year,
        'wr_month' => $wr_month,
        'wr_sort'  => $wr_sort,
    );

    $set += Query::get_empty_fields($target_table);

    $sql = "INSERT INTO {$target_table} SET\n". Query::build_query($set);
    $insert = sql_query($sql);

    if ($insert) {
        $wr_id = sql_insert_id();
        sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
        sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");

        goto_url("form.php?w=u&wr_id={$wr_id}");
    }

} else {
    $set = array(
        "ca_name"    => $ca_name ?? '',
        "wr_subject" => "제목-".time(),
        "wr_content" => $wr_content,
        "wr_sort"    => $wr_sort,
        "wr_year"    => $wr_year,
        "wr_month"   => $wr_month,
        "wr_last"    => G5_TIME_YMDHIS,
    );

    $sql = "UPDATE {$g5['write_prefix']}{$bo_table} SET\n" . Query::build_query($set) . "\nWHERE wr_id = {$wr_id} ";
    $update = sql_query($sql);

    if ($update) goto_url("form.php?w=u&wr_id={$wr_id}");
}
