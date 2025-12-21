<?php
require_once './_config.php';
$page_title.= $w === 'u' ? ' 수정' : ' 등록';
$g5['title'] = $page_title;
require_once G5_ADMIN_PATH . '/admin.head.php';

// add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

if ($w === 'u'){
    $str_sql = "SELECT * FROM {$target_table} WHERE wr_id = '{$wr_id}' ";
    $row = sql_fetch($str_sql);
    $wr = get_list($row, $board, '', '');
}

?>

<section id="anc_bo_basic">

    <h2 class="h2_frm">제휴병원 기본 정보</h2>
    <form name="fwrite" id="fwrite" action="form_update.php" onsubmit="//return fwrite_submit(this);" method="post" autocomplete="off" style="width:<?php echo $width; ?>">
        <input type="hidden" name="bo_table" value="<?= $bo_table ?>">
        <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
        <input type="hidden" name="w" value="<?= $w ?>">

        <div class="tbl_frm01 tbl_wrap">
            <table>
                <caption>제휴병원 기본 정보</caption>
                <colgroup>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><label for="bo_table">병원명<?php echo $sound_only ?></label></th>
                    <td>
                        <input type="text" name="wr_subject" value="<?= $wr['wr_subject'] ?>" id="wr_subject" required class="frm_input required" size="100">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gr_id">전화번호<strong class="sound_only">필수</strong></label></th>
                    <td>
                        <input type="text" name="wr_tel" value="<?= $wr['wr_tel']?>" id="wr_tel" class="frm_input" size="100">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bo_subject">병원주소<strong class="sound_only">필수</strong></label></th>
                    <td class="td_addr_line">
                        <label for="wr_zip" class="sound_only">우편번호</label>
                        <input type="text" name="wr_zip" value="<?php echo $wr['wr_zip']; ?>" id="wr_zip" class="frm_input readonly" size="5" maxlength="6">
                        <button type="button" class="btn_frmline" onclick="win_zip('fwrite', 'wr_zip', 'wr_addr1', 'wr_addr2', 'wr_addr3', 'wr_addr_jibeon');">주소 검색</button><br>
                        <input type="text" name="wr_addr1" value="<?php echo $wr['wr_addr1'] ?>" id="wr_addr1" class="frm_input readonly" size="60">
                        <label for="mb_addr1">기본주소</label><br>
                        <input type="text" name="wr_addr2" value="<?php echo $wr['wr_addr2'] ?>" id="wr_addr2" class="frm_input" size="60">
                        <label for="mb_addr2">상세주소</label>
                        <br>
                        <input type="text" name="wr_addr3" value="<?php echo $wr['wr_addr3'] ?>" id="wr_addr3" class="frm_input" size="60">
                        <label for="mb_addr3">참고항목</label>
                        <input type="hidden" name="wr_addr_jibeon" value="<?php echo $wr['wr_addr_jibeon']; ?>"><br>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bo_mobile_subject">간단 메모</label></th>
                    <td>
                        <textarea name="wr_content" id="wr_content" cols="30" rows="5"><?= nl2br($wr['wr_content'])?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bo_mobile_subject">사용 여부</label></th>
                    <td>
                        <label style="padding-right: .5rem;">
                            <input type="radio" name="wr_use" value="1" <?= ($wr['wr_use'] ?? 1) == 1 ? 'checked': ''; ?>>
                            <span>사용함</span>
                        </label>
                        <label>
                            <input type="radio" name="wr_use" value="0" <?= ($wr['wr_use'] ?? 1) == 0 ? 'checked': ''; ?>>
                            <span>사용안함</span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="btn_fixed_top">
            <a href="list.php" class="btn btn_02">목록</a>
            <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
        </div>
    </form>
</section>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
