<?php
	function get_insegnamenti() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

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
		return $insegnamenti;
	}

	function change_password($password) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.docente set password = $1 where email = $2';
		$query_name = 'change_password';
		$params = array($password, $_SESSION['email']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}
?>