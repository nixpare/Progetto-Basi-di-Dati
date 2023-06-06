<?php
	include_once './assets/php/studente.php';
	session_start();

	if (empty($_SESSION)) {
		http_response_code(301);
		header('Location: /index.php');
		return;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		header('Location: /studente.php');
		return;
   	}

	if (empty($_POST)) {
		goto end;
	}

	switch (true) {
		case isset($_POST['password']): $update_result = change_password($_POST['password']); break;
		case isset($_POST['tel']): $update_result = change_tel($_POST['tel']); break;
		case isset($_POST['indirizzo']): $update_result = change_indirizzo($_POST['indirizzo']); break;
	}

	if (!$update_result) {
		$form_err_message = "Errore nell'aggiornare i dati";
	}

	end:
	init_studente();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Area Studenti</title>
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
				<div class="alert alert-danger" role="alert">
					<?php echo $form_err_message ?>
				</div>
			<?php } ?>
			<table class="my-3">
				<tr>
					<th>N° Matricola</th>
					<td><?php echo $_SESSION['matricola'] ?></td>
					<td class="no-delim"></td>
				</tr>
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
				<tr class="spacer"><td></td></tr>
				<tr id="tel-edit-container">
					<th>Telefono</th>
					<td>
						<form action="" method="post" id="edit-tel" data-edit-container="tel-edit-container">
							<input class="px-0" type="tel" name="tel" id="tel" value="<?php echo $_SESSION['tel'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-tel" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-tel" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-tel" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="address-edit-container">
					<th>Indirizzo</th>
					<td>
						<form action="" method="post" id="edit-address" data-edit-container="address-edit-container">
							<input class="px-0" type="address" name="indirizzo" id="indirizzo" value="<?php echo $_SESSION['indirizzo'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-address" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-address" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-address" data-edit-action="send">Invia</button>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<?php
		$info_corso = get_info_corso($_SESSION['corso']);

		$anni = array('1' => array(), '2' => array(), '3' => array());
		foreach ($info_corso['insegnamenti'] as $codice => $ins) {
			$anni[$ins['anno']][$codice] = $ins;
		}
	?>
	
	<div class="container study-plan">
		<h4>Piano di studi</h4>
		<table class="my-3">
			<tr>
				<th>Corso di Laurea</th>
				<td><?php echo $_SESSION['corso'] ?></td>
				<td><?php echo ucfirst($info_corso['tipo_corso']) ?></td>
			</tr>
		</table>
		<div class="mt-3 d-flex align-items-center">
			<a class="ms-4 btn" href="/studente/carriera.php" target="_blank">Vai alla Carriera</a>
			<a class="ms-4 btn" href="/studente/appelli.php" target="_blank">Vai agli Appelli</a>
		</div>
		
		<div id="study-accordion">
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
					<button class="collapse-btn" data-bs-toggle="collapse" data-bs-target="#piano<?php echo $nome_anno ?>anno" aria-expanded="false" aria-controls="piano<?php echo $nome_anno ?>anno">
						<i class="me-2 fa-solid fa-chevron-right"></i>
						<h5><?php echo $nome_anno . ' anno' ?></h5>
					</button>
				</div>
				<div class="container my-2 accordion-collapse collapse" id="piano<?php echo $nome_anno ?>anno" data-bs-parent="#study-accordion">
					<table>
						<thead>
							<tr>
								<th>Codice</th>
								<th>Insegnamento</th>
								<th>Responsabile</th>
								<th></th>
							</tr>
							<tr class="spacer"><th></th></tr>
						</thead>
						<tbody>
							<?php foreach ($anno as $codice => $ins) { ?>
							<tr>
								<td><?php echo $codice ?></td>
								<td><?php echo $ins['nome'] ?></td>
								<td><?php echo $ins['responsabile'] ?></td>
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