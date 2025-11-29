**g5_write_landing

*추가
wr_page_code : 랜딩페이지 아이디 page_code
wr_access_id : 접근가능한 아이디
wr_use_cate :  카테고리 사용 여부
wr_cate_list :  카테고리 리스트
wr_use_search : 검색사용
wr_fields : 입력 필트 텍스트 설정
wr_sort_field : 랜딩 log 정렬 순서
wr_use : 랜딩페이지 사용 여부

ALTER TABLE g5_write_free
    ADD COLUMN wr_page_code   VARCHAR(255)   NULL DEFAULT NULL COMMENT '랜딩페이지 아이디 page_code',
    ADD COLUMN wr_access_id   VARCHAR(255)   NULL DEFAULT NULL COMMENT '접근가능한 아이디',
    ADD COLUMN wr_use_cate    TINYINT(1)     NULL DEFAULT 0 COMMENT '카테고리 사용 여부(0:사용안함,1:사용)',
    ADD COLUMN wr_cate_list   TEXT           NULL COMMENT '카테고리 리스트',
    ADD COLUMN wr_use_search  TINYINT(1)     NULL DEFAULT 0 COMMENT '검색 사용 여부(0:사용안함,1:사용)',
    ADD COLUMN wr_fields      TEXT           NULL COMMENT '입력 필드 텍스트 설정',
    ADD COLUMN wr_sort_field  VARCHAR(255)   NULL DEFAULT NULL COMMENT '랜딩 log 정렬 순서',
    ADD COLUMN wr_use         TINYINT(1)     NULL DEFAULT 1 COMMENT '랜딩페이지 사용 여부(0:미사용,1:사용)';
