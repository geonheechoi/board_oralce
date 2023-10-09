<?
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <script src="./js/jquery.js"></script>
    <script src="./js/common.js"></script>
</head>
<body>
<div id="board_write">
    <h1><a href="./board_list.php">자유게시판</a></h1>
    <h4>글을 작성하는 공간입니다.</h4>
    <div id="write_area">
        <form action="./board_process.php?action=write" method="post" accept-charset="UTF-8" id="writeForm"
            enctype="multipart/form-data">
            <div id="in_title">
                <textarea name="title" id="utitle" rows="1" cols="55" placeholder="제목" maxlength="50"
                onchange="checkMaxLength(this);"></textarea>
            </div>
            <div id="in_name">
                <textarea name="writer" id="uwriter" rows="1" cols="55" placeholder="작성자" maxlength="10"
                onchange="checkMaxLength(this);"></textarea>
            </div>
            <div id="in_content">
                <textarea name="content" id="ucontent" placeholder="내용" maxlength="300"
                onchange="checkMaxLength(this);"></textarea>
            </div>
            <div id="in_file">
                <input type="file" name="upload_file">
            </div>
            <div class="bt_se">
                <button type="submit">글 작성</button>
                <button type="button" onclick="location.href='./board_list.php'">목록으로 돌아가기</button>
                <button type="button" onclick="location.reload()">글 취소</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<? include "./include/disconn_cloud.php"?>