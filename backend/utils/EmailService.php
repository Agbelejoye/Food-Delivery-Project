<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = SMTP_PORT;

            // Default sender
            $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        } catch (Exception $e) {
            error_log("Email configuration failed: " . $e->getMessage());
        }
    }

    public function sendWelcomeEmail($userEmail, $userName) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail, $userName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Welcome to FoodExpress!';
            
            $htmlBody = $this->getWelcomeEmailTemplate($userName);
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Welcome email failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendOrderConfirmation($userEmail, $userName, $orderData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail, $userName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Order Confirmation - #' . $orderData['order_number'];
            
            $htmlBody = $this->getOrderConfirmationTemplate($userName, $orderData);
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Order confirmation email failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendOrderStatusUpdate($userEmail, $userName, $orderData, $status) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail, $userName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Order Update - #' . $orderData['order_number'];
            
            $htmlBody = $this->getOrderStatusTemplate($userName, $orderData, $status);
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Order status email failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendPasswordReset($userEmail, $userName, $resetToken) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail, $userName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Reset Request';
            
            $resetLink = FRONTEND_URL . '/reset-password?token=' . $resetToken;
            $htmlBody = $this->getPasswordResetTemplate($userName, $resetLink);
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Password reset email failed: " . $e->getMessage());
            return false;
        }
    }

    private function getWelcomeEmailTemplate($userName) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #007bff; text-align: center;'>Welcome to FoodExpress!</h1>
                <p>Dear {$userName},</p>
                <p>Thank you for joining FoodExpress! We're excited to have you as part of our community.</p>
                <p>With FoodExpress, you can:</p>
                <ul>
                    <li>Browse hundreds of restaurants in your area</li>
                    <li>Order your favorite meals with just a few clicks</li>
                    <li>Track your orders in real-time</li>
                    <li>Enjoy fast and reliable delivery</li>
                </ul>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . FRONTEND_URL . "' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Start Ordering Now</a>
                </p>
                <p>If you have any questions, feel free to contact our support team.</p>
                <p>Best regards,<br>The FoodExpress Team</p>
            </div>
        </body>
        </html>
        ";
    }

    private function getOrderConfirmationTemplate($userName, $orderData) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #28a745; text-align: center;'>Order Confirmed!</h1>
                <p>Dear {$userName},</p>
                <p>Your order has been confirmed and is being prepared.</p>
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Order Details</h3>
                    <p><strong>Order Number:</strong> #{$orderData['order_number']}</p>
                    <p><strong>Total:</strong> $" . number_format($orderData['total'], 2) . "</p>
                    <p><strong>Estimated Delivery:</strong> " . date('M j, Y g:i A', strtotime($orderData['estimated_delivery_time'])) . "</p>
                </div>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . FRONTEND_URL . "/orders/{$orderData['id']}' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Track Your Order</a>
                </p>
                <p>Thank you for choosing FoodExpress!</p>
                <p>Best regards,<br>The FoodExpress Team</p>
            </div>
        </body>
        </html>
        ";
    }

    private function getOrderStatusTemplate($userName, $orderData, $status) {
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed and is being prepared.',
            'preparing' => 'Your order is being prepared by the restaurant.',
            'ready_for_pickup' => 'Your order is ready and waiting for pickup.',
            'picked_up' => 'Your order has been picked up and is on the way.',
            'on_the_way' => 'Your order is on the way to you.',
            'delivered' => 'Your order has been delivered. Enjoy your meal!',
            'cancelled' => 'Your order has been cancelled.'
        ];

        $message = $statusMessages[$status] ?? 'Your order status has been updated.';

        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #007bff; text-align: center;'>Order Update</h1>
                <p>Dear {$userName},</p>
                <p>{$message}</p>
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Order Number:</strong> #{$orderData['order_number']}</p>
                    <p><strong>Status:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</p>
                </div>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . FRONTEND_URL . "/orders/{$orderData['id']}' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>View Order Details</a>
                </p>
                <p>Thank you for choosing FoodExpress!</p>
                <p>Best regards,<br>The FoodExpress Team</p>
            </div>
        </body>
        </html>
        ";
    }

    private function getPasswordResetTemplate($userName, $resetLink) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #dc3545; text-align: center;'>Password Reset Request</h1>
                <p>Dear {$userName},</p>
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' style='background-color: #dc3545; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                </p>
                <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
                <p>This link will expire in 24 hours for security reasons.</p>
                <p>Best regards,<br>The FoodExpress Team</p>
            </div>
        </body>
        </html>
        ";
    }
}
?>
