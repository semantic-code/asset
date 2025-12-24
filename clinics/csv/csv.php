<?php
$colspan = 8;
include_once '_config.php';

$result = Board::get($bo_table, array('wr_is_comment = 0'), false);
$list = array_reverse($result['list']);

$file_name = date('YmdHis', time())."_클리닉_목록";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$file_name.".xls");
header("Content-Description:PHP8 Generated Data");

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table border="1">
        <caption>클리닉 목록</caption>
        <thead>
        <tr>
            <th>번호</th>
            <th>병원명</th>
            <th>전화번호</th>
            <th>기본주소</th>
            <th>상세주소</th>
            <th>긴단메모</th>
            <th>사용여부(1:사용함, 0:사용안함)</th>
            <th>등록일</th>
        </tr>
        </thead>
        <?php $num = 1; ?>
        <?php if (empty($list)): ?>
        <tr><td colspan="<?= $colspan ?>">데이터가 없습니다.</td></tr>
        <?php else: foreach ($list as $row):?>
        <tr>
            <td><?= $num++; ?></td>
            <td><?= $row['wr_subject'] ?></td>
            <td><?= $row['wr_tel'] ?></td>
            <td><?= $row['wr_addr1'] ?></td>
            <td><?= $row['wr_addr2'] ?></td>
            <td><?= $row['wr_content'] ?></td>
            <td><?= $row['wr_use'] ?></td>
            <td><?= $row['wr_datetime'] ?></td>
        </tr>
        <?php endforeach; endif; ?>
        <tbody>
        </tbody>
    </table>

</body>
</html>
