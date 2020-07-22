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
                    exec($command,  $f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $sn['m1'] = $f;
                    $f = null;

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_muzi_2.csv";
                    exec($command,  $f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $sn['m2'] = $f;
                    $f = null;

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_zeny_1.csv";
                    exec($command,  $f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $sn['f1'] = $f;
                    $f = null;

                    $command = "grep -i ,\\\"". $surname ."\\\"\, names-flex/prijmeni_zeny_2.csv";
                    exec($command,  $f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $sn['f2'] = $f;
                    $f = null;
                    
                } 
           }
        }


        $fn = array(); 
        $firstnames = preg_split("/[,; ]+/", $this->in_data->firstname );   
        if (is_array($firstnames)){

            foreach ($firstnames as $firstname) {
                preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $firstname));
                if (!empty( $firstname )) {

                    $command = "grep -i ,\\\"". $firstname ."\\\"\, names-flex/krestni_muzi.csv";
                    exec($command,  $f); //var_dump($f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $fn['m'] = $f;
                    $f = "";

                    $command = "grep -i ,\\\"". $firstname ."\\\"\, names-flex/krestni_zeny.csv";
                    exec($command,  $f ); //var_dump($f);
                    $f = explode(",", $f[0]);
                    $f = $f[2];
                    $f = str_replace("\"", "", $f);
                    $fn['f'] = $f;
                    $f = "";
                    
                } 
           }
        }

        $rv = array(
                    'res' => "", 
                    'call' => "",
                    'firstname' => "",
                    'surname' => "",
                    'sex' => "", 
                    'fn' => "", 
                    'sn' => ""
                   );

        $rv['sex'] = "";

        $fn['sex'] = "";
        if  ( !empty( $fn['f']) && empty( $fn['m'])  ) {$fn['sex'] = "F";}
        if  ( !empty( $fn['m']) && empty( $fn['f'])  ) {$fn['sex'] = "M";}

        $sn['sex'] = "";
        if  ( 
              (!empty( $sn['f1']) || !empty( $sn['f2']) ) 
              && 
              ( empty( $sn['m1'])  &&  empty( $sn['m2']) ) 
            )
             { $sn['sex'] = "F"; }

        if  ( 
              (!empty( $sn['m1']) || !empty( $sn['m2']) ) 
                && 
                ( empty( $sn['f1'])  &&  empty( $sn['f2']) ) 
              )
               { $sn['sex'] = "M"; }  
               
        if (
            (  $sn['sex'] != "M" && $fn['sex'] != "M" )  
            &&
            (  $sn['sex'] == "F" || $fn['sex'] == "F" ) 
            )
            { $rv['sex'] = "F"; }  

        if (
             (  $sn['sex'] != "F" && $fn['sex'] != "F" )  
                &&
             (  $sn['sex'] == "M" || $fn['sex'] == "M" ) 
            )
                { $rv['sex'] = "M"; }      
                

        $rv['sn'] = $sn;
        $rv['fn'] =  $fn;

        if ($rv['sex'] == "M"){
            $rv['call'] = "Vážený pane";
            $rv['firstname']   = $this->in_data->firstname;
            $rv['firstname'] = $fn['m'];
            $rv['surname']   = $this->in_data->surname;
            $rv['surname']   =  (!empty($sn['m1']))? $sn['m1']: $sn['m2'];
        } elseif ($rv['sex'] == "F"){
            $rv['call'] = "Vážená paní";
            $rv['firstname']   = $this->in_data->firstname;
            $rv['firstname'] = $fn['f'];
            $rv['surname']   = $this->in_data->surname;
            $rv['surname']   =  (!empty($sn['f1']))? $sn['f1']: $sn['f2'];
        } else {
            $rv['call'] = "Vážený kliente";
        }

        $rv['res']  = $rv['call'] . " ".
                      // $rv['firstname'] . " ".
                       $rv['surname'] ;
                
        return json_encode($rv);
        
    }
   

}
