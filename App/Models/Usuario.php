<?php
namespace App\Models;
use MF\Model\Model;


class Usuario extends Model{

    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function salvar(){
        $query = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
    }

    public function validarCadastro(){
        $valido = true;

        if(strlen($this->__get('nome')) < 3){
            $valido = false;
        }

        if(strlen($this->__get('email')) < 3){
            $valido = false;
        }

        if(strlen($this->__get('senha')) < 3){
            $valido = false;
        }

        return $valido;

    }

    public function getUsuarioEmail(){
        $query = "SELECT email FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar(){
        $query = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($usuario['id'] != "" && $usuario['nome'] != ""){
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }
        
        return $this;
    }

    public function getAll(){
        $query = "SELECT u.id, u.nome, u.email, (SELECT count(*) FROM usuarios_seguidores as us WHERE us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id ) as seguindo_sn FROM usuarios as u WHERE u.nome LIKE :nome AND u.id != :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario($idUsuarioSeguindo){
        $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo) VALUES(:idUsuario, :idUsuarioSeguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->bindValue(':idUsuarioSeguindo', $idUsuarioSeguindo);
        $stmt->execute();

        return true;
    }

    public function deixarSeguirUsuario($idUsuarioSeguindo){
        $query = "DELETE FROM  usuarios_seguidores WHERE id_usuario = :idUsuario AND id_usuario_seguindo =  :idUsuarioSeguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->bindValue(':idUsuarioSeguindo', $idUsuarioSeguindo);
        $stmt->execute();

        return true;
    }

    public function getInfoUsuarios(){
        $query = "SELECT nome FROM usuarios WHERE id = :idUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalTweets(){
        $query = "SELECT count(*) as totalTweets FROM tweets WHERE id_usuario = :idUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getSeguindo(){
        $query = "SELECT count(*) as totalSeguindo FROM usuarios_seguidores WHERE id_usuario = :idUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getSeguidores(){
        $query = "SELECT count(*) as totalSeguidores FROM usuarios_seguidores WHERE id_usuario_seguindo = :idUsuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idUsuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}

?>