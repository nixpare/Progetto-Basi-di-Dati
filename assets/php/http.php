<?php
	function not_get_or_post() {
		if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(400);
			echo 'Invalid request method';
			return true;
		}
		return false;
	}
?>