<?php
include_once('../common.php');
$bo_table = "landing";

if ($page_code !== null) {
    $row = sql_fetch("SELECT wr_use FROM {$g5['write_prefix']}{$bo_table} WHERE wr_page_code = '{$page_code}' ");
    $is_use = $row['wr_use'] ?? null;
}

$state = null;

$skin_path = __DIR__ . "/{$page_code}";
$skin_url  = G5_URL . "/landing/{$page_code}";

if (!is_file("{$skin_path}/landing.skin.php")) {
    $state = 'no_file';
}

if ($page_code == null) {
    $state = 'no_id';
} elseif ($is_use == 0) {
    $state = 'not_use';
}

if ($state !== null) {
    die(render_empty($state, $is_admin));
}

// CSS 적용
add_stylesheet('<link rel="stylesheet" href="'.$skin_url.'/style.css">', 0);

// 출력
include $skin_path.'/landing.skin.php';

function render_empty($state, $is_admin) {
    ob_start();?>
    <div class="landing-wrap">
        <h1>Default Landing Page</h1>
        <p>기본 랜딩페이지 입니다.</p>
        <?php if ($state == 'no_id'): ?>
        <p>랜딩페이지 ID를 지정하지 않으셨습니다.</p>
        <?php elseif ($state == 'not_use'): ?>
            <?php if ($is_admin === 'super'):?>
            <p>랜딩페이지가 '사용안함'으로 설정되어 있습니다.</p>
            <?php endif; ?>
        <?php elseif ($state == 'no_file'): ?>
        <p>랜딩페이지 파일이 없습니다.</p>
            <?php if ($is_admin === 'super'):?>
            <p>랜딩페이지 스킨을 생성하지 않은 경우, 먼저 스킨을 생성하십시오.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php return ob_get_clean();
}
