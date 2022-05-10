<?php

namespace App\Models;

use MF\Model\Model;
use PDOException;

class UsuarioSeguidores extends Model {

    private $id;
    private $id_usuario;
    private $id_usuario_seguindo;

    public function __set($atr, $val)
    {
        $this->$atr = $val;
    }

    public function __get($atr)
    {
        return $this->$atr;
    }

    public function seguir(){
        $query = "insert into usuario_seguidores (id_usuario, id_usuario_seguindo) values (:id_usuario, :id_usuario_seguindo)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id_usuario_seguindo', $this->__get('id_usuario_seguindo'));

        $stmt->execute();

        return true;

    }
    public function deixarDeSeguir(){
        $query = "delete from usuario_seguidores where id_usuario_seguindo = :id_usuario_seguindo and id_usuario = :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario_seguindo', $this->__get('id_usuario_seguindo'));
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));

        $stmt->execute();

        return true;
    }

}
