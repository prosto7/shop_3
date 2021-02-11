<?php 

require_once "./libs/rb.php";
R::setup( 'mysql:host=localhost;dbname=shop',
'root', 'root' ); //for both mysql or mariaDB

session_start();
?>