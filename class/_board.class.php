<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 게시판 리스트 가져오기, 본문 가져오기, 페이징
 * static
 */
class Board {
    /**
     * 게시판 리스트, 총 데이터 수($total), 게시글번호($num)을 반환하는 함수
     *
     * @param string         $bo_table   게시판 테이블 명
     * @param array|false    $where      커스텀 WHERE 조건 (배열), false 사용안함
     * @param int|false|null $page_rows 한 페이지당 목록 수, false 면 LIMIT 없음
     *
     * @return array list, total_count, paging
     */
    public static function get (
        string         $bo_table,
        array|false    $where = array(),
        int|false|null $page_rows = null,
    ): array
    {
        global $g5;

        $board = get_board_db($bo_table);
        $target_table = $g5['write_prefix'] . $bo_table;

        // --------------------------------------------------
        // 1) Paging 기본값
        // --------------------------------------------------
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit_sql = '';
        $use_limit = false;

        if ($page_rows !== false) {
            // 페이징 사용
            $use_limit = true;
            $page_rows = $page_rows ?? ($board['bo_page_rows'] ?? 15);
            $offset = ($page - 1) * $page_rows;

            $limit_sql = "LIMIT {$offset}, {$page_rows}";
        }

        // --------------------------------------------------
        // 2) WHERE 조건 조립
        // --------------------------------------------------
        if ($where === false) $where = array();

        // 카테고리 sca
        $sca = $_GET['sca'] ?? '';
        if ($sca) $where[] = "ca_name = '{$sca}' ";

        // 검색 sfl, stx
        $sfl = $_GET['sfl'] ?? '';
        $stx = $_GET['stx'] ?? '';

        if ($sfl && isset($stx) && $stx !== '') {
            // or 검색
            if (strpos($sfl, '|') !== false) {
                $parts =  array();
                foreach (explode('|', $sfl) as $field) {
                    $field = trim($field);
                    if ($field) $parts[] = "{$field} LIKE '%{$stx}%' ";
                }
                if ($parts) $where[] = "(" . implode(' OR ', $parts) . ")";
            } else {
                $where[] = "{$sfl} LIKE '%{$stx}%' ";
            }
        }
        $where_sql = $where ? " WHERE " . implode(" AND ", $where) : "";

        // --------------------------------------------------
        // 3) ORDER BY
        // --------------------------------------------------
        $order_sql = !empty($board['bo_sort_field']) ? "ORDER BY {$board['bo_sort_field']}" : "ORDER BY wr_num";

        // --------------------------------------------------
        // 4) total count
        // --------------------------------------------------
        $sql_count = "SELECT COUNT(*) cnt FROM {$target_table} {$where_sql}";
        $total_count = sql_fetch($sql_count)['cnt'] ?? 0;

        // --------------------------------------------------
        // 5) 게시판 목록
        // --------------------------------------------------
        $sql = "SELECT * FROM {$target_table} {$where_sql} {$order_sql} {$limit_sql}";
        $result = sql_query($sql);

        $list = array();
        while ($row = sql_fetch_array($result)) {
            $data = get_list($row, $board, '', '');
            $data['file'] = get_file($bo_table, $row['wr_id']);

            $list[] = $data;
        }
        $num = $use_limit ? ($total_count - $offset) : $total_count ;

        // --------------------------------------------------
        // 6) return
        // --------------------------------------------------
        $return = array(
            'list'  => $list,
            'total' => $total_count,
            'num'   => $num,
            'sql'   => $sql
        );

        return $return;
    }
   
    /**
     * 게시판 상세보기 데이터 반환
     *
     * - wr_id 기준으로 단일 게시글을 가져오며
     * - 조회수 증가 옵션(hit=true)도 지원
     * - 첨부파일, 이전글/다음글 정보 포함
     *
     * @param string $bo_table  게시판 테이블명 (예: 'notice', 'portfolio')
     * @param int    $wr_id     글 고유 ID (wr_id)
     * @param bool   $hit       조회수 증가 여부 (true면 wr_hit + 1)
     *
     * @return array|null       게시글 상세 데이터 반환,
     */
    public static function view (
        string $bo_table,
        int $wr_id,
        bool $hit = false
    ) : ?array
    {
        global $g5;

        if (!$wr_id) return null;

        $board = get_board_db($bo_table);
        $target_table = $g5['write_prefix'] . $bo_table;

        // --------------------------------------------------
        // 1) 게시글 가져오기
        // --------------------------------------------------
        $sql = "SELECT * FROM {$target_table} WHERE wr_id = '{$wr_id}' ";
        $row = sql_fetch($sql);

        if (!$row) return null;

        // --------------------------------------------------
        // 2) 조회수 증가
        // --------------------------------------------------
        if ($hit) {
            $update_sql = "UPDATE {$target_table} SET wr_hit = wr_hit + 1 WHERE wr_id = '{$wr_id}' ";
            sql_query($update_sql);
            $row['wr_hit']++;
        }

        // --------------------------------------------------
        // 3) 상세 데이터 구성
        // --------------------------------------------------
        $data = get_list($row, $board, '', '');
        $data['file'] = get_file($bo_table, $wr_id);

        // --------------------------------------------------
        // 4) 이전글, 다음글 (카테고리 자동 적용)
        // --------------------------------------------------
        $ca_name = $data['ca_name'] ?? '';
        $sca_sql = $ca_name !== '' ? "AND ca_name = '{$ca_name}'" : "";
        
        $prev = sql_fetch("SELECT wr_id, wr_subject FROM {$target_table} WHERE wr_id < '{$wr_id}' AND wr_is_comment = 0 {$sca_sql} ORDER BY wr_id DESC LIMIT 1");
        $next = sql_fetch("SELECT wr_id, wr_subject FROM {$target_table} WHERE wr_id > '{$wr_id}' AND wr_is_comment = 0 {$sca_sql} ORDER BY wr_id ASC LIMIT 1");

        $data['prev'] = $prev ?? null;
        $data['next'] = $next ?? null;


        return $data;
    }

