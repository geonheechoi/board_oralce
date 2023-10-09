<?
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";
$seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : '';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판 수정</title>
    <link rel="stylesheet" href="./css/common.css" />
    <script src="./js/jquery.js"></script>
    <script src="./js/common.js"></script>
</head>
<body>
<?php



$query = "SELECT TITLE, CONTENT,WRITER FROM tbl_board WHERE seq=$seq";

$stid = oci_parse($conn, $query);

oci_execute($stid);
$row = oci_fetch_assoc($stid);
if (!$row) {
    echo "레코드를 찾을 수 없습니다.";
    echo "<script>alert('레코드를 찾을 수 없습니다.'); location.href = './board_list.php';</script>";
    exit;
}

?>
<div id="board_write">
    <h1><a href="./board_list.php">자유게시판</a></h1>
    <h4>글을 수정합니다.</h4>
    <div id="write_area">
        <form id="writeForm" action="./board_process.php?action=edit" method="post" enctype="multipart/form-data">
            <input type="hidden" name="seq" value="<?php echo $seq; ?>">
            <div id="in_title">
                <textarea name="title" id="utitle" rows="1" cols="55" placeholder="제목" maxlength="50"
                onChange="checkMaxLength(this);"><?=$row['TITLE'];?></textarea>
            </div>
            <div id="in_name">
                <textarea name="writer" id="uwriter" rows="1" cols="55" placeholder="작성자" maxlength="30"
                onChange="checkMaxLength(this);"><?=$row['WRITER'];?></textarea>
            </div>
            <div id="in_content">
                <textarea name="content" id="ucontent" placeholder="내용" maxlength="300" ><?=$row['CONTENT'];?></textarea>
            </div>
            <div id="in_file">
                <input type="file" name="upload_file">
            </div>
            <div class="bt_se">
                <button type="submit" id="editBtn">글 수정</button>
                <button type="button" onclick="location.href='./board_list.php'">게시판으로 돌아가기</button>
                <li><a href="javascript:history.back();">[뷰로 돌아가기]</a></li>
                <button type="button" onclick="location.reload()">글 취소</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>