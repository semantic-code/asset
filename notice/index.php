<?php
$location = 'notice';
$main_menu = '100000';
$head_menu = $main_menu;
$login_access = 'N';
$page_key = 'mode';
$allow_level = '';

include_once ($_SERVER['DOCUMENT_ROOT'] . '/common.php');

// mode 분리
$get_mode = $_GET[$page_key] ?? '';
$post_mode = $_POST[$page_key] ?? '';

// 접근 가능한 mode
$access_mode = array('list', 'form');
$process_mode = array('insert');

// 예외 메팅
$ex_mode = array(
    'insert' => 'enroll',
    'update' => 'enroll_update',
    'delete' => 'enroll_delete'
);

// 로그인 확인 (필요시)
if ($login_access === 'Y' && empty($_SESSION['user_id'])) {
    ob_start(); ?>
    <script>
        alert('로그인 후 접근 가능합니다.');
        window.history.back();
    </script>
    <?php die(ob_get_clean());
}

// 회원 레벨 검사, 빈값이면 제한 없음 (필요시)
$mb_level = (int)$_SESSION['member_level'] ?? 0;
if (!empty($allow_level) && $mb_level < (int)$allow_level) {
    ob_start(); ?>
    <script>
        alert('해당 페이지는 접근 권한이 없습니다.');
        window.history.back();
    </script>
    <?php die(ob_get_clean());
}

// mode 설정, 가져오기
$mode = $get_mode ?: $post_mode;
if ($mode === '') $mode = 'list';

// 접근 가능한 mode인지 검사
if (!in_array($mode, array_merge($access_mode, $process_mode))) {
    ob_start(); ?>
    <script>
        alert('해당 페이지는 접근할 수 없습니다.');
        window.history.back();
    </script>
    <?php die(ob_get_clean());
}

// 실제 include 파일명 변환
$include_file = $ex_mode[$mode] ?? $mode;
$path = $_SERVER['DOCUMENT_ROOT']. "/ {$location}/{$include_file}.php";

// 파일 확인
if (file_exists($path)) {
    include_once ($path);
    exit;
} else {
    ob_start(); ?>
    <script>
        alert('요청한 페이지를 찾을 수 없습니다.');
        window.history.back();
    </script>
    <?php die(ob_get_clean());
}
