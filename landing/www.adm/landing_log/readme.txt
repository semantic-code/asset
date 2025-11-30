**g5_write_landing_log

*추가

ALTER TABLE g5_write_landing_log
	ADD COLUMN wr_page_code VARCHAR(255)   NULL DEFAULT NULL COMMENT '랜딩페이지 page_code',
    ADD COLUMN wr_memo      TEXT           NULL COMMENT '랜딩페이지 메모',    
    ADD COLUMN wr_field_1   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 1',
    ADD COLUMN wr_field_2   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 2',
    ADD COLUMN wr_field_3   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 3',
    ADD COLUMN wr_field_4   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 4',
    ADD COLUMN wr_field_5   VARCHAR(255)   NULL DEFAULT NULL COMMENT '추가 필드 5',
