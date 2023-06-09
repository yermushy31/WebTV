<?php
require_once "dbmodel.class.php";
class DbService
{
    private DbModel $dbInfos;
    private ?PDO $pdo;


    function __construct(DbModel $model)
    {
        $this->dbInfos = $model;
    }

    private function ouvrirConnexion()
    {
        try {
            $this->pdo = new PDO($this->dbInfos->dsn, $this->dbInfos->user, $this->dbInfos->passwd, $this->dbInfos->options);
        } catch (PDOException $ex) {
            die("Erreur lors de la connexion SQL : " . $ex->getMessage());
        }
    }

    public function executerRequeteSelection(SqlModel $sqlmodel): array
    {
        $this->ouvrirConnexion();
        $query = $this->pdo->prepare($sqlmodel->sql);
        if ($sqlmodel->options == null) {
            $query->execute();
        } else {
            $query->execute($sqlmodel->options);
        }
        $result = $query->fetchAll();
        $this->pdo = null;
        return $result;
    }
    public function executerRequeteInsertion(SqlModel $sqlmodel): int
    {
        $this->ouvrirConnexion();
        $query = $this->pdo->prepare($sqlmodel->sql);
        $query->execute($sqlmodel->options);
        $result = $this->pdo->lastInsertId();
        $this->pdo = null;
        return $result;
    }
    public function executerRequeteMiseaJour(SqlModel $sqlmodel): void
    {
        $this->ouvrirConnexion();
        $query = $this->pdo->prepare($sqlmodel->sql);
        $query->execute($sqlmodel->options);
        $this->pdo = null;
    }
}
