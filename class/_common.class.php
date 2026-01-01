<?php
class Common {
    /**
     * 접근 권한 체크 (그누보드 기준, 범용)
     *
     * @param string|null $mb_id        회원 ID (비회원이면 빈값)
     * @param int|string  $allow_level  허용 최소 레벨 (빈값이면 레벨 제한 없음)
     * @param string      $msg          차단 시 메시지
     */
    public static function check_access(
        ?string $mb_id,
        ?int $allow_level = null,
        string $msg = '접근 권한이 없습니다.'
    ): void
    {
        global $member;

        // 로그인 체크
        if (empty($mb_id)) self::abort('로그인 후 이용 가능합니다.');

        // 레벨 제한 없으면 통과
        if ($allow_level === '' || $allow_level === null) return;

        // 회원 레벨
        $mb_level = (int)$member['mb_level'] ?? 0;
        if ($mb_level < (int)$allow_level) self::abort($msg);
    }

    /**
     * 자바스크립트 alert 커스텀 (클래스 내 사용)
     * @param string $msg 출력할 데이터
     */
    public static function abort(string $msg): void {
        ob_start(); ?>
        <script>
            alert(<?= json_encode(addslashes($msg)) ?>);
            window.history.back();
        </script>
        <?php die(ob_get_clean());
    }

    /**
     * PHP 값을 브라우저 콘솔에 출력
     * @param mixed  $data  출력할 데이터 (string|array|object)
     * @param string $label 콘솔 라벨
     */
    public static function console($data, string $label = 'PHP'): void
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
            | JSON_HEX_AMP
        );

        if ($json === false) return;

        echo "<script>";
        echo "console.log([" . json_encode($label) . "], {$json});";
        echo "</script>";
    }
}
