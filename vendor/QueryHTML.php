<?php

 class QueryHTML {

     public $url;
     public $timeout = 30;
     public $response;
     public $dom;
     public $xpath;

     public function __construct($url)
     {
         $this->url = $url;
     }


     public function curlquery()
     {

         $curl = curl_init();
         curl_setopt_array($curl, array(
             CURLOPT_URL => $this->url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => "",
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_TIMEOUT => $this->timeout,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "GET",
             //CURLOPT_HTTPHEADER => array(),
         ));

         $response = curl_exec($curl);
         $err = curl_error($curl);
         curl_close($curl);
         if ($err) {
             return $err;

         } else {

             $this->response = $response;

         }

     }

     public function initDom()
     {

         $this->curlquery();

         $dom = new \DomDocument();
         @$dom->loadHTML($this->response);

         $this->dom = $dom;
         $this->xpath = new \DomXPath($this->dom);

         return $this;

     }

     public function query($search)
     {
         $res = $this->xpath->query($search);
         return $res;
     }

 }