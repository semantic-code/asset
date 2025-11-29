<?php
include_once '_config.php';

$target_table = $g5['write_prefix'] . $bo_table;
$wr_num = get_next_num($target_table);

$files = $_FILES['bf_file'] ?? array();

if ($w === '') {
    // 기본
    $set = array(
        "wr_num"      => $wr_num,
        "wr_subject"  => $wr_subject,
        "wr_datetime" => date("Y-m-d H:i:s", time()),
        "wr_last"     => date("Y-m-d H:i:s", time()),
        "wr_ip"       => $_SERVER['REMOTE_ADDR'],
        "wr_option"   => "html1"
    );
    // 추가
    $set+= array(
        "wr_page_code"  => $wr_page_code,
        "wr_access_id"  => $wr_access_id,
        "wr_cate_list"  => $wr_cate_list ?? '',
        "wr_use_cate"   => $wr_use_cate ?? 0,
        "wr_use_search" => $wr_use_search ?? 0,
        "wr_fields"     => $wr_fields,
        "wr_sort_field" => $wr_sort_field,
        "wr_use"        => $wr_use,
    );
    $set+= Query::get_empty_fields($target_table);

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/



    $sql = "INSERT INTO {$target_table} SET\n" . Query::build_query($set);
    $insert = sql_query($sql);
    $wr_id = sql_insert_id();

    sql_query(" UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}' ");
    sql_query(" UPDATE {$g5['board_table']} SET bo_count_write = bo_count_write + 1 WHERE bo_table = '{$bo_table}' ");

    if ($insert) {
        // 파일 첨부
        if (!File::attach_files($files, $bo_table, $wr_id)) {
            alert("파일 첨부에 실패했습니다.");

        } else {
            goto_url("list.php");
        }

    } else {
        alert('데이터 저장에 실패했습니다.');
    }

} elseif ($w === 'u') {
    // 수정
    if (!$wr_id) alert('잘못된 접근입니다.');

    $set = array(
        "wr_subject"  => $wr_subject,
        "wr_datetime" => date("Y-m-d H:i:s", time()),
        "wr_last"     => date("Y-m-d H:i:s", time()),
        "wr_ip"       => $_SERVER['REMOTE_ADDR'],
    );
    // 추가
    $set+= array(
        "wr_access_id"  => $wr_access_id,
        "wr_cate_list"  => $wr_cate_list ?? '',
        "wr_use_cate"   => $wr_use_cate,
        "wr_use_search" => $wr_use_search,
        "wr_fields"     => $wr_fields,
        "wr_sort_field" => $wr_sort_field,
        "wr_use"        => $wr_use,
    );

    $sql = "UPDATE {$target_table} SET\n" . Query::build_query($set) . "\nWHERE wr_id = '{$wr_id}' ";
    //die($sql);
    $update = sql_query($sql);

    if ($update) {
        // 삭제요청 파일 삭제
        $sql = "SELECT bf_no FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' ";
        $result = sql_query($sql);
        $keep_file = isset($keep_file) ? $keep_file : array();

        /*
        echo "<pre>";
        print_r($keep_file);
        echo "</pre>";
        exit;
*/

        while ($row = sql_fetch_array($result)) {
            if (!in_array($row['bf_no'], $keep_file)) {
                File::delete_attach_file($bo_table, $wr_id, $row['bf_no']);
            }
        }

        // 새로운 파일 업로드
        if (!File::attach_files($files, $bo_table, $wr_id)) {
            alert("파일 수정에 실패했습니다.");
        } else {
            goto_url("form.php?w=u&wr_id={$wr_id}");
        }

    } else {
        alert('데이터 수정에 실패했습니다.');
    }
}




