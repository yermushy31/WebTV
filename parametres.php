<?php

require_once "pageservice.class.php";
require_once "requestservice.php";
error_reporting(0);
$requestmodel = new RequestModel();
$requestmodel->curl = curl_init();
$requestmodel->url = 'https://app.digiforma.com/api/v1/graphiql';
$requestmodel->query = '{trainingSessions{trainingSessionInstructors{instructor{lastname,firstname}},placeInfo{city}}}';
$requestmodel->token = "";

$requestservice = new RequestService($requestmodel);

$dbmodel = new DbModel();
$dbmodel->dsn = "mysql:host=localhost;dbname=webtv";
$dbmodel->user = "root";
$dbmodel->passwd = "";
$dbmodel->options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
$dbservice = new DbService($dbmodel);
$service = new PageService($dbservice);
$pages = $service->Lister();


$pageCourante = null;

if (isset($_POST['_pageId'])) {
    $id = $_POST['_pageId'];
    if ($id > 0) {
        $pageCourante = array_values(array_filter($pages, function ($p) use ($id) {

            return $p->id == $id;
        }))[array_key_first($pages)];
    }

    if (isset($_POST['_pageAjouter'])) {
        $pageCourante = new PageModel();
        $pageCourante->id = -1;
    }

    if (isset($_POST['_pageEnregister']) || isset($_POST['_pageAjouter'])) {

        $pageCourante->nom = $_POST['_pageNom'];
        if(!empty($_POST['_pageOrdre']))
            $pageCourante->ordre = $_POST['_pageOrdre'];
        $pageCourante->temps = $_POST['_pageTemps'];
        $pageCourante->estAffiche = isset($_POST['_pageAffiche']);
        $pageCourante->html = $_POST['_pageHtml'];
        $pageCourante->weather = isset($_POST['_pageWeather']);
        $pageCourante->news = isset($_POST['_pageNews']);
        $pageCourante->map = isset($_POST['_pageMap']);
        $pageCourante->planning = isset($_POST['_pagePlanning']);
        $pageCourante->customHtml = isset($_POST['_pageCustomHtml']);
        $pageCourante->social = isset($_POST['_pageSocial']);

        if (isset($_FILES['_pageFile']))
            $pageCourante->imageElements = $_FILES['_pageFile'];
    }
    if (isset($_POST['_pageEnregister'])) {
        $service->Modifier($pageCourante);
    }
    if (isset($_POST['_pageAjouter'])) {
        $pageCourante = $service->Ajouter($pageCourante);
    }
    if (isset($_POST['_pageSupprimer'])) {
        $service->Supprimer($pageCourante);
        $pageCourante = null;
    }
    if (isset($_POST['_pageEnregister']) || isset($_POST['_pageAjouter']) || isset($_POST['_pageSupprimer'])) {
        $pages = $service->Lister();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Paramètres webtv</title>
    <script src="script/parametres.js"></script>
    <style>
        .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
            background-color: #00CF83;
            border-color: #00CF83;
        }
    </style>
</head>

<body>
    <section class="bg-image" style="background-image: url('https://images.alphacoders.com/111/1112602.jpg');
            height: 100%; width: auto;
            background-size: cover;
            background-repeat: no-repeat;">

        <header class="header" style="background-color: black;">
            <div class="text-center" style="background: linear-gradient(to right, #121FCF 0%, #00CF83 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;padding: 40px;">
                <h1 class="title">Paramètres Pages</h1>
            </div>
        </header>
        
        <div class="container-lg">
            <div class="custom-box">
                    <br>
                    <div style="margin-left: 10%;">
                        <iframe id="template-preview" src="template.php" width="970" height="550" style="border-radius: 10px;"></iframe>

                    </div>
                <form method="post" id="form_pages">
                    <select name="_pageId" class="form-select"
                        onchange="document.getElementById('form_pages').submit()">

                        <option class="add-page" value='-1'>Ajouter une page</option>
                        <?php foreach ($pages as $p) { ?>
                            <div class="p-3 mb-2 bg-transparent text-dark">.bg-transparent
                                <option value="<?php echo $p->id; ?>" <?php echo $pageCourante && $pageCourante->id == $p->id ? 'selected' : '' ?>>
                                    <?php echo $p->nom; ?>
                                </option>
                            </div>
                        <?php } ?>
                    </select>
                </form>
                <br>
                <form enctype="multipart/form-data" method="post">
                    <div class="form-group">
                        <input name="_pageId" type="hidden" value="<?php echo ($pageCourante?->id); ?>" />
                        <div>
                            <label>Nom de la page</label>
                            <input name="_pageNom" class="form-control transparent-input"
                                value="<?php echo ($pageCourante?->nom); ?>" />
                        </div>
                        <br>
                        <div>
                            <label>Ordre d'affichage</label>
                            <input name="_pageOrdre" type="number" class="form-control transparent-input"
                                value="<?php echo ($pageCourante?->ordre); ?>" />
                        </div>
                        <br>
                        <div>
                            <label>Temps d'affichage (en secondes)</label>
                            <input name="_pageTemps" type="number" class="form-control transparent-input"
                                value="<?php echo ($pageCourante?->temps); ?>" />
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageAffiche" class="form-check-input" type="checkbox" id="pageAffiche"
                                <?php if ($pageCourante?->estAffiche) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageAffiche">A Afficher</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageWeather" class="form-check-input" type="checkbox" id="pageWeather"
                                <?php if ($pageCourante?->weather) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageWeather">Météo</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageMap" class="form-check-input" type="checkbox" id="pageMap"
                                <?php if ($pageCourante?->map) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageMap">Maps</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageNews" class="form-check-input" type="checkbox" id="pageNews"
                                <?php if ($pageCourante?->news) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageNews">News</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pagePlanning" class="form-check-input" type="checkbox" id="pagePlanning"
                                <?php if ($pageCourante?->planning) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pagePlanning">Planning</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageCustomHtml" class="form-check-input" type="checkbox"
                                id="pageCustomHtml" <?php if ($pageCourante?->customHtml) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageCustomHtml">Custom HTML</label>
                        </div>
                        <br>
                        <div class="form-check form-switch">
                            <input name="_pageSocial" class="form-check-input" type="checkbox" id="pageSocial"
                                <?php if ($pageCourante?->social) {
                                    echo ("checked");
                                } ?>>
                            <label class="form-check-label" for="pageSocial">Réseaux Sociaux</label>
                        </div>
                        <br>
                        <div>
                            <label>Contenu HTML de la page :</label>
                            <textarea name="_pageHtml" class="form-control transparent-input"><?php echo ($pageCourante?->html); ?>
                            </textarea>
                        </div>
                        <br>
                        <div>
                            <label>Image Page :</label>
                            <input name="_pageFile" type="file" class="form-control transparent-input" />
                        </div>
                    </div>
                    <br>
                    <div>
                        <?php if ($pageCourante == null) { ?>
                            <button type='submit' name="_pageAjouter" class="btn btn-primary">Ajouter</button>
                        <?php } else { ?>
                            <button type='submit' id="saveButton" name="_pageEnregister" class="btn btn-primary">Enregistrer</button>
                            <button type='submit' onclick="return confirmerSuppression()" name="_pageSupprimer"
                                class="btn btn-primary">Supprimer</button>
                        <?php } ?>

                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>
