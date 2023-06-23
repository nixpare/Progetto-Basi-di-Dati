<?php
	session_start();

	if (empty($_SESSION)) {
		http_response_code(301);
		header('Location: /index.php');
		return;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' &&
		$_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		header('Location: /docente.php');
		return;
   	}

	if (!isset($_GET['data']) || !isset($_GET['insegnamento']) || !isset($_GET['corso'])) {
		http_response_code(400);
		header('Location: /docente.php');
		return;
	}

	$data = $_GET['data'];
	$insegnamento = $_GET['insegnamento'];
	$corso = $_GET['corso'];

	include_once '../assets/php/registro.php';

	if (!check_doc($insegnamento, $corso)) {
		echo $insegnamento. '<br>' . $corso . '<br>' . $_SESSION['email'];
		/* http_response_code(400);
		header('Location: /logout.php'); */
		return;
	}

	if (!empty($_POST)) {
		if ($_POST['voto'] === '') {
			$err_message = 'Inserire un voto valido';
			goto end;
		}

		$result = set_voto($data, $insegnamento, $corso, $_POST['matricola'], $_POST['voto']);
		if (!$result) {
			$err_message = "Errore nell'inserire il voto";
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
	<title>Registro Voti</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/edit.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="stud">
	<header class="container py-4 ps-5 mb-5 d-flex align-items-center justify-content-between">
		<h1>Progetto Basi di Dati</h1>
		<a class="btn" href="/docente.php">Home</a>
	</header>

	<div class="container welcome welcome-user">
		<img src="/assets/img/student-user-img.png" alt="User Picture">
		<div>
			<h2>Registro Voti</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
			<h4><?php echo $data ?></h4>
			<h4><?php echo $insegnamento ?> - <?php echo $corso ?></h4>
		</div>
	</div>

	<?php if (isset($err_message)) { ?>
		<div class="container alert alert-danger">
			<?php echo $err_message ?>
		</div>
	<?php } ?>

	<div class="container mt-5">
		<h4 class="highlight">Iscritti senza voto</h4>
		<table>
			<thead>
				<tr>
					<th>Matricola</th>
					<th>Cognome</th>
					<th>Nome</th>
					<th>Voto</th>
				</tr>
				<tr class="spacer"><th></th></tr>
			</thead>
			<tbody>
				<?php
					$iscritti = get_iscritti($data, $insegnamento, $corso);

					if (empty($iscritti)) {
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
					} else {
						foreach ($iscritti as $iscr) { ?>
				<tr>
					<td><?php echo $iscr['matricola'] ?></td>
					<td><?php echo $iscr['cognome'] ?></td>
					<td><?php echo $iscr['nome'] ?></td>
					
					<?php $editContainer = 'edit-' . $iscr['matricola'] ?>
					<td id="<?php echo $editContainer ?>">
						<div class="d-flex align-items-center justify-content-center">
							<?php $editTarget = 'edit-voto-' . $iscr['matricola'] ?>
							<form action="" method="post" id="<?php echo $editTarget ?>" data-edit-container="<?php echo $editContainer ?>">
								<input class="px-0" type="text" name="voto" value="<?php echo $iscr['voto'] ?>" placeholder="---" size="3" maxlength="2" disabled>
								<input type="hidden" name="matricola" value="<?php echo $iscr['matricola'] ?>">
							</form>
							<div>
								<button data-edit-target="<?php echo $editTarget ?>" data-edit-action="edit">Modifica</button>
								<button data-edit-target="<?php echo $editTarget ?>" data-edit-action="undo">Annulla</button>
								<button data-edit-target="<?php echo $editTarget ?>" data-edit-action="send">Salva</button>
							</div>
						</div>
					</td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>