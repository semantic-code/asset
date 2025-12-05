<?php
include_once '_config.php';

$wr_num = get_next_num($target_table);

// 기본
$set = array(
    "wr_num"      => $wr_num,
    "ca_name"     => $ca_name ?? "대기중",
    "mb_id"       => "guest",
    "wr_name"     => $wr_name,
    "wr_subject"  => $wr_subject ?? "제목-" . time(),
    "wr_content"  => $wr_content ?? "내용-" . time(),
    "wr_email"    => $wr_email,
    "wr_ip"       => $_SERVER['REMOTE_ADDR'],
    "wr_datetime" => G5_TIME_YMDHIS,
    "wr_last"     => G5_TIME_YMDHIS,
    "wr_option"   => "html1"
);

// 추가
$set+= array(
    "wr_sort" => $wr_sort,
    "wr_tel"  => $wr_tel,
);

// 나머지
$set+= Query::get_empty_fields($target_table);

$sql = "INSERT INTO {$target_table} SET\n" . Query::build_query($set);
sql_query($sql);

$wr_id = sql_insert_id();
if ($wr_id > 0) {
    sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
    sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");

    alert("글이 등록되었습니다.", "form.php");
    //goto_url("form.php?w=u&wr_id={$wr_id}");

} else {
    alert("데이터 저장에 실패했습니다.", "form.php");
}
