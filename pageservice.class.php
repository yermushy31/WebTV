<?php

require_once "pagemodel.class.php";
require_once "dbservice.class.php";

class PageService
{
    private DbService $dbservice;
    function __construct(DbService $service)
    {
        $this->dbservice = $service;
    }

    private function read_file($filename)
    {
        if (file_exists($filename) && filesize("$filename") > 0) {
            $handle = fopen("$filename", "r") or exit("<p> Impossible d'ouvrir le fichier</p>");
            $filesize = filesize("$filename");
            $content = fread($handle, $filesize);
            fclose($handle);
            return $content;
        } else {
            return "";
        }
    }
    private function write_file($input_text, $filename)
    {
        if(!empty($input_text)) {
            $handle = fopen("$filename", "w") or exit("<p> Impossible d'ouvrir le fichier</p>");
            fwrite($handle, $input_text);
            fclose($handle);
        }
        
    }
    private function UploadFile(PageModel $page)
    {
        if(isset($page->imageElements))
        $path = __DIR__."//images/".$page->nomImage;
        if(move_uploaded_file($page->imageElements['tmp_name'], $path)) {
            return true;
        } else {
            return false;
        }

    }

    private function check_order(PageModel $page)
    {
        $model = new SqlModel();
        if ($page->id == -1) {
            $model->sql = "Select ordre From pages WHERE ordre=:ordre";
            $model->options = array(':ordre' => $page->ordre);
            $results = $this->dbservice->executerRequeteSelection($model);
        } else {
            $model->sql = "Select ordre From pages WHERE ordre=:ordre And id_pages <> :id";
            $model->options = array(':ordre' => $page->ordre, ':id' => $page->id);
            $results = $this->dbservice->executerRequeteSelection($model);
        }
        if (count($results) == 1) {
            $model->sql = "Update pages Set ordre = ordre + 1 WHERE ordre >= :ordre";
            $model->options = array(':ordre' => $page->ordre);
            $results = $this->dbservice->executerRequeteMiseaJour($model);
        }
    }

    public function Supprimer(PageModel $page)
    {
        $model = new SqlModel();
        if (file_exists($page->nomFichier))
            unlink($page->nomFichier);
        if (file_exists("images/".$page->nomImage)) 
            unlink("images/".$page->nomImage);
        $model->sql = "DELETE FROM pages WHERE id_pages=:id_pages;";
        $model->options = array(':id_pages' => $page->id);
        $results = $this->dbservice->executerRequeteMiseaJour($model);
    }
    public function Ajouter(PageModel $page)
    {
        $model = new SqlModel();
        $this->check_order($page);

        $ext = explode(".", strtolower($page->imageElements['name']));
        if(!empty($ext)) {
            $page->nomImage = time().".".$ext[1]; 
        }
        $page->nomFichier = time().".html";
        $model->sql = "INSERT INTO pages (temps, ordre, libpages, est_affiche, libhtml, image, weather, news, map, planning, customHtml) VALUES (:temps, :ordre, :libpages, :est_affiche, :libhtml, :image, :weather, :news, :map, :planning, :customHtml);";
        $model->options = array(
            ':temps' => $page->temps,
            ':ordre' => $page->ordre,
            ':libpages' => $page->nom,
            ':est_affiche' => $page->estAffiche,
            ':libhtml' => $page->nomFichier,
            ':image' => $page->nomImage,
            ':weather' => $page->weather,
            ':news' => $page->news,
            ':map' => $page->map,
            ':planning' => $page->planning,
            ':customHtml' => $page->customHtml
        );
        $page->id = $this->dbservice->executerRequeteInsertion($model);
        $this->write_file($page->html, $page->nomFichier);
        $this->UploadFile($page);
        return $page;
    }

    public function Modifier(PageModel $page)
    {
        $model = new SqlModel();
        $this->check_order($page);
        $model->sql = "UPDATE pages SET temps=:temps, ordre=:ordre, libpages=:libpages, est_affiche=:est_affiche, image=:image, weather=:weather, news=:news, map=:map, planning=:planning, customHtml=:customHtml WHERE id_pages=:id";
        $model->options = array(
            ':temps' => $page->temps,
            ':ordre' => $page->ordre,
            ':libpages' => $page->nom,
            ':est_affiche' => $page->estAffiche,
            ':image' => $page->nomImage,
            ':weather' => $page->weather,
            ':news' => $page->news,
            ':map' => $page->map,
            ':planning' => $page->planning,
            ':customHtml' => $page->customHtml,
            ':id' => $page->id
        );
        $this->dbservice->executerRequeteMiseaJour($model);
        $this->write_file($page->html, $page->nomFichier);
        $this->UploadFile($page);
    }

    public function Lister()
    {
        $model = new SqlModel();
        $model->sql = "SELECT * FROM pages ORDER BY ordre;";
        $model->options = null;

        $data = $this->dbservice->executerRequeteSelection($model);
        $pages = array();

        if (count($data) >= 1) {
            foreach ($data as $key => $value) {
                $p = new PageModel();
                $p->id = $value['id_pages'];
                $p->ordre = $value['ordre'];
                $p->nom = $value['libpages'];
                $p->estAffiche = $value['est_affiche'];
                $p->temps = $value['temps'];
                $p->nomFichier = $value['libhtml'];
                $p->html = $this->read_file($value['libhtml']);
                $p->nomImage = $value['image'];
                $p->weather = $value['weather'];
                $p->news = $value['news'];
                $p->map = $value['map'];
                $p->planning = $value['planning'];
                $p->customHtml = $value['customHtml'];
                $pages[] = $p;
            }
        }
        /*usort($pages, function ($p1, $p2) {
            return $p1->ordre - $p2->ordre;
        });*/

        return $pages;
    }
}
