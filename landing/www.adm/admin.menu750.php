<?php
$bo_table = 'landing';
$target_table = $g5['write_prefix'] . $bo_table;

$list = Board::get_all($bo_table, array('wr_use = 1'), false); // 랜딩 게시판 목록
$current_page_code = $_GET['page_code'] ?? $list[0]['wr_page_code']; // 현재 선택된 랜딩 page_code

$menu['menu750'] = array(
    array("750000", "랜딩페이지 DB", G5_ADMIN_URL . "/landing_log/list.php?page_code={$current_page_code}", "landing_log"),
);

foreach ($list as $row) {

    if ($member['mb_id'] !== 'admin' && !in_array($member['mb_id'], explode('|', $row['wr_access_id']), true)) continue;

    $subject = $row['wr_subject'] ?? '제목없음';
    $page_code = $row['wr_page_code'] ?? '';

    $code = ($current_page_code === $page_code) ? '750100' : '';
    // 메뉴 URL
    $url = G5_ADMIN_URL . "/landing_log/list.php?page_code={$page_code}";
    // 메뉴 추가
    $menu['menu750'][] = array($code, $subject, $url, 'landing_log');
}
