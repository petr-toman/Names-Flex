<?php

date_default_timezone_set("Europe/Prague");
error_reporting( E_ALL ^ ( E_NOTICE )  );
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
ini_set("auto_detect_line_endings", true);
set_time_limit(30);
ini_set('memory_limit', '128M');


$em = new nameconverter();
echo $em->convert();
/**************************************************************************

**************************************************************************/
class nameconverter
{
     private $in_data       = null;
/**************************************************************************/
    public function __construct(){
      $this->in_data = new stdClass();
      $input_json = json_decode( file_get_contents('php://input') );

      $this->in_data->firstname       = htmlspecialchars(strip_tags($input_json->{'firstname'}));
      $this->in_data->surname         = htmlspecialchars(strip_tags($input_json->{'surname'})); 


    }
/**************************************************************************/
    public function convert()
    {
    
        $surnames = preg_split("/[,; ]+/", $this->in_data->surname );
        $sn = array();
        if (is_array($surnames)){

            foreach ($surnames as $surname) {
                $surname = trim($surname);
                if (!empty( $surname )) {

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_muzi_1.csv";
                    exec($command,  $sn['m1'] );

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_muzi_2.csv";
                    exec($command,  $sn['m2'] );

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_zeny_1.csv";
                    exec($command,  $sn['f1'] );

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_zeny_2.csv";
                    exec($command,  $sn['f2'] );
                    
                } 
           }
        }


        $fn = array(); 
        $firstnames = preg_split("/[,; ]+/", $this->in_data->firstname, PREG_SPLIT_NO_EMPTY );   
        if (is_array($firstnames)){

            foreach ($firstnames as $firstname) {
                preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $firstname));
                if (!empty( $firstname )) {

                    $command = "grep -i ,\\\"". $firstname ."\\\"\, names-flex/krestni_muzi.csv";
                    exec($command,  $fn['m'] );

                    $command = "grep -i ,\\\"". $firstname ."\\\"\, names-flex/krestni_zeny.csv";
                    exec($command,  $fn['f'] );

                } 
           }
        }

        $rv = array();
        $rv['surname'] = $sn;
        $rv['firstname'] =  $fn;
        $rv['call'] = "";

       // var_dump($rv);
            
        return json_encode($rv);
        
    }
   

}
