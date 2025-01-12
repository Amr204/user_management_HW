<?php
// Database connection information
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to return JavaScript alert
function jsAlert($message) {
    echo "<script>alert('$message');</script>";
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($action == 'add') {
        $name = trim($_POST['name']);
        $birthdate = trim($_POST['birthdate']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($birthdate)) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, birthdate) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $birthdate);
            if ($stmt->execute()) {
                jsAlert("User added successfully!");
            } else {
                jsAlert("Error adding user: " . $stmt->error);
            }
            $stmt->close();
        } else {
            jsAlert("Please enter valid data.");
        }
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    if ($action == 'update') {
                        $new_name = trim($_POST['new_name']);
                        if (!empty($new_name)) {
                            $update_stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
                            $update_stmt->bind_param("si", $new_name, $row['id']);
                            if ($update_stmt->execute()) {
                                jsAlert("Name updated successfully!");
                            } else {
                                jsAlert("Error updating name.");
                            }
                            $update_stmt->close();
                        } else {
                            jsAlert("Please enter a valid new name.");
                        }
                    } elseif ($action == 'delete') {
                        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                        $delete_stmt->bind_param("i", $row['id']);
                        if ($delete_stmt->execute()) {
                            jsAlert("Account deleted successfully!");
                        } else {
                            jsAlert("Error deleting account.");
                        }
                        $delete_stmt->close();
                    }
                } else {
                    jsAlert("Incorrect password.");
                }
            } else {
                jsAlert("No account found with this email.");
            }
            $stmt->close();
        } else {
            jsAlert("Please enter valid data.");
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        <form method="POST" action="">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name"><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>

            <label for="birthdate">Birthdate (for adding a user):</label><br>
            <input type="date" id="birthdate" name="birthdate"><br>

            <label for="new_name">New Name (for updating):</label><br>
            <input type="text" id="new_name" name="new_name"><br>

            <label>Select Action:</label><br>
            <input type="radio" id="add" name="action" value="add" required>
            <label for="add">Add User</label><br>
            <input type="radio" id="update" name="action" value="update" required>
            <label for="update">Update Name</label><br>
            <input type="radio" id="delete" name="action" value="delete" required>
            <label for="delete">Delete Account</label><br><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>