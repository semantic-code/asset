<?php
include_once '_config.php';

$str_sql = "SELECT DISTINCT wr_district FROM {$target_table} WHERE (1) AND wr_use = 1 AND wr_city = '{$city}' ORDER BY wr_district ASC ";
$result = sql_query($str_sql);

while ($row = sql_fetch_array($result)){
    $list[] = $row['wr_district'];
}
?>

<?php ob_start();?>
    <option value="">전체</option>
<?php if ($list):?>
    <?php foreach($list as $row):?>
        <option value="<?= $row ?>" <?php echo get_selected($row, $district) ?>><?= $row ?></option>
    <?php endforeach; ?>
<?php endif;?>
<?php $option_html = ob_get_clean();?>

<?php
$arr_result = array("state" => "success", "district" => $option_html);
die(json_encode($arr_result, JSON_UNESCAPED_UNICODE));
