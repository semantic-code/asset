<?php
include_once '_config.php';

$list = Board::view($bo_table, $wr_id);

?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>랜딩 미리보기</title>
    <style>
        body {margin:20px; font-family:sans-serif;}
        .content img {max-width:100%;}
    </style>
</head>
<body>
<?php if ($list): ?>
    <div class="content">
        <?php $src = "{$list['file'][0]['path']}/{$list['file'][0]['file']}"  ; ?>
        <img src="<?= $src ?>" alt="<?= $list['file']['source']?>">
    </div>
<?php else: ?>
    <p>해당 랜딩 데이터가 없습니다.</p>
<?php endif; ?>
</body>
</html>