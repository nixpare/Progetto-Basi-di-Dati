<?php
	include_once '../../assets/php/db.php';
	include_once '../../assets/php/http.php';
	include_once '../../assets/php/docente.php';
	include_once '../../assets/php/segreteria_docenti.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('segr')) {
		return;
	}

	if (empty($_GET) || ! isset($_GET['doc'])) {
		$docente_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
		goto end;
	}

	if (empty($_POST)) {
		goto end;
	}

	switch (true) {
		case isset($_POST['cognome']):
			$field = 'cognome';
			$value = $_POST['cognome'];
			break;
		case isset($_POST['nome']):
			$field = 'nome';
			$value = $_POST['nome'];
			break;
		case isset($_POST['email']):
			$field = 'email';
			$value = $_POST['email'];
			break;
		case isset($_POST['password']):
			$field = 'password';
			$value = $_POST['password'];
			break;
		default:
			http_response_code(400);
			$form_err_message = 'Richiesta non valida';
			goto end;
	}

	$update_result = change_field($_GET['doc'], $field, $value);

	if ($update_result['result'] == 0) {
		if ($update_result['error'] != '') {
			$form_err_message = $update_result['error'];
		} else {
			$form_err_message = "Errore nell'aggiornare i dati";
		}
	} else {
		if (isset($_POST['email'])) {
			http_response_code(301);
			header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?doc=' . $_POST['email']);
			return;
		}
	}

	end:
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestione Docenti</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/collapse.js" defer></script>
	<script src="/assets/js/password-eye.js" defer></script>
	<script src="/assets/js/edit.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="stud">
	<header class="container py-4 ps-5 mb-5 d-flex align-items-center justify-content-between">
		<h1>Progetto Basi di Dati</h1>
		<a class="btn" href="/segreteria.php">Home</a>
	</header>

	<div class="container welcome welcome-user">
		<img src="/assets/img/student-user-img.png" alt="User Picture">
		<div>
			<h2>Gestione Docente</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<?php
			if (!isset($docente_error)) {
				$result = get_docente($_GET['doc']);
				if (!$result['result']) {
					if ($result['error'] != '') {
						$docente_error = $result['error'];
					} else {
						$docente_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
					}
				} else {
					$docente = $result['result'];
				}
			}
		?>

		<?php if (isset($docente_error)) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo $docente_error ?>
			</div>
		<?php } else { ?>
		
		<h4>Docente - <?php echo $docente['cognome'] . ' ' . $docente['nome'] ?></h4>
		<div>
			<?php if (isset($form_err_message)) { ?>
				<div class="alert alert-danger" role="alert">
					<?php echo $form_err_message ?>
				</div>
			<?php } ?>
			<table class="my-3">
				<tr class="spacer"><td></td></tr>
				<tr id="cognome-edit-container">
					<th>Cognome</th>
					<td>
						<form action="" method="post" id="edit-cognome" data-edit-container="cognome-edit-container">
							<input class="px-0" type="text" name="cognome" id="cognome" value="<?php echo $docente['cognome'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-cognome" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-cognome" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-cognome" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="nome-edit-container">
					<th>Nome</th>
					<td>
						<form action="" method="post" id="edit-nome" data-edit-container="nome-edit-container">
							<input class="px-0" type="text" name="nome" id="nome" value="<?php echo $docente['nome'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-nome" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-nome" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-nome" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="email-edit-container">
					<th>Email</th>
					<td>
						<form action="" method="post" id="edit-email" data-edit-container="email-edit-container">
							<input class="px-0" type="email" name="email" id="email" value="<?php echo $docente['email'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-email" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-email" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-email" data-edit-action="send">Invia</button>
					</td>
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

		<?php } ?>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>
