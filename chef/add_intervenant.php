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

// Function to add a new function to the "fonction" table
function addFunction($conn, $libelle)
{
    // Temporarily disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("INSERT INTO fonction (libelle) VALUES (?)");
    if (!$stmt) {
        die("Error in the prepared statement: " . $conn->error);
    }

    $stmt->bind_param("s", $libelle);

    if ($stmt->execute()) {
        // Enable foreign key checks back
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        return $conn->insert_id;
    } else {
        echo "Erreur lors de l'ajout de la fonction: " . $stmt->error;
        return false;
    }
    $stmt->close();
}

// Function to add a new intervenant to the "intervenant" table
function addIntervenant($conn, $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id)
{
    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("INSERT INTO intervenant (nom, prenom, email, adresse, genre, mot_passe, fonction_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error in the prepared statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssi", $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id);

    if ($stmt->execute()) {
        echo "L'intervenant a été ajouté avec succès.";
        // Redirect to the same page after successful insertion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'intervenant: " . $stmt->error;
    }
    $stmt->close();
}

// Handle intervenant addition form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["ajouter_intervenant_submit"])) {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $email = $_POST["email"];
        $adresse = $_POST["adresse"];
        $genre = $_POST["genre"];
        $mot_passe = $_POST["mot_passe"];
        $fonction_id = $_POST["fonction_id"];

        // Check if the entered fonction_id exists in the fonction table
        $stmt_check_fonction = $conn->prepare("SELECT fonction_id FROM fonction WHERE fonction_id = ?");
        $stmt_check_fonction->bind_param("i", $fonction_id);
        $stmt_check_fonction->execute();
        $stmt_check_fonction->store_result();

        if ($stmt_check_fonction->num_rows === 0) {
            // If the fonction_id does not exist, add the new function to the fonction table
            $libelle = $_POST["fonction_libelle"];
            $fonction_id = addFunction($conn, $libelle);
            if ($fonction_id === false) {
                // If adding the function fails, stop the process
                exit();
            }
        }

        addIntervenant($conn, $nom, $prenom, $email, $adresse, $genre, $mot_passe, $fonction_id);
    }
}

// Retrieve intervenant information data from the "intervenant" table
$search_intervenant_id = isset($_GET['search_intervenant_id']) ? $_GET['search_intervenant_id'] : '';
$sql_intervenants = "SELECT * FROM intervenant WHERE intervenant_id LIKE '%$search_intervenant_id%'";
$result_intervenants = $conn->query($sql_intervenants);

// Retrieve function information data from the "fonction" table
$sql_fonctions = "SELECT * FROM fonction";
$result_fonctions = $conn->query($sql_fonctions);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestion des Intervenants</title>
    <!-- Add your CSS styles and other head content here -->
    <style>
        /* Add your CSS styles here */
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
            padding: 70px 110px; /* Add padding for spacing */
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
        // Add your JavaScript here
        function validateForm() {
            // Add client-side form validation here if needed
            return true; // Return true to submit the form, or false to prevent submission
        }

        function confirmDelete() {
            return confirm("vous voulez supprimer cet intervenant?");
        }

        function deleteIntervenant(intervenant_id) {
            if (confirmDelete()) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>" + "?delete_intervenant_id=" + intervenant_id;
            }
        }
    </script>
</head>

<body>
<div class="content-container">
<div class="directeur_side_bar_content">
    <?php
    include 'chef_side_bar.php';
    ?>
    </div>
    <div class="main_content">
    <h1>Gestion des Intervenants</h1>

    <!-- Search intervenant form -->
    <div class="search-form-container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <label for="search_intervenant_id">Rechercher par ID:</label>
            <input type="text" id="search_intervenant_id" name="search_intervenant_id">
            <input type="submit" value="Rechercher" class="btn">
        </form>
    </div>

    <!-- Display intervenants table -->
    <div class="table-container">
        <h2>Table des Intervenants</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Adresse</th>
              
                <th>Genre</th>
                <th>Fonction</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_intervenants !== false && $result_intervenants->num_rows > 0) {
                while ($row = $result_intervenants->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["intervenant_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["prenom"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["adresse"]) . "</td>";
                   
                    echo "<td>" . htmlspecialchars($row["genre"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fonction_id"]) . "</td>";
                    echo '<td class="actions">';
                    echo '<a href="edit_add_intervenant.php?intervenant_id=' . $row["intervenant_id"] . '"><i class="fas fa-edit"></i></a>';
                    echo '<a href="#" onclick="deleteIntervenant(' . $row["intervenant_id"] . ')"><i class="fas fa-trash-alt"></i></a>';
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Aucun intervenant trouvé.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Add a new intervenant form -->
    <div class="form-container">
        <h2>Ajouter un intervenant</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return validateForm()">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="adresse">Adresse:</label>
            <input type="text" id="adresse" name="adresse" required>


            <label for="genre">Genre:</label>
            <select id="genre" name="genre" required>
                <option value="">Sélectionner un genre</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>

            <label for="mot_passe">Mot de passe:</label>
            <input type="password" id="mot_passe" name="mot_passe" required>

            <label for="fonction_id">Fonction:</label>
            <select id="fonction_id" name="fonction_id" required>
                <option value="">Sélectionner une fonction</option>
                <option value="1">docteur</option>
                <option value="2">medcin</option>
                <option value="3">infirmier</option>
                <option value="4"> l'aide soignant</option>
                <option value="5"> le manipulateur radio</option>
                <option value="6">  le diététicien</option>
                <option value="7">  préparateur en pharmacie</option>
                <option value="8">   technicien de laboratoire.</option>
                <option value="9">  anesthésiste</option>
                <option value="10">   technicien de laboratoire.</option>
                <option value="11">   l'infirmière anesthésiste</option>
                <option value="12">  interne</option>
                <option value="13">   chirurgien</option>
                <option value="14">  infirmière instrumentiste</option>
              

                <!-- Add more options based on the existing functions in the "fonction" table -->
            </select>

            <input type="submit" name="ajouter_intervenant_submit" value="Ajouter" class="btn">
        </form>
    </div>

    <?php
    // Handle intervenant deletion
    // ...

// Function to delete an intervenant from the "intervenant" table
function deleteIntervenant($conn, $intervenant_id)
{
    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("DELETE FROM intervenant WHERE intervenant_id = ?");
    if (!$stmt) {
        die("Error in the prepared statement: " . $conn->error);
    }

    $stmt->bind_param("i", $intervenant_id);

    if ($stmt->execute()) {
        echo "L'intervenant a été supprimé avec succès.";
        // Redirect to the same page after successful deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de la suppression de l'intervenant: " . $stmt->error;
    }
    $stmt->close();
}

// ...

// Handle intervenant deletion
if (isset($_GET['delete_intervenant_id'])) {
    $intervenant_id = $_GET['delete_intervenant_id'];
    deleteIntervenant($conn, $intervenant_id);
}

// ...

    // Close the database connection
    $conn->close();
    ob_end_flush();
    ?>
</div>
</div>
</body>

</html>
