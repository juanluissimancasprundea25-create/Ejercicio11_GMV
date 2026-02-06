<?php
//app/Modelos/RepositorioAlumnos.php

require_once __DIR__ . '/ConexionBD.php';
require_once __DIR__ . '/Alumno.php';

class RepositorioAlumnos
{
    private $conexion;

    function __construct()
    {
        $this->conexion = ConexionBD::obtenerConexion();
    }
    //CREATE: insertar alumno
    function insertar($alumnos)
    {
        $sql = "INSERT INTO alumnos (nombre, email, edad, fecha_creacion)
        VALUES (:nombre, :email, :edad, :fecha)";
        $stmt = $this->conexion->prepare($sql);

        //Consulta preparada: evita inyeccion SQL
        $stmt->execute([
            ':nombre' => $alumnos->nombre,
            ':email' => $alumnos->email,
            ':edad' => $alumnos->edad,
            ':fecha' => $alumnos->fechaCreacion
        ]);
        
    }

    //READ : obtener todos
    function obtenerTodos()
    {
        $sql = "SELECT * FROM alumnos ORDER BY fecha_creacion DESC";
        $stmt = $this->conexion->query($sql);
        $alumnos = [];

        //Leemos fila por fila como array asociativo
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {

        //Convertimos el array asociativo en objeto Alumno
        $alumnos[] = new Alumno(
            $fila['id'],
            $fila['nombre'],
            $fila['email'],
            $fila['edad'],
            $fila['fecha_creacion']
        );
        }
        return $alumnos;
    }

    //DELETE: borrar por id
    function borrarPorId($id)
    {
        $sql = "DELETE FROM alumnos WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}