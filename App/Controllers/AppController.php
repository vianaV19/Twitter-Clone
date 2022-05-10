<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController  extends Action
{

    public function timeline()
    {

        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');
        $tweet->__set('id_usuario', $_SESSION['id']);

        $usuario = Container::getModel('usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->seguidores = $usuario->getSeguidores();
        $this->view->seguindo = $usuario->getSeguindo();
        $this->view->numTweets = $usuario->getTotalTweets();

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $this->render('timeline');
    }

    public function quemseguir()
    {
        $this->validaAutenticacao();

        $usuario = Container::getModel('usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->seguidores = $usuario->getSeguidores();
        $this->view->seguindo = $usuario->getSeguindo();
        $this->view->numTweets = $usuario->getTotalTweets();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if ($pesquisarPor != '') {

            $usuario = Container::getModel('usuario');

            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);

            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');
    }

    public function tweet()
    {

        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';

        $id = isset($_GET['id']) ? $_GET['id'] : '';

        if (!empty($acao)) {

            $tweet = Container::getModel('tweet');

            if ($acao  == 'salvar') {
                $tweet->__set('id_usuario', $_SESSION['id']);
                $tweet->__set('tweet', $_POST['tweet']);

                $tweet->salvar();
            } else if ($acao == 'remover') {
                $tweet->__set('id', $id);

                $tweet->remover();
            }
        }


        header('location: /timeline');
    }

    public function validaAutenticacao()
    {
        session_start();

        if (!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome']))  header('location: /?login=erro');
    }

    public function acao()
    {
        $this->validaAutenticacao();

        // print_r($_GET);
        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        if (!empty($acao) && !empty($id_usuario)) {
            $usuarioSeguidores = Container::getModel('UsuarioSeguidores');
            $usuarioSeguidores->__set('id_usuario', $_SESSION['id']);
            $usuarioSeguidores->__set('id_usuario_seguindo', $_GET['id_usuario']);

            if ($acao == 'seguir') {
                $usuarioSeguidores->seguir();
            } else if ($acao == 'deixar_de_seguir') {
                $usuarioSeguidores->deixarDeSeguir();
            }

            header('location: /quemseguir?pesquisarPor=' . $_GET['pesquisarPor']);
        }
    }
}
