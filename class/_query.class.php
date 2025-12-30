<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Query 클래스 — SQL 관련 유틸
 */
class Query {

    /**
     * 배열 데이터를 SQL SET 구문 형태로 변환
     *
     * @param array  $data           입력 데이터 (key => value 형태)
     * @param array  $editor_fields  에디터 전용 필드 (addslashes 대신 stripslashes 저장)
     *
     * @return string SQL SET 구문 문자열
     *
     * @example
     * $sql = "INSERT INTO {$table} SET\n" . $Query->build_query($set, 'wr_content');
     */
    public static function build_query(
        array $data, 
        array $editor_fields = array()
    ): string {
        $set = array();

        foreach ($data as $key => $value) {
            // null
            if (is_null($value)) {
                $set[] = "{$key} = NULL";
                continue;
            }
            // 숫자 (선행 0 제외)
            if (is_numeric($value) && !preg_match('/^0[0-9]+$/', $value)) {
                $set[] = "{$key} = {$value}";
                continue;
            }
            // 에디터 필도 (HTML 그대로 저장)
            if (in_array($key, $editor_fields, true)) {
                $clean_value = stripslashes($value);
                $set[] = "{$key} = '{$clean_value}'";                        
                continue;
            }
            // 일반 문자열
            $escaped_value = addslashes($value);
            $set[] = "{$key} = '{$escaped_value}'";            
        }
        return implode(",\n", $set);
    }

    /**
     * 테이블 구조를 분석해, 기본값이 없는 NOT NULL 필드에 자동으로 빈값('')을 채우기 위한 배열 생성
     *
     * @param string $target_table  대상 테이블명 (예: g5_write_notice)
     * @param array  $ignore_cols   무시할 칼럼 (자동 생성되는 wr_id, wr_num 등)
     * @return array 기본값이 없는 NOT NULL 칼럼 목록
     */
    public static function get_empty_fields(string $target_table, array $ignore_cols = array()): array {
        global $g5;

        $ignore_defaults = array_merge(array(
            'wr_id', 'wr_num', 'wr_parent', 'wr_is_comment',
            'wr_datetime', 'wr_last', 'wr_ip', 'wr_hit'
        ), $ignore_cols);

        $fields = array();
        $sql = "SHOW FULL COLUMNS FROM {$target_table}";
        $res = sql_query($sql);

        while ($row = sql_fetch_array($res)) {
            $field  = $row['Field'];
            $default = $row['Default'];
            $null   = strtoupper($row['Null']);
            $extra  = $row['Extra'];

            if (in_array($field, $ignore_defaults)) continue;
            if (stripos($extra, 'auto_increment') !== false) continue;
            if ($null === 'NO' && is_null($default)) {
                $fields[] = $field;
            }
        }

        return array_fill_keys($fields, '');
    }
}


