<?php
$colspan = 8;
include_once('./_config.php');

// board
$bo_upload_count = $board['bo_upload_count'];
$bo_use_category = $board['bo_use_category'];
$bo_use_search = true;

$g5['title'] = $page_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

$result = Board::get($bo_table, array('wr_is_comment = 0'), $board['bo_page_rows']);
$list = $result['list'];
$num = $result['num'];

foreach ($list as &$row) {
    $row['href'] = "form.php?w=u&wr_id={$row['wr_id']}";
    $row['img_src'] = "{$row['file'][0]['path']}/{$row['file'][0]['file']}";
} unset($row);

// 페이징
$page = $_GET['page'] ?? 1;
$paging = Board::paging($result['total'], $page, $board['bo_page_rows'], 5, "list.php?sca={$sca}&sfl={$sfl}&stx={$stx}&page=");

//print_r2($result['sql']);
?>

<section>
    <div class="btn_fixed_top">
        <a href="form.php" class="btn btn_01">등록</a>
    </div>

    <?php if ($bo_use_category): ?>
        <div class="category-group">
            <?php $arr_cate = explode('|', $board['bo_category_list'] ?? ''); ?>
            <?= Html::category($arr_cate) ?>
        </div>
    <?php endif; ?>

    <?php if ($bo_use_search): ?>
        <div class="search-group">
            <?php $arr_search = array('wr_subject' => '제목'); ?>
            <?= Html::search($arr_search, array('sca')) ?>
        </div>
    <?php endif; ?>

    <!-- 카드형 갤러리 리스트 시작 -->
    <div class="gallery-wrap">

        <?php if (empty($list)): ?>
            <p style="grid-column:1/-1;text-align:center;padding:50px 0;">데이터가 없습니다.</p>

        <?php else: foreach ($list as $row): ?>
            <div class="gallery-item">

                <!-- 이미지 -->
                <img src="<?= $row['img_src'] ?>" alt="이미지">

                <!-- 정보 -->
                <div class="gallery-info">
                    <div class="gallery-title"><?= $row['wr_subject'] ?></div>
                    <div class="gallery-cate"><?= $row['ca_name'] ?></div>
                    <div class="gallery-use"><?= $row['wr_use'] ? '사용중' : '미사용' ?></div>
                    <div class="gallery-date"><?= date("Y-m-d", strtotime($row['wr_datetime'])) ?></div>
                </div>

                <!-- 버튼 -->
                <div class="gallery-btn">
                    <a href="<?= $row['href'] ?>" class="btn-view">보기</a>
                    <a href="list_update.php?w=d&wr_id=<?= $row['wr_id'] ?>"
                       onclick="return confirm('삭제하시겠습니까?')"
                       class="btn-del">삭제</a>
                </div>

            </div>
        <?php endforeach; endif; ?>

    </div>
    <!-- 카드형 갤러리 리스트 끝 -->

    <div class="paging"><?= $paging ?></div>
</section>

<style>
    .gallery-wrap {display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;margin-top:20px;}
    .gallery-item {border:1px solid #ddd;border-radius:8px;background:#fff;padding:15px;display:flex;flex-direction:column;}
    .gallery-item img {width:100%;height:180px;object-fit:cover;border-radius:6px;border:1px solid #eee;}
    .gallery-info {margin-top:10px;}
    .gallery-title {font-size:16px;font-weight:600;margin-bottom:5px;}
    .gallery-cate {font-size:13px;color:#777;margin-bottom:3px;}
    .gallery-use {font-size:13px;color:#333;font-weight:500;margin-bottom:8px;}
    .gallery-date {font-size:12px;color:#aaa;margin-bottom:10px;}
    .gallery-btn {display:flex;gap:8px;margin-top:auto;}
    .gallery-btn a {flex:1;text-align:center;padding:6px 0;border-radius:4px;font-size:13px;text-decoration:none;}
    .gallery-btn .btn-view {background:#007bff;color:#fff;}
    .gallery-btn .btn-del {background:#dc3545;color:#fff;}
</style>


<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');

