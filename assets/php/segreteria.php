<?php
	function change_password($password) {
		$query = 'update uni.segretario set password = $1 where email = $2';
		$query_name = 'change_password';
		$params = array($password, $_SESSION['email']);
		
		$result = db_iu($query_name, $query, $params);
		if ($result['result'] == 0) {
			return false;
		} else {
			return true;
		}
	}
?>