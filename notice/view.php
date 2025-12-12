<?php
include_once '_config.php';
$g5['title'] = "{$page_title} 보기";
include_once G5_ADMIN_PATH . '/admin.head.php';

$view = Board::view($bo_table, $wr_id);
$file_list = $view['file'];

var_dump($co_id);
//print_r2($view);
?>

<section class="view-wrap">
    <!-- 제목 -->
    <h2 class="view-subject"><?= $view['wr_subject'] ?></h2>

    <!-- 메타 정보 -->
    <div class="view-meta">
        <span class="view-name"><?= $view['wr_name'] ?></span>
        <span class="view-date"><?= date('Y-m-d H:i', strtotime($view['wr_datetime'])) ?></span>
    </div>

    <!-- 본문 내용 -->
    <div class="view-content">
        <?= nl2br($view['wr_content']) ?>
    </div>

    <!-- 이미지 미리보기 -->
    <?php if (!empty($file_list)): ?>
        <div class="view-images">
            <?php foreach ($file_list as $file): ?>
            <?php if (!is_array($file)) continue; ?>
                <div class="view-img-box">
                    <img src="<?= $file['path'] . '/' . $file['file'] ?>" alt="<?= $file['source'] ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 첨부파일 -->
    <?php if (!empty($file_list)): ?>
        <div class="view-files">
            <h3 class="files-title">첨부파일</h3>
            <ul>
                <?php foreach ($file_list as $file): ?>
                <?php if (!is_array($file)) continue; ?>
                    <li>
                        <a href="<?= $file['href'] ?>" target="_blank"><?= $file['source'] ?></a>
                        (<?= $file['size'] ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>

<div class="btn_fixed_top">
    <a href="list.php" class="btn btn_02">목록</a>
    <a href="form.php?w=u&wr_id=<?= $wr_id ?>" class="btn btn_01">수정</a>
</div>

<!-- 댓글 -->
<?php include_once "comment.php"; ?>

<style>
    .view-wrap{padding:20px;border:1px #ddd solid;border-radius:5px;max-width:900px;}
    .view-subject{font-size:22px;font-weight:bold;margin-bottom:10px;}
    .view-meta{display:flex;gap:15px;font-size:14px;color:#666;margin-bottom:20px;}
    .view-name{font-weight:bold;color:#333;}
    .view-content{font-size:15px;line-height:1.6;margin-bottom:25px;}
    .view-files{margin-bottom:25px;}
    .files-title{font-size:16px;font-weight:bold;margin-bottom:8px;}
    .view-files ul{margin:0;padding:0;list-style:none;}
    .view-files li{margin-bottom:5px;}
    .view-files a{text-decoration:none;color:#0066cc;}
    .view-files a:hover{text-decoration:underline;}
</style>

<?php

include_once G5_ADMIN_PATH . '/admin.tail.php';
