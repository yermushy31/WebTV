<?php

require_once "pageservice.class.php";
require_once "requestservice.php";

$requestmodel = new RequestModel();
$requestmodel->curl = curl_init();
$requestmodel->url = 'https://app.digiforma.com/api/v1/graphiql';
$requestmodel->query = '{trainingSessions{trainingSessionInstructors{instructor{lastname,firstname}},placeInfo{city}}}';
$requestmodel->token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MzIxNiwibW9kZSI6ImFwaSIsInR5cGUiOiJ1c2VyIiwiZXhwIjoxOTAwNjI3MjAwLCJpc3MiOiJEaWdpZm9ybWEifQ.bmYKNHSSKa65Z8DGyyEDD9bPUiYjx5UnpwlEqnxy1kQ";

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
        $pageCourante->ordre = $_POST['_pageOrdre'];
        $pageCourante->temps = $_POST['_pageTemps'];
        $pageCourante->estAffiche = isset($_POST['_pageAffiche']);
        $pageCourante->html = $_POST['_pageHtml'];
        $pageCourante->weather = isset($_POST['_pageWeather']);
        $pageCourante->news = isset($_POST['_pageNews']);
        $pageCourante->map = isset($_POST['_pageMap']);
        $pageCourante->planning = isset($_POST['_pagePlanning']);
        $pageCourante->customHtml = isset($_POST['_pageCustomHtml']);

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
    <title>Paramètres webtv</title>
    <script src="script/parametres.js"></script>
</head>


<style>
    .custom-box {
        margin-top: 0%;
        padding: 40px;
        background: rgba(0, 0, 0, 0);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(4.8px);
        border: 1px solid rgba(0, 0, 0, 0.33);
    }

    .header {
        padding: 30px;
        background-color: black;

    }

    label {
        font-size: 20px;
        font-weight: 700;
        background: linear-gradient(to left, #00CF91 20%, #3055CF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    input.transparent-input {
        background-color: rgba(0, 0, 0, 0.4);
        border: none !important;
    }

    textarea.transparent-input {
        background-color: rgba(0, 0, 0, 0.4);
        border: none !important;
    }

    .title {
        font-size: 80px;
        background: #3055CF;
        background: linear-gradient(to left, #3055CF 20%, #00CF91 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-select {
        width: 170px;
    }
</style>

<body>
    <section class="bg-image" style="background-image: url('https://images.alphacoders.com/111/1112602.jpg');
            height: 100%; width: auto;
            background-size: cover;
            background-repeat: no-repeat;">

        <header class="header">
            <div class="text-center">
                <h1 class="title">Paramètres Pages</h1>
            </div>
        </header>
        <div class="container-lg">
            <div class="custom-box">
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
                        <div>
                            <label>A Afficher ?</label>
                            <input name="_pageAffiche" type="checkbox" <?php if ($pageCourante?->estAffiche) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>Météo ?</label>
                            <input name="_pageWeather" type="checkbox" <?php if ($pageCourante?->weather) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>Maps ?</label>
                            <input name="_pageMap" type="checkbox" <?php if ($pageCourante?->map) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>News ?</label>
                            <input name="_pageNews" type="checkbox" <?php if ($pageCourante?->news) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>Planning ?</label>
                            <input name="_pagePlanning" type="checkbox" <?php if ($pageCourante?->planning) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>Custom HTML ?</label>
                            <input name="_pageCustomHtml" type="checkbox" <?php if ($pageCourante?->customHtml) {
                                echo ("checked");
                            } ?> />
                        </div>
                        <br>
                        <div>
                            <label>Contenue de la page</label>
                            <textarea name="_pageHtml"
                                class="form-control transparent-input"><?php echo ($pageCourante?->html); ?>
                            </textarea>
                        </div>

                        <div>
                            <label>Image Page</label>
                            <input name="_pageFile" type="file" class="form-control transparent-input" />
                        </div>
                    </div>
                    <br>
                    <div>
                        <?php if ($pageCourante == null) { ?>
                            <button type='submit' name="_pageAjouter" class="btn btn-primary">Ajouter</button>
                        <?php } else { ?>
                            <button type='submit' name="_pageEnregister" class="btn btn-primary">Enregistrer</button>
                            <button type='submit' onclick="return confirmerSuppression()" name="_pageSupprimer"
                                class="btn btn-primary">Supprimer</button>
                        <?php } ?>

                    </div>
            </div>
            </form>
        </div>
        </div>
    </section>
</body>

</html>