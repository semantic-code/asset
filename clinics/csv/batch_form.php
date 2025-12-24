<?php
include_once '_config.php';
$g5['title'] = $page_title .' 일괄등록';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>

<style>
    ol li {padding-bottom: .5rem;}
</style>

<div style="margin-bottom: 1.5rem;">
    <ol>
        <li>좌측 상단의 '템플릿' 버튼을 눌러 clinics_template.csv 파일을 다운 받습니다.</li>
        <li>클리닉 정보를 형식에 맞게 입력합니다.</li>
        <li>파일 선택을 누른 후, 해당 파일을 선택합니다.</li>
        <li>'일괄등록' 버튼을 눌러 데이터를 등록합니다.</li>
        <li>병원명, 전화번호 기준, 동일한 데이터는 수정, 그 외 데이터는 입력됩니다.</li>
    </ol>
</div>

<form action="batch_form_update.php" method="post" enctype="multipart/form-data" onsubmit="return confirm('계속하시겠습니까?');">
    <input type="file" name="csv_file" accept=".csv">
    <input type="submit" class="btn btn_success" value="일괄등록">
</form>
<div class="btn_fixed_top">
    <a href="" class="btn btn_02">목록</a>
    <a href="/download/clinics_template.csv" download class="btn btn_01 ">템플릿</a>
</div>

<?php if ($inserted || $updated || $skipped): ?>
    <div class="batch-result" style="margin-top: 1rem;">
        <h2>업로드 결과</h2>
        <ul>
            <li>신규 등록: <strong><?= number_format($inserted) ?></strong>건</li>
            <li>수정: <strong><?= number_format($updated) ?></strong>건</li>
            <li>건너뜀: <strong><?= number_format($skipped) ?></strong>건</li>
        </ul>
    </div>
<?php endif; ?>

<?php
include_once G5_ADMIN_PATH . '/admin.tail.php';
