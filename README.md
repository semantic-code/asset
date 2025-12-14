## 최종수정 2025.11.29

- notice : 폴더로 구분한 그누보드 어드민 게시판
- contact : 폴더로 구분한 그누보드 상담문의 게시판 (상담문의 게시판 단독 사용)
- landing, landing_log : 폴더로 구분한 그누보드 게시판 (랜딩페이지, 랜딩DB 별도 관리)
- history : 어드민용 연혁게시판

  ### write_update.php 사용안할 시 수정해야할 부분
  - _common.php
  - require_once '../common.php'; => require_once __DIR__ . '/common.php';
 
  ### write_update.php 사용시

  1. 입력 모드
   - $token = get_token
   - session : set_session("ss_write_{$bo_table}_token", $token);
   - input hidden : name = token, value = $token
   - input hidden : name = board, value = 'notice'
     
  2. 수정 모드
   - session : set_session("ss_bo_table", 'notice');
   - session : set_session('ss_wr_id', 10);
   - input hidden : name = wr_id, value = 10
   - input hidden : name = board , value = 'notice'
   - input hidden : name = w, value = u
  
  /skin/board/notice/write_update.skin.php
     - if($custom_url) goto_url($custom_url);
  
  /skin/board/notice/delete.tail.skin.php
     - if($custom_delete_url) goto_url($custom_delete_url);
  
  /bbs/write_update.head.skin.php 생성
     - if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
     - 커스텀시 '자동등록방지 숫자가 틀렸습니다.' 안 나오도록 예외처리
     - if(in_array($bo_table, array('contact'))) $is_guest = false;

