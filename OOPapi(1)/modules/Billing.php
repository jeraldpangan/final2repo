<?php

class Billing
{
    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;        
    }
    public function createBilling($body) {
        $bookingID = $body->bookingID;
        $carID = $body->carID;
        $userID = $body->userID;
        $totalCost = $body->total_cost;
        $amountPaid = $body->amount_paid;
    
        $sqlString = "INSERT INTO billingtable (bookingID, carID, userID, total_cost, amount_paid) 
        VALUES (?, ?, ?, ?, ?)";
        $code = 0;
        $errmsg = "";
        
        try {
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$bookingID, $carID, $userID, $totalCost, $amountPaid]);
            $code = 200;
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 500;
        }
    
        return ["code" => $code, "errmsg" => $errmsg];
    }
    public function updateBilling($billingID, $amountPaid) {
        $sqlString = "UPDATE billingtable 
            SET amount_paid = amount_paid + ?, 
            payment_status = CASE 
            WHEN total_cost = amount_paid + ? THEN 'Paid'
            WHEN total_cost > amount_paid + ? THEN 'Partial'
            ELSE 'Pending'
            END,
            payment_date = CASE 
            WHEN total_cost = amount_paid + ? THEN NOW()
            ELSE payment_date
            END
            WHERE billingID = ?";
        $code = 0;
        $errmsg = "";
    
        try {
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$amountPaid, $amountPaid, $amountPaid, $amountPaid, $billingID]);
            $code = 200;
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 500;
        }
    
        return ["code" => $code, "errmsg" => $errmsg];
    }
    public function getBillingDetails($bookingID = null, $userID = null) {
        $sqlString = "SELECT * FROM billingtable WHERE 1=1";
        if ($bookingID) {
            $sqlString .= " AND bookingID = ?";
        }
        if ($userID) {
            $sqlString .= " AND userID = ?";
        }
    
        $code = 0;
        $data = [];
        $errmsg = "";
    
        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute(array_filter([$bookingID, $userID]));
            $data = $stmt->fetchAll();
            $code = 200;
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 500;
        }
    
        return ["code" => $code, "data" => $data, "errmsg" => $errmsg];
    }
    public function calculateTotalCost($dailyRate, $startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Include the start day
        return $dailyRate * $days;
    }

}