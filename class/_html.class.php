<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Html 클래스 — 파일 업로드 UI / 리스트 / JS
 */
class Html {

    /**
     * 파일 리스트 + 첨부 HTML 생성 (패딩형, 미리보기 구조)
     *
     * @param array    $file         그누보드 파일정보 배열 ($file)
     * @param int|null $upload_count 업로드 허용 개수 (기본값 2)
     * @param string   $accept       input file accept
     * @param string   $title        제목 텍스트 (기본: "파일첨부")
     * @return string  HTML 문자열
     */
    public static function file_upload_list_html(
        array $files = array(),
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
                    <?php if (!empty($files[$i]['file'])): ?>
                        <li class="file-uploaded">
                            <span class="file-name">
                                <?php $href = "{$files[$i]['path']}/{$files[$i]['file']}"; ?>
                                <?php $file_source = htmlspecialchars($files[$i]['source']); ?>
                                <a href="<?= $href ?>" download="<?= $file_source ?>"><?= $file_source ?></a>
                            </span>
                            <label class="file-delete">
                                <input type="checkbox" name="bf_file_del[<?= $i ?>]" value="<?= $i ?>"> 삭제
                            </label>
                        </li>
                    <?php else: ?>
                        <li class="file-input-row"><input type="file" name="bf_file[<?= $i ?>]" <?= $accept ? "accept='{$accept}'" : '' ?>></li>
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
     * @param array  $arr_search '검색 DB 칼럼명' => 검색필드명 ('wr_subject' => '제목')
     * @param array $keep_key 유지할 get key 배열, input hidden     
     * @param bool $include_style 스타일 사용여부
     *
     * @return string
     */
    public static function search (
        array $arr_search    = array(),
        array|bool $keep_key = array('sca'),       
        bool $include_style  = true,
    ): string
    {
        // $keep_key가 false이면 빈배열로 변환
        if ($keep_key === false) $keep_key = array();
        
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

        // 초기화 시 sca 제거
        if (isset($init_query['sca'])) {
            unset($init_query['sca']);
        }

        // 초기화 URL
        $init_url = $action;
        if (!empty($init_query)) {
            $init_url.= '?' . http_build_query($init_query);
        }

        if (!$sca) $sca = $_GET['sca'] ?? '';
        if (!$sfl) $sfl = $_GET['sfl'] ?? '';
        if (!$stx) $stx = $_GET['stx'] ?? '';

        ob_start(); ?>
        <?php if ($include_style): ?>
        <style>
            .ooc-search{display:flex;flex-direction:column;gap:8px;flex:1; margin-bottom: 1rem;}
            .ooc-search-row{display:flex;width:60%;align-items:center;gap:8px;}
            .ooc-search .sel,.ooc-search .inp{flex:4;height:30px;border:1px solid #d1d5db;border-radius:6px;padding:0 10px;background:#fff;}
            .ooc-search .inp{min-width:220px;}
            .ooc-search .inp:focus,.ooc-search .sel:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15);outline:0;}
            .btn{display:inline-flex;align-items:center;justify-content:center;height:30px!important;padding:0 12px;border-radius:6px;line-height:30px!important;text-decoration:none;cursor:pointer;user-select:none;}
            .btn-primary{flex:1;background:#3f51b5;color:#fff;border:1px solid #1d4ed8;}
            .btn-secondary{flex:1;background:#9eacc6;color:#fff!important;border:1px solid #d1d5db;text-align:center;}
        </style>
        <?php endif; ?>
        <form method="get" action="<?= $action ?>" class="ooc-search">
            <?= $hidden_input ?>

            <!-- 검색 줄 -->
            <div class="ooc-search-row">
                <select name="sfl" id="search-field" class="sel">
                    <?php foreach ($arr_search as $field => $option): ?>
                        <option value="<?= $field ?>" <?= get_selected($sfl, $field) ?>><?= $option ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="stx" id="ooc-search-text" class="inp" placeholder="검색어 입력" value="<?= $stx ?>">
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
     * - base_query_string 이 있으면 'key=value&key2=value' 형태로 입력한다.
     *
     * 예시:
     *   Html::category(['공지', '이벤트'], "page_code=abc&temp=list", "공지");
     *
     * 출력 URL 예:
     *   ?page_code=abc&temp=list&sca=공지
     *
     * @param array  $arr_cate               카테고리 목록 배열
     * @param string|bool $base_query_string URL 기본 GET 파라미터 문자열 (key=value&key2=value 형식)     
     * @param bool $include_style            style 사용 여부
     *
     * @return string                        카테고리 탭 HTML 문자열
     */
    public static function category (
        array  $arr_cate               = array(),
        string|bool $base_query_string = '',        
        bool $include_style            = true,
    ): string {

        if (empty($arr_cate)) return '';       

        // 현재 선택된 카테고리
        $sca = $_GET['sca'] ?? '';

        // base_query_string 앞의 ? 또는 & 제거
        $base_query_string = ltrim($base_query_string, '?&');

        // prefix 생성
        // base_query_string 이 있으면 key=value& 형태로 사용
        // base_query_string 이 없으면 prefix=""
        $prefix = $base_query_string ? "{$base_query_string}&" : '';

        ob_start(); ?>
        <?php if ($include_style): ?>
        <style>
            .admin-toolbar{display:flex;gap:12px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fafafa;margin:10px 0 15px;}
            .admin-tabs{display:flex;gap:6px;list-style:none;padding:0;margin:0;}
            .admin-tabs .tab{padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;text-decoration:none;}
            .admin-tabs .tab.is-active{background:#3f51b5;color:#fff;border-color:#1d4ed8;}
        </style>
        <?php endif; ?>
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

    /**
     * 첨부파일 미리보기 HTML 생성
     *
     * @param array $files     게시판 첨부파일 배열
     * @param bool  $show_all  true = 이미지 + 일반파일 / false = 이미지만
     *
     * @return string
     */

    public static function preview(
        array $files,
        bool $show_all = true
    ): string
    {
        ob_start(); ?>
        <style>
            div.view-images{display:flex;gap:10px;flex-wrap:wrap;}
            div.view-img-box{border:1px solid #ddd;padding:5px;border-radius:4px;background:#fafafa;}
            div.view-images img{display:block;max-width:150px;height:auto;border-radius:3px;}
        </style>

        <!-- 파일 미리보기 래퍼 -->
        <div class="view-images">
            <?php foreach ($files as $file): ?>
            <?php
            // continue
            if (!is_array($file)) continue;
            if (empty($file['file'])) continue;

            // 공통변수
            $file_path = $file['path']. '/' . $file['file'];
            $file_name = $file['source'] ?? $file['file'];

            // 이미지 여부
            $is_image = !empty($file['image_type']); ?>
            <?php if ($is_image): ?>
            <!-- 이미지 미리보기 -->
            <div class="view-img-box">
                <img src="<?= $file_path ?>" alt="<?= htmlspecialchars($file_name, ENT_QUOTES) ?>">
            </div>
            <?php else: ?>
            <?php // 이미지 전용 모드이면 일반파일 출력 안함 ?>
            <?php if (!$show_all) continue; ?>
            <!-- 일반 파일 -->
            <div class="view-file-box">
                <?= htmlspecialchars($file_name, ENT_QUOTES) ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * Address input (다음지도) html 출력
     * 다음주소 api 연결 필요함
     *
     * @param array $addr ('칼럼이름' => '저장된 값') 
     * array (우편번호, 기본주소, 상세주소, 참고항목, 도로명/지번 코드)
     * @return string
    */

    public static function address(
        array $addr = array()
    ): string
    {
        $keys = array_keys($addr);

        $zip         = $keys[0] ?? null;
        $addr1       = $keys[1] ?? null;
        $addr2       = $keys[2] ?? null;
        $addr3       = $keys[3] ?? null;
        $addr_jibeon = $keys[4] ?? null;

        ob_start(); ?>
        <style>
            .btn_zip{display:inline-block;background:#9eacc6;color:#fff;height:35px;border:0;border-radius:5px;padding:0 10px;margin-left: .5rem;}
        </style>
        <!--<script src="t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>-->

        <div class="addr-wrap td_addr_line" data-zip="<?= $zip ?>" data-addr1="<?= $addr1 ?>" data-addr2="<?= $addr2 ?>" data-addr3="<?= $addr3 ?>" data-jibeon="<?= $addr_jibeon ?>">
            <?php // 우편번호 ?>
            <label for="<?= $zip ?>" class="sound_only">우편번호</label>
            <input type="text" value="<?= $addr[$zip] ?? '' ?>" id="<?= $zip ?>" class="frm_input readonly" size="5" maxlength="6">
            <button type="button" class="btn_zip">주소 검색</button><br>

            <?php // 기본주소 ?>
            <input type="text" name="<?= $addr1 ?>" value="<?= $addr[$addr1] ?>" id="<?= $addr1 ?>" class="frm_input readonly" size="60">
            <label for="<?= $addr1 ?>">기본 주소</label><br>

            <?php // 상세주소 ?>
            <input type="text" name="<?= $addr2 ?>" value="<?= $addr[$addr2] ?>" id="<?= $addr2 ?>" class="frm_input readonly" size="60">
            <label for="<?= $addr2 ?>">상세 주소</label><br>

            <?php // 참고항목 ?>
            <input type="text" name="<?= $addr3 ?>" value="<?= $addr[$addr3] ?>" id="<?= $addr3 ?>" class="frm_input readonly" size="60">
            <label for="<?= $addr3 ?>">참고항목</label><br>

            <input type="hidden" name="<?= $addr_jibeon ?>" id="<?= $addr_jibeon ?>" value="<?= $addr[$addr_jibeon] ?>">
        </div>
        <?php return ob_get_clean();
    }

    /**
     * Address input (다음지도) 자바스크립트 출력
     * 다음주소 api 연결 필요함
     *
     * @return string
     */

    public static function address_js (): string
    {
        ob_start(); ?>
        <script>
            document.querySelector('.btn_zip').addEventListener('click', function(e){
                const btn = e.target.closest('.btn_zip');
                if (!btn) return;

                const wrap = btn.closest('.addr-wrap');
                if (!wrap) return;

                const zip   = wrap.dataset.zip;
                const addr1 = wrap.dataset.addr1;
                const addr2 = wrap.dataset.addr2;
                const addr3 = wrap.dataset.addr3;
                const jib   = wrap.dataset.jibeon;

                let extra = ''
                new daum.Postcode({
                    oncomplete: function(data) {
                        const address = (data.userSelectedType === 'R') ? data.roadAddress : data.jibunAddress;

                        // bname이 있으면 참고항목 추가
                        if (data.bname) {
                            extra = data.bname;
                        }
                        // buildingName이 있으면 참고항목 추가
                        if (data.buildingName) {
                            extra = extra ?  extra + ', ' + data.buildingName : data.buildingName;
                        }

                        if (extra) {
                            extra = ' (' + extra + ')';
                        }

                        // 전달된 값 입력
                        if (zip)   document.getElementById(zip).value   = data.zonecode;
                        if (addr1) document.getElementById(addr1).value = address;
                        if (addr3) document.getElementById(addr3).value = extra;
                        if (jib)   document.getElementById(jib).value   = data.jibunAddress || '';

                        if (addr2) document.getElementById(addr2).focus()
                    }
                }).open();
            });
        </script>
        <?php $html = ob_get_clean();
        return str_replace(array('<script>', '</script>'), '', $html);
    }
}
