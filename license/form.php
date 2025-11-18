<?php
//auth_check_menu($auth, $sub_menu, 'r');
$cfg = include_once('./_config.php');

$bo_table = $cfg['bo_table'];
$target_table = $cfg['target_table'];
$board = $cfg['board'];
// bo_use_category
$bo_use_category = $cfg['bo_use_category'];
$bo_category_list = $cfg['bo_category_list'];
// bo_upload_count
$bo_upload_count = $cfg['bo_upload_count'];

$g5['title'] = '면허/인증';
$g5['title'].= $w === 'u' ? ' 수정 ' : ' 입력';
include_once (G5_ADMIN_PATH.'/admin.head.php');

if ($w === 'u') {
    $sql = "SELECT * FROM {$target_table} WHERE (1) AND wr_id = '{$wr_id}' ";
    $row = sql_fetch($sql);
    $list = get_list($row, $board, '', '');
    $file = get_file($bo_table, $wr_id);

} else {
    $list = array();
    $file = array();
}
?>



<section class="admin_form_wrap" style="max-width:100%; margin:0 auto;">
    <form name="fadminform" id="fadminform" method="post" action="form_update.php" enctype="multipart/form-data">
        <input type="hidden" name="w" value="<?= $w ?? '' ?>">
        <input type="hidden" name="bo_table" value="<?= $bo_table ?>">
        <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
        <input type="hidden" name="wr_content" value="내용-<?= time() ?>">

        <table style="width:100%; border-collapse:collapse; border-top:2px solid #444;">
            <colgroup>
                <col style="width:200px;">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">제목</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <input type="text" name="wr_subject" value="<?= $list['wr_subject'] ?? '' ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" required>
                </td>
            </tr>
            <?php if ($bo_use_category): ?>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">카테고리</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <select name="ca_name" style="width: 100%;" required>
                        <?php foreach (explode('|', $bo_category_list) ?? array() as $option): ?>
                        <option value="<?= $option ?>" <?= get_selected($list['ca_name'], $option) ?>><?= $option ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php endif; ?>
            <tr style="display: none;">
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">설명</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <textarea name="" rows="5" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">이미지 업로드</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <?= $Html->make_file_upload_list_html($file, $bo_upload_count, "image/*") ?>
                </td>
            </tr>
            <?php if ($file[0]['source']): ?>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">등록된 이미지</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <?php $img_src = "{$file[0]['path']}/{$file[0]['file']}";?>
                    <img src="<?= $img_src ?>" alt="등록된 이미지" style="padding: 1rem; width: 200px;">
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row"
                    style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">사용 여부
                </th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <label><input type="radio" name="wr_use" value="1" <?= ($list['wr_use'] ?? '1') == '1' ? 'checked' : '' ?>> 사용</label>
                    <label style="margin-left:15px;"><input type="radio" name="wr_use" value="0" <?= ($list['wr_use'] ?? '1') == '0' ? 'checked' : '' ?>> 미사용</label>
                </td>
            </tr>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">등록일 </th>
                <td style="padding:10px; border-bottom:1px solid #ddd;"><?= $list['wr_datetime'] ?? date("Y-m-d H:i:s") ?></td>
            </tr>
            </tbody>
        </table>

        <div class="btn_fixed_top">
            <a href="list.php" class="btn btn_02">목록</a>
            <button type="submit" class="btn btn_01">저장</button>
        </div>
    </form>
</section>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');