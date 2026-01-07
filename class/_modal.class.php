<?php
/**
 * Modal
 *
 * <dialog> 기반 공통 모달 클래스
 *
 * PHP 역할
 * - ob_start()를 이용해 공통 modal HTML + JS를 출력
 * - alert / confirm / prompt JS API를 제공
 *
 * JS 사용 방법
 * --------------------------------------------------
 * 1) 레이아웃 또는 페이지 하단에 1회 출력
 *
 *   <?= Modal::base() ?>
 *   <?= Modal::alert() ?>
 *   <?= Modal::confirm() ?>
 *   <?= Modal::prompt() ?>
 *
 * 2) JavaScript에서 Promise 방식으로 사용
 *
 *   await Modal.alert('저장되었습니다.');
 *
 *   var ok = await Modal.confirm('삭제하시겠습니까?');
 *   if (!ok) return;
 *
 *   var name = await Modal.prompt('이름을 입력하세요');
 *
 * 특징
 * - JS 기본 alert / confirm / prompt 완전 대체
 * - Promise 기반 (비동기 흐름 제어)
 * - dialog DOM은 1개만 생성되어 재사용됨
 */

class Modal {
    /**
     * base
     *
     * 공통 dialog HTML + JS 엔진 출력
     * - 실제 dialog DOM은 1개
     * - 모든 alert / confirm / prompt는 이 엔진을 사용
     *
     * ⚠ 반드시 페이지 내 1회만 출력
     */
    public static function base(): string {
        ob_start(); ?>

        <!-- 공통 모달 -->
        <dialog id="app-modal" class="modal">
            <form method="dialog" class="modal-form">
                <h3 class="modal-title"></h3>
                <div class="modal-message"></div>
                <div class="modal-input"></div>
                <div class="modal-buttons"></div>
            </form>
        </dialog>

        <script>
            /**
             * Modal (JavaScript)
             *
             * dialog 제어용 전역 객체
             * - open()은 Promise를 반환
             * - alert / confirm / prompt는 open()을 래핑
             */

            var Modal = (function () {
                var dialog  = document.getElementById('app-modal');
                var titleEl = dialog.querySelector('.modal-title');
                var msgEl   = dialog.querySelector('.modal-message');
                var inputEl = dialog.querySelector('.modal-input');
                var btnEl   = dialog.querySelector('.modal-buttons');

                /**
                 * open
                 *
                 * 공통 모달 오픈 함수
                 * 사용자 선택 결과를 Promise로 반환
                 *
                 * options:
                 *  - title   : 제목
                 *  - message : 메시지 (HTML 허용)
                 *  - input   : 입력 옵션 (prompt용)
                 *  - buttons : 버튼 배열
                 */

                function open(options) {
                    return new Promise(function (resolve) {

                        // 초기화
                        titleEl.textContent = options.title || '';
                        msgEl.innerHTML = options.message || '';
                        inputEl.innerHTML = '';
                        btnEl.innerHTML = '';

                        var inputField = null;

                        // 입력 필드 (prompt)
                        if (options.input) {
                            inputField = document.createElement(options.input.tag || 'input');
                            inputField.type  = options.input.type || 'text';
                            inputField.value = options.input.value || '';
                            inputEl.appendChild(inputField);
                        }

                        // 버튼 생성
                        options.buttons.forEach(function (btn) {
                            var b = document.createElement('button');
                            b.type = 'button';
                            b.textContent = btn.label;

                            b.onclick = function () {
                                dialog.close();
                                if (btn.value === 'input') {
                                    resolve(inputField ? inputField.value : null);
                                } else {
                                    resolve(btn.value);
                                }
                            };

                            btnEl.appendChild(b);
                        });

                        dialog.showModal();
                    });
                }

                return {
                    open: open
                };
            })();
        </script>

        <?php return ob_get_clean();
    }

    /**
     * alert
     *
     * JS alert() 대체
     * - 확인 버튼 1개
     * - resolve(true)
     */
    public static function alert(): string
    {
        ob_start(); ?>
        <script>
            Modal.alert = function (message, title) {
                title = title || '알림';

                return Modal.open({
                    title: title,
                    message: message,
                    buttons: [
                        { label: '확인', value: true }
                    ]
                });
            };
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * confirm
     *
     * JS confirm() 대체
     * - 확인 / 취소
     * - resolve(true | false)
     */
    public static function confirm(): string
    {
        ob_start(); ?>
        <script>
            Modal.confirm = function (message, title) {
                title = title || '확인';

                return Modal.open({
                    title: title,
                    message: message,
                    buttons: [
                        { label: '취소', value: false },
                        { label: '확인', value: true }
                    ]
                });
            };
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * prompt
     *
     * JS prompt() 대체
     * - 입력값 반환
     * - 취소 시 null 반환
     */
    public static function prompt(): string
    {
        ob_start(); ?>
        <script>
            Modal.prompt = function (message, title, def) {
                title = title || '입력';
                def   = def || '';

                return Modal.open({
                    title: title,
                    message: message,
                    input: {
                        tag: 'input',
                        type: 'text',
                        value: def
                    },
                    buttons: [
                        { label: '취소', value: null },
                        { label: '확인', value: 'input' }
                    ]
                });
            };
        </script>
        <?php
        return ob_get_clean();
    }
}
