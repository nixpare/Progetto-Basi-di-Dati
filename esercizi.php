<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Esercizi</title>
</head>
<body>
	<h1>PHP</h1>
	<h2>Arrays</h2>
	<ul>
		<li>
			<?php
				$myArray = array('primo', 'secondo');
				var_dump($myArray)
			?>
		</li>
		<li>
			<?php
				$myArray2 = array('primo' => 1, 2 => 'secondo');
				var_dump($myArray2)
			?>
		</li>
	</ul>
	<h2>Forms</h2>
	<div>
		<?php
			var_dump($_POST)
		?>
	</div>
	<form action="" method="POST">
		<input type="text" name=">
	</form>
</body>
</html>