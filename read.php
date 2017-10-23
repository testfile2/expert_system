<?php
require_once ("algo.php");
require_once "contradiction.php";
if ($argc == 2)
{
	$letter = array('A' => 0,
		'B' => 0,
		'C' => 0,
		'D' => 0,
		'E' => 0,
		'F' => 0,
		'G' => 0,
		'H' => 0,
		'I' => 0,
		'J' => 0,
		'K' => 0,
		'L' => 0,
		'M' => 0,
		'N' => 0,
		'O' => 0,
		'P' => 0,
		'Q' => 0,
		'R' => 0,
		'S' => 0,
		'T' => 0,
		'U' => 0,
		'V' => 0,
		'W' => 0,
		'X' => 0,
		'Y' => 0,
		'Z' => 0);
	if (file_exists($argv[1]))
	{
		$file = fopen($argv[1], "r");
		$rules = array();
		while (!feof($file))
		{
			$line = fgets($file);
			$line = trim($line);
			if ($line != "")
			{
				if ($line[0] == '#')
					;
				else if ($line[0] == '=')
				{
					if (strpos($line, '#'))
					{
						$facts = substr($line, 1, strpos($line, '#') - 1);
						$facts = trim($facts);
					}
				}
				else if ($line[0] == '?')
				{
					if (strpos($line, '#'))
					{
						$qu = substr($line, 1, strpos($line, '#') - 1);
						$qu = trim($qu);
					}
					else
					{
						$qu = trim($line);
						$qu = ltrim($qu, '?');
					}
				}
				else
				{
					if (strpos($line, '#'))
						$line = substr($line, 0, strpos($line, '#'));
					$line = preg_replace('/\s+/', '', $line);
					if (strpos($line, "<=>") || strpos($line, "=>"))
					{
						if (strpos($line, "<=>"))
							$array = explode("<=>", $line);
						else if (strpos($line, "=>"))
							$array = explode("=>", $line);
					}
					else
					{
						echo "Syntax error on: " . $line;
						exit(0);
					}
					$i = 0;
					$check = 1;
					$bracket = 0;
					while (isset($array[0][$i]))
					{
						if ($array[0][$i] == '(')
							$bracket++;
						else if ($array[0][$i] == ')')
							$bracket--;
						else if (ctype_upper($array[0][$i]) || ($array[0][$i] == '!' && ctype_upper($array[0][$i + 1])) && $check == 1)
							$check = 0;
						else if (($array[0][$i] == '+' || $array[0][$i] == '|' || $array[0][$i] == '^') && $check == 0)
							$check = 1;
						else
						{
							echo "1Syntax error on: " . $line;
							exit(0);
						}
						if ($array[0][$i] == '!')
							$i++;
						$i++;
					}
					if ($check == 1 && $bracket != 0)
					{
						echo "2Syntax error on: " . $line;
						exit(0);
					}
					$i = 0;
					$check = 1;
					while (isset($array[1][$i]))
					{
						if ($array[0][$i] == '(')
						{
							$bracket++;
							$i++;
						}
						else if ($array[0][$i] == ')')
						{
							$bracket--;
							$i++;
						}
						if ($check == 1 && ((ctype_upper($array[1][$i])) || ($array[1][$i] == '!' && ctype_upper($array[1][$i + 1]))))
							$check = 0;
						else if (($array[1][$i] == '+' || $array[1][$i] == '|') && $check == 0)
							$check = 1;
						else
						{
							echo "3	Syntax error on: " . $array[1][$i];
							exit(0);
						}
						if ($array[1][$i] == '!')
							$i++;
						$i++;
					}
					if ($bracket != 0)
					{
						echo "3	Syntax error on: " . $array[1][$i];
						exit(0);
					}
					$rules[count($rules)] = $line;
				}
			}
		}
	}
	else
	{
		echo "Failed to open file";
	}
}
else {
	echo "Please provide a file with rules as an argument";
	exit();
}
$i = 0;
while (isset($facts[$i]))
{
	if (isset($letter[$facts[$i]]))
		$letter[$facts[$i]] = 1;
	$i++;
}

$c = search_contradictions($rules, $letter, $facts);
$letter = algo($facts, $qu, $rules, $letter);
if (!$c) {
	$qu = str_split($qu);
	foreach ($qu as $key => $v) {
		echo "Value of $v is: $letter[$v]\n";
	}
}
//var_dump($letter);
?>
