<?php
$sub_menu = "700100";
include_once('_config.php');

$page_title = "랜딩페이지 ";
$page_title.= $w === 'u' ? '수정' : '입력';

$g5['title'] = $page_title;
include_once(G5_ADMIN_PATH.'/admin.head.php');

if ($w === 'u') {
    $list = Board::view($bo_table, $wr_id);

    //파일 데이터, board_files 의 칼럼명을 써야 하기 때문에 직접 가져옴
    $file_sql = "SELECT * FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' ORDER BY bf_no ASC ";
    $result = sql_query($file_sql);
    while ($row = sql_fetch_array($result)) { $files[] = $row;}

} else {
    $list =  array();
}

/*
echo "<pre>";
print_r($files);
echo "</pre>";
*/

?>
<form name="frmLanding" id="frmLanding" action="form_update.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmit(this)">
    <input type="hidden" name="bo_table" value="<?= $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
    <input type="hidden" name="w" value="<?= $w ?>">
    <input type="hidden" name="chk_ld_page" value="1">
    <input type="hidden" name="wr_content" value="내용 - <?= time() ?>">

    <div class="local_ov01 local_ov">
        <span class="btn_ov01"><span class="ov_txt">랜딩페이지 추가/수정</span></span>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <caption>랜딩 정보</caption>
            <colgroup>
                <col class="grid_4">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row">랜딩 ID</th>
                <td>
                    <?php $disabled = $w === 'u' ? 'disabled' : '' ?>
                    <input type="text" name="wr_page_code" id="wr_page_code" value="<?= $list['wr_page_code'] ?? '' ?>" required <?= $disabled ?> class="frm_input" size="30" >
                    <?php if($w !== 'u'): ?>
                        <button type="button" class="btn btn_02" id="btn_id_check" style="margin-left: .5rem;">중복확인</button>
                    <?php endif; ?>
                    <span class="frm_info">URL 식별자 (예: landing.php?ld_page=여기에 필요한 값, 'basic' 제외)</span>
                    <span class="frm_info">저장된 아이디는 수정할 수 없습니다.</span>
                </td>
            </tr>
            <tr>
                <th scope="row">접근 아이디 설정</th>
                <td>
                    <input type="text" name="wr_access_id" value="<?= $list['wr_access_id'] ?? '' ?>" class="frm_input" size="80">
                    <span class="frm_info">admin을 제외한 아이디 설정 (예: master|adm|user01)</span>
                </td>

            </tr>
            <tr>
                <th scope="row">카테고리</th>
                <td>
                    <input type="text" name="wr_cate_list" value="<?= $list['wr_cate_list'] ?? '' ?>" class="frm_input" size="80">
                    <label style="margin-left: .5rem;">
                        <input type="checkbox" name="wr_use_cate" value="1" <?= ($list['wr_use_cate'] ?? 0) == 1 ? "checked" : "" ?>>
                        <span>사용</span>
                    </label>
                    <span class="frm_info">예: 대기중|상담완료 (|로 구분)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">검색 기능 사용</th>
                <td>
                    <label>
                        <input type="checkbox" name="wr_use_search" value="1" <?= ($list['wr_use_search'] ?? 0) == 1 ? "checked" : "" ?>>
                        <span>사용</span>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">제목</th>
                <td><input type="text" name="wr_subject"  value="<?= $list['wr_subject'] ?? '' ?>" required class="frm_input" size="80"></td>
            </tr>
            <tr>
                <th scope="row">이미지 첨부</th>
                <td><?php echo Html::file_upload_html($bo_table, $files ?? array()) ?></td>


            </tr>
            <tr>
                <th scope="row">폼 필드명 정의</th>
                <td>
                    <input type="text" name="wr_fields" value="<?= $list['wr_fields'] ?? '' ?>" class="frm_input" size="80">
                    <span class="frm_info">예: 이름|연락처|이메일 (|로 구분)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">목록 정렬 정의</th>
                <td>
                    <input type="text" name="wr_sort_field" value="<?= $list['wr_sort_field'] ?? '' ?>" class="frm_input" size="80">
                    <span class="frm_info">예: wr_num asc(기본: 최신글이 맨 위로)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">사용 여부</th>
                <td>
                    <label>
                        <input type="checkbox" name="wr_use" value="1" <?= ($wr_use ?? 1) == 1 ? "checked" : "" ?>>
                        <span>사용</span>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <a href="../landing_log/list.php?page_code=<?= $list['wr_page_code'] ?>" class="btn btn_02">내용관리</a>
        <a href="list.php" class="btn btn_03">목록</a>
        <input type="submit" value="저장" class="btn_submit btn">
    </div>
</form>

<script>
    var $target = $("input[name='chk_ld_page']");

    function onSubmit(f){
        if(Number($target.val()) === 0){
            alert('랜딩 아이디 중복확인이 필요합니다.');
            return false;
        }

        return true;
    }

    $(document).ready(function(){
        //파일 업로드 썸네일 보기
        <?= Html::file_upload_js('file_input'); ?>


        $(document).on('click', '#btn_id_check', function(){
            const wr_page_code = $('#wr_page_code').val();
            const bo_table = <?= json_encode($bo_table) ?>;

            if(!wr_page_code) return alert('랜딩 ID값이 누락되었습니다.'), false;

            $.post('id_check.php', {wr_page_code, bo_table}, function(data){
                if(data.state === 'success'){
                    alert(data.msg);
                    $target.val(1);
                    return false;
                }else{
                    alert(data.msg);
                    $target.val(0);
                    return false;
                }
            }, 'json');
        });

        $(document).on('change',  '#wr_page_code', function(){
            $target.val(0);
        });
    });

</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
