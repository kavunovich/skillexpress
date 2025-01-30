<?php
class DbContruct {
    private $pdo;
    private $dbPath;

    public function __construct($dbPath) {
        $this->dbPath = $dbPath;

        try {
            $this->pdo = new PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    private function checkTableExists($table): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=:table");
            $stmt->execute(['table' => $table]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createTable($table, $columns): bool {
        try {
            $columnsSql = [];
            foreach ($columns as $name => $type) {
                $columnsSql[] = "$name $type";
            }
            $columnsSql = implode(', ', $columnsSql);
            $sql = "CREATE TABLE IF NOT EXISTS $table ($columnsSql)";
            $this->pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get($table, $field, $value): mixed {
        if (!$this->checkTableExists($table)) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $field = :value");
            $stmt->execute(['value' => $value]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function set($table, $field, $value, $data): bool {
        if (!$this->checkTableExists($table)) {
            return false;
        }
        try {
            $fields = [];
            foreach ($data as $key => $val) {
                $fields[] = "$key = :$key";
            }
            $fields = implode(', ', $fields);
            $stmt = $this->pdo->prepare("UPDATE $table SET $fields WHERE $field = :value");
            $data['value'] = $value;
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add($table, $data): bool {
        if (!$this->checkTableExists($table)) {
            return false;
        }
        try {
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            $stmt = $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($values)");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function remove($table, $field, $value): bool {
        if (!$this->checkTableExists($table)) {
            return false;
        }
        try {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $field = :value");
            return $stmt->execute(['value' => $value]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getWithOffsetAndLimit($table, $offset, $limit, $orderBy, $asc): array {
        if (!$this->checkTableExists($table)) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $table ORDER BY $orderBy $asc LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getWithOffsetAndLimitWhere($table, $offset, $limit, $field, $value, $orderBy, $asc): array {
        if (!$this->checkTableExists($table)) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $field = :value ORDER BY $orderBy $asc LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->bindValue(':value', (string)$value, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getCountWhere($table, $field, $value): int {
        if (!$this->checkTableExists($table)) {
            return 0;
        }
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM $table WHERE $field = :value");
            $stmt->execute(['value' => $value]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getAll($table): array {
        if (!$this->checkTableExists($table)) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $table");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}