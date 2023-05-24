<?php
	if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'HEAD') {
		http_response_code(301);
		header('Location: /');
		exit();
   	}

	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
		http_response_code(400);
		header('Location: /');
		exit();
   	}

	$userType = $_POST['user-type'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	switch ($userType) {
		case 'segr':
			echo '<h1>Segreteria</h1>';
			echo '<p>Email: ' . $email . ' - Password: ' . $password . '</p>';
			break;
		case 'doc':
			echo '<h1>Docente</h1>';
			echo '<p>Email: ' . $email . ' - Password: ' . $password . '</p>';
			break;
		case 'stud':
			if ($email === 'alessio.pareto@studenti.unimi.it' && $password === 'Ciao') {
				http_response_code(301);
				header('Location: /studente');
				exit();
			}
	}
?>