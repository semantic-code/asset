<?php
include_once('_config.php');

if ($w === '') {

    $wr_num = get_next_num($target_table);

    $set = array(
        'wr_num'      => $wr_num,
        'ca_name'     => $ca_name ?? '',
        'mb_id'       => $member['mb_id'] ?? '_guest_',
        'wr_name'     => $member['mb_name'],
        'wr_subject'  => $wr_subject ?? '제목-내용없음-' . time(),
        'wr_content'  => $wr_content ?? '내용-내용없음-' . time(),
        'wr_datetime' => date("Y-m-d H:i:s"),
        'wr_last'     => date("Y-m-d H:i:s"),
        'wr_ip'       => $_SERVER['REMOTE_ADDR'],
        'wr_option'   => 'html1',
    );
    $set+= array(
        'wr_tel'         => $wr_tel,
        'wr_zip'         => $wr_zip ?? '',
        'wr_addr1'       => $wr_addr1,
        'wr_addr2'       => $wr_addr2,
        'wr_addr3'       => $wr_addr3,
        'wr_addr_jibeon' => $wr_addr_jibeon,
        'wr_use'         => $wr_use,
    );

    $set += Query::get_empty_fields($target_table);

    $sql = "INSERT INTO {$target_table} SET\n". Query::build_query($set, $board['bo_use_dhtml_editor'] ? 'wr_content' : '');
    $insert = sql_query($sql);

    if ($insert) {
        $wr_id = sql_insert_id();
        sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
        sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");

        goto_url("form.php?w=u&wr_id={$wr_id}");

    } else {
        alert("데이터 저장에 실패했습니다.", "list.php");
    }

} else {

    $set = array(
        'ca_name'    => $ca_name ?? '',
        'wr_subject' => $wr_subject,
        'wr_content' => $wr_content,
        'wr_last'    => date("Y-m-d H:i:s"),
    );

    $set+= array(
        'wr_tel'         => $wr_tel,
        'wr_zip'         => $wr_zip ?? '',
        'wr_addr1'       => $wr_addr1,
        'wr_addr2'       => $wr_addr2,
        'wr_addr3'       => $wr_addr3,
        'wr_addr_jibeon' => $wr_addr_jibeon,
        'wr_use'         => $wr_use,
    );

    $sql = "UPDATE {$target_table} SET\n". Query::build_query($set, $board['bo_use_dhtml_editor'] ? 'wr_content' : '') . "\nWHERE wr_id = {$wr_id} ";
    $update = sql_query($sql);

    if ($update) {

        goto_url("form.php?w=u&wr_id={$wr_id}");

    } else {
        alert("데이터 수정에 실패했습니다.", "list.php");
    }
}
