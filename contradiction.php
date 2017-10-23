<?php
require_once 'algo.php';

function invalid_rule($rules)
{
    foreach ($rules as $key => $rule)
    {
        if (!strpos($rule, "<=>") && !strpos($rule, "=>"))
        {
            return false;
        }
    }
    return true;
}

function search_contradictions($rules, $letters, $facts)
{
    foreach ($rules as $key => $rule)
    {
        $contradiction = sc_support($key, $rules, $letters, "");
        if ($contradiction)
            return true;
    }
    return false;
}

function sc_support($skip, $rules, $letters, $facts)
{
    $letters_dup1 = $letters;
    $letters_dup2 = $letters;
    $solve_rule = array();
    $solve_rule[0] = $rules[$skip];

    if (strpos($rules[$skip], "<=>"))
        $exp = explode("<=>", $rules[$skip]);
    else
        $exp = explode("=>", $rules[$skip]);
    $l0 = get_letters($exp[1]);
    foreach ($l0 as $k => $v)
    {
        $letters_dup1 = algo("", $v, $solve_rule, $letters_dup1);
//        echo "rule: $rules[$skip], letter: $v, value: $letters_dup1[$v], value2: $letters_dup2[$v]\n";
    }
    foreach ($rules as $key => $rule)
    {
        $letters_dup2 = $letters;
        if ($skip == $key)
            continue;
        if (strpos($rule, "<=>"))
            $exploded = explode("<=>", $rule);
        else
            $exploded = explode("=>", $rule);
        $l = get_letters($exploded[1]);
        foreach ($l as $k => $v)
        {
            $letters_dup2 = algo("", $v, array(0 => $rule), $letters_dup2);
//            if (!ctype_alpha($v))
//                $v = substr($v, 1);
//            echo "rule: $rule, letter: $v, value: $letters_dup2[$v], value2: $letters_dup1[$v]\n";
        }
        $contradiction = contradicts($letters_dup1, $letters_dup2, $l0, $l, $rules[$skip], $rule);
        if ($contradiction)
        {
            
            return true;
        }
    }
    return false;
}

function contradicts($result1, $result2, $l0, $l1, $rule1, $rule2)
{
    $tmp1 = array();
    $tmp2 = array();
    $l0 = str_split($l0[0]);
    $l1 = str_split($l1[0]);
    foreach ($l0 as $k => $v)
        if (ctype_alpha($v))
            $tmp1[$v] = $v;
    foreach ($l1 as $k => $v)
        if(ctype_alpha($v))
            $tmp2[$v] = $v;
    $arr = array_intersect($tmp1, $tmp2);
    foreach ($arr as $item => $val)
    {
        if (!ctype_alpha($val))
        {
            $i = 0;
//            echo $val[$i]."\n";
            $val = str_split($val);
            foreach($val as $k => $v)
            {
                if (ctype_alpha($v) && $result1[$v] != $result2[$v])
                {
                    echo "$rule1 contradicts $rule2\n";
                    return true;
                }
                $i++;
            }
//            $val = substr($val, 1);
//            echo "val after sub: $val\n";
//            exit;
        }
        else if ($result1[$val] != $result2[$val])
        {
            echo "$rule1 contradicts $rule2\n";
            return true;
        }
    }
    return false;
}

function get_letters($str)
{
    $operators = array(
        0 => '+',
        1 => '|',
        2 => '^',
        3 => '!'
    );
    $return = array();
    foreach ($operators as $key => $value)
    {
        if (strpos($str, $value))
            array_merge($return, explode($value, $str));
    }
    if (count($return) != 0)
    {
        foreach ($return as $k => $v)
        {
            if ($v === "")
                unset($return[$k]);
        }
        return $return;
    }
    return array(0 => $str);
}