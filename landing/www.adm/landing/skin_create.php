<?php
include_once '_config.php';

if(!$page_code) alert('잘못된 접근입니다.');

// 기본 스킨 폴더와 대상 폴더
$src = G5_PATH.'/landing/basic';   // 기본 템플릿
$dst = G5_PATH.'/landing/' . $page_code;    // 새 랜딩 폴더

// 디렉토리 복사 함수
function copy_directory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);

    while(false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..') continue;

        if (is_dir($src.'/'.$file)) {
            copy_directory($src.'/'.$file, $dst.'/'.$file);
        } else {
            copy($src.'/'.$file, $dst.'/'.$file);
        }
    }
    closedir($dir);
}

// 이미 폴더가 있으면 에러 처리
if (is_dir($dst)) {
    alert('이미 생성된 스킨 폴더가 있습니다.', 'list.php');
}

// 복사 실행
copy_directory($src, $dst);

// 완료 후 리스트로 이동
//alert('스킨이 생성되었습니다.', './landing_list.php');
goto_url('list.php');