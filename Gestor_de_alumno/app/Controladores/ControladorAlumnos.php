<?php
//app/Controladores/ControladorAlumnos.php

require_once __DIR__ . '/../Modelos/RepositorioAlumnos.php';

class ControladorAlumnos
{
    private $repositorio;

    function __construct()
        {
            $this->repositorio = new RepositorioAlumnos();
        }

        // LISTAR
        function listar()
        {
            try {
                $alumnos = $this->repositorio->obtenerTodos();
                $this->renderizar('alumnos/listar', ['alumnos' => $alumnos]);

            }catch (Exception $e) {
                $this->registrarError("LISTAR", $e);
                $this->renderizar('alumnos/listar', [
                    'alumnos' => [],
                    'error' => 'No se pudieron cargar los alumnos. Revisar errores.log'
                ]);
            }
        }

        // MOSTRAR FORMULARIO
        function crear()
        {
            $this->renderizar('alumnos/crear', [
                'antiguos' => ['nombre' => '', 'email' => '', 'edad' => '']
            ]);
        }
        
        // GUARDAR
        function guardar()
        {
            $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if (($metodo !== 'POST')) {
                header("Location: index.php?accion=listar");
                exit;
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? "");
            $edad = trim($_POST['edad'] ?? "");

            try {
                $this->validar($nombre, $email, $edad);
                
                $alumnos = new Alumno(
                    null,
                    $nombre,
                    $email,
                    (int)$edad,
                    date('Y-m-d H:i:s')
                );

                $this->repositorio->insertar($alumnos);
                
                exit;
            }catch (Exception $e) {
                $this->registrarError("GUARDAR", $e);

                $this->renderizar('alumnos/crear', [
                    'error' => $e->getMessage(),
                    'antiguos' => [
                        'nombre' => $nombre,
                        'email' => $email,
                        'edad' => $edad
                    ]
                ]);
            }
        }

    // BORRAR
    function borrar()
    {
        $id = $_GET['id'] ?? '';

        try {
            if ($id === '' || !ctype_digit($id)) {
                throw new Exception("Id invalido para borrar");
            }

            $this->repositorio->borrarPorId($id);

        }catch (Exception $e) {
            $this->registrarError("BORRAR", $e);
        }

        header("Location: index.php?accion=listar");
        exit;
    }

    //VALIDACIONES
    function validar($nombre, $email, $edad)
    {
        if (strlen($nombre) < 3) {
            throw new Exception("El nombre debe tener al menos 3 caracteres");
        }

        //email opcional, pero si existe debe ser valido
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es valido");
        }

        if ($edad === '' || !ctype_digit($edad)) {
            throw new Exception("La edad debe ser un numero");
        }

        $edadNum = (int)$edad;
        if ($edadNum < 1 || $edadNum > 120) {
            throw new Exception("La edad debe estar entre 1 y 120");
        }
    }

    // RENDERIZAR 
    function renderizar($vista, $datos = [])
    {
        extract($datos);
        
        $archivoVista = __DIR__ . '/../Vistas/' . $vista . '.php';
        if(!file_exists($archivoVista)) {
            echo "Vista no encontrada: " . $vista;
            return;
        }

        $vistacontenido = $archivoVista;

        require __DIR__ . '/../Vistas/layout.php';
    }

    //LOG de errores en fichero
    function registrarError($contexto, $e)
    {
        $rutaLog = __DIR__ . '/../../storage/errores.log';
        $fecha = date('Y-m-d H:i:s');

        $linea = $fecha . " | " . $contexto . " | " . $e->getMessage(). " | " . $e->getFile() . " | " . $e->getLine() . "\n";
        file_put_contents($rutaLog, $linea, FILE_APPEND);
    }
}