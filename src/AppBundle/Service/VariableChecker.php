<?php

namespace AppBundle\Service;


class VariableChecker
{
    public $delimiter = ";";

    public function __construct(){

	}

    public function check($variables, $conditionString, &$logs=array()){
        $checks = $this->splitVariableString($conditionString, $logs);
        
        $success = true;

        foreach ($checks as $check) {
            
            if (! isset($variables[ $check->variable ])) {
                $logs[] = "Warning: Ignoring unavailable variable: " . $check->variable;
                continue;
            }

            $actualValue = $variables[ $check->variable ];
            $referenceValue = $check->value;

            $result = call_user_func(array($this, $check->comparator), $actualValue, $referenceValue);
            
            if (! $result){
                $logs[] = "Notice: Failed condition : ".$check->conditionString;
                $success = false;
            } else {
                $logs[] = "Notice: Success condition : ".$check->conditionString;
            }
        }

        return $success;
    }

    public function splitVariableString($conditionString, &$logs) {
        $conditions = explode($this->delimiter, $conditionString);
        $checks = array();
        
        foreach ($conditions as $oneConditionString) {
            if (empty(trim($oneConditionString))) {
                continue;
            }

            $check = $this->makeVariableCheck($oneConditionString);
            
            if (! $check) {
                continue;
            }

            $checks[] = $check;
        }

        return $checks;
    }

    public function makeVariableCheck($oneConditionString){
        
        $pattern = "/\s*(\w+)\s*([\=\<\>\!]+)\s*(\-?\w+[\.\w]+)\s*/";
        $match = array();
        $res = preg_match($pattern, $oneConditionString, $match);

        if (!$res || count($match) != 4)  {
            return false;
        }

        $variableCheck = new \StdClass();
        $variableCheck->conditionString = $oneConditionString;
        $variableCheck->variable = $match[1];
        $variableCheck->comparator = $this->getComparatorName($match[2]);
        $variableCheck->value = $match[3];

        if (!$this->variableCheckIsValid($variableCheck)) {
            return false;
        }        

        return $variableCheck;
    }

    public function variableCheckIsValid($variableCheck){
        if(! $variableCheck->variable) {
            return false;
        }

        if(! $variableCheck->comparator) {
            return false;
        }

        return true;
    }

    protected function getComparatorName($symbol) {
        $comparators=array(
            "==" => "equal",
            "=" => "equal",
            "!=" => "notEqual",
            "=!" => "notEqual",
            "<" => "lessThan",
            ">" => "moreThan",
            "=<" => "lessEqualThan",
            "<=" => "lessEqualThan",
            "=>" => "moreEqualThan",
            ">=" => "moreEqualThan",
        );

        if (isset($comparators[$symbol])) {
            return $comparators[$symbol];
        }

        return false;
    }
   

    protected function equal($val1, $val2){
        return ($val1 == $val2);
    }

    protected function notEqual($val1, $val2){
        return ($val1 != $val2);
    }

    protected function moreEqualThan($val1, $val2){
        return ($val1 >= $val2);
    }

    protected function lessEqualThan($val1, $val2){
        return ($val1 <= $val2);
    }

    protected function moreThan($val1, $val2){
        return ($val1 > $val2);
    }

    protected function lessThan($val1, $val2){
        return ($val1 < $val2);
    }


}
