<?php
include 'db.php';

if (!isset($_GET['reference'])) {
    echo "No reference supplied";
    exit;
}

$reference = $_GET['reference'];

// Verify the payment using Paystack's API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer YOUR_PAYSTACK_SECRET_KEY_HERE",
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error: " . $err;
    exit;
}

$result = json_decode($response, true);

if ($result['status'] && $result['data']['status'] === 'success') {
    // Update the database to mark payment as completed
    $stmt = $conn->prepare("UPDATE form_purchases SET payment_status = 'completed' WHERE transaction_reference = :reference");
    $stmt->execute([':reference' => $reference]);
    echo "<script>alert('Payment Successful!'); window.location.href = 'thank_you.php';</script>";
} else {
    echo "<script>alert('Payment verification failed.'); window.location.href = 'buy_form.php';</script>";
}
?>
