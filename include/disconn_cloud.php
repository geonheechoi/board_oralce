<?php
	//=============DB 접속 해제 ========================
	if($stmt)@oci_free_statement($stmt);
	oci_close($conn);
?>