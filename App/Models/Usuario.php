<?php

namespace App\Models;

use MF\Model\Model;
use PDOException;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __set($atr, $val)
    {
        $this->$atr = $val;
    }

    public function __get($atr)
    {
        return $this->$atr;
    }

    //salvar
    public function salvar()
    {
        $query = 'insert into usuarios (nome, email, senha) values (?,?,?)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('nome'));
            $stmt->bindValue(2, $this->__get('email'));
            $stmt->bindValue(3, $this->__get('senha'));

            $stmt->execute();

            return true;
        } catch (PDOException $p) {
            echo 'Erro ao salvar usuario! <br>';
            echo $p->getMessage();
            return false;
        }
    }
    //validar
    public function validar()
    {

        if (strlen($this->__get('nome')) < 3) return false;

        if (strlen($this->__get('email')) < 3) return false;

        if (strlen($this->__get('senha')) < 3) return false;

        return true;
    }

    //autenticar
    public function autenticar()
    {
        $query = "select id, nome, email from usuarios where email = ? and senha = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('email'));
            $stmt->bindValue(2, $this->__get('senha'));

            $stmt->execute();

            $usuario = $stmt->fetch();

            if (!empty($usuario['id']) && !empty($usuario['nome'])) {
                $this->id = $usuario['id'];
                $this->nome = $usuario['nome'];
            }

            return $this;
        } catch (\PDOException $p) {
            echo $p->getMessage();
        }
    }

    //deletar
    public function getAll()
    {
        $query = "
        select 
            id, 
            email, 
            nome,
            (select
                count(*)
            from 
                usuario_seguidores as us
            where 
                us.id_usuario = :id and us.id_usuario_seguindo = u.id
            ) as seguindo_sn
        from 
            usuarios as u
        where 
            nome like :nome and id  != :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', "%" . $this->__get('nome') . "%");
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    //recuperar um usuario por email
    public function getUsuarioPorEmail()
    {
        $query = 'select email from usuarios where email = ?';
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $this->__get('email'));

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getSeguidores(){
        $query = "select count(*) as seguidores from usuario_seguidores
        where id_usuario_seguindo = :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario", $this->__get('id'));

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getSeguindo(){
        $query = "select count(*) as seguindo from usuario_seguidores
        where id_usuario = :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario", $this->__get('id'));

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getTotalTweets(){
        $query = "select count(*) as totalTweets from tweet
        where id_usuario = :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario", $this->__get('id'));

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
