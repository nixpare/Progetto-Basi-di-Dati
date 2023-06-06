<?php
	function get_carriera_completa() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select sostiene.data, sostiene.insegnamento, insegnamento.nome, anno, tipo, voto from
						uni.sostiene
						join
						uni.appello on sostiene.data = appello.data and sostiene.insegnamento = appello.insegnamento and sostiene.corso = appello.corso
						join
						uni.insegnamento on sostiene.insegnamento = insegnamento.codice and sostiene.corso = insegnamento.corso
					where studente = $1 and voto is not null
					order by anno, insegnamento.nome, sostiene.data';
		$query_name = 'get_carriera_completa';
		$params = array($_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$carriera = array();
		while ($row = pg_fetch_array($result)) {
			$carriera[] = $row;
		}
		return $carriera;
	}
?>