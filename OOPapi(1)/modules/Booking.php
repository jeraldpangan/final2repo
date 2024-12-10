<?php

class Booking
{
    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;        
    }

    // phase 1
    public function carMenu($id = null){
        $sqlString = "SELECT carID, car_brand, car_model, daily_rate, seating_capacity FROM carstable WHERE isdeleted=0";
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
    
    public function carBooking($body){
        $carID = $body->carID;
        $userID = $body->userID;  // Added userID
        $book_date = $body->book_date;
        $return_date = $body->return_date;

        $code = 0;
        $payload = null;
        $remarks = "";
        $message = "";
        $errmsg = "";

        try {
            // Check if user exists
            $userCheckSql = "SELECT COUNT(*) as count FROM userstable WHERE userID = ?";
            $userCheckStmt = $this->pdo->prepare($userCheckSql);
            $userCheckStmt->execute([$userID]);
            $userCheckResult = $userCheckStmt->fetch();

            if ($userCheckResult['count'] == 0) {
                $code = 404;
                $errmsg = "User not found.";
                return array("code"=>$code, "errmsg"=>$errmsg);
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
                // Car is already booked for the specified date range
                $code = 409; 
                $payload = null;
                $remarks = "failed";
                $message = "Try selecting different cars or dates";
                $errmsg = "Car is already booked for the selected date range.";
            } else {
                // Insert the booking with total cost calculation
                $insertSql = "
                    INSERT INTO bookingtable (carID, userID, book_date, return_date, total_cost) 
                    SELECT ?, ?, ?, ?, DATEDIFF(?, ?) * c.daily_rate 
                    FROM carstable c 
                    WHERE c.carID = ? AND c.isdeleted = 0";

                $insertStmt = $this->pdo->prepare($insertSql);
                $insertStmt->execute([$carID, $userID, $book_date, $return_date, $return_date, $book_date, $carID]);

                $code = 200;
                $remarks = "Success";
                $message = "Booking successful!";
            }
        } catch (\PDOException $e) {
            $code = 500;
            $errmsg = "Database error: " . $e->getMessage();
        }

        return array(
            "code" => $code,
            "payload" => $payload,
            "remarks" => $remarks,
            "message" => $message,
            "errmsg" => $errmsg
        );
    }
}

?>
