<?php

namespace App\Models;

use MF\Model\Model;
use PDOException;

class Tweet extends Model
{
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __set($atr, $val)
    {
        $this->$atr = $val;
    }

    public function __get($atr)
    {
        return  $this->$atr;
    }
    public function remover(){
        $query = "delete from tweet where id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));

        $stmt->execute();

        return true;
    }

    public function salvar()
    {
        $query = "insert into tweet (id_usuario, tweet) values (?,?)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('id_usuario'));
            $stmt->bindValue(2, $this->__get('tweet'));

            $stmt->execute();

            return $this;
        } catch (\PDOException $p) {
            echo $p->getMessage();
        }
    }

    public function getAll()
    {
        $query = "select 
            t.id,
            t.tweet, 
            date_format(t.data, '%d/%m/%y %h:%i') as data,
            t.id_usuario,
            u.nome 
        from 
            tweet as t 
        left join 
            usuarios as u
        on 
            (t.id_usuario = u.id) 
        where
             t.id_usuario = :id_usuario           
             or t.id_usuario in(select id_usuario_seguindo from usuario_seguidores where id_usuario = :id_usuario)  order  by t.data desc";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
