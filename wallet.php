<?php

include 'config.php';

function random_strings($length_of_string) {
        
    return substr(sha1(time()), 0, $length_of_string);
}


function generate_referal_code()
{
    $rcode = random_strings(14);
    return $rcode;
}  
if ($_SERVER['REQUEST_METHOD'] == "POST") 

{
    if (isset($_POST)) 
{
    $wallet= htmlspecialchars($_POST['wallet']);

    if (empty($wallet) == true || strlen($wallet) < 40) 
    {
        header("Content-Type: application/json");
        http_response_code(400);
        $message = json_encode(array("message" => "Wallet address is invalid", "status" => false));	
        echo $message;
        exit();        
    }
    
    $wallet_id = uniqid();
    $total = 0;

    $rcode = generate_referal_code();

    $sql = "INSERT INTO `wallets`( `id`, `wallet`,`rlink`, `rtotal`)
		    VALUES ('$wallet_id','$wallet', '$rcode','$total')";
    try 
    {
        header("Content-Type: application/json");
        mysqli_query($conn, $sql);
        http_response_code(201);
        $message = json_encode(array("message" => "Data submitted successfully","ref_code" => $rcode, "status" => true));	
        echo $message;
        exit();

    } 
    catch (\Throwable $th) 
    {
        header("Content-Type: application/json");
        http_response_code(500);
        $message = json_encode(array("message" => mysqli_error($conn), "status" => false));	
        echo $message;
    }
}
else
{
    header("Content-Type: application/json");
    http_response_code(400);
    $message = json_encode(array("message" => "Wallet address is invalid", "status" => false));	
    echo $message;
    exit();
}
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    header("Content-Type: application/json");
    
    //$data  = file_get_contents('php://input');
    $json_string = file_get_contents( "php://input");
    $data = json_decode($json_string,true);
    echo $data;
    echo "Hello Put Req";
    exit();
    if(isset($data->refCode) == false || empty($data->refCode) == true) 
    {
        header("Content-Type: application/json");
        http_response_code(400);
        $message = json_encode(array("message" => "Invalid Referal Code", "status" => false));	
        echo $message;
        exit();
    }

    $refCode = $data->refCode;

    $sqlQuery = "SELECT * FROM wallets WHERE rlink = '$refCode'";
    $queryResult = mysqli_query($conn,$sqlQuery);

    if($queryResult != false)
    {

        $row = mysqli_fetch_assoc($queryResult);
        
        $rowId = $row['id'];
        $newTotal = $row['rtotal'] + 1;
    
        $sqlQuery = "UPDATE wallets SET rtotal = '$newTotal' WHERE id = '$rowId'";
    
        // Free result set
        mysqli_free_result($queryResult);
    }

    mysqli_close($con);
    http_response_code(204);    
    exit();
}

?>