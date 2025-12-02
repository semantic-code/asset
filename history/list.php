<?php
require_once '_config.php';

$colspan = 6;
$g5['title'] = $page_title;
require_once G5_ADMIN_PATH . '/admin.head.php';

//카테고리 사용 여부
$bo_use_category = $board['bo_use_category'];
if ($bo_use_category) $colspan--;

$where_sql = '';

//카테고리
if ($sca) $where_sql.= "AND ca_name = '{$sca}' ";
if ($year) $where_sql.= "AND wr_year = '{$year}' ";
if ($month) $where_sql.= "AND wr_month = '{$month}' ";

//wr_sort 기본값
$sql = "SELECT MAX(wr_sort) AS max_sort FROM {$target_table}";
$row = sql_fetch($sql);
$last_sort = $row['max_sort'];
$next_sort = $last_sort ? floor($last_sort / 10) * 10 + 10 : 10;

//페이징 가져오기
$page = $_GET['page']  ?? 1;

//총 레코드 수
$total_sql = " SELECT COUNT(*) AS cnt FROM {$target_table} WHERE (1) {$where_sql}";
$row = sql_fetch($total_sql);
$total_count = $row['cnt'];

//페이징
$page_rows = $board['bo_page_rows'];
$offset = ($page - 1) * $page_rows;
$paging = Board::paging($total_count, $page, $page_rows, 5, G5_ADMIN_URL."/history/list.php?sca={$sca}&page=");

//정렬
$order_by = "ORDER BY  wr_sort DESC";
$sql = "SELECT * FROM {$target_table} WHERE (1) {$where_sql} LIMIT {$offset}, {$page_rows}";
$result = sql_query($sql);

$list = array();
while ($row = sql_fetch_array($result)){
    $data = get_list($row, $board, '', '');
    $data['href'] = "form.php?w=u&wr_id=".$row['wr_id'];
    $list[] = $data;
}

$arr_cate = explode('|', $board['bo_category_list']);

/*
echo "<pre>";
print_r($list);
echo "</pre>";
*/
?>


<style>
    /* 표 간격 */
    .tbl_head01 {margin-top: 10px;}
    /* 상단 툴바 (카테고리 영역) */
    .admin-toolbar {display: flex; gap: 12px; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fafafa; margin: 10px 0 15px;}
    /* 카테고리 탭 */
    .admin-tabs {display: flex; gap: 6px; list-style: none; padding: 0; margin: 0;}
    .admin-tabs .tab {display: inline-block; padding: 5px 10px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; color: #374151; text-decoration: none; font-size: 14px;}
    .admin-tabs .tab:hover {background: #f3f4f6;}
    .admin-tabs .tab.is-active {background: #3f51b5; color: #fff; border-color: #1d4ed8;}
</style>

    <section>
        <h2>연혁 입력</h2>
        <form method="post" action="list_update.php" onsubmit="return on_submit(this)">
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
                        <td><input type="submit" value="등록" class="btn btn_success"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <?php if($bo_use_category): ?>
            <h2>카테고리</h2>
            <div class="admin-toolbar">
                <!-- 카테고리 탭 -->
                <ul class="admin-tabs" role="tablist">
                    <li><a href="?sca=" class="tab <?= ($sca === '') ? 'is-active' : '' ?>">전체</a></li>
                    <?php foreach ($arr_cate as $cate): ?>
                        <?php $is_active = $sca === $cate ? 'is-active' : '' ; ?>
                        <li><a href="?sca=<?= $cate ?>" class="tab <?= $is_active ?>"><?= $cate?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tbl_head01">
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
                        <col style="width:100px;">
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
                        <th>삭제</th>
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
                        <td><a href="<?= $row['href'] ?>"><?= $row['wr_content'] ?></a></td>
                        <td><a href="list_delete.php?w=d&wr_id=<?= $row['wr_id'] ?>" class="btn btn_01 btn_delete" onclick="if(!confirm('삭제하시겠습니까?')) return false">삭제</a></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="paging"><?= $paging ?></div>

        <div class="btn_fixed_top">
            <a href="list.php" class="btn btn_02">초기화</a>
            <a href="form.php" class="btn btn_01">글쓰기</a>
        </div>

    </section>

    <script>
         const param = new URLSearchParams(window.location.search);
         const sca = param.get('sca') || '';
         const year = param.get('year') || '';


        function on_submit(f){
            return true;
        }

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