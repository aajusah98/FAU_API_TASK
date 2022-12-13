<?php 
$response = file_get_contents('https://cris.fau.de/ws-cached/1.0/public/infoobject/getautorelated/person/sy99hacy/pers_2_publ_1');
$xml = simplexml_load_string($response);
$json = json_encode($xml);
$publication_data = json_decode($json,TRUE);

//  get all data which have name="quotationAPA"
$publication_type_data=[];

//  get all data which have name="Publication type"
$quotationAPA_data = [];

//  get all data which have name="publYear"
$publYear_data=[];

//  get all data which have name="cfTitle"
$publlicationTitle_data=[];


function group_by($key, $data) {
    $result = array();

    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}

foreach($publication_data['infoObject'] as $key ){
    foreach ($key['attribute'] as $attribute) {
        if ($attribute['@attributes']['name']=='quotationAPA') {
           $quotationAPA_data[]=$attribute['data'];
        }        
        if ($attribute['@attributes']['name']=='Publication type') {   
                $publication_type_data[]=$attribute['additionalInfo'];         
            }

        if ($attribute['@attributes']['name']=='publYear') {   
            $publYear_data[]=$attribute['data'];         
        }

        
        if ($attribute['@attributes']['name']=='cfTitle') {   
            $publlicationTitle_data[]=$attribute['data'];         
        }
    }
}

// merging publication , publication type and pubyear in one array
$final_output=array();

for ($i=0; $i< count($quotationAPA_data) ; $i++) { 

    $final_output[]=array('quotationAPA'=>$quotationAPA_data[$i],'publicationType'=>$publication_type_data[$i],'publYear'=>$publYear_data[$i],'cfTitle'=>$publlicationTitle_data[$i]);

}

if (isset($_GET['order']) && !empty($_GET['order'])) {
    $oderdate = group_by($_GET['order'], $final_output);
    ksort($oderdate);
}
else{
    $oderdate = group_by('publYear', $final_output);
    ksort($oderdate); 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <title>FAU PUBLICATION API</title>
</head>

<body style="padding: 2rem;">

    <div class="container">
        <div class="row">

            <div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Order
                </a>

                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="?order=publYear">publYear</a>
                    </li>
                    <li><a class="dropdown-item" href="?order=publicationType">publicationType</a>
                    </li>
                </ul>
            </div>

            <div class="accordion" id="accordionExample">
                <?php $i=1; foreach ($oderdate as $yearoder) {?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php print_r($yearoder[0]['publYear']); if (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publicationType") {
                        echo $i;
                    } ?>">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php print_r($yearoder[0]['publYear']); if (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publicationType") {
                        echo $i;
                    } ?>" aria-expanded="true" aria-controls="collapse<?php print_r($yearoder[0]['publYear']); ?>">
                            <?php  if (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publYear") {
                                print_r($yearoder[0]['publYear']); 
                            }
                            elseif (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publicationType") {
                                print_r($yearoder[0]['publicationType']); 
                            }
                            else{
                                print_r($yearoder[0]['publYear']); 
                            }
                                
                                ?>
                        </button>
                    </h2>
                    <div id="collapse<?php print_r($yearoder[0]['publYear']);if (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publicationType") {
                        echo $i;
                    } ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php print_r($yearoder[0]['publYear']);if (isset($_GET['order']) && !empty($_GET['order']) && $_GET['order']=="publicationType") {
                        echo $i;
                    } ?>" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class='row'>
                                <?php  foreach ($yearoder as $key ) { ?>

                                <div class="col" style="padding:1rem;">
                                    <div class="card" style="width: 20rem;">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php print_r($key['cfTitle']); ?>
                                            </h6>
                                            <p class="card-text">
                                                <?php print_r($key['quotationAPA']); ?>

                                            <p>
                                                <b>Publiation Year :</b> <?php print_r($key['publYear']); ?>
                                            </p>
                                            <p>
                                                <b> Publiation Type : </b>
                                                <?php print_r($key['publicationType']); ?>
                                            </p>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php  } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; } ?>
            </div>



        </div>
    </div>


</body>

</html>