<?php
	function filter_passed($corso) {
		if (!isset($_GET['corso']) || $_GET['corso'] === '') {
			return true;
		}

		if (str_contains(strtolower($corso['nome']), strtolower(urldecode($_GET['corso'])))) {
			return true;
		}

		return false;
	}

	function get_corsi() {
		$query = 'select * from uni.corso_laurea order by tipo desc, nome asc';
		$result = db_multi_select('get_corsi', $query, array())['result'];
		
		$corsi = array();
		foreach ($result as $corso) {
			if (!filter_passed($corso)) {
				continue;
			}
			$corsi[] = $corso;
		}
		
		return $corsi;
	}

	function create_corso($nome, $tipo) {
		$query = 'insert into uni.corso_laurea values ($1, $2)';
		$params = array($nome, $tipo);

		return db_iu('create_corso', $query, $params);
	}

	function get_corso($nome) {
		$query = 'select * from uni.corso_laurea where nome = $1';
		return db_single_select('get_corso', $query, array($nome));
	}

	function change_nome($corso, $nome) {
		$query = 'update uni.corso_laurea set nome = $1 where nome = $2';
		$params = array($nome, $corso);
		
		return db_iu('change_nome', $query, $params);
	}

	function get_insegnamenti($corso) {
		$query = 'select * from uni.insegnamento
				where insegnamento.corso = $1
				order by insegnamento.anno, insegnamento.codice, insegnamento.nome';
		return db_multi_select('get_insegnamenti', $query, array($corso))['result'];
	}

	function delete_insegnamento($codice, $corso) {
		$query = 'delete from uni.insegnamento where codice = $1 and corso = $2';
		$params = array($codice, $corso);
		
		return db_iu('delete_insegnamento', $query, $params);
	}

	function delete_corso($nome) {
		$query = 'delete from uni.corso_laurea where nome = $1';
		return db_iu('delete_corso', $query, array($nome));
	}
?>