<?php

require_once "pageservice.class.php";
require_once "requestservice.php";

$requestmodel = new RequestModel();
$requestmodel->curl = curl_init();
$requestmodel->url = "https://newsapi.org/v2/top-headlines?q=France&country=fr&language=fr&pageSize=15&apiKey=6f70fc1752ef4fd6b2ad0e3589a84236";
$requestservice = new RequestService($requestmodel);



$dbmodel = new DbModel();
$dbmodel->dsn = "mysql:host=localhost;dbname=webtv";
$dbmodel->user = "root";
$dbmodel->passwd = "";
$dbmodel->options =  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

$dbservice = new DbService($dbmodel);
$service = new PageService($dbservice);
$pages = $service->Lister();

$News = $requestservice->NewsApiRequest();
foreach ($pages as $value) {
    if ($value->planning == 1) {

        $requestmodel->url = "https://app.digiforma.com/api/v1/graphiql";
        $requestmodel->query = " 
{
    trainingSessions {
      startDate
      endDate
      name
      trainingType
      inter
      placeInfo {
        city
      }
      trainingSessionInstructors {
        instructor {
          lastname
          firstname
        }
      },
      pipelineState
    }
  }";

        $requestmodel->token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MzIxNiwibW9kZSI6ImFwaSIsInR5cGUiOiJ1c2VyIiwiZXhwIjoxOTAwNjI3MjAwLCJpc3MiOiJEaWdpZm9ybWEifQ.bmYKNHSSKa65Z8DGyyEDD9bPUiYjx5UnpwlEqnxy1kQ";
        $DigiFormat = $requestservice->DigiFormatRequest();
    }
}


$requestmodel->token = "947c00c93252788bf42802507b3aab97";
$requestmodel->url = "https://api.openweathermap.org/data/2.5/weather?id=2972315&lang=fr&units=metric&APPID=" . $requestmodel->token;

$weather = $requestservice->WeatherApiRequest();
//var_dump($weather);
//echo json_encode($DigiFormat);
//print_r($News);

?>

<!doctype html>
<html lang="fr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="300">
    <!-- <meta http-equiv="refresh" content="2"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="template_style.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>

