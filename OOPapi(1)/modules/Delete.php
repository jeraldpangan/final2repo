<?php
class Delete{
    protected $pdo;
    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
  
    public function deleteCars($id){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE FROM carstable WHERE carID = ?";
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
    
    public function deleteUsers($id){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE FROM userstable WHERE userID = ?";
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
    public function deleteAdmin($id){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE FROM admintable WHERE adminID = ?";
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