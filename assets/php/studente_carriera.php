<?php
	function get_carriera_completa() {
		$query = 'select * from uni.get_carriera_completa($1)';
		$params = array($_SESSION['matricola']);

		return db_multi_select('get_carriera_completa', $query, $params)['result'];
	}
?>