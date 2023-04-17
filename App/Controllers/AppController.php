<?php

namespace App\Controllers;


use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
    public function timeline()
    {

        $this->validaAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario', $_SESSION['id']);
        
        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;
        
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->infoUsuario =  $usuario->getInfoUsuarios();
        $this->view->totalTweets = $usuario->getTotalTweets();
        $this->view->totalSeguindo = $usuario->getSeguindo();
        $this->view->totalSeguidores = $usuario->getSeguidores();


        $this->render('timeline');

    }

    public function tweet()
    {
        $this->validaAuth();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header("Location: /timeline");
    }

    public function validaAuth(){
        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == "" && $_SESSION['nome'] == "" || !isset($_SESSION['nome'])) {
            header("Location: /?login=error");
        }
    }

    public function quemSeguir(){
        $this->validaAuth();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if($pesquisarPor != ""){
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();

        }
        $this->view->usuarios = $usuarios;


        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->infoUsuario =  $usuario->getInfoUsuarios();
        $this->view->totalTweets = $usuario->getTotalTweets();
        $this->view->totalSeguindo = $usuario->getSeguindo();
        $this->view->totalSeguidores = $usuario->getSeguidores();

        $this->render('quemSeguir');
    }

    public function acao(){
        $this->validaAuth();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $idUsuarioSeguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguirUsuario($idUsuarioSeguindo);
        }else if($acao == 'deixar_de_seguir'){
            $usuario->deixarSeguirUsuario($idUsuarioSeguindo);
        }

        header('Location: /quem_seguir');
    }
}

?>