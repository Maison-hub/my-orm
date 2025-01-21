<?php
namespace iutnc\hellokant\model;

use Exception;
use iutnc\hellokant\Connection\ConnectionFactory;
use iutnc\hellokant\query\Query;

class BaseModel
{
    private array $data;
    protected static string $table;

    public function __construct(?array $data = null)
    {
        $this->data = $data ?? [];
        unset($this->data['table']);
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * @throws Exception
     */
    public function save(): void
    {
        $query = Query::table(static::$table);
        if (isset($this->data['id'])) {
            $query->where('id', '=', $this->data['id'])->update($this->data);
        } else {
            $this->data['id'] = $query->insert($this->data);
        }
    }

    /**
     * @throws Exception
     */
    public function delete(): void
    {
        $query = Query::table(static::$table);
        if (isset($this->data['id'])) {
            $query->where('id', '=', $this->data['id'])->delete();
        }
        else{
            throw new Exception('No id to delete');
        }
    }

    /**
     * @throws Exception
     */
    public static function all(): array
    {
        $query = Query::table(static::$table);
        return $query->get();
    }

    /**
     * @param array $criteria
     * @param array $fields
     * @return BaseModel
     * @throws Exception
     */
    public static function find(array|int $criteria, array $fields = ['*']): BaseModel | array | null
    {
        $query = Query::table(static::$table)->select($fields);

        if (is_int($criteria)) {
            $query->where('id', '=', $criteria);
            $results = $query->get();
            return count($results) === 0 ? null : new static($results[0]);
        }elseif (is_array($criteria)) {
            foreach ($criteria as $criterion) {
                [$col, $op, $val] = $criterion;
                $query->where($col, $op, $val);
            }
        }

        $results = $query->get();
        if (count($results) === 0) {
            return null;
        }
        return array_map(function($attributes) {
            return new static($attributes);
        }, $results);
    }

    /**
     * @param string $relatedModel
     * @param string $foreignKey
     * @return BaseModel|null
     * @throws Exception
     */
    public function belongs_to(string $relatedModel, string $foreignKey): ?BaseModel
    {
        $relatedClass = "iutnc\\hellokant\\model\\$relatedModel";
        $relatedClass = new $relatedClass();
        $relatedTable = $relatedClass::$table;
        $relatedId = $this->data[$foreignKey];
        $query = Query::table($relatedTable)->where('id', '=', $relatedId);
        $result = $query->get();

        if (count($result) === 0) {
            return null;
        }

        return new $relatedClass($result[0]);
    }

    /**
     * @param string $relatedModel
     * @param string $foreignKey
     * @return BaseModel[]
     * @throws Exception
     */
    public function has_many(string $relatedModel, string $foreignKey): array
    {
        $relatedClass = "iutnc\\hellokant\\model\\$relatedModel";
        $relatedClass = new $relatedClass();
        $relatedTable = $relatedClass::$table;
        $relatedId = $this->data['id'];
        $query = Query::table($relatedTable)->where($foreignKey, '=', $relatedId);
        $results = $query->get();

        return array_map(function($attributes) use ($relatedClass) {
            return new $relatedClass($attributes);
        }, $results);
    }

}