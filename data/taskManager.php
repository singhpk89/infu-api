<?php

session_start();

require_once '../vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId' => '44dwuap5k2vbe7mqy7ex495g',
    'clientSecret' => 'p4jtNRuyE7',
    'redirectUri' => 'http://localhost/iphp/data/taskManager.php',
));

// By default, the SDK uses the Guzzle HTTP library for requests. To use CURL,
// you can change the HTTP client by using following line:
// $infusionsoft->setHttpClient(new \Infusionsoft\Http\CurlClient());

// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.
if (isset($_SESSION['token'])) {
	$infusionsoft->setToken(unserialize($_SESSION['token']));
}

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

if ($infusionsoft->getToken()) {
	try {
		$task = taskManager($infusionsoft);
	}
	catch (\Infusionsoft\TokenExpiredException $e) {
		// If the request fails due to an expired access token, we can refresh
		// the token and then do the request again.
		$infusionsoft->refreshAccessToken();

		// Save the serialized token to the current session for subsequent requests
		$_SESSION['token'] = serialize($infusionsoft->getToken());

		$task = taskManager($infusionsoft);
	}

    //echo json_encode($infusionsoft);
	//print_r($infusionsoft);
   // $infusionsoft->a

        var_dump($task);

   // getOrderItem($infusionsoft);
}
else {
	echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}


function getOrderItem($infusionsoft){
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

function saveData(){
    $servername = "localhost";
    $username = "walleriy_domo";
    $password = "Prakash123$$";
    $dbname = "walleriy_infusion";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO infusionsoft (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

    $sql = "INSERT INTO 'infusionsoft'('clientId', 'clientSecret', 'accessToken', 'refreshToken', 'lifeTime') VALUES 
('1','1','1','1','')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

