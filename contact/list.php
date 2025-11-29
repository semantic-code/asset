<?php
//auth_check_menu($auth, $sub_menu, 'r');
$colspan = 9;
include_once('_config.php');

// board
$bo_use_category = $board['bo_use_category'];
$bo_category_list = $board['bo_category_list'];
$bo_upload_count = $board['bo_upload_count'];
$bo_use_search = true;

$g5['title'] = $page_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 페이징
$page = $_GET['page'] ?? 1;
$page_rows = $board['bo_page_rows'];

$result = Board::get($bo_table, array(), $page_rows);
$list = $result['list'];
$num = $result['num'];

$paging = Board::paging($result['total'], $page, $page_rows, 5, "list.php?sca={$sca}&sfl={$sfl}&stx={$stx}&page=");

//print_r2($sql);
?>

<section>
    <!-- 카테고리 -->
    <?php $arr_cate = explode('|', $bo_category_list ?? ''); ?>
    <?php if($bo_use_category && $bo_category_list) echo Html::category($arr_cate); ?>

    <!-- 검색 폼 -->
    <?php $arr_search = array('wr_name' => '이름', 'wr_sort' => '문의유형', 'wr_tel' => '전화번호') ; ?>
    <?php if($bo_use_search) echo Html::search($arr_search); ?>

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
                            <?php if($bo_use_category && $bo_category_list): ?>
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
    $(document).ready(function (){
        //상태변경
        $(document).on('change', '#ca_name', function(){
            const mode = 'update_ca_name';
            const wr_id = $(this).data('wr-id');
            const ca_name = $(this).val();

            $.post("list_update.php", {mode, wr_id, ca_name}, function(data){
                if(data.state === 'success_update_ca_name'){
                    self.location.reload();
                }
            }, 'json');
        });

        //메모저장
        $(document).on('click', '.btn_memo_update', function(){
            const mode = 'update_memo';
            const wr_id = $(this).data('wr-id');
            const wr_memo = $(this).closest('td').find('input[type="text"]').val();

            $.post("list_update.php", {mode, wr_id, wr_memo}, function(){
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

            $.post("list_update.php", {mode, wr_id}, function(data){
                if(data.state === 'success_delete'){
                    self.location.reload();
                }
            }, 'json');
        });
    });
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
