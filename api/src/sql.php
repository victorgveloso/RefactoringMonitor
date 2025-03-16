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
	// Common encodings to check
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16'];
        $encoding = mb_detect_encoding($d, $encodings, true);
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($d, 'UTF-8', $encoding);
        }
        return $d;
    }
    return $d;
}
function getQueryRows($connection, $query) {
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
    try {
        $status = $connection->query($query);
    } catch (\Throwable $th) {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => $th, "query" => $query));
    }
    if ($status === FALSE) {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => "DB result is falsy", "query" => $query));
    } else {
        return json_encode(array("status" => "OK", "message" => "Query executed successfully", "query" => $query));
    }
}
