<?php
$sub_menu = "700100";
include_once('_config.php');

$page_title = "랜딩페이지 ";
$page_title.= $w === 'u' ? '수정' : '입력';

$g5['title'] = $page_title;
include_once(G5_ADMIN_PATH.'/admin.head.php');

$page_rows = $config['cf_page_rows'];
$page_url = "list.php?page=";

// page
$page = $_GET['page'] ?? 1;

// 게시글 가져오기
$result = Board::get($bo_table, array('wr_is_comment = 0'), $page_rows);
$list = $result['list'];
$num = $result['num'];

// 페이징
$paging = Board::paging($result['total'], $page, $page_rows, 5, $page_url);


?>
<style>
    .label_cate { display:inline-block; padding:2px 6px; margin:1px; font-size:11px; border-radius:3px; background:#e0f7fa; border:1px solid #00acc1; color:#006064; }
    .label_field { display:inline-block; padding:2px 6px; margin:1px; font-size:11px; border-radius:3px; background:#fce4ec; border:1px solid #f06292; color:#880e4f; }
    .status-ok { display:inline-block; padding:2px 6px; font-size:12px; background:#e0f7e9; color:#0a7d34; border:1px solid #0a7d34; border-radius:4px; }
    .status-no { display:inline-block; padding:2px 6px; font-size:12px; background:#fdecea; color:#b71c1c; border:1px solid #b71c1c; border-radius:4px; }
</style>

<div class="local_ov01 local_ov">
    <span class="btn_ov01"><span class="ov_txt">랜딩페이지 목록</span></span>
</div>

<div class="btn_fixed_top">
    <a href="form.php" class="btn btn_02">랜딩페이지 추가</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>랜딩 관리 목록</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">카테고리</th>
            <th scope="col">랜딩ID</th>
            <th scope="col">제목</th>
            <th scope="col">사용여부</th>
            <th scope="col">입력필드</th>
            <th scope="col">등록일</th>
            <th scope="col">스킨생성여부</th>
            <th scope="col">바로가기/스킨생성</th>
            <th scope="col">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php if(empty($list)): ?>
            <tr><td colspan="10">등록된 데이터가 없습니다.</td></tr>
        <?php else : foreach ($list as $i => $row): ?>
            <?php $skin_file = G5_PATH."/landing/{$row['wr_page_code']}/landing.skin.php"; ?>
            <?php $is_created = is_file($skin_file); ?>
            <tr>
                <td><?= $num-- ?></td>
                <td>
                    <?php foreach (explode('|', $row['wr_cate_list']) as $cate): ?>
                        <?php if (trim($cate) === '') continue; ?>
                        <span class="label_cate"><?= $cate ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <a href="/landing/?ld_page=<?= $row['wr_id'] ?>" target="_blank">
                        <?php echo $row['wr_page_code']; ?>
                    </a>
                </td>
                <td><?php echo get_text($row['wr_subject']); ?></td>
                <td><?php echo $row['wr_use'] == 1 ? '사용' : '미사용'; ?></td>
                <td>
                    <?php foreach (explode('|', $row['wr_fields']) as $field): ?>
                        <?php if (trim($field) === '') continue; ?>
                        <span class="label_field"><?= $field ?></span>
                    <?php endforeach; ?>
                </td>
                <td><?php echo substr($row['wr_datetime'],0,10); ?></td>
                <td class="td_skin_status">
                    <?php if ($is_created): ?>
                        <span class="status-ok">생성됨</span>
                    <?php else: ?>
                        <span class="status-no">없음</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($is_created): ?>
                        <a href="/landing/?page_code=<?= $row['wr_page_code'] ?>" target="_blank" class="btn btn_03">랜딩페이지 바로가기</a>
                    <?php else: ?>
                        <a href="skin_create.php?page_code=<?= $row['wr_page_code'] ?>" onclick="return confirm('스킨을 생성하시겠습니까?');" class="btn btn_02">생성하기</a>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; justify-content: center; gap: .7rem;">
                        <a href="preview.php?wr_id=<?= $row['wr_id'] ?>" class="btn btn_02" onclick="return open_preview(this.href);">미리보기</a>
                        <a href="form.php?w=u&wr_id=<?= $row['wr_id']; ?>" class="btn btn_03">수정</a>
                        <a href="list_update.php?w=d&wr_id=<?= $row['wr_id'] ?>&page_code=<?= $row['wr_page_code'] ?>&bo_1=<?= $bo_table ?>&bo_2=landing_log" class="btn btn_01" onclick="return confirm('정말 삭제하시겠습니까?\n관련 수집 데이터도 모두 삭제됩니다.');">삭제</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="paging">
        <?= $paging ?>
    </div>
</div>


<script>
    function open_preview(url) {
        window.open(
            url,
            "landingPreview",
            "width=500,height=800,scrollbars=yes,resizable=yes"
        );
        return false;
    }
</script>


<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');



