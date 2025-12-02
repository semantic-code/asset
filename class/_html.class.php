<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Html 클래스 — 파일 업로드 UI / 리스트 / JS
 */
class Html {

    /**
     * 파일 리스트 + 첨부 HTML 생성 (패딩형, 미리보기 구조)
     *
     * @param array     $file         그누보드 파일정보 배열 ($file)
     * @param int|null  $upload_count 업로드 허용 개수 (기본값 2)
     * @param string $accept          input file accept
     * @param string    $title        제목 텍스트 (기본: "파일첨부")
     * @return string HTML 문자열
     */
    public static function file_upload_list_html(
        array $file = array(),
        ?int $upload_count = null,
        string $accept = null,
        string $title = '파일첨부'
    ): string {
        ob_start(); ?>
        <style>
            .file-upload-box{border:1px solid #ccc;padding:10px;border-radius:6px;}
            .file-upload-box .file-list{list-style:none;margin:0;padding:0;}
            .file-upload-box .file-list li{display:flex;justify-content:space-between;align-items:center;padding:8px 10px;margin-bottom:6px;border:1px solid #c9c9c9;border-radius:4px;height:48px;box-sizing:border-box;}
            .file-upload-box .file-list li.file-uploaded{background:#D3E3FD;}
            .file-upload-box .file-list li.file-uploaded .file-name a{color:#000;font-weight:bold;text-decoration:none;}
            .file-upload-box .file-list li.file-input-row{background:#fff;}
            .file-upload-box .file-list li.file-input-row input[type="file"]{height:100%;width:100%; box-sizing:border-box;}
            .file-upload-box .file-delete{margin-left:10px;white-space:nowrap;}
        </style>

        <div class="file-upload-box">
            <ul class="file-list">
                <?php for ($i = 0; $i < $upload_count; $i++): ?>
                    <?php if (!empty($file[$i]['file'])): ?>
                        <li class="file-uploaded">
                            <span class="file-name">
                                <?php $href = "{$file[$i]['path']}/{$file[$i]['file']}"; ?>
                                <?php $file_source = htmlspecialchars($file[$i]['source']); ?>
                                <a href="<?= $href ?>" download="<?= $file_source ?>"><?= $file_source ?></a>
                            </span>
                            <label class="file-delete">
                                <input type="checkbox" name="bf_file_del[]" value="<?= $i ?>"> 삭제
                            </label>
                        </li>
                    <?php else: ?>
                        <li class="file-input-row"><input type="file" name="bf_file[]" <?= $accept ? "accept='{$accept}'" : '' ?>></li>
                    <?php endif; ?>
                <?php endfor; ?>
            </ul>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * 파일 업로드 입력창 HTML 생성
     *
     * @param string $bo_table       게시판 테이블명
     * @param array  $files          기존 업로드된 파일 배열
     * @param string $name           input name 속성명
     * @param string $id             input id 속성명`
     * @param bool   $image_only     이미지 업로드 전용 여부
     * @param bool   $multiple       다중 업로드 허용 여부
     * @param bool   $include_style  CSS 포함 여부 (기본: true)
     *
     * @return string HTML 마크업
     */
    public static function file_upload_html(
        string $bo_table = '',
        array $files = array(),
        string $name = 'bf_file[]',
        string $id = 'file_input',
        bool $image_only = false,
        bool $multiple = true,
        bool $include_style = true
    ): string {
        ob_start(); ?>

        <?php if($include_style): ?>
        <style>
            .file_upload_wrapper {display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start;}
            .file_upload_box {width: 100px; height: 100px; border: 2px dashed #ccc; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                position: relative; overflow: hidden; background: #f9f9f9;}
            .file_upload_box img {width: 100%; height: 100%; object-fit: contain;}
            .file_upload_box.add_box {cursor: pointer;}
            .file_upload_box.add_box label {position: relative;}
            .file_upload_box.add_box label span {position: absolute; top: 50%; left: 50%;
                transform: translate(-50%, -60%); font-size: 48px;
                user-select: none; cursor: pointer;}
            .file_upload_box input[type=file] {display: none;}
            .remove_btn {position: absolute; top: 3px; right: 3px; padding-bottom: 5px;
                background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%;
                width: 20px; height: 20px; cursor: pointer; font-size: 14px; line-height: 18px;
                text-align: center;}
            #existing_files, #preview_container {display: flex; gap: 10px; flex-wrap: wrap;}
        </style>
        <?php endif; ?>

        <div class="file_upload_wrapper">
            <div class="file_upload_box add_box">
                <label for="<?= $id ?>"><span>+</span></label>
                <input type="file" name="<?= $name ?>" id="<?= $id ?>"
                    <?= $multiple ? 'multiple' : '' ?> <?= $image_only ? 'accept=image/*' : '' ?>>
            </div>

            <!-- 기존 파일 영역 -->
            <?php if (!empty($files)) : ?>
            <div id="existing_files">
                <?php foreach ($files as $file): ?>
                    <div class="file_upload_box">
                        <img src="<?= G5_DATA_URL ?>/file/<?= $bo_table ?>/<?= $file['bf_file'] ?>" alt="<?= $file['bf_source'] ?>">
                        <button type="button" class="remove_btn">X</button>
                        <input type="hidden" name="keep_file[]" value="<?= $file['bf_no'] ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- 새 파일 영역 -->
            <div id="preview_container"></div>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * 파일 업로드 미리보기 및 삭제 기능용 JavaScript 반환
     *
     * @param string $id            파일 input 요소의 id (기본: file_input)
     * @param string $preview_id    미리보기 컨테이너 id (기본: preview_container)
     *
     * @return string               JavaScript 코드 (script 태그 제외)
     *
     */
    public static function file_upload_js(
        string $id = 'file_input',
        string $preview_id = 'preview_container'
    ): string {
        ob_start(); ?>
        <script>
            $(document).on('change', '#<?= $id ?>', function(){
                const $original_input = $(this);
                const files = this.files;
                if (!files.length) return;
    
                // 다음 선택용 input 생성
                const $next_file_input  = $original_input.clone().val('');
    
                $next_file_input.removeAttr('name');
    
                // 고유 ID 생성 (파일마다 고유값)
                const fileId = Date.now();
    
                // 원본 input에 data-id 부여
                $original_input.attr('data-file-id', fileId);
    
                $.each(files, function(i, file){
                    const ext = file.name.split('.').pop().toLowerCase();
                    const $box = $('<div>', { class: 'file_upload_box', 'data-file-id': fileId });
                    const $remove_btn = $('<button>', { class: 'remove_btn', text: 'X' });
    
                    // 이미지 미리보기
                    if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                        const reader = new FileReader();
                        reader.onload = function(e){
                            $box.append($('<img>', { src: e.target.result }));
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $box.append(`<div class="file-info"><p style="padding:3px;"><span>${file.name}</span></p></div>`);
                    }
    
                    // 여기서 $original_input 은 박스 안에 넣지 않음
                    // $box.append($original_input); ← 삭제!
    
                    $box.append($remove_btn);
                    $('#<?= $preview_id ?>').append($box);
                });
    
                // 새 input 다시 등록
                $next_file_input.attr('id', '<?= $id ?>');
                $('.add_box').append($next_file_input);
            });
    
            // 삭제 버튼 클릭 시 input + 박스 제거
            $(document).on('click', '.remove_btn', function(){
                const fileId = $(this).closest('.file_upload_box').data('file-id');
                $('input[type="file"][data-file-id="'+fileId+'"]').remove()
    
                $(this).closest('.file_upload_box').remove();
            });
        </script>
        <?php
        $html = ob_get_clean();
        return str_replace(['<script>', '</script>'], '', $html);
    }

    /**
     * 게시판 검색영역 HTML 생성
     *
     * @param array  $arr_search '검색 칼럼' => 검색필드명
     * @param array $keep_key 유지할 get key 배열, input hidden
     * @param string $sca
     * @param string $sfl
     * @param string $stx
     *
     * @return string
     */
    public static function search (
        array $arr_search  = array(),
        array $keep_key = array(),
        string $sca         = '',
        string $sfl         = '',
        string $stx         = '',
    ): string
    {
        $action = strtok($_SERVER['REQUEST_URI'], '?');

        $hidden_input = '';
        $init_query = array(); // 초기화 링크용

        foreach ($keep_key as $key) {
            if (isset($_GET[$key])) {
                $value = htmlspecialchars($_GET[$key], ENT_QUOTES);
                $hidden_input.= "<input type='hidden' name='{$key}' value='{$value}'>\n";
                $init_query[$key] = $_GET[$key];
            }
        }

        // 초기화 URL
        $init_url = $action;
        if (!empty($init_query)) {
            $init_url.= '?' . http_build_query($init_query);
        }

        if (!$sfl) $sfl = $_GET['sfl'] ?? '';
        if (!$stx) $stx = $_GET['stx'] ?? '';

        ob_start(); ?>
        <style>
            .admin-search{display:flex;flex-direction:column;gap:8px;flex:1; margin-bottom: 1rem;}
            .admin-search-row{display:flex;align-items:center;gap:8px;}
            .admin-search-row-full select{min-width:260px;}
            .admin-search .sel,.admin-search .inp{height:30px;border:1px solid #d1d5db;border-radius:6px;padding:0 10px;background:#fff;}
            .admin-search .inp{min-width:220px;}
            .admin-search .inp:focus,.admin-search .sel:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15);outline:0;}
            .btn{display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 12px;border-radius:6px;text-decoration:none;cursor:pointer;user-select:none;}
            .btn-primary{background:#3f51b5;color:#fff;border:1px solid #1d4ed8;}
            .btn-secondary{background:#9eacc6;color:#fff!important;border:1px solid #d1d5db;}
        </style>
        <form method="get" action="<?= $action ?>" class="admin-search">
            <?= $hidden_input ?>

            <!-- 검색 줄 -->
            <div class="admin-search-row">
                <select name="sfl" id="search-field" class="sel">
                    <?php foreach ($arr_search as $field => $option): ?>
                        <option value="<?= $field ?>" <?= get_selected($sfl, $field) ?>><?= $option ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="stx" id="search-text" class="inp" placeholder="검색어 입력" value="<?= $stx ?>">
                <button type="submit" class="btn btn-primary">검색</button>
                <a href="<?= $init_url ?>" class="btn btn-secondary">초기화</a>
            </div>
        </form>
        <?php return ob_get_clean();
    }

    /**
     * 관리자 페이지 카테고리 탭을 생성하는 함수
     *
     * - 카테고리 배열($arr_cate)을 기반으로 탭 버튼을 생성한다.
     * - base_query_string 을 통해 page_code 같은 고정 파라미터를 유지할 수 있다.
     * - base_query_string 이 없을 경우 ?sca=값 형태로 출력된다.
     * - base_query_string 이 있으면 ?key=value&key2=value&sca=값 형태로 출력된다.
     *
     * 예시:
     *   Html::category(['공지', '이벤트'], "page_code=abc&temp=list", "공지");
     *
     * 출력 URL 예:
     *   ?page_code=abc&temp=list&sca=공지
     *
     * @param array  $arr_cate          카테고리 목록 배열
     * @param string $base_query_string URL 기본 GET 파라미터 문자열 (key=value&key2=value 형식)
     * @param string $sca               현재 선택된 카테고리 (없으면 $_GET['sca'] 자동 적용)
     *
     * @return string                   카테고리 탭 HTML 문자열
     */
    public static function category (
        array  $arr_cate = array(),
        string $base_query_string = '',
        string $sca = ''
    ): string {

        if (empty($arr_cate)) return '';

        // 현재 선택된 카테고리
        if (!$sca) $sca = $_GET['sca'] ?? '';

        // base_query_string 앞의 ? 또는 & 제거
        $base_query_string = ltrim($base_query_string, '?&');

        // prefix 생성
        // base_query_string 이 있으면 key=value& 형태로 사용
        // base_query_string 이 없으면 prefix=""
        $prefix = $base_query_string ? "{$base_query_string}&" : '';

        ob_start(); ?>
        <style>
            .admin-toolbar{display:flex;gap:12px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fafafa;margin:10px 0 15px;}
            .admin-tabs{display:flex;gap:6px;list-style:none;padding:0;margin:0;}
            .admin-tabs .tab{padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;text-decoration:none;}
            .admin-tabs .tab.is-active{background:#3f51b5;color:#fff;border-color:#1d4ed8;}
        </style>

        <div class="admin-toolbar">
            <ul class="admin-tabs">

                <!-- 전체 -->
                <li>
                    <a href="?<?= $prefix ?>sca=" class="tab <?= $sca === '' ? 'is-active' : '' ?>">전체</a>
                </li>

                <!-- 카테고리 -->
                <?php foreach ($arr_cate as $cate): ?>
                    <li>
                        <a href="?<?= $prefix ?>sca=<?= urlencode($cate) ?>"
                           class="tab <?= $sca === $cate ? 'is-active' : '' ?>">
                            <?= $cate ?>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>
        </div>

        <?php return ob_get_clean();
    }
}

