<?php
require_once __DIR__ . '/../consts.php';
require_once DbContructUrl;
class NoticeManager {
    private DbContruct $db;
    private string $username;
    public function __construct(string $username) {
        if(!empty(trim($username))) {
            $this->createUserFolder();
            $this->db = new DbContruct($this->getDatabaseUrl());
            $this->username = $username;
        }
    }

    public function createUserFolder(): void {
        $path = __DIR__ . "/../notices/";

        if(!file_exists($path)) {
            mkdir($path, 0770, true);
        }
    }

    public function addNotice(string $title, string $description, string $photo, int $price): void {
        $this->createTable();

        $this->db->add(DefaultTablename, [
            'title'         => $title,
            'description'   => $description,
            'username'      => $this->username,
            'price'         => $price,
            'photo'         => $photo,
            'status'        => 1,
            'creation_date' => time()
        ]);
    }

    public function deleteNotice(int $noticeid): bool {
        return $this->db->remove(DefaultTablename, 'noticeid', $noticeid);
    }

    public function getCount(): int {
        return $this->db->getCountWhere(DefaultTablename, 'username', $this->username);
    }

    public function getNotices(int $offset, int $limit, string $asc): array {
        $this->createTable();

        return $this->db->getWithOffsetAndLimitWhere(DefaultTablename, $offset, $limit, 'username', $this->username, 'noticeid', $asc);
    }

    public function getNotice(int $noticeid): array {
        $this->createTable();

        return $this->db->getWithOffsetAndLimitWhere(DefaultTablename, 0, 1, 'noticeid', $noticeid, 'noticeid', 'ASC');
    }

    public function getAllNotices(int $offset, int $limit, string $asc): array {
        $this->createTable();

        return $this->db->getWithOffsetAndLimit(DefaultTablename, $offset, $limit, 'noticeid', $asc);
    }

    private function getDatabaseUrl(): string {
        return  __DIR__ . "/../notices/" . DefaultTablename . '.db';
    }

    private function createTable(): void {
        $this->db->createTable(DefaultTablename, [
            'noticeid'        => 'INTEGER PRIMARY KEY',
            'photo'           => 'TEXT',
            'username'        => 'TEXT',
            'title'           => 'TEXT',
            'status'          => 'INTEGER',
            'description'     => 'TEXT',
            'price'           => 'INTEGER',
            'creation_date'   => 'INTEGER'
        ]);
    }
}