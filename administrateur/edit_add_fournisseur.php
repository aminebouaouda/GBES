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

// Function to update fournisseur information in the "fournisseur" table
function updateFournisseur($conn, $fournisseur_id, $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo)
{
    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp_name = $_FILES['photo']['tmp_name'];
        $photo_path = "photos/" . basename($photo_name); // Assuming you have a folder named "photos" to store uploaded photos
        move_uploaded_file($photo_tmp_name, $photo_path);
    } else {
        // If no new photo is uploaded, keep the existing photo path
        $photo_path = getExistingPhotoPath($conn, $fournisseur_id);
    }

    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("UPDATE fournisseur SET nom=?, prenom=?, email=?, adresse=?, telephone=?, genre=?, mot_passe=?, photo=? WHERE fournisseur_id=?");
    $stmt->bind_param("ssssssssi", $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo_path, $fournisseur_id);

    if ($stmt->execute()) {
        echo "Les modifications du fournisseur ont été sauvegardées avec succès.";
    } else {
        echo "Erreur lors de la sauvegarde des modifications du fournisseur: " . $conn->error;
    }
    $stmt->close();
}

// Function to get the existing photo path of a fournisseur
function getExistingPhotoPath($conn, $fournisseur_id)
{
    $stmt = $conn->prepare("SELECT photo FROM fournisseur WHERE fournisseur_id = ?");
    $stmt->bind_param("i", $fournisseur_id);
    $stmt->execute();
    $stmt->bind_result($existing_photo);
    $stmt->fetch();
    $stmt->close();
    return $existing_photo;
}

// Retrieve fournisseur information based on the fournisseur_id from the query parameter
$fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
$sql_fournisseur = "SELECT * FROM fournisseur WHERE fournisseur_id = $fournisseur_id";
$result_fournisseur = $conn->query($sql_fournisseur);

// Handle fournisseur update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $adresse = $_POST["adresse"];
    $telephone = $_POST["telephone"];
    $genre = $_POST["genre"];
    $mot_passe = $_POST["mot_passe"];
    $photo = $_FILES['photo']['name']; // Get the photo filename

    updateFournisseur($conn, $fournisseur_id, $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo);

    // After updating the fournisseur, redirect to the view_fournisseur.php page for viewing the updated fournisseur details
    header("Location: add_fournisseur.php?fournisseur_id=" . $fournisseur_id);
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Editer un fournisseur</title>
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

        .form-container img {
            max-width: 100%;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <h1>Editer un fournisseur</h1>

    <?php
    if ($result_fournisseur !== false && $result_fournisseur->num_rows > 0) {
        $row = $result_fournisseur->fetch_assoc();
        ?>
        <!-- Edit fournisseur form -->
        <div class="form-container">
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?fournisseur_id=' . $fournisseur_id; ?>" method="post"
                enctype="multipart/form-data">
                <img src="<?php echo htmlspecialchars($row["photo"]); ?>" alt="Photo du fournisseur">
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

                <label for="telephone">Téléphone:</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($row["telephone"]); ?>"
                    required>

                <label for="genre">Genre:</label>
                <select id="genre" name="genre" required>
                    <option value="Homme" <?php if ($row["genre"] === "Homme")
                        echo "selected"; ?>>Homme</option>
                    <option value="Femme" <?php if ($row["genre"] === "Femme")
                        echo "selected"; ?>>Femme</option>
                </select>

                <label for="mot_passe">Mot de passe:</label>
                <input type="password" id="mot_passe" name="mot_passe"
                    value="<?php echo htmlspecialchars($row["mot_passe"]); ?>" required>

                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*">

                <input type="submit" value="Sauvegarder les modifications" class="btn">
            </form>
        </div>
        <?php
    } else {
        echo "<p>Aucun fournisseur trouvé avec cet ID.</p>";
    }
    ?>

</body>

</html>