<style>
    <?php
    $i = 0;
    if (count($pages) > 0)
        foreach ($pages as $value) {
            echo ("
        .top-content .carousel-item-$i {
            background-image: url('images/" . $value->nomImage . "?v=".time()."');
        }
        ");
            $i++;
        }
    ?>
</style>

<body>
    <!-- Top content -->
    <div class="top-content">
        <!-- Carousel -->
        <div id="carousel-example" class="carousel slide" data-ride="carousel" data-mdb-animation="slide-right">
            <ol class="carousel-indicators">
                <?php
                $x = 1000;
                $i = 0;
                foreach ($pages as $value) {
                    if ($value->estAffiche == 1) {
                        $is_active = $i == 0 ? "class='active'" : "";

                        echo "<li data-target='#carousel-example' data-slide-to='$i' $is_active  data-interval='" . $value->temps * $x . "'>
                        </li>";
                    }
                }
                $i++;
                ?>
            </ol>

            <div class="carousel-inner">
                <?php
                $i = 0;
                $x = 1000;
                foreach ($pages as $value) {
                    if ($value->estAffiche == 1) {
                      
                        $style =  $value->news == 0 &&  $value->map == 0 &&  $value->weather == 0 ? "style='height: 80vh; width: 100% !important;'" : "";
                        $is_active = $i == 0 ? "active" : "";

                        echo "<div class='carousel-item carousel-item-$i $is_active' data-interval='" . $value->temps * $x . "'>";
                ?>
                <div class='carousel-caption'>
                <?php
                echo "<div $style class='row'>";
                        if($value->news==0 && $value->planning==0 && $value->weather==0 && $value->map==0 && $value->customHtml==0) {
                            echo '<div class="col-11"></div>';
                        }
                        if ($value->news == 1) {
                            $style = $value->weather == 1 && $value->planning == 1 ? "style='margin-top: -42px; margin-left: -2vw; '" : "";
                            echo "<div class='col-11'>
                            
                                <div class='news' $style>";
                                $MAX = count($News->articles);
                                $index = rand(0, $MAX-1);
                                if(isset($News->articles[$index]) && $News->articles[$index]->title != null) {
                            echo '<div class="messagedefilant"><div data-text="' . $News->articles[$index]->title. '"><span>' . $News->articles[$index]->title. '</span></div></div>';
                                }
                            echo "
                                </div>
                                </div>";
                        }
                        if ($value->weather == 1) {

                            $class =  $value->news == 0 &&  $value->map == 0 &&  $value->planning == 0 ? "col-11" : "col-3";
                            $style = $value->news == 1 ? "style='margin-top: 0px; height: 60vh;'" : "style='margin-top: 10px; height: 70vh;'";
                            echo "
                                <div class='weather $class' $style>
                                <div class='custom-box'>
                                <img class='icon' src='https://openweathermap.org/img/w/" . $weather->weather[0]->icon . ".png'>
                                <p class='city'>" . $weather->name . "</p>
                                <p class='temperature'>" . round($weather->main->temp) . "&deg;</p>
                                <p class='description'>" . $weather->weather[0]->description . "</p>
                                </div>
                                </div>";
                        }
                        if ($value->map == 1 && $value->planning == 0) {
                            $class = $value->news == 0 && $value->weather == 0 ? "col-11" : "col-8";
                            $style = $value->news == 0 ? "width='520px' height='420px'":"width='420px' height='380px'";
                            echo '<div class="maps '.$class.' offset-1 p-0">
                                <iframe src="https://embed.waze.com/fr/iframe?zoom=11&lat=43.60154&lon=1.44084"'.$style.'>
                                    </iframe>
                                </div>';
                        }
                        if($value->customHtml == 1) {
                           
                           echo "<div class='customHtml col-12 offset-0 p-0'>
                           $value->html
                           </div>";
                        }
                        if ($value->planning == 1 && $value->map == 0) {

                            $listyle = "style='text-align: left;margin: -5px auto;'";
                            $style =  $value->news == 0 &&  $value->map == 0 &&  $value->weather == 0 ? "style='max-height: 420px; margin-top: -37px;'" : "style='margin-left: 10px; max-height: 420px; margin-top: -37px;'";
                            $class = $value->news == 0 &&  $value->map == 0 &&  $value->weather == 0 ? "col-12" : "col-8";
                           // $wrapperStyle = $value->news == 1 && $value->map == 0 && $value->weather == 1 ? "style='text-align: left;margin: -5px auto;padding: 0.5rem 0.5rem 0.5rem 1em;columns: 12rem;column-gap: 0rem;'" : "";
                            echo "
                                <div class='planning $class offset-0 p-0' $style>
                                 
                                <ol class='olist' style='margin: 0; margin-left: -60px;'>
                                <li><img style='margin-left: 30px;' src='images/themanislogo.png' width='30%' height='15%'/></li>
                                <li><h1 style='margin: 5px; font-size: 1.2em; color: black; font-weight: 800px;'> Cette Semaine dans nos salles </h1></li>
                                ";
                            foreach ($DigiFormat as $key => $value) {
                                if (!empty($value)) {
                                    $name = $value['name'] ?? "";
                                    $instructeur = "";
                                    foreach($value['trainingSessionInstructors'] as $another) {
                                       $firstname = $another['instructor']['firstname'];
                                       $lastname  = $another['instructor']['lastname'];
                                       $instructeur = $firstname . " " . $lastname;

                                    }
                                    $startdate = $value['startDate'] ?? "";
                                    $enddate = $value['endDate'] ?? "";
                                    if(!empty($instructeur)) 
                                        echo "<li><span>$name</span><span>$instructeur</span></li>";
                                }
                            }
                            echo '
                                </ol>
                               
                                </div>
                                ';
                        }
                        echo "
                            </div>                 
                            </div>
                            </div>";
                    }
                    $i++;
                }
                ?>
            </div>
            <a class="carousel-control-prev" href="#carousel-example" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel-example" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!-- End carousel -->
    </div>
</body>
</html>