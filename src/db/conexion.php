<?php
/*
    Allan Augusto Anduray Portillo - AP20025
    Archivo de conexion a la base de datos mysql en filess.io
    Se incluye una porcion de codigo para cargar la api de manera local con xampp
*/


function obtenerConexion(): PDO
{
    /*
    $host = '127.0.0.1';
    $port = 3306;
    $dbname = 'p3clavel_casatop';
    $user = 'root';
    $password = '';
    */
    $host = 'dqcm94.h.filess.io';
    $port = 3307;
    $dbname = 'p3clave1_casatop_cryskillas';
    $user = 'p3clave1_casatop_cryskillas';
    $password = '5f3895e58d3e435966ef168e17979c5aaecfa607';
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Error de conexión a la base de datos: ' . $e->getMessage());
    }
}