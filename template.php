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
$dbmodel->options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="template_style.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="script/parametres.js">
    </script>

</head>

<style>
    <?php
    $i = 0;
    if (count($pages) > 0)
        foreach ($pages as $value) {
            echo ("
        .top-content .carousel-item-$i {
            background-image: url('images/" . $value->nomImage . "?v=" . time() . "');
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

                        $style = $value->news == 0 && $value->map == 0 && $value->weather == 0 ? "style='height: 80vh; width: 140% !important;margin-left: -128px;'" : "";
                        $is_active = $i == 0 ? "active" : "";

                        echo "<div class='carousel-item carousel-item-$i $is_active' data-interval='" . $value->temps * $x . "'>";
                        ?>
                        <div class='carousel-caption'>
                            <?php
                            echo "<div $style class='row'>";
                            if ($value->news == 0 && $value->planning == 0 && $value->weather == 0 && $value->map == 0 && $value->customHtml == 0) {
                                echo '<div class="col-11"></div>';
                            }
                            if ($value->news == 1) {
                                $style = $value->weather == 1 && $value->planning == 1 ? "style='margin-top: -42px; margin-left: -2vw; '" : "";
                                echo '
                                <div class="news col-11 offset-0"' . $style . '>
                                    <div id="newsid" class="messagedefilant"></div>
                                </div>';
                                echo '
                            <script>
                            async function displayRandomNews() {
                                var phparray = ' . json_encode($News, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                                var newsContainer = document.querySelector(".news");
                                function displayNews() {
                                    var randomIndex = Math.floor(Math.random() * phparray.articles.length);
                                    var newsHTML = "";
                            
                                    if (phparray.articles[randomIndex].title !== undefined) {
                                        newsHTML += `<div class="messagedefilant"><div data-text="${phparray.articles[randomIndex].title}"><span>${phparray.articles[randomIndex].title}</span></div></div>`;
                                    }
                            
                                    newsContainer.innerHTML = newsHTML;
                                }
                            
                                displayNews(); 
                            
                                setInterval(displayNews, 8000); // Affiche une nouvelle toutes les 8 secondes
                            }
                            
                            displayRandomNews();
                            
                            </script>';
                            }

                            if ($value->weather == 1) {

                                $class = $value->news == 0 && $value->map == 0 && $value->planning == 0 ? "col-11" : "col-3";
                                $style = $value->news == 1 ? "style='margin-top: 0px; height: 60vh;'" : "style='margin-top: 10px; height: 60vh;'";
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
                                $style = $value->news == 0 ? "width='520px' height='420px'" : "width='420px' height='380px'";
                                echo '<div class="maps ' . $class . ' offset-1 p-0">
                                <iframe src="https://embed.waze.com/fr/iframe?zoom=11&lat=43.60154&lon=1.44084"' . $style . '>
                                    </iframe>
                                </div>';
                            }
                            if ($value->customHtml == 1) {

                                echo "<section class='customHtml col-12 offset-0 p-0'>
                           <style>
                           #wrap {
                            width: 100%;
                            height: 1500px;
                            padding: 0;
                            overflow: hidden;
                          }
                          
                          #scaled-frame {
                            width: 100%;
                            height: 2000px;
                            border: 0px;
                          }
                          
                          #scaled-frame {
                            zoom: 0.75;
                            -moz-transform: scale(0.75);
                            -moz-transform-origin: 0 0;
                            -o-transform: scale(0.75);
                            -o-transform-origin: 0 0;
                            -webkit-transform: scale(0.75);
                            -webkit-transform-origin: 0 0;
                          }
                          
                          @media screen and (-webkit-min-device-pixel-ratio:0) {
                            #scaled-frame {
                              zoom: 2;
                            }
                          }
                           </style>
                           <div id='wrap'>
                           <iframe id='scaled-frame' style='width: 100%; height: 100%;'>
                           $value->html
                           </iframe>
                           </div>
                           </section>";
                            }
                            if ($value->planning == 1 && $value->map == 0) {

                                $listyle = "style='text-align: left;margin: -5px auto;'";
                                $style = $value->news == 0 && $value->map == 0 && $value->weather == 0 ? "style='max-height: 500px; margin-top: -59px;'" : "style='margin-left: 10px; max-height: 420px; margin-top: -37px;'";
                                $class = $value->news == 0 && $value->map == 0 && $value->weather == 0 ? "col-12" : "col-8";

                                echo "
                                <div class='planning $class offset-0 p-0' $style>
                                <section style='height: 420px;overflow: hidden;'>
                                <div class='titre-planning' style='max-height: 50px;text-align: center;'>
                                <h4 class='fancy' style=' text-transform: uppercase;margin: 10px; font-size: 2.0rem;'>Cette semaine chez
                                <img style='max-width: 100%; max-height: 85px;' src='images/themanislogo.png'/></h4>
                                
                                </div>
                                <ul style='list-style: none; text-align: left;margin: 0;padding: 0;columns: 2;margin-top: 70px;'>";

                                foreach ($DigiFormat as $key => $value) {
                                    if (!empty($value)) {
                                        $name = $value['name'] ?? "";
                                        $instructeur = "";
                                        foreach ($value['trainingSessionInstructors'] as $another) {
                                            $firstname = $another['instructor']['firstname'];
                                            $lastname = $another['instructor']['lastname'];
                                            $instructeur = $firstname . " " . $lastname;

                                        }
                                        $startdate = $value['startDate'] ?? "";
                                        $enddate = $value['endDate'] ?? "";
                                        if (!empty($instructeur))
                                            echo "<li style='border: 2px solid lightblue; list-style: none; text-align: center;color:black;padding: 5px; margin:5px;'><strong>$name</strong> $instructeur</li>";
                                    }
                                }
                                echo '
                              </ul>
                                </section>
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

            </div>
            <!-- End carousel -->
        </div>
</body>

</html>