<?php

$_SESSION['style'] = 'gconde';

function makeSessionVariables($mysqli){
    
    $sql = "SELECT username, language, style FROM admin";
    if ($stmt = $mysqli->query($sql)){
        while ($row = $stmt->fetch_assoc()){
            $_SESSION['admin'] = $row['username'];
            $_SESSION['language'] = $row['language'];
            $_SESSION['style'] = $row['style'];
        }
    }
    
    
    
}

?>