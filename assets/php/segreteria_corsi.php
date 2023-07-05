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
?>