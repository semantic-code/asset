<?php
$colspan = 8;
include_once('./_config.php');

// board
$bo_upload_count = $board['bo_upload_count'];
$bo_use_category = $board['bo_use_category'];
$bo_use_search = true;

$g5['title'] = $page_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

$result = Board::get($bo_table, array(), $board['bo_page_rows']);
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
        <!-- 카테고리 -->
        <div class="category-group">
            <?php $arr_cate = explode('|', $board['bo_category_list'] ?? ''); ?>
            <?= Html::category($arr_cate) ?>
        </div>
    <?php endif; ?>

    <?php if ($bo_use_search): ?>
        <!-- 검색 -->
        <div class="search-group">
            <?php $arr_search = array('wr_subject' => '제목'); ?>
            <?= Html::search($arr_search, array('sca')) ?>
        </div>
    <?php endif; ?>

    <div class="tbl_head01">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <colgroup>
                <col style="width: 60px;">   <!-- 번호 -->
                <col style="width: 10%;">   <!-- 카테고리 -->
                <col style="width: auto;">  <!-- 제목 -->
                <col style="width: 20%;">   <!-- 이미지 -->
                <col style="width: 10%;">   <!-- 사용 -->
                <col style="width: 10%;">   <!-- 등록일 -->
                <col style="width: 15%;">   <!-- 관리 -->
            </colgroup>
            <thead>
            <tr>
                <th scope="col">NO</th>
                <th scope="col">카테고리</th>
                <th scope="col">제목</th>
                <th scope="col">이미지</th>
                <th scope="col">사용</th>
                <th scope="col">등록일</th>
                <th scope="col">관리</th>
            </tr>
            </thead>

            <tbody>
            <?php if (empty($list)): ?>
                <tr><td colspan="<?= $colspan ?>">데이터가 없습니다.</td></tr>
            <?php else: foreach ($list as $row): ?>
                <tr>
                    <td><?= $num-- ?></td>
                    <td><?= $row['ca_name'] ?></td>
                    <td><?= $row['wr_subject'] ?></td>
                    <td>
                        <img src="<?= $row['img_src'] ?>" alt="이미지출력" style="width: 150px;">
                    </td>
                    <td><?= $row['wr_use'] == 1 ? '사용' : '미사용' ?></td>
                    <td><?= date("Y-m-d", strtotime($row['wr_datetime'])) ?></td>
                    <td class="td_mng">
                        <a href="<?= $row['href'] ?>" class="btn btn_03">보기</a>
                        <a href="list_update.php?w=d&wr_id=<?=$row['wr_id']?>"
                           class="btn btn_02"
                           onclick="return confirm('삭제하시겠습니까?')"
                        >삭제</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="paging"><?= $paging ?></div>
</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
