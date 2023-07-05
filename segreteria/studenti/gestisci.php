<?php
	include_once '../../assets/php/db.php';
	include_once '../../assets/php/http.php';
	include_once '../../assets/php/studente.php';
	include_once '../../assets/php/studente_carriera.php';
	include_once '../../assets/php/segreteria_studenti.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('segr')) {
		return;
	}

	if (empty($_GET) || ! isset($_GET['matricola'])) {
		$studente_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
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
		case isset($_POST['tel']):
			$field = 'tel';
			$value = $_POST['tel'];
			break;
		case isset($_POST['indirizzo']):
			$field = 'indirizzo';
			$value = $_POST['indirizzo'];
			break;
		case isset($_POST['corso']):
			$field = 'corso';
			$value = $_POST['corso'];
			break;
		default:
			http_response_code(400);
			$form_err_message = 'Richiesta non valida';
			goto end;
	}

	$update_result = change_field($_GET['matricola'], $field, $value);

	if ($update_result['result'] == 0) {
		if ($update_result['error'] != '') {
			$form_err_message = $update_result['error'];
		} else {
			$form_err_message = "Errore nell'aggiornare i dati";
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
	<title>Gestione Studenti</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/collapse.js" defer></script>
	<script src="/assets/js/password-eye.js" defer></script>
	<script src="/assets/js/edit.js" defer></script>
	<script src="/assets/js/delete.js" defer></script>
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
			<h2>Gestione Studente</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<?php
			if (!isset($studente_error)) {
				$result = get_studente($_GET['matricola']);
				if (!$result['result']) {
					if ($result['error'] != '') {
						$studente_error = $result['error'];
					} else {
						$studente_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
					}
				} else {
					$studente = $result['result'];
				}
			}
		?>

		<?php if (isset($studente_error)) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo $studente_error ?>
			</div>
		<?php } else { ?>
		
		<h4>Studente - <?php echo $studente['cognome'] . ' ' . $studente['nome'] ?></h4>
		<div>
			<?php if (isset($form_err_message)) { ?>
				<div class="alert alert-danger" role="alert">
					<?php echo $form_err_message ?>
				</div>
			<?php } ?>
			<table class="my-3">
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>N° Matricola</th>
					<td><?php echo $studente['matricola'] ?></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="cognome-edit-container">
					<th>Cognome</th>
					<td>
						<form action="" method="post" id="edit-cognome" data-edit-container="cognome-edit-container">
							<input class="px-0" type="text" name="cognome" id="cognome" value="<?php echo $studente['cognome'] ?>" disabled>
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
							<input class="px-0" type="text" name="nome" id="nome" value="<?php echo $studente['nome'] ?>" disabled>
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
							<input class="px-0" type="email" name="email" id="email" value="<?php echo $studente['email'] ?>" disabled>
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
				<tr class="spacer"><td></td></tr>
				<tr id="tel-edit-container">
					<th>Telefono</th>
					<td>
						<form action="" method="post" id="edit-tel" data-edit-container="tel-edit-container">
							<input class="px-0" type="tel" name="tel" id="tel" value="<?php echo $studente['tel'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-tel" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-tel" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-tel" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="indirizzo-edit-container">
					<th>Indirizzo</th>
					<td>
						<form action="" method="post" id="edit-indirizzo" data-edit-container="indirizzo-edit-container">
							<input class="px-0" type="indirizzo" name="indirizzo" id="indirizzo" value="<?php echo $studente['indirizzo'] ?>" disabled>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-indirizzo" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-indirizzo" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-indirizzo" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="corso-edit-container">
					<th>Corso di Laurea</th>
					<td>
						<form action="" method="post" id="edit-corso" data-edit-container="corso-edit-container">
							<select name="corso" disabled>
							<?php
								$corsi = get_corsi();
								print_r($corsi);
								foreach ($corsi as $c) { ?>
								<option value="<?php echo $c['nome'] ?>" <?php if ($c['nome'] == $studente['corso']) { echo 'selected'; } ?>><?php echo $c['nome'] . ' - ' . ucfirst($c['tipo']) ?></option>
								<?php } ?>
							</select>
						</form>
					</td>
					<td>
						<button data-edit-target="edit-corso" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-corso" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-corso" data-edit-action="send">Invia</button>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<?php
		$carriera = get_carriera_completa($studente['matricola']);
		$carriera_valida = array();
		foreach ($carriera as $risultato) {
			$carriera_valida[$risultato['insegnamento']] = $risultato;
		}

		foreach ($carriera_valida as $risultato) {
			if ($risultato['voto'] < 18) {
				unset($carriera_valida[$risultato['insegnamento']]);
			}
		}
	?>

	<div class="container mt-5">
		<h4 class="highlight">Carriera valida</h4>
		<table>
			<?php if (empty($carriera_valida)) {
				echo '<td>Nessun esame valido</td>'; 
			} else { ?>
				<thead>
					<tr>
						<th>Codice</th>
						<th>Insegnamento</th>
						<th>Voto</th>
						<th>Anno</th>
						<th>Data</th>
						<th>Tipo</th>
					</tr>
					<tr class="spacer"><th></th></tr>
				</thead>
				<tbody>
					<?php foreach ($carriera_valida as $risultato) { ?>
						<tr>
							<td><?php echo $risultato['insegnamento'] ?></td>
							<td><?php echo $risultato['nome'] ?></td>
							<td><?php echo $risultato['voto'] ?></td>
							<td><?php echo $risultato['anno'] . '°' ?></td>
							<td><?php echo $risultato['data'] ?></td>
							<td><?php echo $risultato['tipo'] ?></td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>
	</div>

	<div class="container mt-5" id="exam-accordion">
		<h4 class="highlight">Carriera Completa</h4>
		<?php
			$anni = array('1' => array(), '2' => array(), '3' => array());
			foreach ($carriera as $risultato) {
				$anni[$risultato['anno']][] = $risultato;
			}

			if (empty($anni['1']) and empty($anni['2']) and empty($anni['3'])) {
				echo '<table><td>Nessun appello disponibile</td></table>';
			} else {
				foreach ($anni as $i => $anno) { if (!empty($anno)) { ?>
		<div class="my-2">
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
			<div class="ms-4 my-2 accordion-collapse collapse" id="piano<?php echo $nome_anno ?>anno" data-bs-parent="#exam-accordion">
				<table>
					<thead>
						<tr>
							<th>Codice</th>
							<th>Insegnamento</th>
							<th>Voto</th>
							<th>Data</th>
							<th>Tipo</th>
						</tr>
						<tr class="spacer"><th></th></tr>
					</thead>
					<tbody>
						<?php foreach ($anno as $risultato) { ?>
							<tr>
								<td><?php echo $risultato['insegnamento'] ?></td>
								<td><?php echo $risultato['nome'] ?></td>
								<td><?php echo $risultato['voto'] ?></td>
								<td><?php echo $risultato['data'] ?></td>
								<td><?php echo $risultato['tipo'] ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php } } } ?>
	</div>

	<div class="container my-5">
		<h4 class="mb-3 highlight-warning">Rimozione studente</h4>
		<div class="ms-3">
			<div class="alert alert-warning">
				<p class="m-0">ATTENZIONE! L'operazione non è reversibile</p>
			</div>
			<form action="" method="post" id="deleteForm">
				<button class="warning">Rimuovi</button>
				<input class="d-none" type="checkbox" name="rinuncia">
				<button class="d-none">Annulla</button>
				<button class="d-none warning" type="submit">Conferma Scelta</button>
			</form>
		</div>
	</div>

	<?php } ?>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>
