<?php


namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AuthController  extends Action {

    public function sair()
    {
        session_start();
        
        session_destroy();

        header('location: /');
    }

    public function autenticar(){
        // print_r($_POST);

        $usuario = Container::getModel('usuario');
        
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        $usuario->autenticar();

        if(!empty($usuario->__get('id')) && !empty($usuario->__get('nome'))){
            session_start();

            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            header('location: /timeline');
        }else{
            header('location: /?login=erro');
        }
    }
}