<?php
/*
 * Author: Eugene Yi
 * Date: 11/13/2018
 *
 * Function given an input set of integers and a desired target sum,
 * returns the set of combinations of any length that add up to that target sum.
 *
 */

define("intArgv", 3);
 
$numbers = array();
$sum = 0;

for ($i = 1; $i < count($argv); $i++) {
	$numbers[] = (int)$argv[$i];
}
$sum = (int)$numbers[count($numbers) - 1];
unset($numbers[count($numbers) - 1]);

if (count($argv) < intArgv) {
	echo "Invalid input!\n";
	echo "Usage: php eugene.php [integer1 integer2 ... integerN] target sum\n";	
} else {
	find_combinations_sum($numbers, $sum);
}
exit;

function find_combinations_sum($input, $inputsum){

	$numbers = $input;
	$sum = $inputsum;
	
	for ($i = 0; $i < count($numbers); $i++) {
		// echo "i: " . $i . "\n";
		$last_index = 0;
		
		// Number by itself is the sum.
		if ($numbers[$i] == $sum) {
			$answers = array($i);
			print_answer($numbers, $sum, $answers);
			unset($answers);
			continue;
		}		
		
		for ($j = $i + 1; $j < count($numbers); $j++) {
			if ($j <= $last_index) {
				continue;
			}
			// echo "\tj: " . $j . "\n";
			$answers = array();
			$answers[] = $i;		
			$total = $numbers[$i];

			for ($k = $j; $k < count($numbers); $k++) {
				// echo "\t" . "k: " . $k . "\n";
				if (($total + $numbers[$k]) > $sum) {
					continue;				
				}
				
				$answers[] = $k;
				$total += $numbers[$k];
				// echo "\t" . "total: " . $total . "\n";				
			
				if ($total == $sum) {
					print_answer($numbers, $sum, $answers);
					$last_index = $answers[count($answers) - 1];
				}
			}
			
			unset($answers);
		}
	}
	
	return true;
}

function print_answer($numbers, $sum, $answers) {
	
	$temp_string = "[";
	$temp_string2 = "";
	for ($answers_idx = 0; $answers_idx < count($answers); $answers_idx++) {
		if ($answers_idx > 0) {
			$temp_string .= ", ";
			$temp_string2 .= ", ";
		}
		$temp_string .= $answers[$answers_idx];
		$temp_string2 .= "input[" . $answers[$answers_idx] . "] = " . $numbers[$answers[$answers_idx]];
	}
	echo $temp_string . "]\t=> " . $temp_string2 . ", sum = " . $sum . "\n";
	
	return;
}
?>