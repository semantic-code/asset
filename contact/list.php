<?php
//auth_check_menu($auth, $sub_menu, 'r');
$colspan = 9;
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
$page_rows = 15;
$page_block = 5;
$offset = ($page - 1) * $page_rows;

$sca_where = $sca ? "AND ca_name = '{$sca}'" : "";
$search_where = $stx ? "AND {$sfl} REGEXP '{$stx}'" : "";
$order_sql = $board['bo_sort_field'] ? "ORDER BY {$board['bo_sort_field']}" : "ORDER BY wr_num DESC";
$limit_sql = "LIMIT {$offset}, {$page_rows}";

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$target_table} WHERE (1) {$sca_where} {$search_where} {$order_sql} {$limit_sql}";
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

//print_r2($sql);
?>

<section>
    <!-- 카테고리 -->
    <?php if($bo_use_category && $bo_category_list) echo $Html->make_category_html($arr_cate, $sca); ?>

    <!-- 검색 폼 -->
    <?php $arr = array('wr_name' => '이름', 'wr_sort' => '문의유형', 'wr_tel' => '전화번호') ; ?>
    <?php if($bo_use_search) echo $Html->make_search_html($arr, $sca, $sfl, $stx, "list.php"); ?>

    <div class="tbl_head01">
        <form id="contact-form" onsubmit="return false">
            <table>
                <colgroup>
                    <col style="width: 4%;">    <!-- 번호 -->
                    <col style="width: 8%;">    <!-- 이름 -->
                    <col style="width: 8%;">    <!-- 문의유형 -->
                    <col style="width: 8%;">    <!-- 전화번호 -->
                    <col style="width: auto;">  <!-- 상담내용 -->
                    <col style="width: 10%;">   <!-- 날짜 -->
                    <col style="width: 8%;">    <!-- 상태변경 -->
                    <col style="width: 23%;">   <!-- 간단메모 -->
                    <col style="width: 5%;">    <!-- 관리 -->
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

                        <td>
                            <div style="display: flex;">
                                <input type="text" name="wr_memo" value="<?= $row['wr_memo'] ?>" style="width: 90%; margin-right: .5rem;">
                                <button type="button" class="btn btn_03 btn_memo_update" data-wr-id="<?= $row['wr_id'] ?>" style="width: 90px;">메모저장</button>
                            </div>
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
