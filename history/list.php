<?php
$colspan = 7;
require_once '_config.php';
$g5['title'] = $page_title;
require_once G5_ADMIN_PATH . '/admin.head.php';

// board
$bo_page_rows = $board['bo_page_rows'];
$bo_use_category = $board['bo_use_category'];
if ($bo_use_category) $colspan--;

//wr_sort 기본값
$sql = "SELECT MAX(wr_sort) AS max_sort FROM {$target_table}";
$row = sql_fetch($sql);
$last_sort = $row['max_sort'];
$next_sort = $last_sort ? floor($last_sort / 10) * 10 + 10 : 10;

$where = array();
//카테고리
if ($sca) $where[] = "ca_name = '{$sca}' ";
if ($year) $where[] = "wr_year = '{$year}' ";
if ($month) $where[] = "wr_month = '{$month}' ";

$result = Board::get($bo_table, $where, $bo_page_rows);
$list = $result['list'];

foreach ($list as &$row) {
    $row['href'] = "form.php?w=u&wr_id={$row['wr_id']}";
} unset($row);

//페이징 가져오기
$page = $_GET['page']  ?? 1;
$paging = Board::paging($result['total'], $page, $bo_page_rows, 5, G5_ADMIN_URL."/history/list.php?sca={$sca}&page=");

// 카테고리
$arr_cate = explode('|', $board['bo_category_list']);

?>

<section>
    <?php if($bo_use_category): ?>
        <h2>카테고리</h2>
        <?php echo Html::category($arr_cate, false); ?>
    <?php endif; ?>

    <h2>빠른 연혁 입력</h2>
    <form method="post" action="list_update.php">
        <input type="hidden" name="bo_table" value="<?= $bo_table ?>">

        <div style="background:#f9f9f9; border:1px solid #ddd; padding:10px; margin-bottom:2rem;">
            <p>- 카테고리, 연도, 월, 내용 순으로 입력하세요. (정렬번호는 자동입력 또는 직접 입력)</p>
            <table class="tbl_head01 tbl_wrap">
                <colgroup>
                    <?php if ($bo_use_category) : ?>
                    <col style="width:150px;">
                    <?php endif; ?>
                    <col style="width:100px;">
                    <col style="width:100px;">
                    <col style="width:100px;">
                    <col>
                    <col style="width:100px;">
                </colgroup>
                <thead>
                <tr>
                    <?php if ($bo_use_category) : ?>
                    <th>카테고리</th>
                    <?php endif; ?>
                    <th>연도 (YYYY)</th>
                    <th>월 (MM)</th>
                    <th>정렬번호</th>
                    <th>내용</th>
                    <th>등록</th>
                </tr>
                </thead>
                <tbody>
                <tr style="background: #fff;">
                    <?php if ($bo_use_category) : ?>
                    <td>
                        <select name="ca_name" id="ca_name" class="frm_input" onchange="window.location.href='./list.php?sca=' + this.value " required>
                            <option value="">선택안함</option>
                            <?php foreach ($arr_cate as $row): ?>
                            <option value="<?= $row ?>" <?= get_selected($sca, $row) ?>><?= $row ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php endif; ?>
                    <td>
                        <select name="wr_year" id="wr_year" class="frm_input" required>
                            <option value="">연도 선택</option>
                            <?php for ($i=2014;$i<2040;$i++): ?>
                            <option value="<?= $i ?>" <?= get_selected($year, $i) ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>
                        <select name="wr_month" id="wr_month" class="frm_input" required>
                            <option value="">월 선택</option>
                            <?php for ($i=1;$i<13;$i++): ?>
                            <option value="<?= sprintf('%02d', $i) ?>" <?= get_selected($month, $i) ?>><?= sprintf('%02d', $i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td><input type="number" name="wr_sort" value="<?= $next_sort ?>" size="10" class="frm_input"></td>
                    <td><input type="text" name="wr_content" size="" class="frm_input full_input" required value=""></td>
                    <td><input type="submit" value="등록" class="btn btn_03"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <div class="tbl_head01" style="margin-top: 10px;">
        <h2>연혁 목록</h2>
        <form id="history-form" onsubmit="return false">
            <table>
                <colgroup>
                    <?php if ($bo_use_category) : ?>
                    <col style="width:150px;">
                    <?php endif; ?>
                    <col style="width:100px;">
                    <col style="width:100px;">
                    <col style="width:100px;">
                    <col>
                    <col style="width:150px;">
                </colgroup>
                <thead>
                <tr style="">
                    <?php if ($bo_use_category) : ?>
                    <th>카테고리</th>
                    <?php endif; ?>
                    <th>연도</th>
                    <th>월</th>
                    <th>정렬번호</th>
                    <th>내용</th>
                    <th>관리</th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($list)): ?>
                <tr>
                    <td colspan="<?= $colspan ?>">데이터가 없습니다.</td>
                </tr>
                <?php else : foreach ($list as $row): ?>
                <tr>
                    <?php if ($bo_use_category) : ?>
                    <td><?= $row['ca_name'] ?></td>
                    <?php endif; ?>
                    <td><?= $row['wr_year'] ?></td>
                    <td><?= $row['wr_month'] ?></td>
                    <td><?= $row['wr_sort'] ?></td>
                    <td><?= $row['wr_content'] ?></td>
                    <td>
                        <div style="display: flex; justify-content: center; gap: .5rem;">
                            <a href="<?= $row['href'] ?>" class="btn btn_03 btn_delete">보기</a>
                            <a href="list_update.php?w=d&wr_id=<?= $row['wr_id'] ?>" class="btn btn_02 btn_delete" onclick="if(!confirm('삭제하시겠습니까?')) return false">삭제</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
    </div>

    <div class="paging"><?= $paging ?></div>

    <div class="btn_fixed_top">
        <a href="list.php" class="btn btn_01">초기화</a>
        <a href="form.php" class="btn btn_02" style="display:none;">글쓰기</a>
    </div>

</section>

<script>
     const param = new URLSearchParams(window.location.search);
     const sca = param.get('sca') || '';
     const year = param.get('year') || '';

    $(document).on('change', '#wr_year', function(){
        const wr_year = $('#wr_year').val();
        const href = `list.php?sca=${sca}&year=${wr_year}`;
        window.location.href = href;
    });

     $(document).on('change', '#wr_month', function(){
         const wr_month = $('#wr_month').val();
         const href = `list.php?sca=${sca}&year=${year}&month=${wr_month}`;
         window.location.href = href;
     });
</script>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';

