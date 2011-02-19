<?php
//http://localhost:8082/tests/gam_couchdb/examples.php
include('Nov_CouchDb.php');

////////////////////

// Object Mapping

class Nov_CouchDb_Conf
{
    const CDB1 = 'CDB1';
    
    private static $_conf = array(
        self::CDB1 => array(
            'protocol' => 'http',
            'host'     => 'localhost',
            'port'     => 5984, 
            'user'     => null,
            'password' => null
            )
        );
        
    static function get($key) 
    {
        return self::$_conf[$key];
    }
}

class Nov_CouchDb_Orm
{
    static protected $_db;

    /**
     * @param string $key
     * @return Nov_CouchDb
     */
    static public function connect($key)
    {
        $class = get_called_class();
        extract(Nov_CouchDb_Conf::get($key));
        
        $couchDb = new Nov_CouchDb($host, $port, $protocol, $user, $password);
        return $couchDb->db($class::$_db);
    }
    

}

class CDB_Users extends Nov_CouchDb_Orm {
    static $_db = 'users';
}


CDB_Users::connect(Nov_CouchDb_Conf::CDB1)->insert('gonzalo', array('password' => 'g1'));
$data = CDB_Users::connect(Nov_CouchDb_Conf::CDB1)->select('gonzalo')->asArray();;
print_r($data);

CDB_Users::connect(Nov_CouchDb_Conf::CDB1)->update('gonzalo', array('password' => 'g2'));
$data = CDB_Users::connect(Nov_CouchDb_Conf::CDB1)->select('gonzalo')->asArray();
print_r($data);

CDB_Users::connect(Nov_CouchDb_Conf::CDB1)->delete('gonzalo');
