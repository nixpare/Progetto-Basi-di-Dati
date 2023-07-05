<?php
	function get_insegnamenti() {
		$db = db_connect();

		$query = 'select * from uni.insegnamento
					where responsabile = $1
					order by anno, nome';
		$query_name = 'get_insegnamenti';
		$params = array($_SESSION['email']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$insegnamenti = array();
		while ($row = pg_fetch_array($result)) {
			$row['link_appelli'] = '/docente/appelli.php?codice=' . $row['codice'] . '&corso=' . $row['corso'];
			$insegnamenti[] = $row;
		}

		$_SESSION['insegnamenti'] = $insegnamenti;
		pg_close($db);
		return $insegnamenti;
	}

	function change_field($email, $field, $value) {
		$query = 'update uni.docente set ' . $field .' = $1 where email = $2';
		$params = array($value, $email);
		
		return db_iu('change_' . $field, $query, $params);
	}

	function get_docente($email) {
		$query = 'select * from uni.docente where email = $1';
		$params = array($email);
		
		return db_single_select('get_docente', $query, $params);
	}
?>