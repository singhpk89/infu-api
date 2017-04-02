<?php


session_start();

require_once '../vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => '44dwuap5k2vbe7mqy7ex495g',
    'clientSecret' => 'p4jtNRuyE7',
    'redirectUri' => 'http://localhost/iphp/data/token.php',
));




if (isset($_SESSION['token'])) {
    $infusionsoft->setToken(unserialize($_SESSION['token']));
}

if (isset($_GET['code']) and !$infusionsoft->getToken()) {
    $infusionsoft->requestAccessToken($_GET['code']);
}

function taskManager($infusionsoft) {
    $tasks = $infusionsoft->tasks();
    $task = $tasks->create([
        'title' => 'Test Task',
        'description' => 'This is the task description'
    ]);
    $task->title = 'Real Test Task';
    $task->save();

    return $task;
}

if ($infusionsoft->getToken()) {
    try {
        $task = taskManager($infusionsoft);
    }
    catch (\Infusionsoft\TokenExpiredException $e) {
        $infusionsoft->refreshAccessToken();
        $_SESSION['token'] = serialize($infusionsoft->getToken());
        $task = taskManager($infusionsoft);
    }
    getOrderItem($infusionsoft);
}
else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}


function getOrderItem($infusionsoft){
    saveToken($infusionsoft);
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
        //writeToFile();
        //  $out = array_values(json_encode($darr));

        //echo     json_encode($out);
        //   echo json_encode(new OrderItem($dataObj));


    }catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
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



  function saveToken($infusionsoft){
      $host = 'localhost';
      $dbname = 'token';
      $username = 'root';
      $password = '';


      try {
          $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
          echo "Connected to $dbname at $host successfully.";
          $sql = 'INSERT INTO infusionsoft VALUES('. $infusionsoft->getClientId().','.$infusionsoft->getClientSecret().','.$infusionsoft->token->accessToken.','.$infusionsoft->token->refreshToken.','.$infusionsoft->token->endOfLife.')';
          $conn->query($sql);
          $conn->close();

      } catch (PDOException $pe) {
          die("Could not connect to the database $dbname :" . $pe->getMessage());
      }
  }


function getToken(){
    $host = 'localhost';
    $dbname = 'token';
    $username = 'root';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "Connected to $dbname at $host successfully.";

        $sql = "SELECT * FROM ";
        $result = $conn->query($sql);

        $conn->query($sql);
        $conn->close();

    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
}