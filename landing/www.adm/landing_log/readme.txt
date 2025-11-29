**g5_write_landing_log

*추가

ALTER TABLE g5_write_landing_log
	 ADD COLUMN wr_page_code VARCHAR(255)   NULL DEFAULT NULL COMMENT '랜딩페이지 page_code',
    ADD COLUMN wr_memo      TEXT           NULL COMMENT '랜딩페이지 메모',
    ADD COLUMN wr_cate      VARCHAR(255)   NULL DEFAULT NULL COMMENT '랜딩페이지 카테고리',
    ADD COLUMN wr_field_1   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 1',
    ADD COLUMN wr_field_2   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 2',
    ADD COLUMN wr_field_3   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 3',
    ADD COLUMN wr_field_4   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 4',
    ADD COLUMN wr_field_5   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 5',
    ADD COLUMN wr_field_6   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 6',
    ADD COLUMN wr_field_7   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 7',
    ADD COLUMN wr_field_8   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 8',
    ADD COLUMN wr_field_9   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 9',
    ADD COLUMN wr_field_10  VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 10';
