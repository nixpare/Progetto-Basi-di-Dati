<?php
	function change_password($password) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.segretario set password = $1 where email = $2';
		$query_name = 'change_password';
		$params = array($password, $_SESSION['email']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$num_rows = pg_affected_rows($result);

		if ($num_rows == 0) {
			return false;
		}
		return true;
	}
?>