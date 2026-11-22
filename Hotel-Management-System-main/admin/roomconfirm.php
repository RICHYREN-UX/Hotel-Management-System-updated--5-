<?php

include '../config.php';
include 'paymentcols.php';

ensurePayCols($conn);

$id = (int)$_GET['id'];

$re = mysqli_query($conn, "SELECT * FROM roombook WHERE id = $id");
$row = mysqli_fetch_array($re);

if (!$row) {
    header("Location: roombook.php");
    exit;
}

$Name      = $row['Name'];
$Email     = $row['Email'];
$RoomType  = $row['RoomType'];
$Bed       = $row['Bed'];
$Meal      = $row['Meal'];
$cin       = $row['cin'];
$cout      = $row['cout'];
$stat      = $row['stat'];

// blank / non-numeric values must not crash the calculation
$NoofRoom  = (int)$row['NoofRoom'];
if ($NoofRoom < 1) { $NoofRoom = 1; }
$noofday   = (int)$row['nodays'];
if ($noofday < 1) { $noofday = 1; }

// is there already a bill for this booking?
$chk = mysqli_query($conn, "SELECT id FROM payment WHERE id = $id");
$hasBill = ($chk && mysqli_num_rows($chk) > 0);

if (($stat == "NotConfirm" || $stat == "Confirm") && !$hasBill) {

    $type_of_room = 0;
    if ($RoomType == "Superior Room") {
        $type_of_room = 3000;
    } else if ($RoomType == "Deluxe Room") {
        $type_of_room = 2000;
    } else if ($RoomType == "Guest House") {
        $type_of_room = 1500;
    } else if ($RoomType == "Single Room") {
        $type_of_room = 1000;
    }

    $type_of_bed = 0;
    if ($Bed == "Single") {
        $type_of_bed = $type_of_room * 1 / 100;
    } else if ($Bed == "Double") {
        $type_of_bed = $type_of_room * 2 / 100;
    } else if ($Bed == "Triple") {
        $type_of_bed = $type_of_room * 3 / 100;
    } else if ($Bed == "Quad") {
        $type_of_bed = $type_of_room * 4 / 100;
    }

    $type_of_meal = 0;
    if ($Meal == "Breakfast") {
        $type_of_meal = $type_of_bed * 2;
    } else if ($Meal == "Half Board") {
        $type_of_meal = $type_of_bed * 3;
    } else if ($Meal == "Full Board") {
        $type_of_meal = $type_of_bed * 4;
    }

    $ttot = $type_of_room * $noofday * $NoofRoom;
    $mepr = $type_of_meal * $noofday;
    $btot = $type_of_bed * $noofday;
    $fintot = $ttot + $mepr + $btot;

    $stmt = mysqli_prepare($conn,
        "INSERT INTO payment(id,Name,Email,RoomType,Bed,NoofRoom,cin,cout,noofdays,roomtotal,bedtotal,meal,mealtotal,finaltotal)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "issssissidd" . "sdd",
        $id, $Name, $Email, $RoomType, $Bed, $NoofRoom, $cin, $cout, $noofday,
        $ttot, $btot, $Meal, $mepr, $fintot);

    // only mark Confirm if the bill was really saved
    if (mysqli_stmt_execute($stmt)) {
        mysqli_query($conn, "UPDATE roombook SET stat = 'Confirm' WHERE id = $id");
    }
}

header("Location: roombook.php");
exit;

?>
