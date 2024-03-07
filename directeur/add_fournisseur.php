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

// Function to add a new fournisseur to the "fournisseur" table
function addFournisseur($conn, $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo)
{
    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp_name = $_FILES['photo']['tmp_name'];
        $photo_path = "photos/" . basename($photo_name); // Assuming you have a folder named "photos" to store uploaded photos
        move_uploaded_file($photo_tmp_name, $photo_path);
    } else {
        // If no photo is uploaded, you can set a default photo path or leave it empty based on your requirement
        $photo_path = ""; // Set a default photo path or leave it empty
    }

    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("INSERT INTO fournisseur (nom, prenom, email, adresse, telephone, genre, mot_passe, photo)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo_path);

    if ($stmt->execute()) {
        echo "Le fournisseur a été ajouté avec succès.";
        // Redirect to the same page after successful insertion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de l'ajout du fournisseur: " . $conn->error;
    }
    $stmt->close();
}

// Function to delete a fournisseur from the "fournisseur" table
function deleteFournisseur($conn, $fournisseur_id)
    {
        // Delete the fournisseur from the "fournisseur" table using prepared statements
        $stmt_delete_fournisseur = $conn->prepare("DELETE FROM fournisseur WHERE fournisseur_id = ?");
        $stmt_delete_fournisseur->bind_param("i", $fournisseur_id);
    
        if ($stmt_delete_fournisseur->execute()) {
            echo "Le fournisseur a été supprimé avec succès.";
            // Redirect to the same page after successful deletion
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Erreur lors de la suppression du fournisseur: " . $stmt_delete_fournisseur->error;
        }
        $stmt_delete_fournisseur->close();
    }

// Handle fournisseur addition form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["ajouter_fournisseur_submit"])) {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $email = $_POST["email"];
        $adresse = $_POST["adresse"];
        $telephone = $_POST["telephone"];
        $genre = $_POST["genre"];
        $mot_passe = $_POST["mot_passe"];
        $photo = $_FILES['photo']['name']; // Get the photo filename

        addFournisseur($conn, $nom, $prenom, $email, $adresse, $telephone, $genre, $mot_passe, $photo);
    }
}

// Retrieve fournisseur information data from the "fournisseur" table
$search_fournisseur_id = isset($_GET['search_fournisseur_id']) ? $_GET['search_fournisseur_id'] : '';
$sql_fournisseurs = "SELECT * FROM fournisseur WHERE fournisseur_id LIKE '%$search_fournisseur_id%'";
$result_fournisseurs = $conn->query($sql_fournisseurs);


?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestion des Fournisseurs</title>
    <!-- Add your CSS styles and other head content here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        h1 {
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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

        .table-container {
            max-width: 100%;
            overflow-x: auto;
        }

        .actions a {
            display: inline-block;
            margin-right: 5px;
            color: #007BFF;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .actions .delete-btn {
            background-color: #ff6363;
            color: #fff;
        }

        .actions .edit-btn {
            background-color: #1E90FF;
            color: #fff;
        }

        /* Add styles for the search form */
        .search-form-container {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .search-form-container form {
            display: flex;
            align-items: center;
        }

        .search-form-container label {
            margin-right: 10px;
        }

        .search-form-container input[type="text"],
        .search-form-container input[type="submit"] {
            padding: 8px;
        }
        .content-container {
            display: flex; /* Use flexbox to arrange content */
            justify-content: space-between; /* Space between the two divs */
        }

        .directeur_side_bar_content,
        .main_content {
            flex: 1; /* Let both divs take equal space */
            padding: 10px; /* Add padding for spacing */
        }
        .content-container {
            display: flex; /* Use flexbox to arrange content */
            justify-content: space-between; /* Space between the two divs */
        }

        
        .directeur_side_bar_content,
        .main_content {
            flex: 1; /* Let both divs take equal space */
            padding: 70px 90px; /* Add padding for spacing */
            max-width: calc(100% - 250px); /* Adjust as needed */
        }
        .actions a i {
            font-size: 18px;
            margin-right: 10px;
        }

        .actions a i.fa-edit {
            color: #1E90FF;
            /* Blue color for the edit icon */
        }

        .actions a i.fa-trash-alt {
            color: #ff6363;
            /* Red color for the delete icon */
        }

    </style>
    <script>
        function validateForm() {
            // Add client-side form validation here if needed
            return true; // Return true to submit the form, or false to prevent submission
        }

        function confirmDelete() {
            return confirm("vous voulez supprimer ce fournisseur?");
        }

        function deleteFournisseur(fournisseur_id) {
            if (confirmDelete()) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>" + "?delete_fournisseur_id=" + fournisseur_id;
               
            }
        }
    </script>
</head>

<body>
<div class="content-container">
<div class="directeur_side_bar_content">
    <?php
    include 'directeur_side_bar.php';
    ?>
    </div>
    <div class="main_content">
    <h1>Gestion des Fournisseurs</h1>

    <!-- Search fournisseur form -->
    <div class="search-form-container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <label for="search_fournisseur_id">Rechercher par ID:</label>
            <input type="text" id="search_fournisseur_id" name="search_fournisseur_id">
            <input type="submit" value="Rechercher" class="btn">
        </form>
    </div>

    <!-- Display fournisseurs table -->
    <div class="table-container">
        <h2>Table des Fournisseurs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Genre</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_fournisseurs !== false && $result_fournisseurs->num_rows > 0) {
                while ($row = $result_fournisseurs->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["fournisseur_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["prenom"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["adresse"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["telephone"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["genre"]) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row["photo"]) . "' width='100' height='100'></td>";
                    echo '<td class="actions">';
                    echo '<a href="edit_add_fournisseur.php?fournisseur_id=' . $row["fournisseur_id"] . '"><i class="fas fa-edit"></i></a>';
                    echo '<a href="#" onclick="deleteFournisseur(' . $row["fournisseur_id"] . ')"><i class="fas fa-trash-alt"></i></a>';
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Aucun fournisseur trouvé.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Add a new fournisseur form -->
    <div class="form-container">
        <h2>Ajouter un fournisseur</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
            onsubmit="return validateForm()">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="adresse">Adresse:</label>
            <input type="text" id="adresse" name="adresse" required>

            <label for="telephone">Téléphone:</label>
            <input type="tel" id="telephone" name="telephone" required>

            <label for="genre">Genre:</label>
            <select id="genre" name="genre" required>
                <option value="">Sélectionner un genre</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>

            <label for="mot_passe">Mot de passe:</label>
            <input type="password" id="mot_passe" name="mot_passe" required>

            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo" accept="image/*">

            <input type="submit" name="ajouter_fournisseur_submit" value="Ajouter" class="btn">
        </form>
    </div>

    <?php
    // Function to delete an intervenant from the "intervenant" table

    
    if (isset($_GET['delete_fournisseur_id'])) {
        $fournisseur_id = $_GET['delete_fournisseur_id'];
        deleteFournisseur($conn, $fournisseur_id);
    }
    
    // Close the database connection
    $conn->close();
    ob_end_flush();
    ?>
</div>
</div>
</body>

</html>
