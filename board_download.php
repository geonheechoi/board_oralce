<?php
header('Content-Type: text/html; charset=utf-8');
include "./include/conn_cloud.php";

if (isset($_REQUEST['download']) && $_REQUEST['download'] == 'true' && isset($_REQUEST['seq'])) {
    //$seq = htmlspecialchars($_REQUEST['seq'], ENT_QUOTES, 'UTF-8');
    $seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : null;
    //$download= isset($_REQUEST['download']) ? $_REQUEST['download'] : null;
    if (is_numeric($seq)) {
        $seq = intval($seq);
    } else {
        echo "Invalid request.";
        exit;
    }
    $stmt = oci_parse($conn, "SELECT ADDR_FILE FROM TBL_BOARD WHERE seq =:seq");
    if (!$stmt) {
        $e = oci_error($conn);
        echo "Oracle Error: " . $e['message'];
        exit;
    }
    
    oci_bind_by_name($stmt, ':seq', $seq);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    $file_path = $row['ADDR_FILE'];

    
    if ($row && file_exists($file_path)) {
        $file_name = basename($file_path);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        // Determine MIME type by file extension
        $mime_type = 'application/octet-stream';  // default MIME type
        switch(strtolower($file_extension)) {
            case 'pdf': $mime_type = 'application/pdf'; break;
            case 'txt': $mime_type = 'text/plain'; break;
            case 'doc': $mime_type = 'application/msword'; break;
            case 'docx': $mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; break;
            case 'php': $mime_type = 'text/php'; break;
            case 'css': $mime_type = 'text/css'; break;
            case 'html': $mime_type = 'text/html'; break;
            case 'png': $mime_type = 'image/png'; break;
            case 'jpeg': $mime_type = 'image/jpeg'; break;
            case 'jpg': $mime_type = 'image/jpeg'; break;
            default: break;
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "파일이 없습니다.";
        echo '<a href="board_view.php?seq=' . $seq . '">뷰로 돌아가기</a>';
        exit;
    }
}
    
    // Debug: Print variables
    var_dump($seq);
    var_dump($row);
    var_dump($file_path);
?>
<!--<a href="board_view.php">Back to Board View</a>-->

<? include "./include/disconn_cloud.php"?>