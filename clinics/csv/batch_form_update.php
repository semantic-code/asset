<?php
include_once '_config.php';

$csv_map = array(
    0 => 'wr_subject',
    1 => 'wr_tel',
    2 => 'city',
    3 => 'district',
    4 => 'addr_road',
    5 => 'addr_detail',
    6 => 'wr_use',
);

$area_code = array(
    '서울' => '02',
    '부산' => '051',
    '대구' => '053',
    '인천' => '032',
    '광주' => '062',
    '대전' => '042',
    '울산' => '052',
    '세종' => '044',
    '경기' => '031',
    '강원' => '033',
    '충북' => '043',
    '충남' => '041',
    '전북' => '063',
    '전남' => '061',
    '경북' => '054',
    '경남' => '055',
    '제주' => '064',
);

/*
echo "<pre>";
print_r($_FILES);
echo "</pre>";
exit;
*/

// 파일 체크
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    die("<script>alert('파일 업로드 실패'); history.back();</script>");
}

$file = $_FILES['csv_file']['tmp_name'];
ini_set('auto_detect_line_endings', true);

$handle = fopen($file, "r");
if (!$handle) {
    die("<script>alert('CSV 파일을 열 수 없습니다.'); history.back();</script>");
}

// 카운트
$row_count = 0;
$inserted  = 0;
$updated   = 0;
$skipped   = 0;

// csv loop
while (($data = fgetcsv($handle)) !== false) {
    // 첫줄 : 해더 + BOM 제거
    if ($row_count < 2) {
        if (isset($data[0])) {
            $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
        }
        $row_count++;
        continue;
    }

    // csv_map
    $row_data = array();
    foreach ($csv_map as $idx => $field) {
        $row_data[$field] = trim($data[$idx] ?? '');
    }

    // 필수값 체크
    if ($row_data['wr_subject'] === '') {
        $skipped++;
        continue;
    }

    // 병원명, 전화번호 정규화
    $norm_name = sql_real_escape_string(str_replace(' ', '', $row_data['wr_subject']));
    $norm_tel  = sql_real_escape_string(str_replace(array('-', ' '), '', $row_data['wr_tel']));

    // 기존 데이터 존재 여부 확인
    $sql = "SELECT wr_id 
            FROM {$target_table} 
            WHERE (1)
            AND wr_is_comment = 0 
            AND REPLACE(wr_subject, ' ', '') = '{$norm_name}'
            AND REPLACE(REPLACE(wr_tel, '-', ''), ' ', '') = '{$norm_tel}' 
            LIMIT 1";
    $row = sql_fetch($sql);

    // update, insert
    if ($row && $row['wr_id']) {
        // update
        $set = array(
            'wr_addr1'   => "{$row_data['city']} {$row_data['district']} {$row_data['addr_road']}",
            'wr_addr2'   => $row_data['addr_detail'],
            'wr_use'     => $row_data['wr_use'],
            'wr_last'    => G5_TIME_YMDHIS,
            'wr_ip'      => $_SERVER['REMOTE_ADDR'],
        );

        $sql = "UPDATE {$target_table} SET\n " . Query::build_query($set) . "\nWHERE wr_id = '{$row['wr_id']}'";
        $update = sql_query($sql);

        $updated++;


    } else {
        $wr_num = get_next_num($target_table);
        // INSERT
        $set = array(
            'wr_num'      => $wr_num,
            'wr_option'   => 'html1',
            'wr_subject'  => $row_data['wr_subject'],
            'wr_content'  => '내용-' . time(),
            'mb_id'       => $member['mb_id'],
            'wr_name'     => $member['mb_name'],
            'wr_datetime' => G5_TIME_YMDHIS,
            'wr_last'     => G5_TIME_YMDHIS,
            'wr_ip'       => $_SERVER['REMOTE_ADDR'],
        );
        $set+= array(
            'wr_tel'         => $row_data['wr_tel'],
            'wr_use'         => $row_data['wr_use'],
            'wr_addr1'       => "{$row_data['city']} {$row_data['district']} {$row_data['addr_road']}",
            'wr_addr2'       => $row_data['addr_detail'],
            'wr_addr_jibeon' => 'R',
            'wr_code'        => $area_code[$row_data['city']],
            'wr_city'        => $row_data['city'],
            'wr_district'    => $row_data['district'],
        );
        $set+= Query::get_empty_fields($target_table);

        $sql = "INSERT INTO {$target_table} SET\n" . Query::build_query($set);
        /*
        echo '<pre>';
        print_r($set);
        echo '</pre>';
        exit;
        */

        $insert = sql_query($sql);

        // wr_parent
        $wr_id = sql_insert_id();
        sql_query("UPDATE {$target_table} SET wr_parent = '{$wr_id}' WHERE wr_id = '{$wr_id}'");

        $sql = "UPDATE {$target_table} SET\n" . Query::build_query($set) . "WHERE wr_id = {$wr_id}";
        sql_query($sql);

        $wr_num--;
        $inserted++;
    }
    $row_count++;
}

fclose($handle);

/* --------------------------------------------------
 * 결과
 * -------------------------------------------------- */
$params = http_build_query(array(
    'inserted' => $inserted,
    'updated'  => $updated,
    'skipped'  => $skipped,
));

goto_url("batch_form.php?{$params}");
