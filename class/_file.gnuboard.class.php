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

        if (empty($wr_id)) return false;

        $upload_dir = $upload_dir ?: G5_DATA_PATH . "/file/{$bo_table}";
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, G5_DIR_PERMISSION, true);
            @chmod($upload_dir, G5_DIR_PERMISSION);
        }

        if (empty($files) || empty($files['name'][0])) return true;

        $sql = "SELECT MAX(bf_no) AS max_bf_no FROM {$g5['board_file_table']}
                WHERE bo_table='{$bo_table}' AND wr_id='{$wr_id}'";
        $row = sql_fetch($sql);
        $bf_no = is_null($row['max_bf_no']) ? 0 : (int)$row['max_bf_no'] + 1;

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK || !$files['name'][$i]) continue;

            $original_name = $files['name'][$i];
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $deny_ext = array('php', 'phar', 'exe', 'sh', 'js');
            if (in_array($ext, $deny_ext)) continue;

            $new_name = date('YmdHis') . '_' . md5(uniqid('', true)) . '.' . $ext;
            $dest_path = "{$upload_dir}/{$new_name}";

            if (!move_uploaded_file($files['tmp_name'][$i], $dest_path)) return false;
            @chmod($dest_path, G5_FILE_PERMISSION);

            $info = @getimagesize($dest_path);
            $width = $info[0] ?? 0;
            $height = $info[1] ?? 0;

            $sql = "
                INSERT INTO {$g5['board_file_table']}
                SET bo_table='{$bo_table}', wr_id='{$wr_id}', bf_no='{$bf_no}',
                    bf_source='" . addslashes($original_name) . "',
                    bf_file='{$new_name}',
                    bf_filesize='{$files['size'][$i]}',
                    bf_width='{$width}', bf_height='{$height}', bf_datetime=NOW()
            ";
            sql_query($sql);
            $bf_no++;
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

        sql_query("DELETE FROM {$g5['board_file_table']} WHERE bo_table='{$bo_table}' AND wr_id='{$wr_id}' {$bf_sql}");
        return true;
    }
}


