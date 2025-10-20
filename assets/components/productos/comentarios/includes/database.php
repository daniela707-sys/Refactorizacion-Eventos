<?php
    class Database {
        private $host = 'localhost:3306';
        private $user = 'root';
        private $password = '123456';
        private $database = 'apicomentario';

        public function getConnection(){
            $hostDB = 'mysql:host='.$this->host.";dbname=".$this->database.";";

            try {
                $connection = new PDO($hostDB,$this->user,$this->password);
                $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                return $connection;
            } catch(PDOException $e) {
                die("ERROR: ".$e->getMessage());
            }
        }
    }
?>