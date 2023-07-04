<?php
	function not_get_or_post() {
		if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(400);
			echo 'Invalid request method';
			return true;
		}
		return false;
	}

	function invalid_access($user_type) {
		if (empty($_SESSION)) {
			http_response_code(301);
			header('Location: /index.php');
			return true;
		}

		if ($_SESSION['tipo_utente'] != $user_type) {
			http_response_code(400);
			header('Location: /logout.php');
			return true;
		}

		return false;
	}
?>