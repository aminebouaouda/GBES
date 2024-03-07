<?php
ob_start();
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

// Function to update a new intervenant to the "intervenant" table
function updateIntervenant($conn, $intervenant_id, $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id)
{
    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("UPDATE intervenant SET nom=?, prenom=?, email=?, adresse=?, genre=?, mot_passe=?, fonction_id=? WHERE intervenant_id=?");
    if (!$stmt) {
        die("Error in the prepared statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssii", $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id, $intervenant_id);

    if ($stmt->execute()) {
        echo "Les modifications de l'intervenant ont été sauvegardées avec succès.";
        // Redirect to the view_intervenant.php page for viewing the updated intervenant details
        header("Location: add_intervenant.php?intervenant_id=" . $intervenant_id);
        exit();
    } else {
        echo "Erreur lors de la sauvegarde des modifications de l'intervenant: " . $stmt->error;
    }
    $stmt->close();
}

// Retrieve intervenant information based on the intervenant_id from the query parameter
$intervenant_id = isset($_GET['intervenant_id']) ? $_GET['intervenant_id'] : '';
$sql_intervenant = "SELECT * FROM intervenant WHERE intervenant_id = $intervenant_id";
$result_intervenant = $conn->query($sql_intervenant);

// Handle intervenant update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $adresse = $_POST["adresse"];
    $genre = $_POST["genre"];
    $mot_passe = $_POST["mot_passe"];
    $fonction_id = $_POST["fonction_id"];

    updateIntervenant($conn, $intervenant_id, $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id);
}

// Handle the redirection after saving modifications
if (isset($_POST["save_modifications"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $intervenant_id = $_GET['intervenant_id'];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $adresse = $_POST["adresse"];
    $genre = $_POST["genre"];
    $mot_passe = $_POST["mot_passe"];
    $fonction_id = $_POST["fonction_id"];

    updateIntervenant($conn, $intervenant_id, $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id);

    // After updating the intervenant, redirect back to add_intervenant.php
    header("Location: add_intervenant.php?intervenant_id=" . $intervenant_id);
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Editer un intervenant</title>
    <!-- Add your CSS styles and other head content here -->
    <style>
        /* Add your CSS styles for the edit form here */
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
        .form-container input[type="tel"],
        .form-container select,
        .form-container input[type="password"],
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

        /* ... Add more CSS styles if needed ... */
    </style>
</head>

<body>
    <h1>Editer un intervenant</h1>

    <?php
    if ($result_intervenant !== false && $result_intervenant->num_rows > 0) {
        $row = $result_intervenant->fetch_assoc();
        ?>
        <!-- Edit intervenant form -->
        <div class="form-container">
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?intervenant_id=' . $intervenant_id; ?>" method="post">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($row["nom"]); ?>" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($row["prenom"]); ?>"
                    required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row["email"]); ?>" required>

                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($row["adresse"]); ?>"
                    required>

                <label for="genre">Genre:</label>
                <select id="genre" name="genre" required>
                    <option value="">Sélectionner un genre</option>
                    <option value="Homme" <?php if ($row["genre"] === "Homme")
                        echo "selected"; ?>>Homme</option>
                    <option value="Femme" <?php if ($row["genre"] === "Femme")
                        echo "selected"; ?>>Femme</option>
                </select>

                <label for="mot_passe">Mot de passe:</label>
                <input type="password" id="mot_passe" name="mot_passe"
                    value="<?php echo htmlspecialchars($row["mot_passe"]); ?>" required>

                <label for="fonction_id">Fonction:</label>
                <select id="fonction_id" name="fonction_id" required>
                    <option value="">Sélectionner une fonction</option>
                    <?php
                    // Retrieve function information data from the "fonction" table
                    $sql_fonctions = "SELECT * FROM fonction";
                    $result_fonctions = $conn->query($sql_fonctions);

                    if ($result_fonctions !== false && $result_fonctions->num_rows > 0) {
                        while ($fonction_row = $result_fonctions->fetch_assoc()) {
                            $selected = ($fonction_row["fonction_id"] === $row["fonction_id"]) ? "selected" : "";
                            echo '<option value="' . $fonction_row["fonction_id"] . '" ' . $selected . '>' . htmlspecialchars($fonction_row["libelle"]) . '</option>';
                        }
                    }
                    ?>
                </select>








                <input type="submit" value="Sauvegarder les modifications" class="btn" name="save_modifications">
            </form>
        </div>
        <?php
    } else {
        echo "<p>Aucun intervenant trouvé avec cet ID.</p>";
    }

    ?>

</body>

</html>

<?php
// Close the database connection
$conn->close();
ob_end_flush();
?>