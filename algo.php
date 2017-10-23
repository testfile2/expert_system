<?php
function algo($facts, $qu, $rules, $letter)
{
		$i = 0;
		while (isset($qu[$i]))
		{
				$letter[$qu[$i]] = solve($qu[$i], $rules, $facts);
				$i++;
		}
		return ($letter);
}
function solve($let, $rules, $facts)
{
		if (strpos($facts, $let) !== FALSE)
		{
				return (1);
		}
		else
		{
				foreach ($rules as $key => $value)
				{
						$spos = strpos($value, $let);
						if ($spos !== FALSE)
						{
								$i = 1;
								while (isset($value[strlen($value) - $i]))
								{
										if ($value[strlen($value) - $i] == $let)
										{
												$l = 0;
												$brah = 0;
												$checker = 1;
												$check = array(0, 0);
												if (strpos($value, "<=>") !== FALSE)
												{
														$lit = explode("<=>", $value);
														$brah = 1;
												}
												else
														$lit = explode("=>", $value);
												$xor = explode("^", $lit[0]);
												while (isset($xor[$l]))
												{
														$or = explode("|", $xor[$l]);
														$c = 0;
														while (isset($or[$c]))
														{
																$add = explode("+", $or[$c]);
																$f = 0;
																while (isset($add[$f]))
																{
																		if ($add[$f][0] == '(')
																		{
																				$add[$f] = ltrim($add[$f], '(');
																				$add[$f] = substr($add[$f],0,strpos($add[$f], ")"));
																				if (solve($add[$f], $rules, $facts))
																				{
																						$checker = 1;
																						break;
																				}
																				else
																				{
																						$checker = 0;
																						break;
																				}
																		}
																		else if ($add[$f][0] == '!')
																		{
																				$add[$f] = ltrim($add[$f], '!');
																				if (solve($add[$f], $rules, $facts))
																				{
																						$checker = 0;
																						break;
																				}
																				else
																				{
																						$checker = 1;
																						break;
																				}
																		}
																		else
																		{
																				if (!solve($add[$f], $rules, $facts))
																				{
																						$checker = 0;
																						break;
																				}
																		}
																		$f++;
																}
																if ($checker == 0)
																		break;
																$c++;
														}
														$check[$l] = $checker;
														if ($checker == 1)
																break;
														$l++;
												}
												$l = 0;
												$b = 0;
												while (isset($xor[$l]))
												{
														if ($l == 0)
														{
																$b = $check[0];
														}
														else
														{
																if ($b == $check[$l])
																{
																		$checker = 0;
																		break;
																}
														}
														$l++;
												}
												if ($brah == 1)
												{
														$lol = solve($lit[1], $rules, $facts);	
														if ($lol == $checker)
																return (1);
														else
																return (0);
												}
												else
												{
														if (strpos($lit[1], $let) > 0)
														{
															if ($lit[1][strpos($lit[1], $let) - 1] == '!')
															{
																	if ($checker == 1)
																			return (0);
																	else
																			return (1);	
															}
															else
																	if ($checker == 1)
																			return (1);
																	else
																			return (0);
														}
														else
														{
															if ($checker == 1)
																	return (1);
															else
																	return (0);
														}
												}
										}
										if ($value[strlen($value) - $i] == '>' && $value[strlen($value) - $i - 1] == '=')
												break;
										$i++;
								}
						}
				}
				return (0);
		}
}
?>
