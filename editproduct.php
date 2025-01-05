
<?php
session_start();
if (isset($_SESSION['sessionid'])) {
    $adminemail = $_SESSION['adminemail'];
    $adminpass = $_SESSION['adminpass'];
    $userid = $_SESSION['usr_id'];
} else {
    echo "<script>alert('No session available. Please login.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit();
}

// Koneksi ke database
include("dbconnect.php");
// Validasi `eventid` dari URL
if (isset($_GET['eventid'])) {
    $eventid = $_GET['eventid'];

    // Ambil data event
    try {
        $sqlloadevents = "SELECT * FROM `tbl_events_request` WHERE `ev_id` = :eventid";
        $stmt = $conn->prepare($sqlloadevents);
        $stmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $event = null; // Inisialisasi jika data tidak ditemukan
            echo "<script>alert('Event not found!');</script>";
            echo "<script>window.location.replace('request.php');</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        exit();
    }
} else {
    echo "<script>alert('Error: No event ID provided.');</script>";
    echo "<script>window.location.replace('request.php');</script>";
    exit();
}


// Validasi `eventid` dari URL
if (isset($_POST['submit'])) {
    $eventTitle = $_POST['ev_title'];
    $description = $_POST['ev_description'];
    $eventType = $_POST['ev_type'];
    $dateFrom = $_POST['ev_datefrom'];
    $dateTo = $_POST['ev_dateto'];
    $location = $_POST['ev_location'];
    $numDays = $_POST['ev_numDays'];

    // SQL untuk mengupdate event
    $sqlUpdateRequest = "UPDATE `tbl_events_request` 
                         SET `ev_title` = :eventTitle, `ev_description` = :description, 
                             `ev_type` = :eventType, `ev_datefrom` = :dateFrom, 
                             `ev_dateto` = :dateTo, `ev_location` = :location, 
                             `ev_numdays` = :numDays 
                         WHERE `ev_id` = :eventid";

    try {
        $stmtUpdate = $conn->prepare($sqlUpdateRequest);
        $stmtUpdate->bindParam(':eventTitle', $eventTitle);
        $stmtUpdate->bindParam(':description', $description);
        $stmtUpdate->bindParam(':eventType', $eventType);
        $stmtUpdate->bindParam(':dateFrom', $dateFrom);
        $stmtUpdate->bindParam(':dateTo', $dateTo);
        $stmtUpdate->bindParam(':location', $location);
        $stmtUpdate->bindParam(':numDays', $numDays);
        $stmtUpdate->bindParam(':eventid', $eventid, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            echo "<script>alert('Event updated successfully!');</script>";
            echo "<script>window.location.replace('request.php');</script>";
            exit();
        } else {
            echo "<script>alert('Failed to update event.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="w3-container w3-padding-32 w3-card w3-light-grey w3-margin">
        <h2 class="w3-text-green">Edit Event</h2>
        <form method="POST" action="">
            <label class="w3-text-black"><b>Event Title</b></label>
            <input class="w3-input w3-border w3-round" type="text" name="ev_title" 
                   value="<?= htmlspecialchars($event['ev_title'] ?? '') ?>" required>

            <label class="w3-text-black"><b>Description</b></label>
            <textarea class="w3-input w3-border w3-round" name="ev_description" required>
                <?= htmlspecialchars($event['ev_description'] ?? '') ?>
            </textarea>

            <label class="w3-text-black"><b>Event Type</b></label>
            <input class="w3-input w3-border w3-round" type="text" name="ev_type" 
                   value="<?= htmlspecialchars($event['ev_type'] ?? '') ?>" required>

            <label class="w3-text-black"><b>Date From</b></label>
            <input class="w3-input w3-border w3-round" type="date" name="ev_datefrom" 
                   value="<?= htmlspecialchars($event['ev_datefrom'] ?? '') ?>" required>

            <label class="w3-text-black"><b>Date To</b></label>
            <input class="w3-input w3-border w3-round" type="date" name="ev_dateto" 
                   value="<?= htmlspecialchars($event['ev_dateto'] ?? '') ?>" required>

            <label class="w3-text-black"><b>Location</b></label>
            <input class="w3-input w3-border w3-round" type="text" name="ev_location" 
                   value="<?= htmlspecialchars($event['ev_location'] ?? '') ?>" required>

            <label class="w3-text-black"><b>Number of Days</b></label>
            <input class="w3-input w3-border w3-round" type="number" name="ev_numDays" 
                   value="<?= htmlspecialchars($event['ev_numdays'] ?? '') ?>" required>

            <button class="w3-button w3-green w3-round w3-margin-top" type="submit" name="submit">Update Event</button>
        </form>
    </div>
</body>

</html>

