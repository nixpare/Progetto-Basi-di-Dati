<?php
	function login_studente($email, $password) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select matricola, nome, cognome, corso from uni.studente
					where email = $1 and password = $2';
		$query_name = 'login_studente';
		$params = array($email, $password);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		return pg_fetch_assoc($result);
	}
?>