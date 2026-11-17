<?php

require_once "../service/workoutService.php";

header('Content-Type : application/json');
$methode = $_SERVER["REQUEST_METHOD"];
$workoutservice = new workoutService;
if($methode == "GET"){
    echo json_encode($workoutservice->listerGroupeMusculaire());
}
if($methode == "POST"){
    echo json_encode($workoutservice->ajouterGroupeMusculaire());
}