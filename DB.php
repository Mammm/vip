<?php
class DB
{
    private $database = 'mysql';
    private $host;
    private $dbName;
    private $userName;
    private $passWord;
    private $single;

    public function __construct()
    {
        $this->host = '112.74.212.63';
        $this->dbName = 'inman_b2c';
        $this->userName = 'inman_b2c';
        $this->passWord = 'c7BUdoEcjk';
        $this->connect();
    }

    private function connect()
    {
        $dsn = "{$this->database}:host={$this->host};dbname={$this->dbName}";
        $this->single = new PDO($dsn, $this->userName, $this->passWord);
    }

    public function beginTransaction()
    {
        return $this->single->beginTransaction();
    }

    public function commit()
    {
        return $this->single->commit();
    }

    public function rollBack()
    {
        return $this->single->rollBack();
    }

    public function query($sql)
    {
        return ($this->single->query($sql))->fetchAll();
    }

    public function update($sql)
    {
        return $this->single->exec($sql);
    }

    public function delete($sql)
    {
        return $this->update($sql);
    }
}