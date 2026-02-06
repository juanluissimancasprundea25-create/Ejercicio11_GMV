<?php
//public/index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

//Incluimos el controlador
require_once __DIR__ . '/../app/Controladores/ControladorAlumnos.php';

//Creamos el controlador 
$controlador = new ControladorAlumnos();

//Leemos la accion (si no viene, listar)
$accion = $_GET['accion'] ?? 'listar';

//Decidimos que hacer
switch ($accion) {
    case 'listar':
        $controlador->listar();
        break;
    case 'crear':
        $controlador->crear();
        break;
    case 'guardar':
        $controlador->guardar();
        break;
    case 'borrar':
        $controlador->borrar();
        break;
    default:
        echo "Accion no valida";
}