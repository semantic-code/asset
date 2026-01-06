<?php
$colspan = 9;
include_once('./_config.php');
$g5['title'] = $page_title . ' 목록';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// board
$bo_use_category = $board['bo_use_category'];
$bo_page_rows = $board['bo_page_rows'];
$bo_use_search = true;

// list
$result = Board::get($bo_table, array('wr_is_comment = 0'), $bo_page_rows);
$list = $result['list'];
$num = $result['num'];

foreach ($list as &$row) {
    $row['href'] = "form.php?w=u&wr_id={$row['wr_id']}";
} unset($row);

// 페이징
$page = $_GET['page'] ?? 1;
$paging = Board::paging($result['total'], $page, $bo_page_rows, 5, "list.php?sfl={$sfl}&stx={$stx}&page=");

//print_r2($result['sql']);
?>

<section>
    <?php if ($bo_use_search): ?>
        <!-- 검색 -->
        <div class="search-group">
            <?php $arr_search = array('wr_subject' => '병원명'); ?>
            <?= Html::search($arr_search, array('sca')) ?>
        </div>
    <?php endif; ?>

    <div class="tbl_head01">
        <table>
            <colgroup>
                <col style="width:4%;">
                <col>
                <col style="width:10%;">
                <col style="width:15%;">
                <col style="width:12%;">
                <col style="width:15%;">
                <col style="width:6%;">
                <col style="width:7%;">
                <col style="width:7%;">
            </colgroup>
            <thead>
            <tr>
                <th>번호</th>
                <th>병원명</th>
                <th>전화번호</th>
                <th>기본주소</th>
                <th>상세주소</th>
                <th>간단 메모</th>
                <th>사용여부</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                <tr><td colspan="<?= $colspan ?>">데이터가 없습니다.</td></tr>
                <?php else: foreach ($list as $row): ?>
                <tr>
                    <td><?= $num-- ?></td>
                    <td><?= $row['wr_subject']; ?></td>
                    <td><?= $row['wr_tel']; ?></td>
                    <td><?= $row['wr_addr1']; ?></td>
                    <td><?= $row['wr_addr2']; ?></td>
                    <td><?= $row['wr_content']; ?></td>
                    <td><?= $row['wr_use'] == 1 ? '사용' : '사용안함'; ?></td>
                    <td><?= date('Y-m-d', strtotime($row['datetime'])); ?></td>
                    <td class="td_mng">
                        <a href="<?= $row['href'] ?>" class="btn btn_03">보기</a>
                        <a href="list_update.php?w=d&wr_id=<?=$row['wr_id']?>" class="btn btn_02" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="paging"><?= $paging ?></div>

        <div class="btn_fixed_top">
            <a href="form.php" class="btn btn_submit">등록</a>
        </div>
    </div>
</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
