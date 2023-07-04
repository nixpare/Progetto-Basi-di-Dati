<?php
	function db_connect() {
		return pg_connect('host=localhost user=bdlab password=bdlab dbname=project');
	}

	function db_last_error($db) {
		$query_error = pg_last_error($db);
		if (str_contains($query_error, 'DETAIL')) {
			$query_error = explode('DETAIL:', pg_last_error($db), 2)[1];
		} else if (str_contains($query_error, 'ERROR')) {
			$query_error = explode('CONTEXT', pg_last_error($db), 2)[0];
		}
		
		return trim($query_error);
	}

	function db_single_select($query_name, $query, $params) {
		$db = db_connect();
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$lastError = db_last_error($db);
		pg_close($db);
		
		return array('result' => pg_fetch_assoc($result), 'error' => $lastError);
	}

	function db_multi_select($query_name, $query, $params) {
		$db = db_connect();

		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$lastError = db_last_error($db);
		pg_close($db);

		$tuples = array();
		while ($row = pg_fetch_array($result)) {
			$tuples[] = $row;
		}

		return array('result' => $tuples, 'error' => $lastError);
	}

	function db_iu($query_name, $query, $params) {
		$db = db_connect();

		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			$num_rows = 0;
		} else {
			$num_rows = pg_affected_rows($result);
		}
		$lastError = db_last_error($db);

		pg_close($db);

		return array('result' => $num_rows, 'error' => $lastError);
	}
?>