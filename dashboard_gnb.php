<?php if (defined('_DASHBOARD_')): ?>
    <li class="gnb_li on">
        <button type="button"
                class="btn_op menu-extend menu-order-0"
                title="통합관리"
                onclick="location.href='<?= G5_ADMIN_URL ?>/dashboard.php'">
            통합관리
        </button>

        <div class="gnb_oparea_wr">
            <div class="gnb_oparea">
                <h3>통합관리</h3>
                <ul>
                    <li>
                        <a href="<?= G5_ADMIN_URL ?>/dashboard.php"
                           class="gnb_2da on">
                            대시보드
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
<?php endif; ?>
