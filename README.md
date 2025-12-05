## 최종수정 2025.11.29

- notice : 폴더로 구분한 그누보드 어드민 게시판
- contact : 폴더로 구분한 그누보드 상담문의 게시판 (상담문의 게시판 단독 사용)
- landing, landing_log : 폴더로 구분한 그누보드 게시판 (랜딩페이지, 랜딩DB 별도 관리)
- history : 어드민용 연혁게시판

  ### 수정해야할 부분
  - require_once '../common.php'; => require_once $_SERVER['DOCUMENT_ROOT'] . '/common.php';
