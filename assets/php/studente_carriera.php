<?php
	function get_carriera_completa($matricola) {
		$query = 'select * from uni.get_carriera_completa($1)';
		$params = array($matricola);

		return db_multi_select('get_carriera_completa', $query, $params)['result'];
	}

	function delete_studente($matricola) {
		$query = 'delete from uni.studente where matricola = $1';
		return db_iu('delete_studente', $query, array($matricola));
	}
?>