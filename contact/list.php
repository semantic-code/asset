<?php
//auth_check_menu($auth, $sub_menu, 'r');
$colspan = 8;
$cfg = include_once('./_config.php');

$sub_menu = $cfg['sub_menu'];
// board
$bo_table = $cfg['bo_table'];
$board = $cfg['board'];
$target_table = $cfg['target_table'];
$bo_use_category = $cfg['bo_use_category'];
$bo_category_list = $cfg['bo_category_list'];
$bo_upload_count = $cfg['bo_upload_count'];
$bo_use_search = true;

$arr_cate = explode('|', $bo_category_list ?? array());

$arr_search = array(
    'wr_subject' => '제목'
);
$sca_where = $stx ? "AND {$sfl} REGEXP '{$stx}' " : '';

$g5['title'] = $cfg['page_title'];
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
<style>
    .admin-toolbar{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fafafa;margin:10px 0 15px;}
    .admin-tabs{display:flex;gap:6px;list-style:none;padding:0;margin:0;}
    .admin-tabs .tab{display:inline-block;padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;text-decoration:none;font-size:14px;}
    .admin-tabs .tab:hover{background:#f3f4f6;}
    .admin-tabs .tab.is-active{background:#3f51b5;color:#fff;border-color:#1d4ed8;}
    .admin-search{display:flex;flex-direction:column;gap:8px;flex:1;}
    .admin-search__row{display:flex;align-items:center;gap:8px;}
    .admin-search__row--full select{min-width:260px;}
    .admin-search .sel,.admin-search .inp{height:30px;border:1px solid #d1d5db;border-radius:6px;padding:0 10px;background:#fff;}
    .admin-search .inp{min-width:220px;}
    .admin-search .inp:focus,.admin-search .sel:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15);outline:0;}
    .btn{display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 12px;border-radius:6px;text-decoration:none;cursor:pointer;user-select:none;}
    .btn-primary{background:#3f51b5;color:#fff;border:1px solid #1d4ed8;}
    .btn-primary:hover{background:#1d4ed8;}
    .btn-line{background:#fff;color:#374151;border:1px solid #d1d5db;}
    .btn-line:hover{background:#f3f4f6;}
    .tbl_head01{margin-top:10px;}
</style>
<section>
    <?php if($board['bo_use_category']): ?>
        <div class="admin-toolbar">
            <!-- 카테고리 탭 -->
            <ul class="admin-tabs" role="tablist">
                <li><a href="?sca=&sfl=<?= $sfl ?>&stx=<?= $stx ?>" class="tab <?= ($sca === '') ? 'is-active' : '' ?>">전체</a></li>
                <?php foreach ($arr_cate ?? array() as $cate): ?>
                    <?php $is_active = $sca === $cate ? 'is-active' : '' ; ?>
                    <li><a href="?sca=<?= $cate?>&sfl=<?= $sfl ?>&stx=<?= $stx ?>" class="tab <?= $is_active ?>"><?= $cate?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <!-- 검색 폼 -->
    <form class="admin-search" method="get" action="list.php" style="display: block;">
        <input type="hidden" name="sca" value="<?= $sca ?>">
        <!-- 검색 줄 -->
        <div class="admin-search__row">
            <label for="search_field" class="sound-only">검색조건</label>
            <select name="sfl" id="search_field" class="sel">
                <option value="wr_name" <?= get_selected('wr_name', $sfl) ?>>이름</option>
                <option value="wr_sort" <?= get_selected('wr_dept', $sfl) ?>>문의유형</option>
                <option value="wr_tel" <?= get_selected('wr_tel', $sfl) ?>>전화번호</option>
            </select>
            <input type="text" name="stx" id="search_text" class="inp" placeholder="검색어 입력" value="<?= $stx ?>">
            <button type="submit" class="btn btn-primary">검색</button>
            <a href="list.php" class="btn btn_02">초기화</a>
        </div>
    </form>


    <div class="tbl_head01">
        <form id="contact-form" onsubmit="return false">
            <table>
                <colgroup>
                    <col style="width: 4%;">     <!-- 번호 -->
                    <col style="width: 8%;">    <!-- 이름 -->
                    <col style="width: 8%;">    <!-- 문의유형 -->
                    <col style="width: 8%;">    <!-- 전화번호 -->
                    <col style="width: auto;">     <!-- 상담내용(가장 넓게) -->
                    <col style="width: 10%;">    <!-- 날짜 -->
                    <col style="width: 8%;">    <!-- 상태변경 -->
                    <col style="width: 23%;">    <!-- 간단메모 -->
                    <col style="width: 5%;">     <!-- 관리 -->
                </colgroup>
                <thead>
                <tr style="">
                    <th>번호</th>
                    <th>이름</th>
                    <th>문의유형</th>
                    <th>전화번호</th>
                    <th>상담내용</th>
                    <th>날짜</th>
                    <th>상태변경</th>
                    <th>간단메모</th>
                    <th>관리</th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($list)): ?>
                    <tr>
                        <td colspan="<?= $colspan ?>">데이터가 없습니다.</td>
                    </tr>
                <?php else: foreach ($list as $row): ?>
                    <tr>
                        <td><?= $num-- ?></td>
                        <td><?= $row['wr_name'] ?></td>
                        <td><?= $row['wr_sort'] ?></td>
                        <td><?= $row['wr_tel'] ?></td>
                        <td><?= htmlspecialchars($row['wr_content'] ?? '', ENT_QUOTES) ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($row['wr_datetime'])) ?></td>
                        <td>
                            <?php if($board['bo_use_category'] && $board['bo_category_list']): ?>
                                <select id="ca_name" name="ca_name" data-wr-id="<?= $row['wr_id'] ?>">
                                    <?php foreach ($arr_cate ?? array() as $cate): ?>
                                        <option value="<?= $cate ?>" <?= get_selected($row['ca_name'], $cate) ?>><?= $cate ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif;?>
                        </td>

                        <td style="display: flex;">
                            <input type="text" name="wr_memo" value="<?= $row['wr_memo'] ?>" style="width: 90%; margin-right: .5rem;">
                            <button type="button" class="btn btn_03 btn_memo_update" data-wr-id="<?= $row['wr_id'] ?>" style="width: 90px;">메모저장</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn_01 btn_delete" data-wr-id="<?= $row['wr_id'] ?>">삭제</button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
    </div>

    <div class="paging"><?= $paging ?></div>

</section>

<script>
    const bo_table = <?= json_encode($bo_table) ?>;

    $(document).ready(function (){
        //상태변경
        $(document).on('change', '#ca_name', function(){
            const mode = 'update_ca_name';
            const wr_id = $(this).data('wr-id');
            const ca_name = $(this).val();

            $.post("list_update.php", {mode, bo_table, wr_id, ca_name}, function(data){
                if(data.state === 'success_update_ca_name'){
                    self.location.reload();
                }
            }, 'json');
        });

        //메모저장
        $(document).on('click', '.btn_memo_update', function(){
            const mode = 'update_memo';
            const wr_id = $(this).data('wr-id');
            const memo = $(this).closest('td').find('input[type="text"]').val();

            $.post("list_update.php", {mode, bo_table, wr_id, memo}, function(){
                if(data.state === 'success_update_memo'){
                    self.location.reload();
                }
            }, 'json');
        });

        //상담글삭제
        $(document).on('click', '.btn_delete', function(){
            if(!confirm("글을 삭제하시겠습니까?\n한번 삭제한 글은 복구할 수 없습니다.")) return false;
            const mode = 'delete';
            const wr_id = $(this).data('wr-id');

            $.post("list_update.php", {mode, bo_table, wr_id}, function(data){
                if(data.state === 'success_delete'){
                    self.location.reload();
                }
            }, 'json');
        });
    });
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
