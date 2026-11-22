<?php
// Makes sure the payment table has the payment-method columns.
// Safe to include many times; it only alters the table once.
function ensurePayCols($conn)
{
    $cols = array(
        'paymethod' => "VARCHAR(30) NULL DEFAULT NULL",
        'payref'    => "VARCHAR(60) NULL DEFAULT NULL",
        'paiddate'  => "DATETIME NULL DEFAULT NULL",
    );
    foreach ($cols as $name => $def) {
        $r = mysqli_query($conn, "SHOW COLUMNS FROM payment LIKE '$name'");
        if ($r && mysqli_num_rows($r) == 0) {
            mysqli_query($conn, "ALTER TABLE payment ADD COLUMN $name $def");
        }
    }
}
?>