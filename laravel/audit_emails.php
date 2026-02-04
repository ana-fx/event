<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transactions = App\Models\Transaction::whereNotNull('reseller_id')
    ->where('status', 'paid')
    ->with('reseller')
    ->get();

$output = "Total Reseller Transactions: " . $transactions->count() . "\n\n";

foreach ($transactions as $trx) {
    $output .= "--------------------------------------------------\n";
    $output .= "Transaction Code: " . $trx->code . "\n";
    $output .= "Date: " . $trx->created_at . "\n";
    if ($trx->reseller) {
        $output .= "Reseller: " . $trx->reseller->name . " <" . $trx->reseller->email . ">\n";
    } else {
        $output .= "Reseller: [Deleted or Missing]\n";
    }
    $output .= "Buyer (Sent To): " . $trx->name . " <" . $trx->email . ">\n";

    if ($trx->reseller && $trx->reseller->email === $trx->email) {
        $output .= "ALERT: Buyer email matches Reseller email!\n";
    }
}
$output .= "--------------------------------------------------\n";
file_put_contents('audit_log.txt', $output);
