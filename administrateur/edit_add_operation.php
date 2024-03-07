<?php
// Database connection parameters (replace with your actual database credentials)
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

// Function to update an operation in the "op_chirgurie" table
function updateOperation($conn, $op_chirgurie_id, $nom, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id)
{
    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("UPDATE op_chirgurie 
                            SET nom = ?, date_operation = ?, date_fin = ?, heure_debut = ?, heure_fin = ?, patient_id = ?
                            WHERE op_chirgurie_id = ?");
    $stmt->bind_param("ssssiii", $nom, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id, $op_chirgurie_id);

    if ($stmt->execute()) {
        echo "L'opération de chirurgie a été mise à jour avec succès.";
        // Redirect to the operations table page after successful update
        header("Location: add_client.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'opération de chirurgie: " . $conn->error;
    }
    $stmt->close();
}

// Retrieve operation details from the "op_chirgurie" table
// ... (previous PHP code)

// Retrieve operation details from the "op_chirgurie" table
if (isset($_GET['op_chirgurie_id'])) {
    $op_chirgurie_id = $_GET['op_chirgurie_id'];
    $sql_operation = "SELECT * FROM op_chirgurie WHERE op_chirgurie_id = $op_chirgurie_id";
    $result_operation = $conn->query($sql_operation);

    if ($result_operation !== false && $result_operation->num_rows > 0) {
        $row = $result_operation->fetch_assoc();
        $nom = $row["nom"];
        $date_operation = $row["date_operation"];
        $date_fin = $row["date_fin"];
        $heure_debut = $row["heure_debut"];

        // The "heure_fin" value will be set to the existing value if available, or an empty string if not.
        // This way, it will be correctly displayed in the form.
        $heure_fin = !empty($row["heure_fin"]) ? $row["heure_fin"] : '';

        $patient_id = $row["patient_id"];
    } else {
        echo "Opération de chirurgie non trouvée.";
        $conn->close();
        exit();
    }
} else {
    echo "Identifiant de l'opération de chirurgie non spécifié.";
    $conn->close();
    exit();
}

// ... (rest of the PHP code)



// Handle operation update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom_operation"];
    $date_operation = $_POST["date_operation"];
    $date_fin = $_POST["date_fin"];
    $heure_debut = $_POST["heure_debut"];
    $heure_fin = $_POST["heure_fin"];
    $patient_id = $_POST["patient_id"];

    updateOperation($conn, $op_chirgurie_id, $nom, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id);

    // No need to redirect here because the updateOperation function handles the redirection.
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier l'Opération de Chirurgie</title>

    <style>
    
    
    body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="number"],
        .form-container input[type="tel"],
        .form-container select,
        .form-container input[type="password"],
        .form-container input[type="date"],
        .form-container input[type="time"],
        .form-container input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }

        .form-container select {
            margin-bottom: 20px;
        }

        .form-container .btn {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .form-container .btn:hover {
            background-color: #45a049;
        }
</style>
</head>

<body>
    <div class="form-container">
    <h1>Modifier l'Opération de Chirurgie</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?op_chirgurie_id=' . $op_chirgurie_id; ?>" method="post">
        <label for="nom_operation">Nom de l'opération:</label>
        <input type="text" id="nom_operation" name="nom_operation" required value="<?php echo htmlspecialchars($nom); ?>">

        <label for="date_operation">Date d'opération:</label>
        <input type="date" id="date_operation" name="date_operation" required value="<?php echo htmlspecialchars($date_operation); ?>">

        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" required value="<?php echo htmlspecialchars($date_fin); ?>">

        <label for="heure_debut">Heure de début:</label>
        <input type="time" id="heure_debut" name="heure_debut" required value="<?php echo htmlspecialchars($heure_debut); ?>">

        <label for="heure_fin">Heure de fin:</label>
        <input type="time" id="heure_fin" name="heure_fin" required value="<?php echo htmlspecialchars($heure_fin); ?>">

        <label for="patient_id">Patient:</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">Sélectionner un patient</option>
            <?php
            // Retrieve patient IDs and names from the "patient" table
            $sql_patients_dropdown = "SELECT patient_id, CONCAT(nom, ' ', prenom) AS patient_name FROM patient";
            $result_patients_dropdown = $conn->query($sql_patients_dropdown);

            if ($result_patients_dropdown !== false && $result_patients_dropdown->num_rows > 0) {
                while ($row = $result_patients_dropdown->fetch_assoc()) {
                    $patient_id_option = htmlspecialchars($row["patient_id"]);
                    $patient_name_option = htmlspecialchars($row["patient_name"]);
                    $selected = ($patient_id_option == $patient_id) ? "selected" : "";
                    echo "<option value='$patient_id_option' $selected>$patient_name_option</option>";
                }
            }
            ?>
        </select>

        <input type="submit" value="Mettre à jour" class="btn">
    </form>
    </div>
</body>

</html>

<?php
$conn->close();
?>
