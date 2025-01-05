<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['sessionid'])) {
    $adminemail = $_SESSION['adminemail'];
    $adminpass = $_SESSION['adminpass'];
    $userid = $_SESSION['usr_id'];
    $adminid = $_SESSION['adminid'] ?? 'Unknown';
} else {
    echo "<script>alert('No session available. Please login.');</script>";
    echo "<script>window.location.replace('login.php')</script>";
    exit();
}

// Form data handler
if (isset($_POST['submit'])) {
    $eventTitle = $_POST['ev_title'];
    $description = $_POST['ev_description'];
    $eventType = $_POST['ev_type'];
    $dateFrom = $_POST['ev_datefrom'];
    $dateTo = $_POST['ev_dateto'];
    $location = $_POST['ev_location'];
    $numDays = $_POST['ev_numDays'];

    // Validation
    if (empty($eventTitle) || empty($description) || empty($eventType) || empty($dateFrom) || empty($dateTo) || empty($location) || !is_numeric($numDays) || $numDays <= 0) {
        echo "<script>alert('All fields are required and must be valid.');</script>";
        echo "<script>window.location.replace('request.php');</script>";
        exit();
    }

    include("dbconnect.php");

    // Use prepared statements
    try {
        $stmt = $conn->prepare("INSERT INTO `tbl_events_request` (`ev_title`, `ev_description`, `ev_type`, `ev_datefrom`, `ev_dateto`, `ev_location`, `ev_numdays`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$eventTitle, $description, $eventType, $dateFrom, $dateTo, $location, $numDays]);
        echo "<script>alert('Success')</script>";
        echo "<script>window.location.replace('request.php')</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Failed: {$e->getMessage()}')</script>";
        echo "<script>window.location.replace('request.php')</script>";
    }
}

//search operation based on search form
if (isset($_GET['btnsearch'])) {
    $search = $_GET['search'];
    $searchby = $_GET['searchby'];

    if ($searchby == "title") {
        $sqlloadevents = "SELECT * FROM `tbl_events_request` WHERE `ev_title` LIKE '%$search%'";
    }
    if ($searchby == "content") {
        $sqlloadevents = "SELECT * FROM `tbl_events_request` WHERE `ev_description` LIKE '%$search%'";  
    }
}else{
    $sqlloadevents = "SELECT * FROM `tbl_events_request`";
}


if (isset($_GET['submit'])) {
    $operation = $_GET['submit'];
    $eventid = $_GET['eventid'];
    if ($operation == "delete") {
        $sqldeleteevents = "DELETE FROM `tbl_events_request` WHERE `ev_id` = '$eventid'";
        try{
            include("dbconnect.php"); // database connection
            $conn->query($sqldeleteevents);
            echo "<script>alert('Success')</script>";
            echo "<script>window.location.replace('request.php')</script>";
        }catch(PDOException $e){
            echo "<script>alert('Failed!!!')</script>";
            echo "<script>window.location.replace('request.php')</script>";
        }
    }
}

//     $sqlievents = "INSERT INTO `tbl_events_request`( `ev_title`, `ev_description`, `ev_type`, `ev_datefrom`, `ev_dateto`, `ev_location`, `ev_numdays`)
//                    VALUES ('$eventTitle', '$description','$eventType','$dateFrom','$dateTo','$location','$numDays')";

//     try {
//         include("dbconnect.php"); // database connection
//         $conn->query($sqlievents);
//         echo "<script>alert('Success')</script>";
//         echo "<script>window.location.replace('request.php')</script>";
//     } catch (PDOException $e) {
//         echo "<script>alert('Failed!!!')</script>";
//         echo "<script>window.location.replace('request.php')</script>";
//     }
//  }

 //load data
$results_per_page = 6;
if (isset($_GET["pageno"])) {
    $pageno = (int) $_GET["pageno"];
    $page_first_result = ($pageno - 1) * $results_per_page;
} else {
    $pageno = 1;
    $page_first_result = 0;
}
include("dbconnect.php"); // database connection

$stmt = $conn->prepare($sqlloadevents);
$stmt->execute();
$number_of_rows = $stmt->rowCount();
$number_of_page = ceil($number_of_rows / $results_per_page);
$sqlloadevents = $sqlloadevents . " LIMIT $page_first_result, $results_per_page";
$stmt = $conn->prepare($sqlloadevents);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

function randomString($length = 10)
{
    $characters =
        "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function truncate($string, $length, $dots = "...")
{
    return strlen($string) > $length
        ? substr($string, 0, $length - strlen($dots)) . $dots
        : $string;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyEvent - Event Registration</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body>

    <!-- Sidebar -->
    <nav class="w3-sidebar w3-bar-block w3-collapse w3-top w3-card"
        style="z-index:3; width:250px; background-color:rgb(112, 57, 102);" id="mySidebar"
        aria-label="Sidebar Navigation">
        <div class="w3-container w3-display-container w3-padding-16">
            <button onclick="close_menu()" class="fa fa-remove w3-hide-large w3-button w3-display-topright"
                aria-label="Close menu"></button>
            <h3 class="w3-wide" style="color: #ecf0f1;"><b>Events</b></h3>
        </div>
        <div class="w3-padding-64 w3-large w3-text-grey" style="font-weight:bold;">
            <a href="mainpage.php" class="w3-bar-item w3-button" role="link">
                <i class="fa fa-newspaper-o" aria-hidden="true"></i> News
            </a>
            <a href="memberpage.php" class="w3-bar-item w3-button" role="link">
                <i class="fa fa-users" aria-hidden="true"></i> Members
            </a>
            <a href="request.php" class="w3-bar-item w3-button" role="link">
                <i class="fa fa-calendar" aria-hidden="true"></i> Events
            </a>
            <a href="products.php" class="w3-bar-item w3-button" role="link">
                <i class="fa fa-box" aria-hidden="true"></i> Products
            </a>
            <a href="profilepage.html?adminid=<?php echo htmlspecialchars($adminid); ?>" class="w3-bar-item w3-button"
                role="link">
                <i class="fa fa-user" aria-hidden="true"></i> Profile
            </a>
            <a href="logout.php" class="w3-bar-item w3-button" role="link">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Search Form -->

    <!-- Top hamburger menu -->
    <header class="w3-bar w3-top w3-hide-large w3-hide small w3-xlarge">
        <div class="w3-bar-item w3-padding-24 w3-wide"></div>
        <a href="javascript:void(0)" class="w3-bar-item w3-button w3-padding-24 w3-right" onclick="open_menu()"><i
                class="fa fa-bars"></i></a>
        <div class="w3-hide-large w3-padding-24 w3-wide w3-right">
            <h6>News</h6>
        </div>
    </header>
    <!-- Top hamburger menu -->

    <div class="w3-main" style="margin-left:250px; background-color: rgb(255, 243, 224); padding: 20px;">
        <header class="w3-center"
            style="background-color: rgb(112, 57, 102); padding: 32px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
            <h1 style="color: #ffffff; font-size: 2.5em;">Event Registration Request</h1>
            <p style="color: #ffffff; font-size: 1.2em;">Your One Stop Event Manager</p>
        </header>

        <div class="w3-bar-item" style="margin-top: 20px;">
            <button class="w3-button w3-round w3-margin-right w3-hover-shadow"
                style="background-color: rgb(112, 57, 102); color: #ffffff;"
                onclick="document.getElementById('idabout').style.display='block'">
                About
            </button>
            <button class="w3-button w3-round w3-hover-shadow"
                style="background-color: rgb(112, 57, 102); color: #ffffff;"
                onclick="document.getElementById('id01').style.display='block'">
                New Request
            </button>
        </div>

        <!-- Search new form -->
        <div class="w3-container"
            style="margin-top: 20px; background-color: rgb(138, 52, 102); border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <form action="request.php">
                <header class="w3-center w3-padding">
                    <div class="w3-row w3-center">
                        <div class="w3-third w3-padding">
                            <input class="w3-input w3-round w3-border" type="text" name="search" placeholder="Search"
                                aria-label="Search">
                        </div>
                        <div class="w3-third w3-padding">
                            <select class="w3-input w3-round w3-border" name="searchby" aria-label="Search by">
                                <option value="title">Title</option>
                                <option value="content">Content</option>
                            </select>
                        </div>
                        <div class="w3-third w3-padding">
                            <button class="w3-button w3-padding w3-round w3-silver w3-hover-shadow" type="submit"
                                name="btnsearch" aria-label="Search">
                                <i class="fa fa-search" aria-hidden="true"></i> Search
                            </button>
                        </div>
                    </div>
                </header>
            </form>
        </div>
        <!-- search form ended -->
        <!-- 
        //list request by user -->
        <div class="w3-container"
            style="margin-top: 20px; background-color:rgb(247, 102, 11); border-radius: 8px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div style="background-color: #333; padding: 20px;">
                <h2 style="color: #fff;">Latest Request Updates</h2>
                <p style="color: #ccc;">Manage your bookings and stay updated on the latest status of your requests
                    here!</p>
            </div>


            <?php


if ($number_of_rows > 0) {
    echo "<div>";
    echo "<ul class='w3-ul w3-border'>";

    foreach ($rows as $event) {
        // Ambil data dari array $event
        $eventid = isset($event['ev_id']) ? $event['ev_id'] : 'Unknown ID';
        $eventTitle = isset($event['ev_title']) ? $event['ev_title'] : 'Unknown Title';
        $description = isset($event['ev_description']) ? truncate($event['ev_description'], 250) : 'No Description';
        $eventType = isset($event['ev_type']) ? $event['ev_type'] : 'Unknown Type';
        $dateFrom = isset($event['ev_datefrom']) ? date_format(date_create($event['ev_datefrom']), "d-m-Y") : 'Unknown Date';
        $dateTo = isset($event['ev_dateto']) ? date_format(date_create($event['ev_dateto']), "d-m-Y") : 'Unknown Date';
        $location = isset($event['ev_location']) ? $event['ev_location'] : 'Unknown Location';
        $numDays = isset($event['ev_numdays']) ? $event['ev_numdays'] : 0;
        $eventdate = isset($event['ev_date']) ? date_format(date_create($event['ev_date']), "d-m-Y h:i a") : 'Unknown Date';

        // Masukkan data ke dalam <li>
        
        // Start the loop for each event (ensure $eventid and other variables are defined for each iteration)
        $bgColor = ($eventid % 2 == 0) ? "rgb(206, 181, 135)" : "rgb(193, 201, 231)";

        echo "<li class='w3-padding-16 w3-hover-shadow w3-border' style='background-color:$bgColor;'>";
        echo "
        <div style='border: 2px solid #4CAF50; padding: 5px; margin-bottom: 10px; border-radius: 5px; background-color: #f9f9f9; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <h4 style='margin: 0; color: #4CAF50;'>Event ID: $eventid</h4>
        </div>
        ";
        echo "<h4>$eventTitle</h4>";
        echo "<p>Type: $eventType</p>";
        echo "<p>Location: $location</p>";
        echo "<p>Duration: $numDays day(s) ($dateFrom to $dateTo)</p>";
        echo "<p>Description: $description</p>";
        echo "<p>Created on: $eventdate</p>";
        echo "<ul class='w3-right'>";
        echo "<a href='editproduct.php?eventid=$eventid' class='w3-button w3-round w3-small w3-green'>&nbsp;Edit&nbsp;&nbsp;</a>";
        echo "<a href='request.php?submit=delete&eventid=$eventid' class='w3-button w3-round w3-small w3-red' onclick=\"return confirm('Delete this event no $eventid?');\">Delete</a>";
        echo "<a href='javascript:void(0)' onclick=\"document.getElementById('id$eventid').style.display='block'\" class='w3-button w3-round w3-small w3-teal'>&nbsp;Read&nbsp;</a>";
        echo "</ul>";
        echo "</li>";
    
                                //Dynamic modal window    
                                echo " <div id='id$eventid' class='w3-modal'>
                                <div class='w3-modal-content w3-card-4'>
                                    <header class='w3-container w3-purple'>
                                        <span onclick='document.getElementById(\"id$eventid\").style.display=\"none\"'
                                        class='w3-button w3-display-topright fa fa-close'></span>
                                        <h3>$eventTitle</h3>
                                    </header>
                                   
                                    <div class='w3-container w3-center w3-padding-large'>
                                        <p> $description </p>
                                    </div>
                                     <div class='w3-container'>
                                        Event Type: $eventType  <br>
                                        Event Location: $location <br>
                                        Event Duration: $numDays day(s) ($dateFrom to $dateTo)
                                    </div>
                                    <div class='w3-container w3-padding'>
                                        <p>  Created on: $eventdate</p>
                                    </div>
                                    <footer class='w3-container w3-purple w3-center'>Putry Event</footer>
                                </div>
                               </div>";
                        $eventid++;
    }


    echo "</ul>";
    echo "</div>";
} else {
    echo "<h2>No events found</h2>";
}
?>
            <?php
                echo "<div class='w3-container w3-padding w3-row w3-center'>";
                for ($page = 1; $page <= $number_of_page; $page++) {
                    echo '<a href="request.php?pageno=' . $page . '" style="text-decoration: none">&nbsp&nbsp' . $page . " </a>";
                }
                echo " ( " . $pageno . " )";
                echo "</div>";
            ?>
        </div>


        <div id="id01" class="w3-modal">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px;">
                <!-- //modal request by user -->
                <div class=" w3-container w3-card-4 w3-light-grey w3-margin w3-padding-large">
                    <form action="request.php" method="POST" enctype="multipart/form-data" class="w3-margin"
                         onsubmit="showLoading(); return confirmSubmission()">
                        <span onclick="document.getElementById('id01').style.display='none'"
                        class="w3-button w3-display-topright">&times;</span>
                        <h2 class="w3-center">Event Registration Request</h2>
                        <form id="eventForm" class="w3-container">

                            <!-- Event Title -->
                            <label class="w3-text-black"><b>Event Title</b></label>
                            <input class="w3-input w3-border" type="text" id="eventTitle" name="ev_title" required
                                oninput="validateField(this)">

                            <!-- Description -->
                            <label class="w3-text-black"><b>Description</b></label>
                            <textarea class="w3-input w3-border" id="description" name="ev_description" rows="4"
                                required oninput="validateField(this)"></textarea>

                            <!-- Event Type -->
                            <label class="w3-text-black"><b>Event Type</b></label>
                            <select class="w3-select w3-border" id="eventType" name="ev_type" required
                                onchange="validateField(this)">
                                <option value="" disabled selected>Choose event type</option>
                                <option value="Conference">Conference</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Networking">Networking</option>
                            </select>

                            <!-- Date From -->
                            <label class="w3-text-black"><b>Date From</b></label>
                            <input class="w3-input w3-border" type="date" id="dateFrom" name="ev_datefrom" required
                                onchange="calculateDays()">

                            <!-- Date To -->
                            <label class="w3-text-black"><b>Date To</b></label>
                            <input class="w3-input w3-border" type="date" id="dateTo" name="ev_dateto" required
                                onchange="calculateDays()">

                            <!-- Location -->
                            <label class="w3-text-black"><b>Location</b></label>
                            <input class="w3-input w3-border" type="text" id="location" name="ev_location" required
                                oninput="validateField(this)">

                            <!-- Number of Days -->
                            <label class="w3-text-black"><b>Number of Days</b></label>
                            <input class="w3-input w3-border" type="text" id="numDays" name="ev_numDays" readonly>

                            <!-- Submit Button -->
                            <button class="w3-button w3-blue w3-margin-top" name="submit" type="submit">Submit</button>
                            <button type="button" class="w3-button w3-red w3-margin-top"
                                onclick="clearForm()">Clear</button>
                        </form>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div id="idabout" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px;">
            <header class="w3-container w3-purple">
                <span onclick="document.getElementById('idabout').style.display='none'"
                    class="w3-button w3-display-topright">&times;</span>
                <h3>About App</h3>
            </header>
            <div class="w3-container w3-padding" style="background-color: rgb(255, 243, 224);">
                <p class="w3-justify">
                    This application is developed for Putry Event Sdn Bhd. At Putry Event Sdn Bhd, we specialize in
                    creating unforgettable experiences.
                    Established with a passion for delivering excellence, we are a full-service event management company
                    that brings your visions to life.
                    From corporate gatherings to personal celebrations, we provide customized solutions tailored to meet
                    the unique needs of our clients.
                    Our services include event planning, design, coordination, and execution, ensuring every detail is
                    handled with care and precision.
                    With a dedicated team of professionals and a commitment to quality, Putry Event Sdn Bhd is here to
                    turn your ideas into remarkable events.
                    Your satisfaction is our priority, and we strive to make every event a moment to remember. Let us
                    make your next event extraordinary!
                </p>
            </div>
            <footer class="w3-container w3-purple w3-center">
                <p>Putry Event</p>
            </footer>
        </div>
    </div>

    <script>

     function confirmSubmission() {
        return confirm("Are you sure you want to submit this new event request?");
    }    
    // Show loading spinner
    function showLoading() {
        document.getElementById('loading').style.display = 'block';
    }

    // Clear form function
    function clearForm() {
        document.getElementById('eventTitle').value = '';
        document.getElementById('description').value = '';
        document.getElementById('eventType').selectedIndex = 0;
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        document.getElementById('location').value = '';
        document.getElementById('numDays').value = '';
    }

    // Validate individual fields
    function validateField(field) {
        if (field.value.trim() === '') {
            field.classList.add('w3-border-red');
        } else {
            field.classList.remove('w3-border-red');
        }
    }


    // JavaScript to calculate the number of days
    document.getElementById('dateTo').addEventListener('change', calculateDays);
    document.getElementById('dateFrom').addEventListener('change', calculateDays);

    function calculateDays() {
        const dateFrom = new Date(document.getElementById('dateFrom').value);
        const dateTo = new Date(document.getElementById('dateTo').value);

        if (dateFrom && dateTo && dateTo >= dateFrom) {
            const timeDiff = Math.abs(dateTo - dateFrom);
            const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;;
            document.getElementById('numDays').value = daysDiff;
        } else {
            document.getElementById('numDays').value = "";
        }
    }

    // Validation function
    function validateForm() {
        const eventTitle = document.getElementById('ev_tTitle').value.trim();
        const description = document.getElementById('ev_description').value.trim();
        const eventType = document.getElementById('ev_Type').value;
        const dateFrom = document.getElementById('ev_dateFrom').value;
        const dateTo = document.getElementById('ev_dateTo').value;
        const location = document.getElementById('ev_location').value.trim();
        const numDays = document.getElementById('ev_numDays').value;

        if (!eventTitle || !description || !eventType || !dateFrom || !dateTo || !location) {
            alert("Please fill in all required fields.");
            return false;
        }

        if (new Date(dateFrom) > new Date(dateTo)) {
            alert("'Date From' cannot be later than 'Date To'.");
            return false;
        }

        if (!numDays || numDays <= 0) {
            alert("The number of days must be valid.");
            return false;
        }

        return true;
    }
    </script>

</body>

</html>
