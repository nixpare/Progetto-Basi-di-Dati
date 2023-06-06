<?php
	session_start();
	session_destroy();
	http_response_code(301);
	header('Location: /index.php');
?>