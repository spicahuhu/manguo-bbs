<?php

function array_key_translate($data,$key='id') {
    if(is_array($data)) {
        $temp = array();
        foreach($data as $val) {
            $temp[$val[$key]] = $val;
        }
        return $temp;
    }
    return $data;
}

function logic($name,$group='') {
    $name = parse_name($name, 1);
    if (empty($group)) {
        $group = MODULE_NAME;
    }
    $class = '\\Logic\\' . parse_name($group, 1) . '\\' . $name . 'Logic';
    if (class_exists($class)) {
        $logic = $class::getInstance();
    } else {
        $logic_key = $name;
        $class = '\\Logic\\' . $name . 'Logic';
        $logic = class_exists($class) ? $class::getInstance() : \Logic\BaseLogic::getInstance();
    }
    return $logic;
}


function model($name,$group='') {
    $name = parse_name($name,1);
    if(empty($group)) {
        $group = MODULE_NAME;
    }
    $class = parse_name($group,1) .'\\Model\\' . $name .'Model';
    if(class_exists($class)){
        $model = $class::getInstance();
    } else {
        $class = '\\Model\\'.$name.'Model';
        $model = class_exists($class) ? $class::getInstance() : M($name);
    }
    return $model;
}
