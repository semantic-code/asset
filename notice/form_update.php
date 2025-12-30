<?php
include_once('./_config.php');

$files = $_FILES['bf_file'];

if ($w === '') {

    $wr_num = get_next_num($target_table);

    $set = array(
        'wr_num'      => $wr_num,
        'ca_name'     => $ca_name ?? '',
        'mb_id'       => $member['mb_id'] ?? 'guest',
        'wr_name'     => $member['mb_name'] ?? '비회원',
        'wr_subject'  => $wr_subject ?? '제목-내용없음-' . time(),
        'wr_content'  => $wr_content ?? '내용-내용없음-' . time(),
        'wr_datetime' => date("Y-m-d H:i:s"),
        'wr_last'     => date("Y-m-d H:i:s"),
        'wr_ip'       => $_SERVER['REMOTE_ADDR'],
        'wr_option'   => 'html1',
    );
    $set+= array(
        //'wr_use'        => $wr_use,
    );

    $set += Query::get_empty_fields($target_table);

    $sql = "INSERT INTO {$target_table} SET\n". Query::build_query($set, $board['bo_use_dhtml_editor'] ? array('wr_content') : array());
    $insert = sql_query($sql);

    if ($insert) {
        $wr_id = sql_insert_id();
        sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
        sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");
    }        
    if (!File::attach_files($files, $bo_table, $wr_id)) {
        alert("파일 저장 실패", "form.php?w=u&wr_id={$wr_id}");
    }        
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
        //'wr_use' => $wr_use,
    );

    $sql = "UPDATE {$target_table} SET\n". Query::build_query($set, $board['bo_use_dhtml_editor'] ? array('wr_content') : array()) . "\nWHERE wr_id = {$wr_id} ";
    $update = sql_query($sql);

    if ($update) {
        // 파일삭제
        foreach ($bf_file_del ?? array() as $bf_no) {
            File::delete_attach_file($bo_table, $wr_id, $bf_no);
        }
        // 파일 업로드
        if (!File::attach_files($files ?? array(), $bo_table, $wr_id)){
            alert("파일 업로드를 실패했습니다.", "list.php");
        }      
        goto_url("form.php?w=u&wr_id={$wr_id}");

    } else {
        alert("데이터 수정에 실패했습니다.", "list.php");
    }

}


