<?php
//http://localhost:8082/tests/gam_couchdb/examples.php
include('Nov_CouchDb.php');

$couchDb = new Nov_CouchDb('localhost', 5984);

// INSERT
$couchDb->db('users')->insert('gonzalo', array('password' => "g1"));

// SELECT
$data = $couchDb->db('users')->select('gonzalo')->asArray();
print_r($data);

//UPDATE
$couchDb->db('users')->update('gonzalo', array('password' => 'g2'));

//DELETE
$out = $couchDb->db('users')->delete('gonzalo')->asArray();
print_r($out);

// Different outputs
$data = $couchDb->db('users')->select('dummy')->asArray();
print_r($data);

$data = $couchDb->db('users')->select('dummy')->asObject();
print_r($data);

$data = $couchDb->db('users')->select('dummy')->asJson();
print_r($data);

// Exceptions tests
try {
    $couchDb->db('users')->update('gonzalo', array('password' => 'g2'));
} catch (Nov_CouchDb_Exception_NoDataFound $e) {
    echo "No data found \n";
}

$couchDb->db('users')->insert('gonzalo1', array('password' => "g1"));
try {
    $couchDb->db('users')->insert('gonzalo1', array('password' => "g1"));
} catch (Nov_CouchDb_Exception_DupValOnIndex $e) {
    echo "Dup Val On Index \n";
}
try {
    $couchDb->db('users')->delete('gonzalo');
} catch (Nov_CouchDb_Exception_NoDataFound $e) {
    echo "No data found \n";
}   

$couchDb->db('users')->delete('gonzalo1');