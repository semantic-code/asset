<?php
//auth_check_menu($auth, $sub_menu, 'r');
$colspan = 8;
$cfg = include_once('./_config.php');

// board
$bo_table = $cfg['bo_table'];
$board = $cfg['board'];
$target_table = $cfg['target_table'];
$bo_upload_count = $cfg['bo_upload_count'];
$bo_use_category = $cfg['bo_use_category'];
$bo_use_search = true;

$arr_search = array(
    'wr_subject' => '제목'
);
$sca_where = $stx ? "AND {$sfl} REGEXP '{$stx}' " : '';

$g5['title'] = '면허/인증 목록';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 페이징
$page = $_GET['page'] ?? 1;
$page_rows = 5;
$page_block = 5;
$offset = ($page - 1) * $page_rows;

$order_sql = $board['bo_sort_field'] ? "ORDER BY {$board['bo_sort_field']}" : "ORDER BY wr_num DESC";
$limit_sql = "LIMIT {$offset}, {$page_rows}";

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$target_table} WHERE (1) {$sca_where} {$order_sql} {$limit_sql}";
$result = sql_query($sql);
$total_count = sql_fetch('SELECT FOUND_ROWS()')['FOUND_ROWS()'];

while ($row = sql_fetch_array($result)) {
    $data = get_list($row, $board, '', '');
    $file = get_file($bo_table, $row['wr_id']);

    $data['href'] = "form.php?w=u&wr_id={$row['wr_id']}";
    $data['img_name'] = $file[0]['source'];
    $data['img_src'] = "{$file[0]['path']}/{$file[0]['file']}";

    $list[] = $data;
}

$num = $total_count - $offset;
$paging = $Page->get_paging_html($total_count, $page, $page_rows, $page_block, "list.php?sca={$sca}&sfl={$sfl}&stx={$stx}&page=");

//print_r2($sub_menu);
?>

<section>
    <div class="btn_fixed_top">
        <a href="form.php" class="btn btn_01">등록</a>
    </div>

    <?php if ($bo_use_category): ?>
    <!-- 카테고리 -->
    <div class="category-group" style="display: flex; gap: .5rem; padding: .7rem; margin-bottom: 1rem; border: 1px #ccc solid; border-radius: 5px; background: #ebebeb;">
        <?php $btn_css = ($sca ?? '') === "" ? "btn_primary" : "btn_secondary"; ?>
        <a href="?sca=" class="btn <?= $btn_css ?>">전체</a>

        <?php foreach(explode('|', $cfg['bo_category_list']) ?? array() as $ca_name): ?>
        <?php $btn_css = ($sca === $ca_name) ? "btn_primary" : "btn_secondary"; ?>
        <a href="?sca=<?= urlencode($ca_name) ?>" class="btn <?= $btn_css ?>"><?= $ca_name ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($bo_use_search): ?>
    <!-- 검색 -->
    <div class="search-group" style="display:flex; gap:.5rem; padding:.7rem; margin-bottom:1rem; border:1px #ccc solid; border-radius:5px; background:#ebebeb; align-items:center;">
        <form name="fsearch" method="get" action="list.php" style="display:flex; gap:.5rem; width:100%; align-items:center;">
            <input type="hidden" name="sca" value="<?= $sca ?? '' ?>">
            <select name="sfl" style="flex:0 0 120px; padding:.3rem .5rem; border:1px solid #ccc; border-radius:4px;">
                <?php foreach ($arr_search ?? array() as $col => $keyword): ?>
                <option value="<?= $col ?>" <?= get_selected($sfl, $col) ?>><?= $keyword?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="stx" value="<?= get_text($stx) ?>" placeholder="검색어 입력" style="padding:.3rem .5rem; height: 35px; width: 50%; border:1px solid #ccc; border-radius:4px;">
            <button type="submit" class="btn btn_primary" style="padding:.4rem 1rem;">검색</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="tbl_head01">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <colgroup>
                <col style="width: 60px;">  <!-- 번호 -->
                <col style="width: 10%;">  <!-- 카테고리 -->
                <col style="width: auto;">  <!-- 제목 -->
                <col style="width: 20%;">  <!-- 이미지 -->
                <col style="width: 10%;">  <!-- 사용 -->
                <col style="width: 10%;">  <!-- 등록일 -->
                <col style="width: 15%;">  <!-- 관리 -->
            </colgroup>
            <thead>
            <tr>
                <th scope="col" id="mb_list_id">NO</th>
                <th scope="col" id="mb_list_id">카테고리</th>
                <th scope="col" id="mb_list_id">제목</th>
                <th scope="col" id="mb_list_id">이미지</th>
                <th scope="col" id="mb_list_id">사용</th>
                <th scope="col" id="mb_list_id">등록일</th>
                <th scope="col" id="mb_list_id">관리</th>
            </tr>
            </thead>
            <tbody>

            <?php if (empty($list)): ?>
            <tr><td colspan="<?= $colspan ?>">데이터가 없습니다.</td></tr>
            <?php else: foreach ($list as $row): ?>
            <tr class="">
                <td headers="mb_list_id" ><?=$num-- ?></td>
                <td headers="mb_list_id" ><?=$row['ca_name']?></td>
                <td headers="mb_list_id" ><?=$row['wr_subject']?></td>
                <td headers="mb_list_id" >
                    <img src="<?=$row['img_src']?>" alt="이미지출력" download="<?=$row['img_name']?>" style="width: 150px;">
                </td>
                <td headers="mb_list_id" ><?=$row['wr_use'] == 1 ? '사용' : '미사용'?></td>
                <td headers="mb_list_id" ><?=date("Y-m-d", strtotime($row['wr_datetime'])) ?></td>
                <td headers="mb_list_id" class="td_mng">
                    <a href="<?= $row['href'] ?>" class="btn btn_03">보기</a>
                    <a href="list_update.php?w=d&wr_id=<?=$row['wr_id']?>" class="btn btn_02"
                       onclick="return confirm('삭제하시겠습니까?')"
                    >삭제</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="paging"><?= $paging?></div>

</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
