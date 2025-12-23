<?php
$sub_menu = '400100';
$page_title = 'Clinics';
$g5['title'] = $page_title;
include_once G5_PATH . '/head.php';

// city 가져오기
$sql = "SELECT wr_city city FROM {$target_table} WHERE wr_is_comment = 0 AND wr_use = 1 GROUP BY wr_city ORDER BY wr_code";
$get_data = sql_query($sql);

/* -----------------------------
    Board 조건 구성
----------------------------- */
$where = array('wr_is_comment = 0', 'wr_use = 1');
if ($city) $where[] = "wr_city = '{$city}'";
if ($district) $where[] = "wr_district = '{$district}'";
if ($keyword) $where[] = "wr_subject LIKE '%{$keyword}%'";

/* -----------------------------
    Board 데이터
----------------------------- */
$result = Board::get($bo_table, $where, false);
$list = $result['list'];

?>

<main class="clinic">
    <div class="clinic-page subPage">
        <div class="clinic-header subPage_header">
            <div class="caution">해당 페이지는 특정 병원에 권유나 추천이 아닌 정보 전달 목적으로 제작되었습니다</div>
        </div>
        <form id="search_form" method="get">
            <div>
                <select name="city" id="city">
                    <option value="">전체</option>
                    <?php foreach ($get_data as $row): ?>
                        <option value="<?= $row['city'] ?>" <?= get_selected($row['city'], $city) ?>><?= $row['city'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <select name="district" id="district">
                    <option value="">전체</option>
                </select>
            </div>

            <div class="search-input-wrapper">
                <input type="text" name="keyword" placeholder="검색어를 입력하세요" value="<?= $keyword ?>">
                <button type="submit" class="search-icon"><img src="/img/sch_btn.png" alt="sch_btn"></button>
            </div>
        </form>
        <div class="contents">
            <div class="hospital_list" id="append">
                <?php if (empty($list)): ?>
                    <dl class="empty">업데이트 중</dl>
                <?php else: ?>
                    <ul>
                        <?php foreach ($list as $idx => $row): ?>
                            <li data-index="<?= $idx ?>">
                                <dl>
                                    <dt><?= $row['wr_subject'] ?></dt>
                                    <dd><?= $row['wr_addr1'] ?></dd>
                                    <dd><?= $row['wr_tel'] ?></dd>
                                </dl>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="map" id="map" style="width:100%;"></div>
        </div>
    </div>
</main>

<script>
    function getDistrict(city, district) {
        $.post('ajax.getDistrict.php', {city, district}, function(data) {
            $("#district").html(data.district);
        }, 'json');
    }

    document.addEventListener("DOMContentLoaded", function() {

        // 초기 로딩시 값 가져오기
        const city = <?= json_encode($_GET['city']) ?>;
        const district = <?= json_encode($_GET['district']) ?>;
        getDistrict(city, district);

        // 지역 선택
        $(document).on('change', '#city', function() {
            const city = $(this).val();
            getDistrict(city);
        });
    });
</script>

<?php
include_once G5_PATH . '/tail.php';
