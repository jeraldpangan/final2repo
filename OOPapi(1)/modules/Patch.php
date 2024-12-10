<?php
class Patch{
    protected $pdo;
    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    public function patchCars($body, $id){
        $values = [];
        $errmsg = "";
        $code = 0;
        foreach($body as $value){
            array_push($values, $value);
        }
        array_push($values, $id);
        
        try{
            $sqlString = "UPDATE carstable SET daily_rate=?, plate_no=? WHERE carID = ?";
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
    public function archiveCars($id){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE carstable SET isdeleted=1 WHERE carID = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$id]);
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
    public function patchUsers($body, $id){
        $values = [];
        $errmsg = "";
        $code = 0;
        foreach($body as $value){
            array_push($values, $value);
        }
        array_push($values, $id);
        
        try{
            $sqlString = "UPDATE userstable SET user_password =?, user_email=? WHERE userID = ?";
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
    public function archiveUsers($id){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE userstable SET isdeleted=1 WHERE userID = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$id]);
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
    
}
?>