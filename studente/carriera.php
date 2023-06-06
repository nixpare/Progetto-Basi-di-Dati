<?php
	error_reporting(E_ALL);
	session_start();

	if (empty($_SESSION)) {
		http_response_code(301);
		header('Location: /index.php');
		return;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD' &&
		$_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		header('Location: /studente.php');
		return;
   	}

	if (!empty($_POST)) {
		if (isset($_POST['rinuncia'])) {
			http_response_code(301);
			header('Location: /logout.php');
			return;
		}
	}

	include_once '../assets/php/carriera.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Carriera Studente</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/utente.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
	<script src="/assets/js/collapse.js" defer></script>
	<script src="/assets/js/carriera.js" defer></script>
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
			<h2>Carriera</h2>
			<h3><?php echo $_SESSION['nome'] ?></h3>
		</div>
	</div>

	<?php
		$carriera = get_carriera_completa();
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
		<h4>Carriera valida</h4>
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
		<h4>Carriera Completa</h4>
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

	<div class="container my-4">
		<h4 class="mb-3">Rinuncia agli studi</h4>
		<div class="ms-3">
			<div class="alert alert-warning">
				<p class="m-0">ATTENZIONE! L'operazione non è reversibile, una volta confermata verrà loggato fuori.</p>
			</div>
			<form action="" method="post" id="rinunciaForm">
				<button class="warning">Rinuncio</button>
				<input class="d-none" type="checkbox" name="rinuncia">
				<button class="d-none">Annulla</button>
				<button class="d-none warning" type="submit">Conferma Scelta</button>
			</form>
		</div>
	</div>

	<footer>
		<p>Designed by <a href="https://nixpare.com/">NixPare</a></p>
	</footer>
</body>
</html>