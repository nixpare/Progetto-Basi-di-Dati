<?php
	include_once '../assets/php/db.php';
	include_once '../assets/php/http.php';
	include_once '../assets/php/studente_appelli.php';
	
	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('stud')) {
		return;
	}

	if (empty($_POST)) {
		goto end;
	}

	switch ($_POST['action']) {
		case 'delete':
			$result = delete_iscrizione($_POST['data'], $_POST['insegnamento']);
			if (!$result) {
				$delete_err_message = "Errore nell'annullamento dell'iscrizione";
			}
			break;
		case 'add':
			$result = add_iscrizione($_POST['data'], $_POST['insegnamento']);
			if (!$result) {
				$add_err_message = "Errore durante l'iscrizione all'esame";
			}
			break;
	}

	end:
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Iscrizione Appelli</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/collapse.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="stud">
	<header class="container py-4 ps-5 mb-5 d-flex align-items-center justify-content-between">
		<h1>Progetto Basi di Dati</h1>
		<a class="btn" href="/studente.php">Home</a>
	</header>

	<div class="container welcome welcome-user">
		<img src="/assets/img/student-user-img.png" alt="User Picture">
		<div>
			<h2>Gestione Appelli</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-4">
		<h5 class="highlight">Filtro</h5>
		<p>Filtra gli Appelli per nome dell'insegnamento o codice</p>
		<form class="my-3" action="">
			<input type="text" name="insegnamento" placeholder="Filtro" <?php if (!empty($_GET)) {
				if (isset($_GET['insegnamento'])) {
					echo 'value="' . urldecode($_GET['insegnamento']). '"';
				} else if (isset($_GET['codice'])) {
					echo 'value="' . urldecode($_GET['codice']). '"';
				}
			} ?> >
			<button type="submit">Cerca</button>
			<?php if (!empty($_GET)) { ?>
				<a class="btn ms-3" href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>">Rimuovi filtri</a>
			<?php } ?>
		</form>
	</div>

	<?php if (isset($delete_err_message)) { ?>
		<div class="alert alert-danger">
			<?php echo $delete_err_message ?>
		</div>
	<?php } ?>

	<div class="container mt-5">
		<h4 class="highlight">Iscrizioni</h4>
		<table>
			<thead>
				<tr>
					<th>Data</th>
					<th>Codice</th>
					<th>Insegnamento</th>
					<th>Tipo</th>
					<th></th>
				</tr>
				<tr class="spacer"><th></th></tr>
			</thead>
			<tbody>
				<?php
					$iscrizioni = get_iscrizioni();
					if (empty($iscrizioni)) {
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td></td>';
					} else {
						foreach ($iscrizioni as $iscr) { ?>
				<tr>
					<td><?php echo $iscr['data'] ?></td>
					<td><?php echo $iscr['insegnamento'] ?></td>
					<td><?php echo $iscr['nome'] ?></td>
					<td><?php echo ucfirst($iscr['tipo']) ?></td>
					<?php if (date('Y-m-d') < $iscr['data']) { ?>
						<td>
							<form action="" method="post">
								<input class="d-none" type="text" name="action" value="delete">
								<input class="d-none" type="text" name="data" value="<?php echo $iscr['data'] ?>">
								<input class="d-none" type="text" name="insegnamento" value="<?php echo $iscr['insegnamento'] ?>">
								<button type="submit">Annulla</button>
							</form>
						</td>
					<?php } else { ?>
						<td class="text-center"><button disabled> --- </button></td>
					<?php } ?>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>

	<?php if (isset($add_err_message)) { ?>
		<div class="alert alert-danger">
			<?php echo $add_err_message ?>
		</div>
	<?php } ?>

	<?php
		$disponibili = get_disponibili();

		$anni = array('1' => array(), '2' => array(), '3' => array());
		foreach ($disponibili as $appello) {
			$anni[$appello['anno']][] = $appello;
		}
	?>

	<div class="container mt-5" id="exam-accordion">
		<h4 class="highlight">Disponibili</h4>
		<?php
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
							<th>Data</th>
							<th>Codice</th>
							<th>Insegnamento</th>
							<th>Tipo</th>
							<th></th>
						</tr>
						<tr class="spacer"><th></th></tr>
					</thead>
					<tbody>
						<?php foreach ($anno as $codice => $ins) { ?>
						<tr>
							<td><?php echo $ins['data'] ?></td>
							<td><?php echo $ins['insegnamento'] ?></td>
							<td><?php echo $ins['nome'] ?></td>
							<td><?php echo ucfirst($ins['tipo']) ?></td>
							<td>
								<form action="" method="post">
									<input class="d-none" type="text" name="action" value="add">
									<input class="d-none" type="text" name="data" value="<?php echo $ins['data'] ?>">
									<input class="d-none" type="text" name="insegnamento" value="<?php echo $ins['insegnamento'] ?>">
									<button type="submit">Iscriviti</button>
								</form>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php } } } ?>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>