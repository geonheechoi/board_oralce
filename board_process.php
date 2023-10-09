<?
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";



$action = isset($_REQUEST['action']) ? htmlspecialchars($_REQUEST['action'], ENT_QUOTES, 'UTF-8') : '';
$writer = isset($_REQUEST['writer']) ? htmlspecialchars($_REQUEST['writer'], ENT_QUOTES, 'UTF-8') : '';
$title = isset($_REQUEST['title']) ? htmlspecialchars($_REQUEST['title'], ENT_QUOTES, 'UTF-8') : '';
$content = isset($_REQUEST['content']) ? htmlspecialchars($_REQUEST['content'], ENT_QUOTES, 'UTF-8') : '';
$seq = isset($_REQUEST['seq']) ? intval($_REQUEST['seq']) : 0;
$file_dir = 'C:\\APM_Setup\\htdocs\\board_oracle\\uploadfolder\\';
if($action == false || $action =='' ){
	echo "<script>alert('오류가 발생했습니다.');</script>";
	echo "<meta http-equiv='refresh' content='0; url=./board_list.php'>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
	<script src="./js/jquery.js"></script>
    <script src="./js/common.js"></script>
</head>

<body>
    <?


//	$timestamp = date('Y-m-d H:i:s', time());
	
		
	if ($action == 'delete') {
		$stmt = oci_parse($conn, "DELETE FROM tbl_board WHERE seq = :seq_bv");
		oci_bind_by_name($stmt, ':seq_bv', $seq, -1, SQLT_INT);
		if (oci_execute($stmt)) {
					echo "<script>
							alert('글이지워졌습니다');
							location.href = './board_list.php';
							
						</script>";
				} else {
					echo "<script>
							alert('글지우기에 실패했습니다.');
							location.href = './board_list.php';
						</script>";
				}
	}if ($action == 'edit') {
		$file = $_FILES['upload_file'];
		//$file_name = $file['name'];
		$file_name = $_FILES['upload_file']['name'];
		$file_name = preg_replace("/[^a-zA-Z0-9._-]/", '', $file_name);

		$file_size = $file['size'];
	
		if (!empty($file_name)) {
			if ($file['error'] !== UPLOAD_ERR_OK) {
				echo "<script>alert('파일 업로드 문제.'); </script>";
			}
	
			$allowed_extensions = array('pdf', 'txt', 'doc', 'docx', 'png', 'jpeg', 'jpg');
			$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
	
			if (!in_array($file_extension, $allowed_extensions)) {
				echo "<script>alert('확장자 허용불가.'); </script>";
			}
	
			if ($file_size > 350 * 1024 * 1024) {
				echo "<script>alert('파일이 너무 큽니다.'); </script>";
			}
	
			$file_destination = $file_dir . $file_name;
	
			if (!move_uploaded_file($file['tmp_name'], $file_destination)) {
				echo "<script>alert('파일업로드를  실패하였습니다.'); </script>";
			}
			
			$stmt = oci_parse($conn, "UPDATE tbl_board SET writer = :writer, title = :title, content = :content, up_at = sysdate, addr_file = :addr_file WHERE seq = :seq");
		} else {
			$stmt = oci_parse($conn, "UPDATE tbl_board SET writer = :writer, title = :title, content = :content, up_at = sysdate WHERE seq = :seq");
		}
	
		oci_bind_by_name($stmt, ':writer', $writer);
		oci_bind_by_name($stmt, ':title', $title);
		oci_bind_by_name($stmt, ':content', $content);
	
		if (!empty($file_name)) {
			oci_bind_by_name($stmt, ':addr_file', $file_destination);
		}
	
		oci_bind_by_name($stmt, ':seq', $seq, -1, SQLT_INT);
	
		if (oci_execute($stmt)) {
			echo "<script>alert('글을 수정하는데 성공하였습니다.'); location.href = './board_view.php?seq=$seq';</script>";
	
		} else {
			
			echo "<script>alert('글을 수정하는데 실패하였습니다.'); </script>";
		}
	}
	
	/*
	function exit_with_alert($msg) {
		echo "<script>alert('$msg'); </script>";
	    exit;
	}
	*/
	
	
	
	
	
	
 	else if ($action == 'write') {
		// 최대 SEQ 값을 찾는 SQL 쿼리
		$find_max = "SELECT MAX(SEQ) AS SEQ FROM tbl_board";
		$stmt_max = oci_parse($conn, $find_max);
		oci_execute($stmt_max);
		$max_result = oci_fetch_assoc($stmt_max);
		$seq = $max_result['SEQ'] + 1; // 최대 SEQ 값에 1을 더하여 다음 SEQ 값 설정
		$file_dir = 'C:\\APM_Setup\\htdocs\\board_oracle\\uploadfolder\\';
		// 파일 정보
		$file = $_FILES['upload_file'];
		$file_name = $file['name'];
	//	$file_name = iconv('UTF-8', 'ASCII//TRANSLIT', $fe['name']);
		$file_name = preg_replace("/[^a-zA-Z0-9._-]/", '', $file_name);

		$file_size = $file['size'];

		// 파일이 없을 경우
		if (empty($file_name)) {
			$stmt = oci_parse($conn, "INSERT INTO tbl_board(seq, writer, title, content, reg_at) VALUES (:seq, :writer, :title, :content, sysdate)");
			oci_bind_by_name($stmt, ':seq', $seq);
			oci_bind_by_name($stmt, ':writer', $writer);
			oci_bind_by_name($stmt, ':title', $title);
			oci_bind_by_name($stmt, ':content', $content);
		//	oci_bind_by_name($stmt, ':reg_at', $timestamp);
			
			if (oci_execute($stmt)) {
				echo "<script>alert('글을 작성하는데 성공하였습니다.'); location.href = './board_list.php';</script>";
			} else {
				echo "<script>alert('글을 작성하는데 실패하였습니다.'); </script>";
			}
		} else {
			// 파일 오류 검사
			if ($file['error'] !== UPLOAD_ERR_OK) {
				echo "<script>alert('파일 업로드에 문제가 있습니다.'); location.href = './board_write.php';</script>";
				exit;
			}
			
			// 확장자 검사
			$allowed_extensions = array('pdf', 'txt', 'doc', 'docx', 'png', 'jpeg', 'jpg');
			$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
			if (!in_array($file_extension, $allowed_extensions)) {
				echo "<script>alert('허용되지 않은 확장자입니다.'); location.href = './board_write.php';</script>";
				exit;
			}
			
			// 파일 크기 검사
			if ($file_size > 350 * 1024 * 1024) {
				echo "<script>alert('파일 크기가 너무 큽니다.'); location.href = './board_write.php';</script>";
				exit;
			}
			
			// 파일 저장 경로 설정
			
			$file_destination = $file_dir . $file_name;
			
			// 파일 이동
			if (move_uploaded_file($file['tmp_name'], $file_destination)) {
				$stmt = oci_parse($conn, "INSERT INTO tbl_board(seq, writer, title, content, reg_at, addr_file) VALUES (:seq, :writer, :title, :content, sysdate, :addr_file)");

				oci_bind_by_name($stmt, ':seq', $seq);
				oci_bind_by_name($stmt, ':writer', $writer);
				oci_bind_by_name($stmt, ':title', $title);
				oci_bind_by_name($stmt, ':content', $content);
				//oci_bind_by_name($stmt, ':reg_at', $timestamp);
				oci_bind_by_name($stmt, ':addr_file', $file_destination);
				
				if (oci_execute($stmt)) {
					echo "<script>alert('글과 파일을 작성하는데 성공하였습니다.'); location.href = './board_list.php';</script>";
				} else {
					echo "<script>alert('글과 파일을 작성하는데 실패하였습니다.'); </script>";
				}
			} else {
				echo "<script>alert('파일 업로드에 실패하였습니다.'); </script>";
			}
		}
	}
	
	
	
	
	
?>
</body>
</html>
<?php include "./include/disconn_cloud.php"?>