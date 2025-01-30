<?php
require_once __DIR__ . '/../consts.php';
require_once DbContructUrl;
class AuthManager {
    private DbContruct $db;
    public string $token;
    public function __construct() {
        $this->db = new DbContruct(DbUsersUrl);
    }
    public function createUser(string $first_name, string $username, string $password): int {
        $this->createUsersTable();  

        if(!empty($this->db->get(DefaultTablename, 'username', $username))) {
            return 0;
        }

        $this->token = bin2hex(random_bytes(16));

        $this->db->add(DefaultTablename, [
            'first_name'    => $first_name,
            'creation_date' => time(),
            'username'      => $username,
            'password'      => $password,
            'token'         => $this->token
        ]);

        return 200;
    }

    public function setAvatar(string $avatar, string $username): mixed {
        return $this->db->set(DefaultTablename, 'username', $username, [
            'avatar'    => $avatar
        ]);
    }

    public function getAvatarByUsername(string $username): mixed {
        return $this->db->get(DefaultTablename, 'username', $username)['avatar'] ?? null;
    }

    public function getAvatarByToken(string $token): mixed {
        return $this->db->get(DefaultTablename, 'token', $token)['avatar'] ?? null;
    }

    public function getTokenByUsername(string $username): mixed {
        return $this->db->get(DefaultTablename, 'username', $username)['token'] ?? null;
    }

    public function getUsernameByToken(string $token): mixed {
        return $this->db->get(DefaultTablename, 'token', $token)['username'] ?? null;
    }

    public function getFirstNameByToken(string $token): mixed {
        return $this->db->get(DefaultTablename, 'token', $token)['first_name'] ?? null;
    }

    public function getFirstNameByUsername(string $username): mixed {
        return $this->db->get(DefaultTablename, 'username', $username)['first_name'] ?? null;
    }


    public function getCreationDateByToken(string $token): mixed {
        return $this->db->get(DefaultTablename, 'token', $token)['creation_date'] ?? null;
    }

    public function getPasswordByUsername(string $username): mixed {
        return $this->db->get(DefaultTablename, 'username', $username)['password'] ?? null;
    }

    private function createUsersTable(): void {
        $this->db->createTable(DefaultTablename, [
            'userid'        => 'INTEGER PRIMARY KEY',
            'avatar'        => 'TEXT',
            'first_name'    => 'TEXT',
            'username'      => 'TEXT',
            'password'      => 'TEXT',
            'token'         => 'TEXT',
            'creation_date' => 'INTEGER'
        ]);
    }
}