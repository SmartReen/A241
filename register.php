<?php

// $nameErr = $emailErr =  "";
// $name = $email = $gender  = "";

// if (isset($_POST['submit'])) {
//     $email =($_POST["useremail"]);
//     $password = sha1($_POST['password']);
//     $name = $_POST['username'];
//     $phone = $_POST['userphone'];
//     $gender = $_POST['usergender'];
//     $datebirth = $_POST['userdatebirth'];
//     $sqlregister = "INSERT INTO tbl_users (usr_email, usr_name, usr_phone, usr_gender, usr_password, usr_dbirth) 
//                 VALUES ('$email', '$name', '$phone', '$gender', '$password', '$datebirth')";


    
// try{
//     include("dbconnect.php"); // database connection
//     $conn->query($sqlregister);
//     echo "<script>alert('Success')</script>";
//     echo "<script>window.location.replace('login.php')</script>";
//     }catch(PDOException $e){
//     echo "<script>alert('Failed!!!')</script>";
//     echo "<script>window.location.replace('register.php')</script>";
//    }
// }

?>
<?php

$nameErr = $emailErr =  "";
$name = $email = $gender  = "";

if (isset($_POST['submit'])) {
    $email = ($_POST["useremail"]);
    $password = sha1($_POST['password']);
    $name = $_POST['username'];
    $phone = $_POST['userphone'];
    $gender = $_POST['usergender'];
    $datebirth = $_POST['userdatebirth'];

    // Check if email already exists
    $sqlCheckEmail = "SELECT * FROM tbl_users WHERE usr_email = :email";
    
    try {
        include("dbconnect.php"); // database connection
        $stmt = $conn->prepare($sqlCheckEmail);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // If email exists, show error
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already exists! Please use a different email.')</script>";
            echo "<script>window.location.replace('register.php')</script>";
        } else {
            // Proceed with registration
            $sqlregister = "INSERT INTO tbl_users (usr_email, usr_name, usr_phone, usr_gender, usr_password, usr_dbirth) 
                            VALUES ('$email', '$name', '$phone', '$gender', '$password', '$datebirth')";
            $conn->query($sqlregister);
            echo "<script>alert('Success')</script>";
            echo "<script>window.location.replace('login.php')</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Failed!!!')</script>";
        echo "<script>window.location.replace('register.php')</script>";
    }
}

?>

<html>

<head>
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body>
    <header class="w3-center w3-padding-32 w3-teal">
        <div class="w3-margin">
            <h1>Putry Event Sdn Bhd</h1>
            <h3>Your One Stop Event Manager</h3>
        </div>
    </header>
    <div class="w3-hide-small" style="height:100px">
    </div>
    <div class="w3-light-grey w3-container w3-padding w3-border w3-round" style="max-width: 600px;margin:auto">
        <h1>Register</h1>
        <p><span class="error">* required field</span></p>
        <form action="register.php" method="post">
            <input class="w3-input w3-round w3-border" type="email" id="useremailid" name="useremail"
                placeholder="Enter Email Adress" required><br>
            <input class="w3-input w3-round w3-border" type="text" id="usernameid" name="username"
                placeholder="Enter Name" required><br>
            <input class="w3-input w3-round w3-border" type="text" id="userphoneid" name="userphone"
                placeholder="Enter Phone Number" required><br>
            <input class="w3-input w3-round w3-border" type="text" id="usergenderid" name="usergender"
                placeholder="Enter Gender" required><br>
            <input class="w3-input w3-round w3-border" type="password" id="passwordid" name="password" minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                placeholder="Enter Password" required><br>
            <input class="w3-input w3-round w3-border" type="password" id="passwordid" name="password" minlength="8" 
                placeholder="Confirm Password " required><br>
            <input class="w3-input w3-round w3-border" type="text" id="userbornid" name="userdatebirth"
                placeholder="Enter Date of Birth" required><br>
            
            <input class="w3-input w3-round w3-button w3-teal" type="submit" name="submit" value="Register">
        </form>
    </div>
    <div class="w3-center w3-container">
        <a href="login.php">Login</a>
    </div>
    <div class="" style="height:200px">
    </div>
    <footer class="w3-container w3-grey">
        <p style="text-align: center">
            Copyright &copy; 2023 Putry Event Sdn Bhd   
        </p>
    </footer>
</body>

</html>
