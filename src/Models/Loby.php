<?php

namespace app\Models;
use app\Core\Config;
use app\Core\Request;
use PDO;

class Loby extends AbstractModel{

    public function daj_aktivnosti(){
        /*promeni malooo */
        $query = <<<EOD
        select naziv from preduzeca
        limit 3;
        EOD;

        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez;
        }

    }

    public function daj_preduzeca(){
        $query = <<<EOD
            select naziv from preduzeca
            limit 3;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez;
        }

    }
}