<?php

session_start();

require_once '../vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => '44dwuap5k2vbe7mqy7ex495g',
    'clientSecret' => 'p4jtNRuyE7',
    'redirectUri' => 'http://localhost/iphp/data/Order.php',
));

// By default, the SDK uses the Guzzle HTTP library for requests. To use CURL,
// you can change the HTTP client by using following line:
// $infusionsoft->setHttpClient(new \Infusionsoft\Http\CurlClient());

// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.



$tok = new \Infusionsoft\Token();
$tok->setRefreshToken('27paydbnudfy8fxrr8z2k9e3');
$tok->setAccessToken('ut3dd7jvaztrsv8kr7kwqj5v');
$tok->setEndOfLife(1490029370);
$infusionsoft->setToken($tok);




//if (isset($_SESSION['token'])) {
//	$infusionsoft->setToken(unserialize($_SESSION['token']));
//}

// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
    $infusionsoft->requestAccessToken($_GET['code']);
}



function taskManager($infusionsoft) {
    $tasks = $infusionsoft->tasks();

    // first, create a new task
    $task = $tasks->create([
        'title' => 'Test Task',
        'description' => 'This is the task description'
    ]);

    // oops, we wanted a different title
    $task->title = 'Real Test Task';
    $task->save();

    return $task;
}

if ($infusionsoft->getToken()) {;

    $table = 'OrderItem';
    $recordID = '45';
    $fields = array('OrderId','ProductId','SubscriptionPlanId','ItemName','Qty','CPU','ItemDescription','PPU','Notes');
    $queryData = array('OrderId' => '~>~0');//'LastUpdated => ~>~ 2017-01-01 00:00:00';
    try{
        //$dataObj = $infusionsoft->data()->load($table, $recordID, $fields);
        //echo json_encode(new OrderItem($dataObj));

        $dataObj = $infusionsoft->data()->query($table, 100, 0, $queryData, $fields, 'OrderId',false);

        $darr = array();
        $count = 0;
        foreach ($dataObj as &$value) {
            $darr[$count]= new OrderItem($value);
            $count++;
           // echo json_encode(new OrderItem($value));
        }
        $res = array();
        $res['status']=true;
        $res['message']='';
        $res['data']=$darr;
        echo json_encode($res);
      //  $out = array_values(json_encode($darr));

        //echo     json_encode($out);
     //   echo json_encode(new OrderItem($dataObj));


    }catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
}
else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}

class OrderItem{
    public $OrderId;
    public $ProductId;
    public $SubscriptionPlanId;
    public $ItemName;
    public $Qty;
    public $CPU;
    public $ItemDescription;
    public $PPU;
    public $Notes;

//    function OrderItem()
//    {
//        return $this;
//    }


    function OrderItem($arr){
        $this->OrderId = $arr['OrderId']==NULL?NULL:$arr['OrderId'];
        $this->ProductId = $arr['ProductId']==NULL?NULL:$arr['ProductId'];
        $this->SubscriptionPlanId = $arr['SubscriptionPlanId']==NULL?NULL:$arr['SubscriptionPlanId'];
        $this->ItemName = $arr['ItemName']==NULL?NULL:$arr['ItemName'];
        $this->CPU = $arr['CPU']==NULL?NULL:$arr['CPU'];
        $this->PPU = $arr['PPU']==NULL?NULL:$arr['PPU'];
        $this->Qty = $arr['Qty']==NULL?NULL:$arr['Qty'];
        //   $this->ItemDescription = getValue($arr,'ItemDescription');
        // $this->Notes = getValue($arr,'Notes');
        return $this;
    }
}

function getValue($arr,$key){
    $val = NULL;
    // return
    if($arr[$key])
        $val = $arr[$key];
    return $val;
}