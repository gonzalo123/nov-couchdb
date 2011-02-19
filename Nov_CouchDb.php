<?php
require_once('Nov_Http.php');

class Nov_CouchDb_Exception_NoDataFound extends Nov_CouchDb_Exception{}
class Nov_CouchDb_Exception_DupValOnIndex extends Nov_CouchDb_Exception{}

class Nov_CouchDb_Exception extends Exception
{
    const ERROR_PARSING_OUTPUT = -1;
    const ERROR_WITH_RESULSET = -2;
    
    const NO_DATA_FOUND = 1;
}

class Nov_CouchDb
{
    private $_protocol;
    private $_host;
    private $_port;
    private $_user;
    private $_password;

    public function __construct($host, $port=Nov_Http::DEF_PORT , $protocol=Nov_Http::HTTP, $user = null, $password=null)
    {
        $this->_host     = $host;
        $this->_port     = $port;
        $this->_protocol = $protocol;
        $this->_user     = $user;
        $this->_password = $password;
    }

    private $_db;
    /**
     * @param string $db
     * @return Nov_CouchDb
     */
    public function db($db)
    {
        $this->_db = $db;
        return $this;
    }
    
    private function _manageExceptions(Nov_Http_Exception $e)
    {
        switch ($e->getCode()) {
            case Nov_Http_Exception::NOT_FOUND:
                throw new Nov_CouchDb_Exception_NoDataFound('No Data Found');
                break;
            case Nov_Http_Exception::CONFLICT:
                throw new Nov_CouchDb_Exception_DupValOnIndex('Dup Val On Index');
                break;
            default:
                throw new Nov_CouchDb_Exception($e->getMessage(), $e->getCode());
                break;
            
        }
    }
    /**
     * @param string $key
     * @return Nov_CouchDb_Resulset
     */
    public function select($key)
    {
        try {
            $out = Nov_Http::connect($this->_host, $this->_port, $this->_protocol)
                ->setCredentials($this->_user, $this->_password)
                ->doGet("{$this->_db}/{$key}");
        } catch (Nov_Http_Exception $e) {
            $this->_manageExceptions($e);
        }
        return new Nov_CouchDb_Resulset($out);
    }

    /**
     * @param string $key
     * @param array $values
     * @return Nov_CouchDb_Resulset
     */
    public function insert($key, $values)
    {
        try {
            $out = Nov_Http::connect($this->_host, $this->_port, $this->_protocol)
                ->setCredentials($this->_user, $this->_password)
                ->setHeaders(array('Content-Type' =>  'application/json'))
                ->doPut("{$this->_db}/{$key}", json_encode($values));
        } catch (Nov_Http_Exception $e) {
            $this->_manageExceptions($e);
        }
        return new Nov_CouchDb_Resulset($out);
    }

    /**
     * @param string $key
     * @param array $values
     * @return Nov_CouchDb_Resulset
     */
    public function update($key, $values)
    {
        try {
            $http = Nov_Http::connect($this->_host, $this->_port, $this->_protocol)
                ->setCredentials($this->_user, $this->_password);
            $out = $http->doGet("{$this->_db}/{$key}");
            $reg = json_decode($out);
            $out = $http->setHeaders(array('Content-Type' =>  'application/json'))
                ->doPut("{$this->_db}/{$key}", json_encode($reg));
        } catch (Nov_Http_Exception $e) {
            $this->_manageExceptions($e);
        }
        return new Nov_CouchDb_Resulset($out);
    }

    /**
     * @param string $key
     * @return Nov_CouchDb_Resulset
     */
    public function delete($key)
    {
        try {
            $http = Nov_Http::connect($this->_host, $this->_port, $this->_protocol)
                ->setCredentials($this->_user, $this->_password);
            $out = $http->doGet("{$this->_db}/{$key}");
            $reg = json_decode($out);
            $out = $http->doDelete("{$this->_db}/{$key}", array('rev' => $reg->_rev));
        } catch (Nov_Http_Exception $e) {
            $this->_manageExceptions($e);
        }
        return new Nov_CouchDb_Resulset($out);
    }
}

class Nov_CouchDb_Resulset
{
    private $_data;

    function __construct($data)
    {
        $this->_data = $data;
    }

    function asArray()
    {
        return (array) json_decode($this->_data);
    }

    function asJson()
    {
        return $this->_data;
    }

    function asObject()
    {
        return json_decode($this->_data);
    }
}