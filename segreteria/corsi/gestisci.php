<?php
	include_once '../../assets/php/db.php';
	include_once '../../assets/php/http.php';
	include_once '../../assets/php/segreteria_corsi.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('segr')) {
		return;
	}

	if (empty($_GET) || ! isset($_GET['corso'])) {
		$corso_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
		goto end;
	}

	if (empty($_POST)) {
		goto end;
	}

	switch ($_POST['action']) {
		case 'edit-info':
			if (!isset($_POST['nome'])) {
				http_response_code(400);
				$form_err_message = 'Richiesta non valida';
				goto end;
			}

			$update_result = change_nome($_GET['corso'], $_POST['nome']);
			break;
		case 'edit-ins':
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
	<title>Gestione Corsi di Laurea</title>
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
			<h2>Gestione Corsi di Laurea</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<?php
			if (!isset($corso_error)) {
				$result = get_corso($_GET['corso']);
				if (!$result['result']) {
					if ($result['error'] != '') {
						$corso_error = $result['error'];
					} else {
						$corso_error = 'Errore nel caricare la pagina, tornare indietro e riprovare';
					}
				} else {
					$corso = $result['result'];
				}
			}
		?>

		<?php if (isset($corso_error)) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo $corso_error ?>
			</div>
		<?php } else { ?>
		
		<h4>Corso - <?php echo $corso['nome'] . ' (' . ucfirst($corso['tipo']) . ')' ?></h4>
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
							<input class="px-0" type="text" name="nome" id="nome" value="<?php echo $corso['nome'] ?>" disabled>
							<input type="hidden" name="action" value="edit-info">
						</form>
					</td>
					<td>
						<button data-edit-target="edit-nome" data-edit-action="edit">Modifica</button>
						<button data-edit-target="edit-nome" data-edit-action="undo">Annulla</button>
						<button data-edit-target="edit-nome" data-edit-action="send">Invia</button>
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
