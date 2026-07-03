<?php
// app/Services/CreditService.php
namespace App\Services;

use App\Config\Database;
use App\Models\{User, CreditLedger};

class CreditService
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Award credits after successful payment.
     * Uses a DB transaction to ensure atomicity.
     */
    public function awardPurchaseCredits(int $userId, int $transactionId, float $credits, string $pfPaymentId): bool
    {
        try {
            $this->db->beginTransaction();

            // Lock the user row for update
            $stmt = $this->db->prepare("SELECT credit_balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            $newBalance = (float)$user['credit_balance'] + $credits;

            // Update balance
            $this->db->prepare("UPDATE users SET credit_balance = ? WHERE id = ?")
                     ->execute([$newBalance, $userId]);

            // Write ledger entry
            $this->db->prepare("
                INSERT INTO credit_ledger (user_id, type, amount, balance_after, reference_id, reference_type, notes)
                VALUES (?, 'purchase', ?, ?, ?, 'transaction', ?)
            ")->execute([$userId, $credits, $newBalance, $transactionId, "PayFast ref: {$pfPaymentId}"]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('[CreditService] awardPurchaseCredits failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deduct credits per billing tick during a call.
     * Returns the new balance, or false if insufficient.
     */
    public function deductCallCredits(int $userId, int $callId, float $amount): float|false
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT credit_balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if ((float)$user['credit_balance'] < $amount) {
                $this->db->rollBack();
                return false;
            }

            $newBalance = (float)$user['credit_balance'] - $amount;

            $this->db->prepare("UPDATE users SET credit_balance = ? WHERE id = ?")
                     ->execute([$newBalance, $userId]);

            $this->db->prepare("
                INSERT INTO credit_ledger (user_id, type, amount, balance_after, reference_id, reference_type)
                VALUES (?, 'call_debit', ?, ?, ?, 'call')
            ")->execute([$userId, -$amount, $newBalance, $callId]);

            $this->db->commit();

            if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $userId) {
                $_SESSION['credits'] = $newBalance;
            }

            return $newBalance;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('[CreditService] deductCallCredits failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getBalance(int $userId): float
    {
        $stmt = $this->db->prepare("SELECT credit_balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return (float)($stmt->fetchColumn() ?? 0);
    }
}