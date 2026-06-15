<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "resolvex_db"
);

if(!$conn)
{
    die("Connection Failed");
}

?>