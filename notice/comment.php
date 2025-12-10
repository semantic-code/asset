<?php
$sql = "SELECT * FROM {$target_table} WHERE wr_parent = {$wr_id} AND wr_is_comment = 1 AND wr_comment_reply = '' ORDER BY wr_comment";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)){
    $data = get_list($row, $board, '', '');
    $comment[] = $data;
}

//print_r2($cmt);
?>
<section class="comment-wrap">
    <!-- 댓글 목록 -->
    <div class="comment-list">
        <h3 class="comment-title">댓글</h3>

        <?php if ($comment): foreach ($comment as $row): ?>
            <?php if ($row['wr_id'] === $co_id): ?>
            <form id="frm" method="post" action="comment_update.php">
                <input type="hidden" name="wr_id" value="<?= $row['wr_parent'] ?>">
                <input type="hidden" name="co_id" value="<?= $co_id ?>">

                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-name"><?= $row['wr_name'] ?></span>
                        <span class="comment-date"><?= $row['datetime'] ?></span>
                    </div>
                    <textarea name="wr_content" class="comment-textarea"><?= $row['wr_content'] ?></textarea>
                    <div class="comment-actions" style="display: flex;">
                        <button type="submit" class="btn-update" name="action" value="update">댓글 등록</button>
                        <a href="view.php?wr_id=<?= $row['wr_parent'] ?>" class="btn-cancel">등록취소</a>
                    </div>
                </div>
            </form>

            <?php else: ?>

            <form id="comment_edit_form_<?= $row['wr_id'] ?>" method="post" action="comment_update.php" onsubmit="return confirm('해당 댓글을 삭제하시겠습니까?')">
                <input type="hidden" name="wr_id" value="<?= $row['wr_parent'] ?>">
                <input type="hidden" name="co_id" value="<?= $row['wr_id'] ?>">

                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-name"><?= $row['wr_name'] ?></span>
                        <span class="comment-date"><?= $row['datetime'] ?></span>
                    </div>
                    <div class="comment-content"><?= $row['wr_content'] ?></div>
                    <div class="comment-actions" style="display: flex;">
                        <a href="view.php?w=r&wr_id=<?= $row['wr_parent']  ?>&co_id=<?= $row['wr_id'] ?>" class="btn-edit">수정</a>
                        <button type="submit" class="btn-del" name="action" value="delete">삭제</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        <?php endforeach; endif; ?>
    </div>

    <?php if ($w !== 'r'): ?>
    <div class="comment-write">
        <form id="comment_form" method="post" action="comment_update.php" onsubmit="if (!this.wr_content.value.trim()) return (alert('내용을 입력하세요.'), false);">
            <input type="hidden" name="wr_id" value="<?= $wr_id ?>">

            <textarea name="wr_content" class="comment-textarea" placeholder="댓글을 입력하세요"></textarea>
            <div class="comment-submit-wrap">
                <button type="submit" class="btn-submit" name="action" value="insert">댓글 등록</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</section>

<style>
    .comment-wrap{padding:15px;border:1px #ddd solid;margin-top:20px;border-radius:5px;}
    .comment-title{font-size:16px;font-weight:bold;margin-bottom:12px;}
    .comment-item{padding:10px;border-bottom:1px #eee solid;}
    .comment-header{display:flex;gap:10px;font-size:13px;color:#666;margin-bottom:5px;}
    .comment-date{margin-bottom: .5rem;}
    .comment-name{font-weight:bold;color:#333;}
    .comment-content{font-size:14px;margin-bottom:8px;}
    .comment-actions{display:flex;gap:8px;}
    .btn-edit,.btn-del,.btn-update,.btn-cancel{padding:5px 10px;font-size:12px;border:1px #ccc solid;background:#f5f5f5;cursor:pointer;}
    .comment-write{margin-top:15px;}
    .comment-textarea{width:100%;height:80px;margin-bottom:10px;padding:10px;border:1px #ccc solid;border-radius:4px;font-size:14px;resize:vertical;}
    .comment-submit-wrap{text-align:right;margin-top:8px;}
    .btn-submit{padding:6px 14px;background:#333;color:#fff;border:none;border-radius:5px;font-size:13px;cursor:pointer;}
</style>


