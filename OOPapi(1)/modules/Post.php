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
    public function postUsers($body){
        $values = [];
        $errmsg = "";
        $code = 0;
        foreach($body as $value){
            array_push($values, $value);
        }
        
        try{
            $sqlString = "INSERT INTO userstable(name,user_email, user_password, contact_no,isdeleted) VALUES (?,?,?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);
            $code = 200;
            $message="User added successfully";
            $data = null;
            return array("message"=>$message,"data"=>$data, "code"=>$code);
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 400;
        }
        
        return array("errmsg"=>$errmsg, "code"=>$code);
    
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