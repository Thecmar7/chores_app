<?php
	/******************************************************************************
	 * MAIN! chore_backgound:
	 * 	Cause every good app needs a main.
	 *	This manages a chores text file and then sends a message to a given 
	 *	person that is part of the text file.
	 *
	 *	chores_file.txt format:
	 *		<week>
	 *		<chore_1>:<name_1>:<email_1>:<last_poked_hour_1>:<last_poked_day_1>
	 *		<chore_2>:<name_2>:<email_2>:<last_poked_hour_2>:<last_poked_day_2>
	 *		...
	 *		<chore_n>:<name_n>:<email_n>:<last_poked_hour_n>:<last_poked_day_n>
	 ******************************************************************************/
	function main() {
		// 
		$date_rep = "W:Y";
		
		// the textfile that has the list of people and their chores
		$chores_list = "chores_list.txt";

		// open that list
		$chores = fopen($chores_list, "r"); 
		
		// get the number that is at the top that is the week of the last rotation
		list($next_rotation_small, $next_rotation_large) = explode(":", fgets($chores));
		list($small_rep, $large_rep) = explode(":", $date_rep);

		// we don't need that file in main anymore
		fclose($chores);

		// rotate
		//if ($next_rotation_small < date($small_rep) || $next_rotation_large < date($larg_rep)) {
		//	$rotate = chores_rotate($next_rotation_small, $next_rotation_large, $chores_list, $small_rep, $large_rep);
		//}

		rotate($chores_list, $small_rep, $large_rep);

		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if ($_GET['action'] == "poke") {
				poke_roommate($_GET["person"], $chores_list, $rep_small, $rep_large);
			}
		}
		header("Location: http://web.engr.oregonstate.edu/~smithcr/chores_app/chores.php");
	}

	/******************************************************************************
	 * poke_roommate($poke_recip, $chores_file, $date_rep)
	 * 	$poke_recipient:should be a string of the name of the person that will 
	 *					recieve the message
	 *	$chores_file:	the file path that contains the chores
	 *	$date_rep:		the string that the date should be represented as
	 *
	 *	Description:	This function take in a name and a formatted chore 
	 *					file, and the date representaion. It takes the name and 
	 *					looks through the file to find the name then it will send a
	 *					message to the name's email in the file.
	 ******************************************************************************/
	function poke_roommate($poke_recip, $chores_file, $rep_small, $rep_large) {
		$chores = fopen($chores_file, "r"); 	
		$newFile = fgets($chores);
		
		while (($line = fgets($chores)) !== false) {
			list($chore, $person, $number, $last_poked_day, $last_poked_hour) = explode(":", $line);
			if ($poke_recip == $person && ($last_poked_hour + 3 <= (date("G")) || $last_poked_day < date("j"))) {
				mail ($number , "CHORE REMINDER" , "please do your $chore");
				$newFile = $newFile . $chore . ":" . $person . ":" . $number . ":" . date("j:G") ."\n";
			 } else {
			 	$newFile = $newFile . $chore. ":" . $person . ":" . $number . ":" . $last_poked_day . ":" . $last_poked_hour ."";
			 }
		}
		fclose($chores);
		$chores = fopen($chores_list, "w");
		fwrite($chores, $newFile);
		fclose($chores);
	}

	/******************************************************************************
	 * chores_rotate($now, $next, $difference, $chores_list, $date_rep) {
	 *	$next_small:	The last date of rotation
	 *	$next_large:	The date that the date may have rolled over to
	 *	$difference:	This is the amount of difference between the weeks
	 *	$chores_list:	The Path to the formatted text file
	 *	$date_rep_*:	The way that the page is representeing the date of the 
	 *					turnover
	 *
	 *	Description:	This function takes the file and checks if it needs to 
	 *					rotate the chores based on the difference and the date
	 *					representation
	 ******************************************************************************/
	function chores_rotate($next_small, $next_large, $chores_list, $date_rep_small, $date_rep_large) {
		
		// small and large/date and the turnover
		$now_small = date($date_rep_small);
		$now_large = date($date_rep_large);


		

		if($now != 0) {
			if($now >= $next + $difference) {
				for ($i = 0; $i < ($now - ($next+$difference)); $i += $difference) {
					rotate($chores_list);
				}
				return True;
			} else {
				return False;
			}
		} else if ($next != 0) {
			for ($i = 52; $i > ($next+$difference); $i -= $difference) {
				rotate($chores_list);
			}
			return True;
		} else {
			return False;
		}
	} 

	/******************************************************************************
	 * function rotate($chores_list)
	 *	$chores_list:	The path to a formatted chore file
	 *	$rep_small:		
	 *	$rep_large:		
	 *
	 *	Description:	This rotates the file upward, taking the top name and 
	 *					moving it to the bottom and moving the rest of the 
	 *					names up.
	 ******************************************************************************/
	function rotate($chores_list, $rep_small, $rep_large) {
		//open file
		$chores = fopen($chores_list, "r");
		
		// start a string for the new file & add the date
		$newFile = date($rep_small) . ":" . date($rep_large) . "\n";

		// get the line that holds the date
		fgets($chores);			

		// now get the first person
		$first_person = fgets($chores); 	
		list($fp_chore, $fp_person, $fp_number, $fp_last_poked_day, $fp_last_poked_hour) = explode(":", $first_person);
		$newFile = $newFile . $fp_chore. ":";
		while (($line = fgets($chores)) !== false) {
			list($chore, $person, $number, $last_poked_day, $last_poked_hour) = explode(":", $line);
			$newFile = $newFile . $person . ":" . $number . ":" . $last_poked_day . ":" . $last_poked_hour . $chore . ":";
		}
		$newFile = $newFile . $fp_person . ":" . $fp_number . ":" . $fp_last_poked_day . ":" . $fp_last_poked_hour;
		fclose($chores);
		$chores = fopen($chores_list, "w");
		fwrite($chores, $newFile);
		fclose($chores);
	}

	main();

?>