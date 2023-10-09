<?
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link rel="stylesheet" type="text/css" href="./css/common.css">
    <script src="./js/jquery.js"></script>
    <script src="./js/common.js"></script>
</head>
<body>
<?
    $seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : null;


    if (is_numeric($seq)) {
        $seq = intval($seq);
    } else {
        echo "Invalid request.";
        exit;
    }

    // Increase view count
    $stmt = oci_parse($conn, "UPDATE tbl_board SET view_count = view_count + 1 WHERE seq = :seq");
    oci_bind_by_name($stmt, ':seq', $seq);
    $updateResult = oci_execute($stmt);
    
    if (!$updateResult) {
        $e = oci_error($stmt);
        error_log("Oracle Update Error: " . $e['message']);
        echo "<script>alert('An error occurred. Please try again.');</script>";
        // 오류 발생 시 처리 필요
    }
    
    // Fetch board info
    $stmt = oci_parse($conn, "SELECT * FROM tbl_board WHERE seq = :seq");
    oci_bind_by_name($stmt, ':seq', $seq);
    $result = oci_execute($stmt);
    
    if (!$result) {
        echo "<script>alert('An error occurred. Please try again.');</script>";
        // 오류 발생 시 처리 필요
    }
    
    $board = oci_fetch_assoc($stmt);
    
    // Get uploaded file info
    $file_name = $board['ADDR_FILE']; // Oracle 컬럼명은 대문자로 작성되므로 주의
    
    if (!empty($file_name)) {
        // 파일이 업로드된 경우 다운로드 링크 제공
        echo '<div id="bo_file">';
        echo '</div>';
    }
?>
<div id="board_read">
    <h2><?=$board['TITLE']?></h2>
    <div id="user_info">
        <?=$board['WRITER']." ".$board['REG_AT']." 조회:".$board['VIEW_COUNT']?>
        <div id="bo_line"></div>
    </div>
    <div id="bo_content">
        <?=nl2br($board['CONTENT'])?>
    </div>
    <div id="bo_file">
    <p>첨부 파일:
        <?php
        if (!empty($file_name)) {
            // 파일이 업로드된 경우 다운로드 버튼과 파일 이름 표시
            echo '<a href="./board_download.php?seq=' . $seq . '&download=true" class="download-button">다운로드</a>';
            echo '<span class="file-name">' . $file_name . '</span>';
        } else {
            // 파일이 없는 경우 메시지 표시
            echo '파일이 없습니다.';
        }
        ?>
    </p>
    </div>
    <div id="bo_ser">
        <ul>
            <li><a href="./board_list.php">[목록으로]</a></li>
            <li><a href="./board_edit.php?seq=<?=$seq?>">[수정]</a></li>

            <li><a href="./board_delete.php?seq=<?= $seq ?>">[삭제]</a></li>

        </ul>
    </div>
</div>
</body>
</html>
<? include "./include/disconn_cloud.php"?>