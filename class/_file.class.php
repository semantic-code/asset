<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * File 클래스 — 파일 업로드 / 삭제, 그누보드
 */
class File {

    /**
     * 파일 첨부 처리
     *
     * @param array  $files        기본 : $_FILES['bf_file']
     * @param string $bo_table     게시판 테이블명
     * @param int    $wr_id        글 고유 아이디
     * @param string $upload_dir   업로드 경로 (기본: /data/file/{bo_table})
     * @return bool  파일 업로드 성공여부
     */
    public static function attach_files(
        array $files,
        string $bo_table,
        int $wr_id,
        string $upload_dir = ''
    ): bool {
        global $g5;

        if (!$wr_id || empty($files['name'])) return true;

        $upload_dir = $upload_dir ?: G5_DATA_PATH . "/file/{$bo_table}";
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, G5_DIR_PERMISSION, true);
            @chmod($upload_dir, G5_DIR_PERMISSION);
        }

        foreach ($files['name'] as $bf_no => $original_name) {
            if (!$original_name) continue;
            if ($files['error'][$bf_no] !== UPLOAD_ERR_OK) continue;

            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $deny_ext = array('php', 'phar', 'exe', 'sh', 'js');
            if (in_array($ext, $deny_ext)) continue;

            $new_name = date('YmdHis') . '_' . md5(uniqid('', true)) . '.' . $ext;
            $dest_path = "{$upload_dir}/{$new_name}";

            if (!move_uploaded_file($files['tmp_name'][$bf_no], $dest_path)) return false;
            @chmod($dest_path, G5_FILE_PERMISSION);

            $info = @getimagesize($dest_path);
            $width = $info[0] ?? 0;
            $height = $info[1] ?? 0;
            $bf_type = ($width > 0) ? 1 : 0;

            // bf_no 존재 여부 확인
            $row = sql_fetch("SELECT bf_no FROM {$g5['board_file_table']} WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' AND bf_no = '{$bf_no}' ");

            if ($row) {
                $sql = "
                UPDATE {$g5['board_file_table']} SET
                    bf_source   = '".addslashes($original_name)."',
                    bf_file     = '{$new_name}',
                    bf_filesize = '{$files['size'][$bf_no]}',
                    bf_width    = '{$width}',
                    bf_height   = '{$height}',
                    bf_type     = '{$bf_type}',
                    bf_datetime = NOW()
                WHERE bo_table='{$bo_table}'
                  AND wr_id='{$wr_id}'
                  AND bf_no='{$bf_no}'
                ";
                sql_query($sql);

            } else {
                $sql = "
                INSERT INTO {$g5['board_file_table']} SET 
                    bo_table = '{$bo_table}', 
                    wr_id = '{$wr_id}', 
                    bf_no = '{$bf_no}', 
                    bf_source = '" . addslashes($original_name) . "', 
                    bf_file = '{$new_name}', 
                    bf_filesize = '{$files['size'][$bf_no]}', 
                    bf_width = '{$width}', 
                    bf_height = '{$height}', 
                    bf_type = '{$bf_type}', 
                    bf_datetime = NOW(); ";
                sql_query($sql);
            }
        }

        return true;
    }

    /**
     * 게시물에 연결된 첨부파일 삭제
     *
     * @param string   $bo_table   게시판 테이블명
     * @param int      $wr_id      글 고유 ID
     * @param int|null $bf_no      일부 파일만 삭제할 경우 지정
     * @param string   $upload_dir 업로드 경로 (기본: /data/file/{bo_table})
     * @return bool 성공 여부 (삭제할 파일이 없어도 true)
     */
    public static function delete_attach_file(
        string $bo_table,
        int $wr_id,
        ?int $bf_no = null,
        string $upload_dir = ''
    ): bool {
        global $g5;

        if (!$bo_table || !$wr_id) return false;

        $upload_dir = $upload_dir ?: G5_DATA_PATH . "/file/{$bo_table}";
        $bf_sql = is_null($bf_no) ? "" : " AND bf_no='{$bf_no}'";

        $res = sql_query("SELECT * FROM {$g5['board_file_table']} WHERE bo_table='{$bo_table}' AND wr_id='{$wr_id}' {$bf_sql}");
        while ($f = sql_fetch_array($res)) {
            $path = "{$upload_dir}/{$f['bf_file']}";
            if (is_file($path)) @unlink($path);
        }

        // 파일 삭제
        /*sql_query("DELETE FROM {$g5['board_file_table']} WHERE bo_table='{$bo_table}' AND wr_id='{$wr_id}' {$bf_sql}");*/

        // 파일 업로드
        $delete_sql = "
            UPDATE {$g5['board_file_table']}
            SET
                bf_source    = '',
                bf_file      = '',
                bf_download  = 0,
                bf_content   = '',
                bf_fileurl   = '',
                bf_thumburl  = '',
                bf_storage   = '',
                bf_filesize  = 0,
                bf_width     = 0,
                bf_height    = 0,
                bf_type      = 0,
                bf_datetime  = '".G5_TIME_YMDHIS."'
            WHERE bo_table = '{$bo_table}'
              AND wr_id    = '{$wr_id}'
              {$bf_sql}
        ";
        sql_query($delete_sql);

        return true;
    }

    /**
     * 첫 번째 실제 첨부파일 확장자 반환
     * 파일 없으면 null
     */
    public static function get_ext (
        array $files
    ): ?string
    {
        foreach ($files as $key => $file) {
            // count 제외
            if ($key === 'count') continue;
            // 파일 슬롯이 아니면 제외
            if (!is_array($file)) continue;
            // 파일명 없으면 제외
            if (empty($file['file'])) continue;
            // 실제 파일 존재 확인
            $real_path = G5_DATA_PATH. '/file/' . basename($file['path']) . '/' . $file['file'];
            if (!is_file($real_path)) continue;

            // 첫 번째 유효 파일 확장자 반환
            return strtolower(pathinfo($file['file'], PATHINFO_EXTENSION));
        }

        return null;
    }
}


