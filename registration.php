<?php
// Define variables
$name = $email = $password = $confirm_password = "";
$nameErr = $emailErr = $passwordErr = $confirmErr = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ----------------------- NAME VALIDATION -----------------------
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    // ----------------------- EMAIL VALIDATION -----------------------
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // ----------------------- PASSWORD VALIDATION -----------------------
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters long";
        }
        if (!preg_match('/[!@#$%^&*]/', $password)) {
            $passwordErr = "Password must include at least one special character (! @ # $ % ^ & *)";
        }
    }

    // ----------------------- CONFIRM PASSWORD VALIDATION -----------------------
    if (empty($_POST["confirm_password"])) {
        $confirmErr = "Please confirm password";
    } else {
        $confirm_password = $_POST["confirm_password"];
        if ($password !== $confirm_password) {
            $confirmErr = "Passwords do not match";
        }
    }

    // ----------------------- IF ALL VALID -----------------------
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmErr)) {

        // Read existing JSON file
        $file = "users.json";
        $jsonData = file_get_contents($file);

        if ($jsonData === false) {
            die("Error reading JSON file.");
        }

        $users = json_decode($jsonData, true);

        if ($users === null) {
            $users = []; // If file empty, create array
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Store new user
        $newUser = [
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword
        ];

        $users[] = $newUser;

        // Save back to JSON file
        if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT))) {
            $successMsg = "Registration successful!";
        } else {
            die("Error writing to JSON file.");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration form</title>
    <style>
        .error {color:red;}
        .success {color:green;font-weight:bold;}
        .detail{border: solid black;margin: 50px; background-color: lightblue;}
        .start{text-align: center;}
    </style>
</head>
<body>

<h2 class="start">Registration Form</h2>

<?php if ($successMsg != "") : ?>
    <div class="success"><?= $successMsg ?></div>
<?php endif; ?>

<form method="post" class="detail">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?= $name ?>">
    <span class="error"><?= $nameErr ?></span><br><br>

    <label>Email:</label><br>
    <input type="text" name="email" value="<?= $email ?>">
    <span class="error"><?= $emailErr ?></span><br><br>

    <label>Password:</label><br>
    <input type="password" name="password">
    <span class="error"><?= $passwordErr ?></span><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password">
    <span class="error"><?= $confirmErr ?></span><br><br>

    <button type="submit">Register</button>

</form>

</body>
</html>
