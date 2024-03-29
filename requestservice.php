<?php
require_once "requestmodel.class.php";
class RequestService
{

    private RequestModel $requestmodel;
    function __construct(RequestModel $request)
    {
        $this->requestmodel = $request;
    }

    public function DigiFormatRequest()
    {
        $url = $this->requestmodel->url;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->requestmodel->token
        );
        $data = array(
            'query' => $this->requestmodel->query
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $sessions[] = array();
       
        if (!empty($response)) {
            foreach ($response['data']['trainingSessions'] as $value) {
                // if ($value['pipelineState'] == "ongoing" && $value['trainingType'] == "Action de formation") {
                if ($value['pipelineState'] == "ongoing") {
                    $currentDate = new DateTime();
                    $startDateSession = new DateTime($value['startDate'] ?? "");
                    $endDateSession = new DateTime($value['endDate'] ?? "");
            
                    $startDateWeek = clone $currentDate;
                    $startDateWeek->modify('this week')->setTime(0, 0, 0);
                    $endDateWeek = clone $startDateWeek;
                    $endDateWeek->modify('+7 days')->setTime(23, 59, 59);

                    if ($startDateSession >= $startDateWeek && $endDateSession <= $endDateWeek && $startDateSession != "" && $endDateSession != "") {
                       
                                 $sessions[] = $value;
                                    
                    }
                }
            }
            return $sessions;
        }
    }


    public function NewsApiRequest()
    {
        $headers = [
            "Content-Type: application/json"
        ];
        curl_setopt($this->requestmodel->curl, CURLOPT_URL, $this->requestmodel->url);
        curl_setopt($this->requestmodel->curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->requestmodel->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->requestmodel->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->requestmodel->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
        $data = json_decode(curl_exec($this->requestmodel->curl));
        curl_close($this->requestmodel->curl);

        return $data;
    }

    public function WeatherApiRequest()
    {
        $json = file_get_contents($this->requestmodel->url);
        $data = json_decode($json);
        return $data;
    }
}