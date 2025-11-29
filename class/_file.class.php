<?php
/**
 * File 클래스 — 파일 업로드 / 삭제 PDO
 */
class File {

    /**
     * 파일 첨부 처리
     *
     * @param array  $files        기본 : $_FILES['bf_file']
     * @param string $bo_table     게시판 테이블명
     * @param int    $idx        글 고유 아이디
     * @param string $upload_dir   업로드 경로 (기본: /data/file/{bo_table})
     * @return bool  파일 업로드 성공여부
     */
    public static function attach_files(
        array $files,
        string $bo_table,
        int $idx,
        string $upload_dir = ''
    ): bool {

        global $DB, $tb;

        if (empty($idx)) return false;

        $data_path = DATA_PATH;

        $upload_dir = $upload_dir ?: "{$data_path}/file/{$bo_table}";
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0707, true);
            @chmod($upload_dir, 0707);
        }

        if (empty($files) || empty($files['name'][0])) return true;

        $sql = "SELECT MAX(bf_no) AS max_bf_no FROM {$tb['attach_files']} WHERE (1) AND bo_table=:bo_table AND idx=:idx";
        $row = $DB->row($sql,  array('bo_table' => $bo_table, 'idx' => $idx));
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
            @chmod($dest_path, 0770);

            $info = @getimagesize($dest_path);
            $width = $info[0] ?? 0;
            $height = $info[1] ?? 0;

            $insert_data = array(
                'bo_table'   => $bo_table,
                'idx'        => $idx,
                'bf_no'      => $bf_no,
                'bf_source'  => $original_name,
                'bf_file'    => $new_name,
                'bf_filesize'=> $files['size'][$i],
                'bf_width'   => $width,
                'bf_height'  => $height,
                'bf_datetime'=> date('Y-m-d H:i:s'),
            );

            $DB->insert($tb['attach_files'], $insert_data);
            $bf_no++;
        }

        return true;
    }

    /**
     * 게시물에 연결된 첨부파일 삭제
     *
     * @param string   $bo_table   게시판 테이블명
     * @param int      $idx      글 고유 ID
     * @param int|null $bf_no      일부 파일만 삭제할 경우 지정
     * @param string   $upload_dir 업로드 경로 (기본: /data/file/{bo_table})
     * @return bool 성공 여부 (삭제할 파일이 없어도 true)
     */
    public static function delete_file(
        string $bo_table,
        int $idx,
        ?int $bf_no = null,
        string $upload_dir = ''
    ): bool {

        global $DB, $tb;

        if (!$bo_table || !$idx) return false;

        $data_path = DATA_PATH;

        $upload_dir = $upload_dir ?: "{$data_path}/file/{$bo_table}";
        $delete_data = array("bo_table" => $bo_table, "idx" => $idx);

        $bf_sql = '';
        if(!is_null($bf_no)) {
            $bf_sql = " AND bf_no=:bf_no";
            $delete_data['bf_no'] = $bf_no;
        }

        $res = $DB->query("SELECT * FROM {$tb['attach_files']} WHERE bo_table=:bo_table AND idx=:idx {$bf_sql}", $delete_data);
        foreach ($res as $f) {
            $path = "{$upload_dir}/{$f['bf_file']}";
            if (is_file($path)) @unlink($path);
        }

        $DB->query("DELETE FROM {$tb['attach_files']} WHERE bo_table=:bo_table AND idx=:idx {$bf_sql}", $delete_data);
        return true;
    }
}

