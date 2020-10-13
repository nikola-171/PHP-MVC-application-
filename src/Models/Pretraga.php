<?php

namespace app\Models;
use app\Core\Config;
use app\Core\Request;
use app\Core\SecurityMonitor;
use PDO;

class Pretraga extends AbstractModel{

    public function pretrazi_stranice($kriterijum){
        $query = <<<EOD
            select link,naslov from Pretraga
            where sadrzaj like '%${kriterijum}%';
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $rez;
    

    }
}