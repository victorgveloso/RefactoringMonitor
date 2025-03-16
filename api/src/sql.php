<?php

use Firebase\JWT\JWT;

function getCommitRefactoringsCount($connection, $projectID, $authorEmail) {
    
    $whereClause = "";
    if ($projectID != "") {
        $whereClause = "WHERE revisiongit.project = $projectID";
    }
    $q = "SELECT c.authorName , c.authorEmail, COUNT(c.id) AS refactoringCommitsCount
    FROM (
        SELECT DISTINCT revisiongit.id, revisiongit.authorName, revisiongit.authorEmail 
        FROM revisiongit
        INNER JOIN refactoringgit r ON r.revision = revisiongit.id
        $whereClause
        ) c
    GROUP BY c.authorEmail
    HAVING c.authorEmail = '$authorEmail';";
    $rows = getQueryRows($connection, $q);
    return $rows[0]["refactoringCommitsCount"];
}
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
function getQueryRows($connection, $query) {
    if (strpos($query, "INSERT") || strpos($query, "UPDATE") || strpos($query, "DELETE")) {
        $query = "$query RETURNING *";
    }
    $result = $connection->query($query);
    $rows = array();
    if ($result->numColumns() > 0) {
        while($res = $result->fetchArray()) {
            $rows[] = $res;
        }
    }
    return utf8ize($rows);
}
function selectQuery($connection, $query) {
    $rows = getQueryRows($connection, $query);
    return json_encode($rows);
}

function getProjectRows($connection, $projectID) {
    $whereClause = "";
    if ($projectID != "") {
        $projectID = SQLite3::escapeString($projectID);
        $whereClause = "WHERE projectgit.id = $projectID";
    } 
    $qur = "SELECT projectgit.*, COUNT(revisiongit.id) AS numberOfCommits
            FROM projectgit 
                LEFT OUTER JOIN revisiongit ON projectgit.id = revisiongit.project
            $whereClause
            GROUP BY projectgit.id";

    return getQueryRows($connection, $qur);
}
function getUser($jwt) {
    if (isset($jwt)) {
        try {
            $secretKey = base64_decode(getSecretKey());
            JWT::$leeway = 60;
            $token = JWT::decode($jwt, $secretKey, array('HS512'));
            return $token->data;
        } catch (Exception $e) {
            echo $e;
        }
    }

    unauthorized();
    return null;
}
function updateQuery($connection, $query) {
    if (strpos($query, "INSERT") || strpos($query, "UPDATE") || strpos($query, "DELETE")) {
        $query = "$query RETURNING *";
    }
    try {
        $status = $connection->query($query);
    } catch (\Throwable $th) {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => $th, "query" => $query));
    }
    if ($status === FALSE) {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => "DB result is falsy", "query" => $query));
    } else {
        return json_encode(array("status" => "OK", "message" => "Query executed successfully", "query" => $query, "result" => $status));
    }
}