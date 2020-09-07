<?php

namespace Core;

class Validator
{
    public static function make(array $data, array $rules)
    {
        $errors = null;
        foreach ($data as $dataKey => $dataValue) {
            foreach ($rules as $ruleKey => $ruleValue) {
                if ($ruleKey == $dataKey) {
                    $rulesArray = explode('|', $ruleValue);
                    foreach ($rulesArray as $ruleFormat) {
                        $rule = explode(':', $ruleFormat);
                        if (isset($rule[1])) {
                            switch ($rule[0]) {
                                case 'max':
                                    if (strlen($dataValue) > $rule[1])
                                        $errors[$ruleKey] = "O campo {$ruleKey} pode ter no máximo {$rule[1]} caracters!";
                                    break;
                                case 'min':
                                    if (strlen($dataValue) < $rule[1])
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ter no mínimo {$rule[1]} caracters!";
                                    break;
                                case 'maxValue':
                                    if ($dataValue > $rule[1])
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser menor ou igual a {$rule[1]}.";
                                    break;
                                case 'minValue':
                                    if ($dataValue < $rule[1])
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser maior ou igual a  {$rule[1]}.";
                                    break;
                                case 'minDate':
                                    if (strtotime($dataValue) < strtotime($rule[1]))
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser maior ou igual a  ".date('d/m/Y', strtotime($rule[1])).".";
                                    break;
                            }
                        } else {
                            switch ($ruleFormat) {
                                case 'required':
                                    if ($dataValue == ' ' || empty($dataValue))
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser preenchido!";
                                    break;
                                case 'email':
                                    if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL))
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser um email!";
                                    break;
                                case 'numeric':
                                    if (!is_numeric($dataValue))
                                        $errors[$ruleKey] = "O campo {$ruleKey} precisa ser do tipo númerico!";
                                    break;
                            }
                        }
                    }
                }
            }
        }
        if (isset($errors)) {
            Session::set('err', $errors);
            Session::set('inputs', $data);
            return true;
        } else {
            unset($_SESSION['err']);
            unset($_SESSION['inputs']);
            return false;
        }
    }
}
