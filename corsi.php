<?php
	include_once './assets/php/corsi.php';

	if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
		http_response_code(400);
		header('Location: /index.php');
		return;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Corsi di Laurea</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/corsi.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/collapse.js" defer></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="segr">
<header class="container py-4 ps-5 mb-5 d-flex align-items-center justify-content-between">
		<h1>Progetto Basi di Dati</h1>
		<a class="btn" href="/index.php">Login</a>
	</header>

	<div class="container welcome">
		<h2>Corsi di Laurea</h2>
	</div>

	<?php if (empty($_GET)) { ?>
		<div class="container">
			<h3>Tutti i corsi di laurea</h3>
			<p>Di seguito verranno mostrati tutti i corsi di laurea disponibili</p>
			<table>
				<thead>
					<tr>
						<th>Nome</th>
						<th>Tipo</th>
						<th></th>
					</tr>
					<tr class="spacer"><th></th></tr>
				</thead>
				<tbody>
					<?php foreach(get_corsi() as $corso) { ?>
					<tr>
						<td><?php echo $corso['nome'] ?></td>
						<td><?php echo $corso['tipo'] ?></td>
						<td><a class="btn" href="<?php echo $corso['info_link'] ?>">Più info</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } else { ?>
		<?php
			$corso = get_informazioni_corso($_GET['corso']);

			if (!$corso) {
				echo '<table><td>Nessun corso trovato con "' . $_GET('corso') . '"</td></table>';
			} else { ?>
		<div class="container">
			<a class="my-3 btn" href="/corsi.php">Tutti i Corsi</a>
			<h4 class="mt-3"><?php echo $corso['nome_corso'] ?></h4>
			<p>Di seguito verranno mostrati tutti gli insegnamenti del seguente corso</p>
			<table>
				<thead>
					<tr>
						<th>Codice</th>
						<th>Nome</th>
						<th>Anno</th>
						<th>Descrizione</th>
						<th>Responsabile</th>
					</tr>
					<tr class="spacer"><th></th></tr>
				</thead>
				<tbody>
					<?php foreach($corso['insegnamenti'] as $ins) { ?>
					<tr>
						<td><?php echo $ins['codice'] ?></td>
						<td><?php echo $ins['nome'] ?></td>
						<td><?php echo $ins['anno'] ?></td>
						<td><?php echo $ins['descrizione'] ?></td>
						<td><?php echo $ins['nome_responsabile'] ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } } ?>
	
	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>