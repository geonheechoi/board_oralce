<?php
	//=============DB ���� ���� ========================
	if($stmt)@oci_free_statement($stmt);
	oci_close($conn);
?>