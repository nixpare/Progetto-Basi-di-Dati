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

	include_once '../assets/php/docente_appelli.php';

	if (!empty($_POST)) {
		switch ($_POST['action']) {
			case 'delete':
				$result = delete_appello($_POST['data'], $_POST['insegnamento'], $_POST['corso']);
				if (!$result) {
					$delete_err_message = "Errore nella cancellazione dell'appello";
				}
				break;
			case 'add':
				$split = explode(':', $_POST['insegnamento-corso'], 2);

				$result = add_appello($_POST['data'], $split[0], $split[1], $_POST['tipo']);
				if (!$result[0]) {
					$add_err_message = $result[1];
				}
				break;
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestione Appelli</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
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
			<h2>Gestione Appelli</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<div class="container my-4">
		<h5 class="highlight">Filtro</h5>
		<p>Filtra gli Appelli per nome dell'insegnamento o codice</p>
		<form class="my-3" action="">
			<input type="text" name="insegnamento" placeholder="Insegnamento" <?php if (!empty($_GET)) {
				if (isset($_GET['insegnamento'])) {
					echo 'value="' . urldecode($_GET['insegnamento']). '"';
				} else if (isset($_GET['codice'])) {
					echo 'value="' . urldecode($_GET['codice']). '"';
				}
			} ?> >
			<input type="text" name="corso" placeholder="Corso" <?php if (!empty($_GET)) {
				if (isset($_GET['corso'])) {
					echo 'value="' . urldecode($_GET['corso']). '"';
				}
			} ?> >
			<button type="submit">Cerca</button>
			<?php if (!empty($_GET)) { ?>
				<a class="btn ms-3" href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>">Rimuovi filtri</a>
			<?php } ?>
		</form>
	</div>

	<div class="container mt-5">
		<?php if (isset($delete_err_message)) { ?>
			<div class="my-3 alert alert-danger">
				<?php echo $delete_err_message ?>
			</div>
		<?php } ?>
		<h4 class="highlight">Appelli</h4>
		<table>
			<thead>
				<tr>
					<th>Data</th>
					<th>Codice</th>
					<th>Corso</th>
					<th>Insegnamento</th>
					<th>Tipo</th>
					<th></th>
				</tr>
				<tr class="spacer"><th></th></tr>
			</thead>
			<tbody>
				<?php
					$appelli = get_appelli();

					if (empty($appelli)) {
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td> --- </td>';
						echo '<td></td>';
					} else {
						foreach ($appelli as $app) { ?>
				<tr>
					<td><?php echo $app['data'] ?></td>
					<td><?php echo $app['insegnamento'] ?></td>
					<td><?php echo $app['corso'] ?></td>
					<td><?php echo $app['nome_insegnamento'] ?></td>
					<td><?php echo ucfirst($app['tipo']) ?></td>
					<?php if (date('Y-m-d') < $app['data']) { ?>
						<td class="text-center">
							<form action="" method="post">
								<input class="d-none" type="text" name="action" value="delete">
								<input class="d-none" type="text" name="data" value="<?php echo $app['data'] ?>">
								<input class="d-none" type="text" name="insegnamento" value="<?php echo $app['insegnamento'] ?>">
								<input class="d-none" type="text" name="corso" value="<?php echo $app['corso'] ?>">
								<button type="submit">Cancella</button>
							</form>
						</td>
					<?php } else { ?>
						<td><a class="btn" target="_blank"
							href="/docente/registro.php?data=<?php echo $app['data']?>&insegnamento=<?php echo $app['insegnamento'] ?>&corso=<?php echo $app['corso'] ?>">
							Registro Voti
						</a></td>
					<?php } ?>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>

	<div class="container mt-5">
		<?php if (isset($add_err_message)) { ?>
			<div class="my-3 alert alert-danger">
				<?php echo $add_err_message ?>
			</div>
		<?php } ?>
		<h4 class="highlight">Crea Appelli</h4>
		<form action="" method="post">
			<input class="d-none" type="text" name="action" value="add">
			<input type="date" name="data">
			<label for="insegnamento-corso">Insegnamento</label>
			<select name="insegnamento-corso">
				<?php foreach ($_SESSION['insegnamenti'] as $ins) { ?>
				<option value="<?php echo $ins['codice'] . ':' . $ins['corso'] ?>">
					<?php echo $ins['nome'] . ' (' . $ins['codice'] . ') - ' . $ins['corso'] ?>
				</option>
				<?php } ?>
			</select>
			<label for="tipo">Tipo</label>
			<select name="tipo">
				<option value="scritto">Scritto</option>
				<option value="scritto">Orale</option>
			</select>
			<button type="submit">Crea</button>
		</form>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>