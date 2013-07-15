<?php

$_SESSION['style'] = 'gconde';

function makeSessionVariables($mysqli){
    
    $sql = "SELECT username, language, style FROM admin";
    if ($stmt = $mysqli->query($sql)){
        if ($row = $stmt->fetch_assoc()){
            $_SESSION['admin'] = $row['username'];
            $_SESSION['language'] = $row['language'];
            $_SESSION['style'] = $row['style'];
        }
        $stmt->close();
    }
}

function changeStyle ($style, $mysqli) {
    $sql = "UPDATE admin SET style = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $style);
    $stmt->execute();
    $stmt->close();
}

function changeLanguage ($lang, $mysqli) {
    $sql = "UPDATE admin SET language = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $lang);
    $stmt->execute();
    $stmt->close();
}

function changeAdmin ($email, $mysqli) {
     $sql = "SELECT username FROM members where email = '{$email}'";
     $name = "";
    if ($stmt = $mysqli->query($sql)){
        if ($row = $stmt->fetch_assoc()){
            $name = $row['username'];
            $stmt->close();
        }
    }
    if (isset($name) && $name != "") {
        $sql = "UPDATE admin SET email = ?, name = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $email, $name);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    return false;
}

?>