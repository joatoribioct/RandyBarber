<?php

namespace Controllers;

use Clases\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                //comprobar que exista el usuario
                
                $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    if($usuario->comprobarContraseñayVerificado($auth->contraseña)) {
                        //Autentificar al usuario todo listo
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redirecionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('location: /admin');
                            

                        } else {
                            header('location: /cita');

                        }


                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            } 
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }
    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {
                    $usuario->crearToken();
                    $usuario->guardar();

                    //enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstruciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');

                } else {
                    Usuario::setAlerta('error', 'Usuario no existe o no esta confirmado');
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/olvide', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        //Buscar usuario por token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Valido');
            $error = true;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //leer la nueva contraseña y guardarlo
            $contraseña = new Usuario($_POST);
            $alertas = $contraseña->ValidarNuevaContraña();
            

            if(empty($alertas)) {
                $usuario->contraseña = null;
            
                $usuario->contraseña = $contraseña->contraseña;
                $usuario->hashPassword();
                $usuario->token = null;
                
                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
            
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/recuperar', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
    public static function crear(Router $router) {
        $usuario =  new Usuario;

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validarNuevacuenta();

            // Revisar que alerta este vacio

            if(empty($alertas)) {
                // VERIFICAR QUE EL USUARIO NO ESTE REGISTRADO
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear la contraseña
                    $usuario->hashPassword();

                    // GENERAR UN TOKEN UNICO
                    $usuario->crearToken();
                    //enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    //crear el usuario
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                    
                    
                }
            }
        }
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');

    }
    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            // modificar usuario confurnadi

           

            $usuario->confirmado = "1";
            $usuario->token = null;

            $usuario->guardar();
            
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

}