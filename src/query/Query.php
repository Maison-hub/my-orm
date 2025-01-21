<?php
namespace iutnc\hellokant\query;
use iutnc\hellokant\Connection\ConnectionFactory;
use Exception;
use iutnc\hellokant\model\BaseModel;
use PDO;

class Query {
    private string $sqltable;
    private string $fields = '*';
    private ?array $where = null;
    private array $args = [];
    private string $sql = '';

    public static function table( string $table) : Query {
        $query = new Query;
        $query->sqltable= $table;
        return $query;
    }
    public function select( array $fields) : Query {
        $this->fields = implode( ',', $fields);
        return $this;
    }

    public function where(string $col,string $op, mixed $val) : Query {
        $this->where[] = $col . $op . '?';
        $this->args[]=$val;
        return $this;
    }

    /**
     * @throws Exception
     * @return BaseModel[]
     */
    public function get(): array {
        $this->sql = 'select ' . $this->fields . ' from ' . $this->sqltable;
        if ($this->where) {
            $this->sql .= ' where ' . implode(' and ', $this->where);
        }
        $stmt = $this->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws Exception
     */
    public function delete() : void {
        $this->sql = 'delete from '  . $this->sqltable . " where " . implode(' and ', $this->where);
        $this->execute();
    }

    /**
     * @throws Exception
     */
    public function insert(array $data) : string {
        $this->sql = 'INSERT INTO ' . $this->sqltable . ' (' . implode(',', array_keys($data)) . ') values (' . implode(',', array_fill(0, count($data), '?')) . ')';
        $this->args = array_values($data);
        $this->execute();
        return ConnectionFactory::getConnection()->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function update(array $data) : void {$this->sql = 'update ' . $this->sqltable . ' set ';
        $this->sql .= implode('=?,', array_keys($data)) . '=? where ' . implode(' and ', $this->where);
        $this->args = array_merge(array_values($data), $this->args);
        $this->execute();
    }

    /**
     * @throws Exception
     */
    private function execute(): \PDOStatement {
        $pdo = ConnectionFactory::getConnection();
        $stmt = $pdo->prepare($this->sql);
        $stmt->execute($this->args);
        return $stmt;
    }
}