<?php
include_once '_config.php';

$target_table_1 = $g5['write_prefix'] . $bo_1;
$target_table_2 = $g5['write_prefix'] . $bo_2;

if (!$wr_id) alert('잘못된 접근입니다.');

if($w === 'd'){
    //파일, db 삭제
    File::delete_attach_file($bo_table, $wr_id);

    //랜딩페이지 삭제
    $sql = "DELETE FROM {$target_table_1} WHERE wr_id = '{$wr_id}' ";
    sql_query($sql);

    //랜딩 페이지 데이터 삭제
    $sql = "DELETE FROM {$target_table_2} WHERE wr_page_code = '{$page_code}' ";
    sql_query($sql);

    //landing/{ld_page} 폴더 삭제
    rrmdir(G5_PATH.'/landing/'. $page_code);

    goto_url("list.php");
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;

    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object === '.' || $object === '..') continue;

        $path = $dir . '/' . $object;

        if (is_dir($path)) {
            rrmdir($path); // 하위폴더도 다시 rrmdir() 호출
        } else {
            @unlink($path); // 파일 삭제
        }
    }
    @rmdir($dir); // 폴더 삭제
}