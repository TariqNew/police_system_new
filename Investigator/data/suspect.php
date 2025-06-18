<?php

// Get all suspects
function getAllSuspects($conn) {
    $sql = "SELECT * FROM suspects";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->rowCount() >= 1 ? $stmt->fetchAll() : 0;
}

// Get single suspect by ID
function getSuspectById($id, $conn) {
    $sql = "SELECT * FROM suspects WHERE suspect_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->rowCount() === 1 ? $stmt->fetch() : 0;
}

// Check if username is unique (for suspects)
function unameIsUnique($uname, $conn, $suspect_id = 0) {
    $sql = "SELECT username, suspect_id FROM suspects WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$uname]);

    if ($stmt->rowCount() >= 1) {
        $suspect = $stmt->fetch();
        return ($suspect_id != 0 && $suspect['suspect_id'] == $suspect_id) ? 1 : 0;
    } else {
        return 1;
    }
}

// Verify password for suspect
function suspectPasswordVerify($suspect_pass, $conn, $suspect_id) {
    $sql = "SELECT password FROM suspects WHERE suspect_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$suspect_id]);

    if ($stmt->rowCount() === 1) {
        $suspect = $stmt->fetch();
        return password_verify($suspect_pass, $suspect['password']) ? 1 : 0;
    } else {
        return 0;
    }
}

// Remove suspect by ID
function removeSuspect($suspect_id, $conn) {
    $sql = "DELETE FROM suspects WHERE suspect_id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$suspect_id]);
}

//Search suspect 
function searchSuspects($key, $conn) {
    $search = "%$key%";
    $sql = "SELECT * FROM suspects 
            WHERE fname LIKE ? 
               OR lname LIKE ? 
               OR username LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$search, $search, $search]);

    return $stmt->rowCount() >= 1 ? $stmt->fetchAll() : 0;
}


function getLatestCaseTitleBySuspect($conn, $suspect_id) {
    $sql = "SELECT case_title FROM criminal_cases 
            WHERE suspect_id = ? 
            ORDER BY date_reported DESC 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$suspect_id]);
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchColumn();
    } else {
        return 'No Case';
    }
}

function getCaseBySuspectId($suspect_id, $conn) {
    $sql = "SELECT * FROM cases WHERE suspect_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$suspect_id]);

    if ($stmt->rowCount() == 1) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        return 0;
    }
}


function searchSuspectsWithInvestigator($key, $conn){
    $sql = "SELECT s.*, c.investigator_id, o.fname AS investigator_fname, o.lname AS investigator_lname
            FROM suspects s
            LEFT JOIN cases c ON s.suspect_id = c.suspect_id
            LEFT JOIN officers o ON c.investigator_id = o.officer_id
            WHERE s.fname LIKE ? OR s.lname LIKE ? OR s.username LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeKey = "%$key%";
    $stmt->execute([$likeKey, $likeKey, $likeKey]);

    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return 0;
    }
}


function getSuspectWithInvestigatorById($suspect_id, $conn) {
    $sql = "SELECT s.*, 
                   c.investigator_id, 
                   o.fname AS investigator_fname, 
                   o.lname AS investigator_lname
            FROM suspects s
            LEFT JOIN cases c ON s.suspect_id = c.suspect_id
            LEFT JOIN officers o ON c.investigator_id = o.officer_id
            WHERE s.suspect_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$suspect_id]);

    return $stmt->rowCount() === 1 ? $stmt->fetch(PDO::FETCH_ASSOC) : 0;
}
