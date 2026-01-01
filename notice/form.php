<?php
//auth_check_menu($auth, $sub_menu, 'r');
include_once('./_config.php');
$page_title.= $w === 'u' ? ' 수정 ' : ' 입력';
$g5['title'] = $page_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

// editor, captcha
include_once (G5_EDITOR_LIB);
include_once(G5_CAPTCHA_PATH . '/captcha.lib.php');

// use_dhtml_editor
$bo_use_dhtml_editor = $board['bo_use_dhtml_editor'];

// bo_use_category
$bo_use_category = $board['bo_use_category'];
$bo_category_list = $board['bo_category_list'];
// bo_upload_count
$bo_upload_count = $board['bo_upload_count'];

$list = array();
$files = array();
if ($w === 'u') {
    $list = Board::view($bo_table, $wr_id);
    $files = $list['file'];
}
    
?>

<section class="admin_form_wrap" style="max-width:100%; margin:0 auto;">
    <form name="fadminform" id="fadminform" method="post" action="form_update.php" enctype="multipart/form-data" onsubmit="return fwrite_submit(this)">
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

            <?php // wr_content textarea ?>
            <tr style="display: none;">
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">설명</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <textarea name="" rows="5" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"></textarea>
                </td>
            </tr>

            <?php // wr_content editor ?>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">내용</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <div class="wr_content <?= $board['bo_select_editor'] ?>">
                        <?= editor_html('wr_content', $list['wr_content'] ?? '', $bo_use_dhtml_editor) ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">이미지 업로드</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <?= Html::file_upload_list_html($files, $bo_upload_count, "image/*") ?>
                </td>
            </tr>
            <?php if (File::get_ext($files)): ?>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">파일 미리보기</th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <div class="view-images">
                        <?php foreach ($files as $file): ?>
                            <?php if (!is_array($file)) continue; ?>
                            <?php if (empty($file['file'])) continue; ?>
                            <?php if ($file['image_type']): ?>
                            <!-- 이미지 미리보기 -->
                            <div class="view-img-box">
                                <img src="<?= $file['path'] . '/' . $file['file'] ?>" alt="<?= $file['source'] ?>">
                            </div>
                            <?php else: ?>
                            <!-- 일반 파일 -->
                            <div class="view-file-box">                                
                                <?= htmlspecialchars($file['source'] ?? $file['file'], ENT_QUOTES) ?>                                
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;">
                    사용 여부
                </th>
                <td style="padding:10px; border-bottom:1px solid #ddd;">
                    <label><input type="radio" name="wr_use" value="1" <?= ($list['wr_use'] ?? '1') == '1' ? 'checked' : '' ?>> 사용</label>
                    <label style="margin-left:15px;"><input type="radio" name="wr_use" value="0" <?= ($list['wr_use'] ?? '1') == '0' ? 'checked' : '' ?>> 미사용</label>
                </td>
            </tr>
            <tr>
                <th scope="row" style="background:#f7f7f7; text-align:left; padding:10px; border-bottom:1px solid #ddd;"><?= ($w === 'u') ? '수정일' : '등록일' ; ?></th>
                <td style="padding:10px; border-bottom:1px solid #ddd;"><?= $list['wr_datetime'] ?? date("Y-m-d H:i:s") ?></td>
            </tr>
            </tbody>
        </table>

        <?php if ($board['bo_use_captcha']): ?>
        <div class="write_div" style="margin-top: 1rem;">
            <?php echo captcha_html() ;?>
        </div>
        <?php endif; ?>

        <div class="btn_fixed_top">
            <a href="/<?= $bo_table ?>/<?= $page_key ?>=list" class="btn btn_02">게시판 이동</a>
            <a href="list.php" class="btn btn_02">목록</a>
            <button type="submit" class="btn btn_01">저장</button>
        </div>
    </form>
</section>

<script>
    function fwrite_submit(f){
         //editor
        <?php //echo get_editor_js('wr_content', $bo_use_dhtml_editor);?>
        <?php //echo chk_editor_js('wr_content', $bo_use_dhtml_editor);?>

        //captcha
        <?php echo chk_captcha_js() ;?>
    }
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');



