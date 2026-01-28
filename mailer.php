<?php
// CORS headers - AJAX istekleri için gerekli
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: text/plain; charset=UTF-8');

// OPTIONS isteğini handle et (preflight request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Sadece POST metodunu kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Sadece POST metodu kabul edilir.';
    exit;
}

// Hata raporlamayı aç (geliştirme için, production'da kapatın)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Form verilerini al (yeni alanlar)
$contact_name = isset($_POST['contact_name']) ? trim($_POST['contact_name']) : '';
$company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$employee_count = isset($_POST['employee_count']) ? trim($_POST['employee_count']) : '';
$service_type = isset($_POST['service_type']) ? trim($_POST['service_type']) : '';

// Geriye dönük uyumluluk (eski alanlar)
$legacy_name = isset($_POST['name']) ? trim($_POST['name']) : '';

$name = $contact_name !== '' ? $contact_name : $legacy_name;
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basit validasyon
if (empty($name) || empty($email) || empty($message) || empty($company_name) || empty($phone)) {
    http_response_code(400);
    echo 'Lütfen zorunlu alanları doldurun.';
    exit;
}

// Email formatını kontrol et
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Geçerli bir e-posta adresi girin.';
    exit;
}

// Güvenlik için HTML etiketlerini temizle
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$company_name = htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$employee_count = htmlspecialchars($employee_count, ENT_QUOTES, 'UTF-8');
$service_type = htmlspecialchars($service_type, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Mail ayarları
$to = 'info@marmaragida.com.tr'; 
$subject = 'İletişim Formu - Marmara Gıda';

// Mail içeriği
$email_message = "Yeni bir iletişim formu mesajı aldınız.\n\n";
$email_message .= "Firma Yetkilisi: " . $name . "\n";
$email_message .= "Firma Adı: " . $company_name . "\n";
$email_message .= "Telefon: " . $phone . "\n";
if (!empty($employee_count)) {
    $email_message .= "Çalışan/Kişi Sayısı: " . $employee_count . "\n";
}
if (!empty($service_type)) {
    $email_message .= "Hizmet Türü: " . $service_type . "\n";
}
$email_message .= "E-posta: " . $email . "\n";
$email_message .= "Mesaj:\n" . $message . "\n";

// Mail başlıkları
$headers = "From: " . $name . " <" . $email . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Mail gönder
if (mail($to, $subject, $email_message, $headers)) {
    http_response_code(200);
    echo 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
} else {
    http_response_code(500);
    echo 'Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
}
?>
