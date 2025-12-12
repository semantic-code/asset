<?php
$colspan = 8;
include_once('./_config.php');
$sub_menu = '500200';

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
                    <a href="<?= $row['href'] ?>" class="btn btn_03">보기</a>
                    <a href="list_update.php?w=d&wr_id=<?= $row['wr_id'] ?>"
                       onclick="return confirm('삭제하시겠습니까?')"
                       class="btn btn_02">삭제</a>
                </div>

            </div>
        <?php endforeach; endif; ?>

    </div>
    <!-- 카드형 갤러리 리스트 끝 -->

    <div class="paging"><?= $paging ?></div>
</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
