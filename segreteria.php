<?php
	include_once './assets/php/segreteria.php';
	session_start();

	if (empty($_SESSION)) {
		http_response_code(301);
		header('Location: /index.php');
		return;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		header('Location: /segreteria.php');
		return;
   	}

	if (empty($_POST)) {
		goto end;
	}

	if (isset($_POST['password'])) {
		$update_result = change_password($_POST['password']);
	}

	if (!$update_result) {
		$form_err_message = "Errore nell'aggiornare i dati";
	}

	end:
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Area Segreteria</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/password-eye.js" defer></script>
	<script src="/assets/js/edit.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="stud">
	<header class="container py-4 ps-5 mb-5 d-flex align-items-center justify-content-between">
		<h1>Progetto Basi di Dati</h1>
		<a class="btn" href="/logout.php">Logout</a>
	</header>

	<div class="container welcome welcome-user">
		<img src="/assets/img/student-user-img.png" alt="User Picture">
		<div>
			<h2>Benvenuto!</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<h4>Informazioni dell'account</h4>
		<div>
			<?php if (isset($form_err_message)) { ?>
				<div class="my-3 alert alert-danger" role="alert">
					<?php echo $form_err_message ?>
				</div>
			<?php } ?>
			<table class="my-3">
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Email</th>
					<td><?php echo $_SESSION['email'] ?></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="password-edit-container">
					<th>Password</th>
					<td>
						<form action="" method="post" id="edit-password" data-edit-container="password-edit-container">
							<div class="password-eye-container">
								<input class="px-0" type="password" name="password" id="password" placeholder="********" autocomplete="new-password" disabled>
								<i class="fa-solid fa-eye password-eye"></i>
							</div>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-password" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-password" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-password" data-edit-action="send">Invia</button>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="container my-5">
		<h4>Gestionale</h4>
		<div class="ms-4 my-3 d-flex align-items-center gap-3">
			<a class="btn" href="/segreteria/studenti.php">Studenti</a>
			<a class="btn" href="/segreteria/docenti.php">Docenti</a>
			<a class="btn" href="/segreteria/corsi.php">Corsi di Laurea</a>
		</div>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>