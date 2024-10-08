<?php

namespace Model;

class Usuario extends ActiveRecord {
    //base de datos

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'contraseña', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $contraseña;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->contraseña = $args['contraseña'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }
    // Mensaje de validacion para crear cuenta
    public function validarNuevacuenta() {
        if(!$this->nombre) {
            self::$alertas['error'] [] = 'El Nombre es obligatorio';
        }
        if(!$this->apellido) {
            self::$alertas['error'] [] = 'El Apellido es obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'] [] = 'El Email es obligatorio';
        }
        if(!$this->contraseña) {
            self::$alertas['error'] [] = 'La Contraseña es obligatoria';
        }
        if(strlen($this->contraseña) < 6) {
            self::$alertas['error'] [] = 'La Contraseña debe conter al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'] [] = 'El Email es obligatorio';
        }
        if(!$this->contraseña) {
            self::$alertas['error'] [] = 'La Contraseña es obligatoria';
        }
        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'] [] = 'El Email es obligatorio';
        }
        return self::$alertas;

    }

    public function ValidarNuevaContraña() {
        if(!$this->contraseña) {
            self::$alertas['error'] [] = 'La Contraseña es obligatoria';
        }
        if(strlen($this->contraseña) < 6) {
            self::$alertas['error'] [] = 'La Contraseña debe conter al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        
        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El Usuario ya existe';
        }

        return $resultado;
    }

    public function hashPassword() {
        $this->contraseña = password_hash($this->contraseña, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarContraseñayVerificado($contraseña) {
        $resultado = password_verify($contraseña, $this->contraseña);

        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = "Contraseña Incorrecta o tu cuenta no ha sido confirmada";
        } else {
            return true;
        }
    }
}