<?php

session_start();
//session_destroy();

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
if (isset($_SESSION['token'])) {
	$infusionsoft->setToken(unserialize($_SESSION['token']));
}

// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
	$infusionsoft->requestAccessToken($_GET['code']);
	$_SESSION['token'] = serialize($infusionsoft->getToken());
}

function resthookManager($infusionsoft) {
	$resthooks = $infusionsoft->resthooks();

	// first, create a new task
	$resthook = $resthooks->create([
		'eventKey' => 'contact.add',
		'hookUrl' => 'http://infusionsoft.app/verifyRestHook.php'
	]);
    var_dump($resthook);
	$resthook = $resthooks->find($resthook->id)->verify();

	return $resthook;
}

if ($infusionsoft->getToken()) {
	try {
		$resthook = resthookManager($infusionsoft);
	}
	catch (\Infusionsoft\TokenExpiredException $e) {
		// If the request fails due to an expired access token, we can refresh
		// the token and then do the request again.
		$infusionsoft->refreshAccessToken();

		// Save the serialized token to the current session for subsequent requests
		$_SESSION['token'] = serialize($infusionsoft->getToken());

		$resthook = resthookManager($infusionsoft);
	}

    $tokenObj = $infusionsoft->getToken();
    echo $tokenObj->accessToken;
    $infusionsoft->data()->load();
    $infusionsoft->refreshAccessToken();

    $table = 'OrderItem';
    $recordID = '45';
    $fields = array('OrderId','ProductId','SubscriptionPlanId','ItemName','Qty','CPU','ItemDescription','PPU','Notes');
    $dataObj = $infusionsoft->data()->load($table, $recordID, $fields);

    echo json_encode(new OrderItem($dataObj));
	//var_dump($resthook);
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