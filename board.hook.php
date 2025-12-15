<?php
if (!defined('_GNUBOARD_')) exit;

// contact 폴더를 게시판 ID 금지어에서 제외
add_replace('get_bo_table_banned_word', 'allow_contact_bo_table', 10, 1);

function allow_contact_bo_table($folders)
{

    if (is_array($folders)) {
        $folders = array_diff($folders, array('products'));
        $folders = array_values($folders); // 인덱스 정리
    }

    return $folders;
}
