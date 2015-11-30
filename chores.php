<!DOCTYPE html>
<html>
<head>
	<title>Chore</title>
	<link type="text/css" rel="stylesheet" href="chore_style.css">
</head>
<body>
<h1>
chores
</h1>
	<p>
		If you find that this chore has yet to be completed within the Green house 
		then "bother" the person that needs to get on their chore. It will send them 
		a friendly anonymous text reminder that they need to do their chore.
	</p>
	<?php
		$chores_one = fopen("chores_list.txt", "r"); 
		$week = fgets($chores_one);

		while (($line = fgets($chores_one)) !== false) {
			 list($chore, $person, $number, $last_poked) = explode(":", $line);

			 ?>
			 <div id="chore">
				 <label> <?=$chore?></label>
				 <form method="POST" action="chores_background.php?action=poke&person=<?=$person?>">
				 	<input type="submit" value="bother">
				 </form> 
			 </div>
			 <?php
		}
	?>
	
	
	
</body>
</html>