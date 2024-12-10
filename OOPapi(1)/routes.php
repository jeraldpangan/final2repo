<?php
require_once "./modules/Get.php";
require_once "./modules/Post.php";
require_once "./config/database.php";
require_once "./modules/Patch.php";
require_once "./modules/Delete.php";
require_once "./modules/Auth.php";
require_once "./modules/Booking.php";
require_once "./modules/Billing.php";
$db = new Connection();
$pdo = $db->connect();
$post = new Post($pdo);
$get = new Get($pdo);
$patch = new Patch($pdo);
$delete = new Delete($pdo);
$auth = new Authentication($pdo);
$book = new Booking($pdo);
$bill = new Billing($pdo);

if(isset($_REQUEST['request'])){
    $request = explode("/", $_REQUEST['request']);
}
else{
    echo "URL does not exist.";
}
switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        if($auth->isAuthorized()){
        switch($request[0]){
            case "cars":
                if(count($request) > 1){
                    echo json_encode($get->getCars($request[1]));
                }
                else{
                    echo json_encode($get->getCars());
                }
            break;
            case "users":
                if(count($request) > 1){
                    echo json_encode($get->getUsers($request[1]));
                }
                else{
                    echo json_encode($get->getUsers());
                }
            break;
            case "admin":
                if(count($request) > 1){
                    echo json_encode($get->getAdmin($request[1]));
                }
                else{
                    echo json_encode($get->getAdmin());
                }
            break;
            case "transaction":
                echo $get->getTransaction();
            break;
            case"carchecking":
                echo json_encode($book->carMenu());
            break;
            case "billing":
                echo json_encode($bill->getBillingDetails($request[1], $request[2]));
            break;
            default:
                http_response_code(401);
                echo "Invalid endpoint";
            break;
        }
    
        }else{
            echo "Unauthorized";
        }
        break;
    case "POST":
        $body = json_decode(file_get_contents("php://input"));
        switch($request[0]){
            case "cars":
                echo json_encode($post->postCars($body));
            break;
            case "users":
                echo json_encode($post->postUsers($body));
            break;
            case "admin":
                echo json_encode($post->postAdmin($body));
            break;
            case "transaction":
                echo $post->postTransaction();
            break;
            case "useraccount":
                echo json_encode($auth->addAccounts($body));
            break;
            
            case "login":
                echo json_encode($auth->login($body));
            break;
            
            case "carbooking":
                if($auth->isAuthorized()){
                echo json_encode($book->carBooking($body));
            break;
                }else{
                    echo "Unauthorized";
                }
            default:
                http_response_code(400);
                echo "Invalid endpoint";
            break;
        }
    break;
    case "PATCH":
        $body = json_decode(file_get_contents("php://input"));
        switch($request[0]){
            case "cars":
                echo json_encode($patch->patchCars($body, $request[1]));
            break;
        }
        break;
    case "DELETE":
        switch($request[0]){
            case "cars":
                echo json_encode($patch->archiveCars($request[1]));
            break;
        }
            case "destroycars":
                echo json_encode($delete->deleteCars($request[1]));
            break;
    default:
        http_response_code(400);
        echo "Invalid Request Method";
    break;
}
?>