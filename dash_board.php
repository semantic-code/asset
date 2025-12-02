<?php
$sub_menu = '350100';
include_once('./_common.php');

$g5['title'] = '통합관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
    .dashboard{max-width:900px; margin: 0 auto;}
    .section-title{font-size:20px;font-weight:bold;margin:20px 0 10px;}
    .divider{height:1px;background:#ddd;margin:10px 0;}
    .card-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:30px;}
    .card{border:1px solid #ddd;border-radius:6px;padding:20px;text-align:center;background:#f5f5f5;}
    .card a{text-decoration:none;color:#333;font-size:16px;font-weight:bold;display:block;}
    .card:hover{background:#fff;}
</style>

<section>
    <div class="dashboard">

        <!-- 게시판 관리 -->
        <h2 class="section-title">내용관리</h2>
        <div class="divider"></div>

        <div class="card-grid">
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/license/list.php">면허 / 인증</a></div>
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/equipment/list.php">장비현황</a></div>
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/business/list.php">시공사례</a></div>
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/portfolio/list.php">연혁</a></div>
        </div>

        <!-- 상담 및 문의 -->
        <h2 class="section-title">문의하기</h2>
        <div class="divider"></div>

        <div class="card-grid">
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/contact/list.php">문의하기</a></div>
        </div>

        <!-- 공지사항 게시판 -->
        <h2 class="section-title">공지사항</h2>
        <div class="divider"></div>

        <div class="card-grid">
            <div class="card"><a href="<?= G5_ADMIN_URL ?>/notice/list.php">문의하기</a></div>
        </div>
    </div>
</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
