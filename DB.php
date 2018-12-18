<?php
class DB
{
    private $database = 'mysql';
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $single;

    public function __construct($config)
    {
        $this->host = $config['host'];
        $this->dbName = $config['dbName'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->connect();
    }

    private function connect()
    {
        $dsn = "{$this->database}:host={$this->host};dbname={$this->dbName}";
        $this->single = new PDO($dsn, $this->username, $this->password);
        $this->single->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
        if (!$handle = $this->single->query($sql))
            return [];

        return $handle->fetchAll(PDO::FETCH_ASSOC);
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