<?php 
class Post{
    protected $pdo;
    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function postCars($body){
        $values = [];
        $errmsg = "";
        $code = 0;
        foreach($body as $value){
            array_push($values, $value);
        }
        
        try{
            $sqlString = "INSERT INTO carstable(carID,car_brand, car_model, manu_year, daily_rate, AC, seating_capacity, plate_no, isdeleted) VALUES (?,?,?,?,?,?,?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);
            $message="Car added successfully";
            $code = 200;
            $data = null;
            return array("message"=>$message,"data"=>$data, "code"=>$code);
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 400;
        }
        
        return array("errmsg"=>$errmsg, "code"=>$code);
    }
    public function postUsers($body) {
        $errmsg = "";
        $code = 0;
    
        // Ensure the body contains required properties
        if (!isset($body->name) || !isset($body->contact_no) || !isset($body->drivers_license)) {
            return array("errmsg" => "Invalid input data", "code" => 400);
        }
    
        // Prepare values for insertion
        $values = [$body->name, $body->contact_no, $body->drivers_license, 0];
    
        try {
           
            // Check if driver's license is already associated with another user
            $sqlCheckLicense = "SELECT userID FROM userstable WHERE drivers_license = ?";
            $sqlLicenseCheck = $this->pdo->prepare($sqlCheckLicense);
            $sqlLicenseCheck->execute([$body->drivers_license]);
    
            if ($sqlLicenseCheck->rowCount() > 0) {
                $errmsg = "Driver's license number is already associated with another user.";
                $code = 400;
                return array("errmsg" => $errmsg, "code" => $code);
            }
    
            // Insert the new user
            $sqlString = "INSERT INTO userstable(name, contact_no, drivers_license, isdeleted) VALUES (?, ?, ?, ?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);
    
            // Retrieve the inserted userID
            $userID = $this->pdo->lastInsertId();
            $message = "User added successfully. Register an account to login. Your userID is " . $userID;
    
            $code = 200;
            $data = null;
            return array("message" => $message, "data" => $data, "code" => $code);
        } catch (\PDOException $e) {
            // Log error details securely
            error_log("Database error: " . $e->getMessage());
            $errmsg = "An error occurred while processing your request.";
            $code = 400;
        }
    
        return array("errmsg" => $errmsg, "code" => $code);
    }
    
    
    
    public function postAdmin($body){
        $values = [];
        $errmsg = "";
        $code = 0;
        foreach($body as $value){
            array_push($values, $value);
        }
        
        try{
            $sqlString = "INSERT INTO admintable(lname,fname,admin_email, admin_password,isdeleted) VALUES (?,?,?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);
            $code = 200;
            $data = null;
            return array("data"=>$data, "code"=>$code);
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 400;
        }
        
        return array("errmsg"=>$errmsg, "code"=>$code);
    
    }
    public function postTransaction(){
        return "postTransaction to pre";
    }
}
?>