    /**
     * HTML 페이지네이션 생성 함수 (독립 실행형)
     *
     * @param int $total_count 전체 레코드 수
     * @param int $page 현재 페이지 번호
     * @param int $page_rows 한 페이지당 데이터 개수
     * @param int $page_block 한 번에 표시할 페이지 번호 개수
     * @param string $base_url 페이지 이동용 기본 URL (예: '?page=')
     * @param int $style_type 0=스타일없음, 1=기본, 2=대체
     *
     * @return string HTML 출력
     */
    public static function paging (
        int $total_count,
        int $page = 1,
        int $page_rows = 10,
        int $page_block = 5,
        string $base_url = '?page=',
        int $style_type = 1
    ): string
    {
        $total_page = ceil($total_count / $page_rows);
        if ($total_page <= 1 ) return '';

        $page = max(1, (int) $page);
        $start_page = floor(($page - 1) / $page_block) * $page_block + 1;
        $end_page = min($start_page + $page_block - 1, $total_page);

        // 이전, 다음, 처음, 마지막 버튼 설정
        $symbol_first = '&laquo;&laquo;';   // «« 또는 '처음'
        $symbol_prev  = '&laquo;';         // « 또는 '이전'
        $symbol_next  = '&raquo;';         // » 또는 '다음'
        $symbol_last  = '&raquo;&raquo;';  // »» 또는 '마지막'

        // 그누보드 기본 색상 : #3f51b5
        $admin_color = '#3f51b5';
        $default_color = '#A07C6A';

        ob_start();?>

        <?php if ($style_type === 1) : ?>
        <style>
            <?php $page_button_color = defined('G5_IS_ADMIN') ? $admin_color : $default_color; ?>
            :root{--page-button-color:<?= $page_button_color?>;}
            .pager a,.pager span {display:inline-block;padding:4px 8px;margin:0 2px;border:1px solid var(--page-button-color, #3f51b5);border-radius:5px;color:var(--page-button-color, #3f51b5);text-decoration:none;font-size:12px;}
            .pager .active {background:var(--page-button-color, #3f51b5);color:#fff;border-color:var(--page-button-color, #3f51b5);}
            .pager .disabled {color:#ccc;border-color:var(--page-button-color, #3f51b5);pointer-events:none;}
        </style>
    <?php elseif ($style_type === 2) : ?>
        <style>
            <?php $page_button_color = defined('G5_IS_ADMIN') ? $admin_color : $default_color; ?>
            :root{--page-button-color:<?= $page_button_color?>;}
            .pager a,.pager span {display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;margin:0 3px;border-radius:50%;border:1px solid var(--page-button-color, #3f51b5);color:var(--page-button-color, #3f51b5);background:#fff;text-decoration:none;font-size:13px;transition:all .2s;}
            .pager .active {background:var(--page-button-color, #3f51b5);color:#fff;border-color:var(--page-button-color, #3f51b5);}
            .pager .disabled {opacity:0.5;border-color:var(--page-button-color, #3f51b5);pointer-events:none;}
        </style>
    <?php endif; ?>

        <div class="pager" style="text-align: center; padding: 15px 0;">
            <!-- 첫 페이지 -->
            <?php if ($page > 1) : ?>
                <a href="<?= $base_url?>1"><?= $symbol_first ?></a>
            <?php else : ?>
                <span class="disabled"><?= $symbol_first ?></span>
            <?php endif; ?>

            <!-- 이전 페이지 -->
            <?php if ($page > 1) : ?>
                <a href="<?= $base_url ?><?= max(1, $page -1) ?>"><?= $symbol_prev ?></a>
            <?php else : ?>
                <span class="disabled"><?= $symbol_prev ?></span>
            <?php endif; ?>

            <!-- 페이지 번호 -->
            <?php for ($i = $start_page; $i <= $end_page; $i++) : ?>
                <?php if ($i == $page) : ?>
                    <span class="active"><?= $i ?></span>
                <?php else : ?>
                    <a href="<?= $base_url ?><?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- 다음 페이지 -->
            <?php if ($page < $total_page) : ?>
                <a href="<?= $base_url ?><?= min($total_page, $page + 1)?>"><?= $symbol_next ?></a>
            <?php else : ?>
                <span class="disabled"><?= $symbol_next?></span>
            <?php endif; ?>

            <!-- 마지막 페이지 -->
            <?php if ($page < $total_page) : ?>
                <a href="<?= $base_url ?><?= $total_page ?>"><?= $symbol_last ?></a>
            <?php else : ?>
                <span class="disabled"><?= $symbol_last ?></span>
            <?php endif; ?>

        </div>

        <?php return ob_get_clean();
    }
}


