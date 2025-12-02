<?php
include_once('_config.php');
include_once  G5_ADMIN_PATH . '/admin.head.php';

$g5['title'] = ($w === 'u') ? "{$page_title} 수정" : "{$page_title} 입력";

//카테고리
$bo_use_category = $board['bo_use_category'];
$arr_cate = explode('|', $board['bo_category_list']);

if($w ==='u'){
    $sql = "SELECT * from {$g5['write_prefix']}{$bo_table} WHERE wr_id = {$wr_id} ";
    $list = sql_fetch($sql);
} else {
    //wr_sort 기본값
    $sql = "SELECT MAX(wr_sort) AS max_sort FROM {$target_table}";
    $row = sql_fetch($sql);
    $last_sort = $row['max_sort'];
    $next_sort = $last_sort ? floor($last_sort / 10) * 10 + 10 : 10;
}

?>

<section class="cbox">
    <form name="fhistory" id="fhistory" action="./form_update.php" method="post" onsubmit="return fhistory_submit(this);">
        <input type="hidden" name="bo_table" value="<?= $bo_table ?>">
        <input type="hidden" name="w" value="<?= $w ?>">
        <input type="hidden" name="wr_id" value="<?= $wr_id ?>">

        <div class="tbl_frm01 tbl_wrap">
            <table>
                <?php if ($bo_use_category) : ?>
                <tr>
                    <th>카테고리</th>
                    <td>
                        <select name="ca_name" class="frm_input">
                            <?php foreach ($arr_cate as $row): ?>
                            <option value="<?= $row ?>" <?= get_selected($list['ca_name'], $row) ?>><?= $row ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>정렬번호</th>
                    <td><input type="number" name="wr_sort" value="<?= $list['wr_sort'] ?? $next_sort ?>" required class="frm_input"></td>
                </tr>
                <tr>
                    <th>연도</th>
                    <td><input type="number" name="wr_year" value="<?= $list['wr_year'] ?? '' ?>" required class="frm_input"></td>
                </tr>
                <tr>
                    <th>월</th>
                    <td><input type="number" name="wr_month" value="<?= $list['wr_month'] ?? '' ?>" min="1" max="12" required class="frm_input"></td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td><input type="text" name="wr_content" value="<?= $list['wr_content'] ?? '' ?>" required class="frm_input full_input" maxlength="255" style="width: 100%;"></td>
                </tr>
            </table>
        </div>

        <div class="btn_fixed_top">
            <a href="list.php" class="btn btn_02">목록</a>
            <input type="submit" value="저장" class="btn btn_submit">
        </div>
    </form>
</section>

<?php
include_once  G5_ADMIN_PATH . '/admin.tail.php';
