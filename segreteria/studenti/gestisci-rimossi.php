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

	end:
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestione Studenti Rimossi</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
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
			<h2>Gestione Studente Rimosso</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-5">
		<?php
			if (!isset($studente_error)) {
				$result = get_studente_rimosso($_GET['matricola']);
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
				<tr>
					<th>Email</th>
					<td><?php echo $studente['email'] ?></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Nome</th>
					<td><?php echo $studente['nome'] ?></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Cognome</th>
					<td><?php echo $studente['cognome'] ?></td>
					<td class="no-delim"></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="container mt-5">
		<h4 class="highlight">Carriera</h4>
		<table>
			<?php $carriera = get_carriera_rimossa($_GET['matricola']);
			if (empty($carriera)) {
				echo '<td>Nessun esame valido</td>'; 
			} else { ?>
				<thead>
					<tr>
						<th>Insegnamento</th>
						<th>Corso</th>
						<th>Voto</th>
						<th>Data</th>
					</tr>
					<tr class="spacer"><th></th></tr>
				</thead>
				<tbody>
					<?php $last = $carriera[0]['insegnamento'];
					foreach ($carriera as $risultato) { ?>
						<tr>
							<td><?php echo $risultato['insegnamento'] ?></td>
							<td><?php echo $risultato['corso'] ?></td>
							<td><?php echo $risultato['voto'] ?></td>
							<td><?php echo $risultato['data'] ?></td>
						</tr>
					<?php if ($risultato['insegnamento'] != $last) {
						$last = $risultato['insegnamento'];
						echo '<tr class="spacer"><td></td></tr>';
					} } ?>
				</tbody>
			<?php } ?>
		</table>
	</div>

	<?php } ?>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>
