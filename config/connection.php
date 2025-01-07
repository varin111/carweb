<?php
require_once __DIR__ . '/vars.php';
require_once __DIR__ . '/functions.php';
date_default_timezone_set('Asia/Baghdad');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->error);
}
$conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

function auth(): array
{
    $user_id = getSession('user_id');
    if (!$user_id) {
        $user_id = $_COOKIE['user_login'];
    }
    $user = query_select('users', '*', "id = $user_id");
    return $user;
}

function selectAll(string $table, string $columns = '*', string $where = null, int $limit = null, int $offset = null): array
{
    global $conn;
    $sql = "SELECT $columns FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $sql .= " ORDER BY $table.id DESC";
    if ($limit !== null) {
        $sql .= " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function query_select(string $table, string $columns = '*', string $where = null): array
{
    global $conn;
    $sql = "SELECT $columns FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0)  ? $result->fetch_all(MYSQLI_ASSOC)[0] : [];
}

function query_select_with_join(string $table, string $join, string $columns = '*', string $where = null, int $limit = null, int $offset = null,string $group_by = null): array
{
    global $conn;
    $sql = "SELECT $columns FROM $table $join";
    if ($where) {
        $sql .= " WHERE $where";
    }
    if ($group_by) {
        $sql .= " GROUP BY $group_by";
    }
    $sql .= " ORDER BY $table.id DESC";
    if ($limit !== null) {
        $sql .= " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }
    }
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function query_insert(string $table, array $data): void
{
    global $conn;
    $columns = implode(", ", array_keys($data));
    $values = "'" . implode("', '", array_values($data)) . "'";
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
    $conn->query($sql);
}

function query_update(string $table, array $data, string $where): void
{
    global $conn;
    $set = '';
    foreach ($data as $key => $value) {
        $set .= "$key = '$value', ";
    }
    $set = rtrim($set, ', ');
    $sql = "UPDATE $table SET $set WHERE $where";
    $conn->query($sql);
}

function query_delete(string $table, string $where): void
{
    global $conn;
    $sql = "DELETE FROM $table WHERE $where";
    $conn->query($sql);
}

function count_query(string $table, string $where = null): int
{
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC)[0]['count'] : 0;
}

function data_exists(string $table, string $join, string $columns = '*', string $where = null): bool
{
    global $conn;
    $sql = "SELECT $columns FROM $table $join";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? true : false;
}

function run_sql(string $sql): bool|mysqli_result   
{
    global $conn;
    return $conn->query($sql);
}
