<?php

class Billing {
    protected $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;        
    }

    // Function to add payment and update billing table
    public function addPayment($body) {
        $bookingID = $body->bookingID;
        $amountPaid = $body->amount_paid;
        $code = 0;
        $errmsg = "";

        try {
            // Validate if the booking exists
            $bookingSql = "SELECT bookingID, userID FROM bookingtable WHERE bookingID = ?";
            $bookingStmt = $this->pdo->prepare($bookingSql);
            $bookingStmt->execute([$bookingID]);
            $booking = $bookingStmt->fetch();

            if (!$booking) {
                return array("code" => 404, "errmsg" => "Booking not found.");
            }

            // Insert payment into billing table
            $insertSql = "
                INSERT INTO billingtable (bookingID, amount_paid) 
                VALUES (?, ?)";
            $insertStmt = $this->pdo->prepare($insertSql);
            $insertStmt->execute([$bookingID, $amountPaid]);

            // Update user's total amount paid (VIP points)
            $this->updateVIPPoints($booking['userID'], $amountPaid);

            return array(
                "code" => 200,
                "remarks" => "Success",
                "message" => "Payment successfully recorded!"
            );

        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "errmsg" => "Database error: " . $e->getMessage()
            );
        }
    }

    // Function to update VIP points for the user
    public function updateVIPPoints($userID, $amountPaid) {
        try {
            // Update the total amount paid by the user in accountstable (VIP points)
            $updateSql = "
                UPDATE accountstable 
                SET vip_points = vip_points + ? 
                WHERE userID = ?";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([$amountPaid, $userID]);

            // Check if the user's VIP points reach the threshold for VIP access
            $this->checkVIPAccess($userID);

        } catch (\PDOException $e) {
            error_log("Error updating VIP points: " . $e->getMessage());
        }
    }

    // Function to check if the user should be granted VIP access
    private function checkVIPAccess($userID) {
        try {
            // Fetch the user's current VIP points
            $checkSql = "SELECT vip_points FROM accountstable WHERE userID = ?";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$userID]);
            $user = $checkStmt->fetch();

            if ($user && $user['vip_points'] >= 500000) {
                // Grant the user access to expensive cars
                $this->grantVIPAccess($userID);
            }
        } catch (\PDOException $e) {
            error_log("Error checking VIP access: " . $e->getMessage());
        }
    }

    // Function to grant VIP access to expensive cars
    private function grantVIPAccess($userID) {
        try {
            $updateSql = "UPDATE accountstable SET vip_access = 1 WHERE userID = ?";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([$userID]);
        } catch (\PDOException $e) {
            error_log("Error granting VIP access: " . $e->getMessage());
        }
    }

    // Function to compute the total amount paid by a user across all transactions
    public function getTotalPaid($userID) {
        try {
            // Calculate the total amount paid by the user
            $sql = "
                SELECT SUM(amount_paid) as total_paid 
                FROM billingtable b
                INNER JOIN bookingtable bt ON b.bookingID = bt.bookingID
                WHERE bt.userID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userID]);
            $result = $stmt->fetch();

            return array(
                "code" => 200,
                "total_paid" => $result['total_paid'] ?? 0
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
