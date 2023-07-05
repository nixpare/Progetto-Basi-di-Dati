<?php
	include_once '../../assets/php/db.php';
	include_once '../../assets/php/http.php';
	include_once '../../assets/php/segreteria_insegnamenti.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('segr')) {
		return;
	}

	if (empty($_GET) || !isset($_GET['codice']) || !isset($_GET['corso'])) {
		$insegnamento_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
		goto end;
	}

	if (empty($_POST)) {
		goto end;
	}

	switch ($_POST['action']) {
		case 'edit-info':
			switch (true) {
				case isset($_POST['nome']):
					$field = 'nome';
					$value = $_POST['nome'];
					break;
				case isset($_POST['anno']):
					$field = 'anno';
					$value = $_POST['anno'];
					break;
				case isset($_POST['descrizione']):
					$field = 'descrizione';
					$value = $_POST['descrizione'];
					break;
				case isset($_POST['responsabile']):
					$field = 'responsabile';
					$value = $_POST['responsabile'];
					break;
				default:
					http_response_code(400);
					$form_err_message = 'Richiesta non valida';
					goto end;
			}

			$update_result = change_field($_GET['codice'], $_GET['corso'], $field, $value);
			break;
		case 'add-prop':
			return;
			break;
		case 'remove-prop':
			return;
			break;
		default:
			http_response_code(400);
			$form_err_message = 'Richiesta non valida';
			goto end;
	}

	if ($update_result['result'] == 0) {
		if ($update_result['error'] != '') {
			$form_err_message = $update_result['error'];
		} else {
			$form_err_message = "Errore nell'aggiornare i dati";
		}
	} else {
		if (isset($_POST['nome']) && $_POST['nome'] != $_GET['corso']) {
			http_response_code(301);
			header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?corso=' . $_POST['nome']);
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
	<title>Gestione Insegnamenti</title>
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
			<h2>Gestione Insegnamenti</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<?php
			if (!isset($insegnamento_error)) {
				$result = get_insegnamento($_GET['codice'], $_GET['corso']);
				if (!$result['result']) {
					if ($result['error'] != '') {
						$insegnamento_error = $result['error'];
					} else {
						$insegnamento_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
					}
				} else {
					$insegnamento = $result['result'];
				}
			}
		?>

		<?php if (isset($insegnamento_error)) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo $insegnamento_error ?>
			</div>
		<?php } else { ?>
		
		<h4>Insegnamento - <?php echo $insegnamento['nome'] . ' (' . ucfirst($insegnamento['codice']) . ') - ' . $insegnamento['corso'] ?></h4>
		<div>
			<?php if (isset($form_err_message)) { ?>
				<div class="alert alert-danger" role="alert">
					<?php echo $form_err_message ?>
				</div>
			<?php } ?>
			<table class="my-3">
				<tr class="spacer"><td></td></tr>
				<tr id="nome-edit-container">
					<th>Nome</th>
					<td>
						<form action="" method="post" id="edit-nome" data-edit-container="nome-edit-container">
							<input class="px-0" type="text" name="nome" id="nome" value="<?php echo $insegnamento['nome'] ?>" disabled>
							<input type="hidden" name="action" value="edit-info">
						</form>
					</td>
					<td>
						<button data-edit-target="edit-nome" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-nome" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-nome" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="anno-edit-container">
					<th>Anno</th>
					<td>
						<form action="" method="post" id="edit-anno" data-edit-container="anno-edit-container">
							<select name="anno" disabled>
								<option value="1" <?php if ($insegnamento['anno'] == 1) echo 'selected' ?>>Anno 1</option>
								<option value="2" <?php if ($insegnamento['anno'] == 2) echo 'selected' ?>>Anno 2</option>
								<option value="3" <?php if ($insegnamento['anno'] == 3) echo 'selected' ?>>Anno 3</option>
							</select>
							<input type="hidden" name="action" value="edit-info">
						</form>
					</td>
					<td>
						<button data-edit-target="edit-anno" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-anno" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-anno" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="descr-edit-container">
					<th>Descrizione</th>
					<td>
						<form action="" method="post" id="edit-descr" data-edit-container="descr-edit-container">
							<textarea type="text" name="descrizione" placeholder="Descrizione" cols="40" rows="4" disabled>
								<?php echo $insegnamento['descrizione'] ?>
							</textarea>
							<input type="hidden" name="action" value="edit-info">
						</form>
					</td>
					<td>
						<button data-edit-target="edit-descr" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-descr" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-descr" data-edit-action="send">Invia</button>
					</td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr id="responsabile-edit-container">
					<th>Responsabile</th>
					<td>
						<form action="" method="post" id="edit-responsabile" data-edit-container="responsabile-edit-container">
							<select name="responsabile" disabled>
								<?php $docenti = get_suitable_docenti();
								foreach ($docenti as $doc) { ?>
									<option value="<?php echo $doc['email'] ?>" <?php
									if ($doc['email'] == $insegnamento['responsabile']) {
										echo 'selected';
									} ?>>
										<?php echo $doc['cognome'] . ' ' . $doc['nome'] ?>
									</option>
								<?php } ?>
							</select>
							<input type="hidden" name="action" value="edit-info">
						</form>
					</td>
					<td>
						<button data-edit-target="edit-responsabile" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-responsabile" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-responsabile" data-edit-action="send">Invia</button>
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
