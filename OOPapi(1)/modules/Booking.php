<?php

class Booking
{
    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;        
    }

    // phase 1
    public function carMenu($id = null, $userID = null){
        $sqlString = "SELECT carID, car_brand, car_model, daily_rate, seating_capacity FROM carstable WHERE isdeleted=0";
        
        // If user is provided, check their VIP points
        if ($userID != null) {
            // Get VIP points of the user
            $vipCheckSql = "SELECT vip_points FROM accountstable WHERE userID = ?";
            $vipCheckStmt = $this->pdo->prepare($vipCheckSql);
            $vipCheckStmt->execute([$userID]);
            $vipResult = $vipCheckStmt->fetch();
            
            if ($vipResult) {
                $vipPoints = $vipResult['vip_points'];
                
                // If VIP points are >= 500,000, allow luxury cars (daily_rate >= 200,000)
                if ($vipPoints >= 500000) {
                    $sqlString .= " OR daily_rate >= 200000";  // Allow luxury cars
                }
            }
        }
        
        if ($id != null) {
            $sqlString .= " AND carID=" . $id; 
        }
        
        $data = array();
        $errmsg = "";
        $code = 0;
        
        try {
            if($result = $this->pdo->query($sqlString)->fetchAll()) {
                foreach($result as $record) {
                    array_push($data, $record);
                }
                $result = null;
                $code = 200;
                return array("code"=>$code, "data"=>$data); 
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch(\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }
        
        return array("code"=>$code, "errmsg"=>$errmsg);
    }
    
    public function carBooking($body){
        $carID = $body->carID;
        $userID = $body->userID;
        $book_date = $body->book_date;
        $return_date = $body->return_date;
    
        $code = 0;
        $errmsg = "";
        
        try {
            // Check if user exists
            $userCheckSql = "SELECT COUNT(*) as count, vip_points FROM accountstable WHERE userID = ?";
            $userCheckStmt = $this->pdo->prepare($userCheckSql);
            $userCheckStmt->execute([$userID]);
            $userCheckResult = $userCheckStmt->fetch();
    
            if ($userCheckResult['count'] == 0) {
                return array("code" => 404, "errmsg" => "User not found.");
            }
    
            // Get VIP points of the user
            $vipPoints = $userCheckResult['vip_points'];
            
            // Check if the car is a luxury car and if the user has enough VIP points
            $carCheckSql = "SELECT daily_rate FROM carstable WHERE carID = ?";
            $carCheckStmt = $this->pdo->prepare($carCheckSql);
            $carCheckStmt->execute([$carID]);
            $carData = $carCheckStmt->fetch();
    
            if ($carData && $carData['daily_rate'] >= 200000 && $vipPoints < 500000) {
                return array(
                    "code" => 403,
                    "errmsg" => "You don't have enough VIP points to book a luxury car."
                );
            }
    
            // Check for overlapping bookings
            $checkSql = "
                SELECT COUNT(*) as count 
                FROM bookingtable 
                WHERE carID = ? 
                  AND ((book_date BETWEEN ? AND ?) 
                       OR (return_date BETWEEN ? AND ?) 
                       OR (book_date <= ? AND return_date >= ?))";
    
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$carID, $book_date, $return_date, $book_date, $return_date, $book_date, $return_date]);
            $result = $checkStmt->fetch();
    
            if ($result['count'] > 0) {
                return array(
                    "code" => 409,
                    "errmsg" => "Car is already booked for the selected date range."
                );
            }
    
            // Insert the booking
            $insertSql = "
                INSERT INTO bookingtable (carID, userID, book_date, return_date, total_cost) 
                SELECT ?, ?, ?, ?, 
                       CASE 
                           WHEN DATEDIFF(?, ?) > 0 THEN DATEDIFF(?, ?) * c.daily_rate
                           ELSE c.daily_rate
                       END AS total_cost
                FROM carstable c 
                WHERE c.carID = ? AND c.isdeleted = 0";
    
            $insertStmt = $this->pdo->prepare($insertSql);
            $insertStmt->execute([$carID, $userID, $book_date, $return_date, $return_date, $book_date, $return_date, $book_date, $carID]);
    
            return array(
                "code" => 200,
                "remarks" => "Success",
                "message" => "Booking successful!"
            );
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "errmsg" => "Database error: " . $e->getMessage()
            );
        }
    }
    
    
}

?>
