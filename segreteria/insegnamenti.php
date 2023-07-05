<?php
	include_once '../assets/php/db.php';
	include_once '../assets/php/http.php';
	include_once '../assets/php/segreteria_insegnamenti.php';

	session_start();

	if (not_get_or_post()) {
		return;
   	}

	if (invalid_access('segr')) {
		return;
	}

	if (empty($_POST)) {
		goto end;
	}

	$result = create_insegnamento(
		$_POST['codice'], $_POST['corso'],
		$_POST['anno'], $_POST['nome'],
		$_POST['descrizione'], $_POST['responsabile']
	);

	if ($result['result'] == 1) {
		goto end;
	}
	if ($result['error'] != '') {
		$err_message = $result['error'];
	} else {
		$err_message = 'Errore creazione docente';
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

	<div class="container my-4">
		<h5 class="highlight">Filtro</h5>
		<p>Filtra gli Insegnamenti per codice, nome o corso di laurea</p>
		<form class="my-3" action="">
			<input type="text" name="filter" placeholder="Codice, nome o corso di laurea" <?php if (!empty($_GET)) {
				if (isset($_GET['filter'])) {
					echo 'value="' . urldecode($_GET['filter']). '"';
				}
			} ?> >
			<button type="submit">Cerca</button>
			<?php if (!empty($_GET)) { ?>
				<a class="btn ms-3" href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>">Rimuovi filtri</a>
			<?php } ?>
		</form>
	</div>

	<div class="container mt-5">
		<h4 class="highlight">Insegnamenti</h4>
		<table>
			<thead>
				<tr>
					<th>Codice</th>
					<th>Nome</th>
					<th>Corso</th>
					<th>Anno</th>
					<th></th>
				</tr>
				<tr class="spacer"><th></th></tr>
			</thead>
			<tbody>
				<?php
					$insegnamenti = get_insegnamenti();
					if (empty($insegnamenti)) {
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td></td>';
					} else {
						foreach ($insegnamenti as $ins) { ?>
				<tr>
					<td><?php echo $ins['codice'] ?></td>
					<td><?php echo $ins['nome'] ?></td>
					<td><?php echo $ins['corso'] . ' (' . ucfirst($ins['tipo']) . ')' ?></td>
					<td><?php echo $ins['anno'] ?></td>
					<td><a class="btn" href="<?php
						echo '/segreteria/insegnamenti/gestisci.php?codice=' . $ins['codice'] . '&corso=' . $ins['corso']
					?>">Gestisci</a></td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>

	<div class="container mt-5">
		<?php if (isset($err_message)) { ?>
			<div class="my-3 alert alert-danger">
				<?php echo $err_message ?>
			</div>
		<?php } ?>

		<h4 class="highlight">Crea insegnamento</h4>
		<form action="" method="post">
			<table class="my-3">
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Codice</th>
					<td><input type="text" name="codice" placeholder="Codice"></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Nome</th>
					<td><input type="text" name="nome" placeholder="Nome"></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Corso</th>
					<td><select name="corso">
						<?php $corsi = get_corsi();
						foreach ($corsi as $corso) { ?>
							<option value="<?php
								echo $corso['nome'] ?>"><?php echo $corso['nome'] . ' (' . ucfirst($corso['tipo']) . ')'
							?></option>
						<?php } ?>
					</select></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Anno</th>
					<td><select name="anno">
						<option value="1">Anno 1</option>
						<option value="2">Anno 2</option>
						<option value="3">Anno 3</option>
					</select></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Descrizione</th>
					<td><textarea type="text" name="descrizione" placeholder="Descrizione" cols="40" rows="4"></textarea></td>
					<td class="no-delim"></td>
				</tr>
				<tr class="spacer"><td></td></tr>
				<tr>
					<th>Docente</th>
					<td><select name="responsabile">
						<?php $docenti = get_suitable_docenti();
						foreach ($docenti as $doc) { ?>
							<option value="<?php
								echo $doc['email'] ?>"><?php echo $doc['cognome'] . ' ' . $doc['nome']
							?></option>
						<?php } ?>
					</select></td>
					<td class="no-delim"></td>
				</tr>
			</table>
			<button type="submit">Crea</button>
		</form>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>
