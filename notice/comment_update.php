<?php
include_once('./_config.php');

if ($action === 'insert') {

    //if (empty($wr_content)) goto_url("view.php?wr_id={$wr_id}");

    // wr_num
    $sql = "SELECT wr_num FROM {$target_table} WHERE wr_is_comment = 0 AND wr_parent = {$wr_id}";
    $row = sql_fetch($sql);
    $wr_num = $row['wr_num'];

    // wr_comment
    $sql = "SELECT wr_comment FROM {$target_table} WHERE wr_is_comment = 1 AND wr_parent = {$wr_id} AND wr_comment_reply = '' ORDER BY wr_comment DESC LIMIT 1";
    $row = sql_fetch($sql);
    $wr_comment = $row['wr_comment'] + 1;

    $set = array(
        "wr_num"        => $wr_num,
        "wr_parent"     => $wr_id,
        "wr_is_comment" => 1,
        "wr_comment"    => $wr_comment,
        "wr_content"    => $wr_content,
        "wr_datetime"   => G5_TIME_YMDHIS,
        "wr_last"       => '',
        "wr_ip"         => $_SERVER['REMOTE_ADDR'],
    );

    $set+= Query::get_empty_fields($target_table);

    $sql = "INSERT INTO {$target_table} SET\n" . Query::build_query($set);
    $insert = sql_query($sql);

    if ($insert) {
        // 댓글 갯수 부모 wr_comment 업데이트
        $row = sql_fetch("SELECT COUNT(*) cnt FROM g5_write_notice WHERE wr_parent = {$wr_id} AND wr_is_comment = 1 AND wr_comment_reply = ''");
        $wr_comment = $row['cnt'];
        sql_query("UPDATE {$target_table} SET wr_comment = {$wr_comment} WHERE wr_id = {$wr_id} ");

        goto_url("view.php?wr_id={$wr_id}");
    }

} elseif ($action === 'update') {
    $sql = "UPDATE {$target_table} SET wr_content = '{$wr_content}' WHERE wr_id = {$co_id}";
    $update = sql_query($sql);

    if ($update) {
        goto_url("view.php?&wr_id={$wr_id}");
    }
} elseif ($action === 'delete') {

    $sql = "DELETE FROM {$target_table} WHERE wr_id = {$co_id} ";
    $delete = sql_query($sql);
    sql_query("UPDATE {$target_table} SET wr_comment = wr_comment - 1 WHERE wr_id = {$wr_id} ");

    if ($delete) goto_url("view.php?wr_id={$wr_id}");
}

