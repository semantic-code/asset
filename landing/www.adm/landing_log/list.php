<?php
$colspan = 5;
include_once('_config.php');

$page_code = $_GET['page_code'] ?? '';

// 랜딩페이지 설정 가져오기
$sql = "SELECT * FROM {$g5['write_prefix']}{$cf_bo_table} WHERE wr_page_code = '{$_GET['page_code']}' ";
$cf = sql_fetch($sql);

$g5['title'] = $page_title;
include_once(G5_ADMIN_PATH.'/admin.head.php');

// 필드값 여부
list($field_1, $field_2, $field_3, $field_4, $field_5) = explode('|', $cf['wr_fields']);
$is_use_field_1 = isset($field_1) ? 1 : 0;
$is_use_field_2 = isset($field_2) ? 1 : 0;
$is_use_field_3 = isset($field_3) ? 1 : 0;
$is_use_field_4 = isset($field_4) ? 1 : 0;
$is_use_field_5 = isset($field_5) ? 1 : 0;

if ($is_use_field_1) $colspan++;
if ($is_use_field_2) $colspan++;
if ($is_use_field_3) $colspan++;
if ($is_use_field_4) $colspan++;
if ($is_use_field_5) $colspan++;

$page_rows = 7;//$config['cf_page_rows'];
$page_url = "list.php?page=";

// page
$page = $_GET['page'] ?? 1;

// 게시글 가져오기
$result = Board::get($bo_table, array("wr_is_comment = 0", "wr_page_code = '{$page_code}'"), $page_rows);
$list = $result['list'];
$num = $result['num'];

// 페이징
$paging = Board::paging($result['total'], $page, $page_rows, 5, $page_url);

$arr_cate = explode('|', $cf['wr_cate_list']);

foreach (explode('|', $cf['wr_fields']) as $i => $field) {
    $arr_search['wr_field_' . ($i + 1)] = $field;
}

$target_sql = "SELECT wr_id FROM {$g5['write_prefix']}{$cf_bo_table} WHERE wr_page_code = '{$page_code}' ";
$row = sql_fetch($target_sql);
$target_wr_id = $row['wr_id'];

/*
echo "<pre>";
print_r($target_wr_id);
echo "</pre>";
*/

?>
<style>
    .label_cate { display:inline-block; padding:2px 6px; margin:1px; font-size:11px; border-radius:3px; background:#e0f7fa; border:1px solid #00acc1; color:#006064; }
    .label_field { display:inline-block; padding:2px 6px; margin:1px; font-size:11px; border-radius:3px; background:#fce4ec; border:1px solid #f06292; color:#880e4f; }
    .status-ok { display:inline-block; padding:2px 6px; font-size:12px; background:#e0f7e9; color:#0a7d34; border:1px solid #0a7d34; border-radius:4px; }
    .status-no { display:inline-block; padding:2px 6px; font-size:12px; background:#fdecea; color:#b71c1c; border:1px solid #b71c1c; border-radius:4px; }
</style>

<section>
    <?php if ($cf['wr_use_cate']) : ?>
    <div class="adm-category">
        <?= Html::category($arr_cate, "page_code={$page_code}") ?>
    </div>
    <?php endif; ?>
    <div class="adm-search">
        <?= Html::search($arr_search, array('page_code', 'sca')) ?>
    </div>
    <div class="tbl_head01">
        <form id="contact-form" onsubmit="return false">
            <table>
                <thead>
                <tr style="">
                    <th>번호</th>
                    <?php if($is_use_field_1): ?>
                    <th><?= $field_1 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_2): ?>
                    <th><?= $field_2 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_3): ?>
                    <th><?= $field_3 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_4): ?>
                    <th><?= $field_4 ?></th>
                    <?php endif; ?>
                    <?php if($is_use_field_5): ?>
                    <th><?= $field_5 ?></th>
                    <?php endif; ?>
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
                <?php else : foreach ($list as $row): ?>
                <tr>
                    <td><?= $num-- ?></td>
                    <?php if($is_use_field_1): ?>
                        <td><?= $row['wr_field_1'] ?></td>
                    <?php endif; ?>
                    <?php if($is_use_field_2): ?>
                        <td><?= $row['wr_field_2'] ?></td>
                    <?php endif; ?>
                    <?php if($is_use_field_3): ?>
                        <td><?= $row['wr_field_3'] ?></td>
                    <?php endif; ?>
                    <?php if($is_use_field_4): ?>
                        <td><?= $row['wr_field_4'] ?></td>
                    <?php endif; ?>
                    <?php if($is_use_field_5): ?>
                        <td><?= $row['wr_field_5'] ?></td>
                    <?php endif; ?>
                    <td><?= date('Y-m-d H:i:s', strtotime($row['wr_datetime'])) ?></td>
                    <td>
                        <?php if($cf['wr_cate_list'] && $cf['wr_use_cate']): ?>
                            <select id="ca_name" name="ca_name" data-wr-id="<?= $row['wr_id'] ?>">
                                <?php foreach ($arr_cate as $cate): ?>
                                    <option value="<?= $cate ?>" <?= get_selected($row['ca_name'], $cate) ?>><?= $cate ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif;?>
                    </td>
                    <td>
                        <div style="display: flex; gap: .5rem;">
                            <input type="text" name="wr_memo" value="<?= $row['wr_memo'] ?>" style="flex: 1; margin-right: .5rem;">
                            <button type="button" class="btn btn_03 btn_memo_update" data-wr-id="<?= $row['wr_id'] ?>">메모저장</button>
                        </div>
                    </td>
                    <td style="width: 90px;">
                        <button type="button" class="btn btn_01 btn_delete" data-wr-id="<?= $row['wr_id'] ?>">삭제</button>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        <?php if($member['mb_id'] === 'admin'): ?>
            <div class="btn_fixed_top">
                <a href="../landing/form.php?w=u&wr_id=<?= $target_wr_id ?>" class="btn btn_02">랜딩페이지 설정</a>
            </div>
        <?php endif; ?>
</div>

<div class="paging"><?= $paging ?></div>

</section>

<script>
const page_code = <?= json_encode($page_code) ?>;
const bo_table = <?= json_encode($bo_table) ?>;

$(document).ready(function (){
    //상태변경
    $(document).on('change', 'select[name="ca_name"]', function(){
        const mode = 'update_category';
        const wr_id = $(this).data('wr-id');
        const ca_name = $(this).val();

        $.post("list_update.php", {mode, wr_id, ca_name}, function(data){
            if(data.state === 'success_update_category'){
                window.location.reload();
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
                window.location.reload();
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
                window.location.reload();
            }
        }, 'json');
    });
});
</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');





