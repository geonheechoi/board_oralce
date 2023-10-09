<?php
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";
$search = isset($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search'], ENT_QUOTES, 'UTF-8') : null;

// 페이지당 표시할 항목 수
$list = 10;

// 현재 페이지 번호 가져오기
$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

// ROWNUM 계산
$seek = ($page - 1) * $list;

// 검색어를 사용한 동적 쿼리 생성


$search_query = ''; // 초기화된 검색어 쿼리

if ($search !== null) {
    // 검색어가 제공되면 검색어 쿼리를 생성
    $search_param = '%' . $search . '%';
    $search_query = "title LIKE '$search_param'";
}else {
    // 검색어가 없을 때의 처리를 추가
    $search_query = '1=1'; // 혹은 다른 기본 조건을 설정
}

// 전체 레코드 수 조회 쿼리
$count_query = "SELECT COUNT(*) FROM tbl_board WHERE $search_query";
$stmt_count = oci_parse($conn, $count_query);
oci_execute($stmt_count);
$row_count = oci_fetch_array($stmt_count, OCI_BOTH);
$total_count = $row_count[0];
oci_free_statement($stmt_count);

// 페이지 수 계산
$total_page = ceil($total_count / $list);

// 메인 쿼리
$sql = "SELECT seq, title, writer, view_count, reg_at FROM tbl_board WHERE $search_query ORDER BY seq DESC OFFSET $seek ROWS FETCH NEXT $list ROWS ONLY";
$stmt2 = oci_parse($conn, $sql);
oci_execute($stmt2);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <script src="./js/jquery.js"></script> <!-- jQuery 경로 확인 필요 -->
    <script src="./js/common.js"></script>
</head>

<body>
    <div id="dynamic-content"></div>
    <div id="board_head">
        <h1>자유게시판</h1>
        <div id="write_btn">
            <a href="./board_write.php"><button>글쓰기</button></a>
        </div>
        <form action="./board_list.php" method="get">
            <div id="search_box"></div>
            <div class="search-wrapper">
                <input type="text" name="search" id="searchInput" placeholder="검색어를 입력하세요" value="<?= isset($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search'], FILTER_SANITIZE_STRING) : '' ?>"/>
                <button type="submit" id="searchButton" onclick="return validateSearch()">검색</button>
            </div>
        </form>
    </div>
    <h4>자유롭게 글을 쓸 수 있는 게시판입니다.</h4>
    <table class="list-table">
        <thead>
            <tr>
                <th width="100">조회수</th>
                <th width="100">등록일자</th>
                <th width="70">게시물번호</th>
                <th width="200">제목</th>
                <th width="120">작성자</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = oci_fetch_assoc($stmt2)) {
                $view_count = isset($row['VIEW_COUNT']) ? $row['VIEW_COUNT'] : 'N/A';
                $reg_at = isset($row['REG_AT']) ? $row['REG_AT'] : 'N/A';
                $seq = isset($row['SEQ']) ? $row['SEQ'] : 'N/A';
                $title = isset($row['TITLE']) ? $row['TITLE'] : 'N/A';
                $writer = isset($row['WRITER']) ? $row['WRITER'] : 'N/A';
                ?>
                <!-- HTML 코드 시작 -->
                <tr>
                    <td><?=$view_count?></td>
                    <td><?=$reg_at?></td>
                    <td><?=$seq?></td>
                    <td><a href='./board_view.php?seq=<?=$seq?>&search=<?=$search?>&page=<?=$page?>'><?=$title?></a></td>
                    <td><?=$writer?></td>
                </tr>
                <!-- HTML 코드 끝 -->
                <?php
            }
            oci_free_statement($stmt2);
            ?>
        </tbody>
    </table>
    <div id="page_num">
        <ul>
            <?php
            if ($page <= 1) {
                echo "<li class='fo_re'>처음</li>";
            } else {
                echo "<li><a href='./board_list.php?search=$search&page=1'>처음</a></li>";
            }
            if ($page > 1) {
                $pre = $page - 1;
                echo "<li><a href='./board_list.php?search=$search&page=$pre'>이전</a></li>";
            }
            for ($i = 1; $i <= $total_page; $i++) {
                if ($page == $i) {
                    echo "<li class='fo_re'>[{$i}]</li>";
                } else {
                    echo "<li><a href='./board_list.php?search=$search&page=$i'>[{$i}]</a></li>";
                }
            }
            if ($page < $total_page) {
                $next = $page + 1;
                echo "<li><a href='./board_list.php?search=$search&page=$next'>다음</a></li>";
            }
            if ($page >= $total_page) {
                echo "<li class='fo_re'>마지막</li>";
            } else {
                echo "<li><a href='./board_list.php?search=$search&page=$total_page'>마지막</a></li>";
            }
            ?>
        </ul>
    </div>
    
</body>

</html>
<?php include "./include/disconn_cloud.php"?>