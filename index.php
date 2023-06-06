<?php
	if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'HEAD') {
		session_start();

		if (!empty($_SESSION) && isset($_SESSION['tipo_utente'])) {
			switch ($_SESSION['tipo_utente']) {
				case 'stud':
					http_response_code(301);
					header('Location: /studente.php');
					return;
			}
		}

		goto end;
   	}

	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		$form_err_message = 'Metodo di richiesta non valido';
		goto end;
   	}

	if (empty($_POST)) {
		http_response_code(400);
		$form_err_message = 'Richiesta vuota ricevuta';
		goto end;
   	}

	$userType = $_POST['user-type'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	include_once './assets/php/login.php';

	switch ($userType) {
		case 'segr':
			echo '<h1>Segreteria</h1>';
			echo '<p>Email: ' . $email . ' - Password: ' . $password . '</p>';
			return;
		case 'doc':
			echo '<h1>Docente</h1>';
			echo '<p>Email: ' . $email . ' - Password: ' . $password . '</p>';
			return;
		case 'stud':
			$result = login_studente($email, $password);

			if (!$result) {
				http_response_code(400);
				$form_err_message = 'Accesso fallito';
				goto end;
			}

			session_start();
			session_reset();

			$_SESSION['email'] = $email;
			$_SESSION['tipo_utente'] = $userType;
			$_SESSION['matricola'] = $result['matricola'];
			$_SESSION['nome'] = $result['nome'] . ' ' . $result['cognome'];
			$_SESSION['corso'] = $result['corso'];

			http_response_code(301);
			header('Location: /studente.php');
			return;
	}

	end:
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/index.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/index.js" defer></script>
	<script src="/assets/js/password-eye.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="segr">
	<header class="container py-4 ps-5 mb-5">
		<h1>Progetto Basi di Dati</h1>
	</header>

	<div class="container welcome">
		<h2>Login</h2>
		<p>Effettua l'accesso per entrare nella tua area riservata</p>
	</div>

	<?php if (isset($form_err_message)) { ?>
		<div class="container alert alert-danger" role="alert">
			<?php echo $form_err_message ?>
		</div>
	<?php } ?>
	
	<div class="container my-5 p-0 d-flex align-items-center justify-content-center login">
		<form action="" method="post">
			<div class="container p-0 d-flex flex-nowrap user-btns-container">
				<button class="user-btn" type="button" target="segr" disabled>Segreteria</button>
				<button class="user-btn" type="button" target="doc">Docente</button>
				<button class="user-btn" type="button" target="stud">Studente</button>
				<input type="hidden" name="user-type" id="user-type" value="segr">
			</div>
			<fieldset class="mt-3 mb-4 d-flex flex-column align-items-center justify-content-center">
				<input type="email" name="email" id="email" placeholder="Email" required>
				<div class="password-eye-container">
					<input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password" required>
					<i class="fa-solid fa-eye password-eye"></i>
				</div>
				<button class="mt-3" type="submit">Accedi</button>
			</fieldset>
		</form>
	</div>

	<div class="container welcome">
		<div class="course-container">
			<h2>Corsi di Laurea</h2>
			<p>Visualizza le informazioni di tutti i Corsi di Laurea</p>
			<a class="btn align-self-end" href="/corsi" target="_blank">Vai</a>
		</div>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>