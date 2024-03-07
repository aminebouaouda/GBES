<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gbes1";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle password and username changes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST["admin_id"];
    $new_username = $_POST["new_username"];
    $new_password = $_POST["new_password"];

    // Update the admin's username and password
    $update_query = "UPDATE admins SET username = '$new_username', password = '$new_password' WHERE admin_id = $admin_id";
    if ($conn->query($update_query) === TRUE) {
        $success_message = "Password and username changed successfully.";
    } else {
        $error_message = "Error updating record: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        input[type="text"],
        input[type="password"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .change-button {
           
            padding: 6px 12px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .change-button:hover {
            background-color: #45a049;
        }

        .success-message,
        .error-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .success-message {
            color: #2ecc71;
            background-color: #d4edda;
        }

        .error-message {
            color: #c0392b;
            background-color: #f8d7da;
        }

        .icon {
            margin-right: 5px;
        }

        .content-container {
            display: flex;
            /* Use flexbox to arrange content */
            justify-content: space-between;
            /* Space between the two divs */
        }

        .directeur_side_bar_content,
        .main_content {
            flex: 1;
            /* Let both divs take equal space */
            padding: 10px;
            /* Add padding for spacing */
        }

        .content-container {
            display: flex;
            /* Use flexbox to arrange content */
            justify-content: space-between;
            /* Space between the two divs */
        }


        .directeur_side_bar_content,
        .main_content {
            flex: 1;
            /* Let both divs take equal space */
            padding: 70px 10px;
            /* Add padding for spacing */
            max-width: calc(100% - 250px);
            /* Adjust as needed */
        }
    </style>
</head>

<body>
    <div class="content-container">
        <div class="directeur_side_bar_content">
            <?php
            include 'directeur_side_bar.php';
            ?>
        </div>
        <div class="main_content">
            <h1>Admin Management</h1>

            <!-- Display the list of admins -->
            <table>
                <tr>
                    <th>Admin ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php
                $select_query = "SELECT * FROM admins";
                $result = $conn->query($select_query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["admin_id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["password"] . "</td>";
                        echo "<td>" . $row["role"] . "</td>";
                        echo '<td><form method="post" action="">';
                        echo '<input type="hidden" name="admin_id" value="' . $row["admin_id"] . '">';
                        echo '<input type="text" name="new_username" placeholder="New Username" required>';
                        echo '<input type="password" name="new_password" placeholder="New Password" required>';
                        echo '<button type="submit" class="change-button"><i class="fas fa-check-circle icon"></i></button>';
                        echo '</form></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No admins found.</td></tr>";
                }
                ?>
            </table>

            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle icon"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle icon"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>