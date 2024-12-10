<?php 
class Get{
    protected $pdo;
    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    public function getCars($id = null){
        $sqlString = "SELECT * FROM carstable WHERE isdeleted=0";
        if($id != null){
            $sqlString .= " AND carID=" . $id; 
        }
        $data = array();
        $errmsg = "";
        $code = 0;
        
        try{
            if($result = $this->pdo->query($sqlString)->fetchAll()){
                foreach($result as $record){
                    array_push($data, $record);
                }
                $result = null;
                $code = 200;
                return array("code"=>$code, "data"=>$data); 
            }
            else{
                $errmsg = "No data found";
                $code = 404;
            }
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code"=>$code, "errmsg"=>$errmsg);
    }
   public function getUsers($id = null){
        $sqlString = "SELECT * FROM userstable";
        if($id != null){
            $sqlString .= " WHERE userID=" . $id; 
        }
        $data = array();
        $errmsg = "";
        $code = 0;
        
        try{
            if($result = $this->pdo->query($sqlString)->fetchAll()){
                foreach($result as $record){
                    array_push($data, $record);
                }
                $result = null;
                $code = 200;
                return array("code"=>$code, "data"=>$data); 
            }
            else{
                $errmsg = "No data found";
                $code = 404;
            }
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code"=>$code, "errmsg"=>$errmsg);
    }
    public function getAdmin($id = null){
        $sqlString = "SELECT * FROM admintable";
        if($id != null){
            $sqlString .= " WHERE adminID=" . $id; 
        }
        $data = array();
        $errmsg = "";
        $code = 0;
        
        try{
            if($result = $this->pdo->query($sqlString)->fetchAll()){
                foreach($result as $record){
                    array_push($data, $record);
                }
                $result = null;
                $code = 200;
                return array("code"=>$code, "data"=>$data); 
            }
            else{
                $errmsg = "No data found";
                $code = 404;
            }
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code"=>$code, "errmsg"=>$errmsg);
    }
    public function getTransaction(){
        return "this is my get transaction endpoint.";
    }
    
}
?>