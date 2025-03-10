<?php
function getCommitRefactoringsCount($connection, $projectID, $authorEmail, $refactoringType) {
    
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
    $result = $connection->query($query);
    $rows = array();
    if ($result->num_rows > 0) {
        while($r = $result->fetch_assoc()) {
            $rows[] = $r;
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
    $qur = "SELECT projectgit.*, COUNT(lambdastable.id) AS numberOfLambdas, COUNT(revisiongit.id) AS numberOfCommits,
            COUNT(CASE 
                WHEN lambdastable.status = 'NEW' THEN lambdastable.status
                ELSE NULL
            END) AS numberOfNewLambdas
            FROM projectgit 
                LEFT OUTER JOIN revisiongit ON projectgit.id = revisiongit.project
                LEFT OUTER JOIN lambdastable ON lambdastable.revision = revisiongit.id
            $whereClause
            GROUP BY projectgit.id";

    return getQueryRows($connection, $qur);
}
function getUser($jwt) {

    if (isset($jwt)) {
        try {
            $secretKey = base64_decode(getSecretKey());
            $token = \Firebase\JWT\JWT::decode($jwt, $secretKey, array('HS512'));
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
        $status = $connection->exec($query);
    } catch (\Throwable $th) {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => $th, "query" => $query));
    }
    if ($status === TRUE) {
        return json_encode(array("status" => "OK", "message" => "Query executed successfully", "query" => $query));
    } else {
        return json_encode(array("status" => "ERROR", "message" => $connection->lastErrorMsg(), "error" => "DB result is falsy", "query" => $query));
    }
}