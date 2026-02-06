<?php
// app/Modelos/ConeionDB.php

class ConexionBD
{
    private static $conexion = null;

    public static function obtenerConexion()
    {
        if (self::$conexion === null) {

            $host = "localhost";
            $baseDatos = "centro";
            $usuario = "root";
            $password = "root123";

            try{
                $dsn = "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4";self::$conexion = new PDO($dsn, $usuario,$password);
                
                //Hacemos que PDO lance excepciones si hay errores
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }catch (PDOException $e) {
                //En un proyecto real no mostariamos detalles
                die("Error de conexion con al base de datos");
            }
        }

        return self::$conexion;
    }
}