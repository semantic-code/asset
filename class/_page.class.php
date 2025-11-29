<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Page 클래스 — 커스텀 페이징
 */
class Page {
    /**
     * HTML 페이지네이션 생성 함수 (독립 실행형)
     *
     * @param int $total_count 전체 레코드 수
     * @param int $page 현재 페이지 번호
     * @param int $limit 한 페이지당 데이터 개수
     * @param int $page_block 한 번에 표시할 페이지 번호 개수
     * @param string $base_url 페이지 이동용 기본 URL (예: '?page=')
     * @param int $style_type 0=스타일없음, 1=기본, 2=대체
     *
     * @return string HTML 출력
     */
    public static function get_paging_html (
        int $total_count,
        int $page = 1,
        int $limit = 10,
        int $page_block = 5,
        string $base_url = '?page=',
        int $style_type = 1
    ): string
    {
        $total_page = ceil($total_count / $limit);
        if ($total_page <= 1 ) return '';

        $page = max(1, (int) $page);
        $start_page = floor(($page - 1) / $page_block) * $page_block + 1;
        $end_page = min($start_page + $page_block - 1, $total_page);

        // 이전, 다음, 처음, 마지막 버튼 설정
        $symbol_first = '&laquo;&laquo;';   // «« 또는 '처음'
        $symbol_prev  = '&laquo;';         // « 또는 '이전'
        $symbol_next  = '&raquo;';         // » 또는 '다음'
        $symbol_last  = '&raquo;&raquo;';  // »» 또는 '마지막'

        ob_start();?>

        <?php if ($style_type === 1) : ?>
        <style>
            /** 그누보드 기본 색상 : #3f51b5 **/
            <?php $page_button_color = defined('G5_IS_ADMIN') ? '#3f51b5' : '#A07C6A'; ?>
            :root{--page-button-color:<?= $page_button_color?>;}
            .pager a,.pager span {display:inline-block;padding:4px 8px;margin:0 2px;border:1px solid var(--page-button-color, #3f51b5);border-radius:5px;color:var(--page-button-color, #3f51b5);text-decoration:none;font-size:12px;}
            .pager .active {background:var(--page-button-color, #3f51b5);color:#fff;border-color:var(--page-button-color, #3f51b5);}
            .pager .disabled {color:#ccc;border-color:var(--page-button-color, #3f51b5);pointer-events:none;}
        </style>
        <?php elseif ($style_type === 2) : ?>
        <style>
            /** 그누보드 기본 색상 : #3f51b5 **/
            <?php $page_button_color = defined('G5_IS_ADMIN') ? '#3f51b5' : '#A07C6A'; ?>
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

