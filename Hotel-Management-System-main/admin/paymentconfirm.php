<?php

include '../config.php';
include 'paymentcols.php';

ensurePayCols($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)$_POST['id'];
    $method = $_POST['method'] ?? 'Cash';
    $ref    = trim($_POST['reference'] ?? '');
    $from   = $_POST['from'] ?? '';

    $allowed = array('Cash', 'M-Pesa', 'Card', 'Bank Transfer');
    if (!in_array($method, $allowed)) {
        $method = 'Cash';
    }

    // save the payment method on the bill
    $stmt = mysqli_prepare($conn,
        "UPDATE payment SET paymethod = ?, payref = ?, paiddate = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $method, $ref, $id);
    mysqli_stmt_execute($stmt);

    // mark the booking as paid
    mysqli_query($conn, "UPDATE roombook SET stat = 'Paid' WHERE id = $id");

    if ($from == 'roombook') {
        header("Location: roombook.php");
    } else {
        header("Location: payment.php");
    }
    exit;
}

header("Location: payment.php");
exit;

?>