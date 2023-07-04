<?php
	include_once './assets/php/http.php';
	include_once './assets/php/db.php';
	include_once './assets/php/docente.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('doc')) {
		return;
	}

	if (empty($_POST)) {
		goto end;
	}

	if (!isset($_POST['password'])) {
		http_response_code(400);
		$form_err_message = 'Richiesta non valida';
		goto end;
	}

	$update_result = change_field($_SESSION['email'], 'password', $_POST['password']);

	if ($update_result['result'] == 0) {
		if ($update_result['error'] != '') {
			$form_err_message = $update_result['error'];
		} else {
			$form_err_message = "Errore nell'aggiornare i dati";
		}
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
	<title>Area Docenti</title>
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

	<?php
		$insegnamenti = get_insegnamenti();

		$anni = array('1' => array(), '2' => array(), '3' => array());
		foreach ($insegnamenti as $ins) {
			$anni[$ins['anno']][] = $ins;
		}
	?>
	
	<div class="container study-plan">
		<h4 class="highlight">Insegnamenti</h4>
		<div class="mt-3 d-flex align-items-center">
			<a class="ms-4 btn" href="/docente/appelli.php" target="_blank">Vai agli Appelli</a>
		</div>
		<div>
			<?php foreach ($anni as $i => $anno) { if (!empty($anno)) { ?>
			<div class="my-4">
				<div class="d-flex align-items-center">
					<?php
						switch ($i) {
							case '1': $nome_anno = 'Primo'; break;
							case '2': $nome_anno = 'Secondo'; break;
							case '3': $nome_anno = 'Terzo'; break;
						}
					?>
					<div class="d-flex align-items-center justify-content-center">
						<i class="me-2 fa-solid fa-chevron-right"></i>
						<h5><?php echo $nome_anno . ' anno' ?></h5>
					</div>
				</div>
				<div class="container my-2">
					<table>
						<thead>
							<tr>
								<th>Codice</th>
								<th>Corso</th>
								<th>Nome</th>
								<th></th>
							</tr>
							<tr class="spacer"><th></th></tr>
						</thead>
						<tbody>
							<?php foreach ($anno as $ins) { ?>
							<tr>
								<td><?php echo $ins['codice'] ?></td>
								<td><?php echo $ins['corso'] ?></td>
								<td><?php echo $ins['nome'] ?></td>
								<td><a class="btn" href="<?php echo $ins['link_appelli'] ?>" target="_blank">Appelli</a></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } } ?>
		</div>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>