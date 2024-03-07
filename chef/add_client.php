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

// Function to add a new patient to the "patient" table
function addPatient($conn, $nom, $prenom, $age, $email, $adresse, $telephone, $genre, $mot_passe, $photo)
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
    $stmt = $conn->prepare("INSERT INTO patient (nom, prenom, age, email, adresse, telephone, genre, mot_passe, photo)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssss", $nom, $prenom, $age, $email, $adresse, $telephone, $genre, $mot_passe, $photo_path);

    if ($stmt->execute()) {
        echo "Le patient a été ajouté avec succès.";
        // Redirect to the same page after successful insertion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de l'ajout du patient: " . $conn->error;
    }
    $stmt->close();
}

// Function to add a new operation to the "op_chirgurie" table
function addOperation($conn, $nom, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id)
{
    // Prevent SQL injection by using prepared statements
    $stmt = $conn->prepare("INSERT INTO op_chirgurie (nom, date_operation, date_fin, heure_debut, heure_fin, patient_id)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nom, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id);

    if ($stmt->execute()) {
        echo "L'opération de chirurgie a été ajoutée avec succès.";
        // Redirect to the same page after successful insertion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'opération de chirurgie: " . $conn->error;
    }
    $stmt->close();
}

// Function to delete a patient and associated records from "op_chirgurie" table

function deletePatient($conn, $patient_id)
{
    // Delete associated records from "op_chirgurie" table using prepared statements
    $stmt_delete_operations = $conn->prepare("DELETE FROM op_chirgurie WHERE patient_id = ?");
    $stmt_delete_operations->bind_param("i", $patient_id);

    if ($stmt_delete_operations->execute()) {
        // Then, delete the patient from the "patient" table using prepared statements
        $stmt_delete_patient = $conn->prepare("DELETE FROM patient WHERE patient_id = ?");
        $stmt_delete_patient->bind_param("i", $patient_id);

        if ($stmt_delete_patient->execute()) {
            echo "Le patient et ses opérations de chirurgie associées ont été supprimés avec succès.";
        } else {
            echo "Erreur lors de la suppression du patient: " . $conn->error;
        }
        $stmt_delete_patient->close();
    } else {
        echo "Erreur lors de la suppression des opérations de chirurgie associées: " . $conn->error;
    }
    $stmt_delete_operations->close();
}


// Handle patient and operation addition form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["ajouter_patient_submit"])) {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $age = $_POST["age"];
        $email = $_POST["email"];
        $adresse = $_POST["adresse"];
        $telephone = $_POST["telephone"];
        $genre = $_POST["genre"];
        $mot_passe = $_POST["mot_passe"];
        $photo = $_FILES['photo']['name']; // Get the photo filename

        addPatient($conn, $nom, $prenom, $age, $email, $adresse, $telephone, $genre, $mot_passe, $photo);
    } elseif (isset($_POST["ajouter_operation_submit"])) {
        $nom_operation = $_POST["nom_operation"];
        $date_operation = $_POST["date_operation"];
        $date_fin = $_POST["date_fin"];
        $heure_debut = $_POST["heure_debut"];
        $heure_fin = $_POST["heure_fin"];
        $patient_id = $_POST["patient_id"];

        addOperation($conn, $nom_operation, $date_operation, $date_fin, $heure_debut, $heure_fin, $patient_id);
    }
}

// Retrieve patient information data from the "patient" table
$search_patient_id = isset($_GET['search_patient_id']) ? $_GET['search_patient_id'] : '';
$sql_patients = "SELECT * FROM patient WHERE patient_id LIKE '%$search_patient_id%'";
$result_patients = $conn->query($sql_patients);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Gestion des Patients</title>
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
            padding-left: 10px;
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
            padding: 60px 0px;
            padding-left: 90px;
            /* Add padding for spacing */
            max-width: calc(100% - 250px);
            /* Adjust as needed */
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
            return confirm("Are you sure you want to delete this patient and their associated operations?");
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
            <h1>Gestion des Patients</h1>

            <!-- Search patient form -->
            <div class="search-form-container">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <label for="search_patient_id">Rechercher par ID:</label>
                    <input type="text" id="search_patient_id" name="search_patient_id">
                    <input type="submit" value="Rechercher" class="btn">
                </form>
            </div>

            <!-- Display patients table -->
            <div class="table-container">
                <h2>Table des Patients</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Age</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Genre</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    if ($result_patients !== false && $result_patients->num_rows > 0) {
                        while ($row = $result_patients->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["patient_id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["prenom"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["adresse"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["telephone"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["genre"]) . "</td>";
                            echo "<td><img src='" . htmlspecialchars($row["photo"]) . "' width='100' height='100'></td>";
                            echo '<td class="actions">';
                            echo '<a href="edit_add_client.php?patient_id=' . $row["patient_id"] . '"><i class="fas fa-edit"></i></a>';
                            echo '<a href="?delete_patient_id=' . $row["patient_id"] . '" onclick="return confirmDelete()"><i class="fas fa-trash-alt"></i></a>';
                            echo "</td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Aucun patient trouvé.</td></tr>";
                    }
                    ?>
                </table>
            </div>

            <!-- Add a new patient form -->
            <div class="form-container">
                <h2>Ajouter un patient</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
                    onsubmit="return validateForm()">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" required>

                    <label for="prenom">Prénom:</label>
                    <input type="text" id="prenom" name="prenom" required>

                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" required>

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

                    <input type="submit" name="ajouter_patient_submit" value="Ajouter" class="btn">
                </form>
            </div>
            <hr>
            <!-- Display operations de chirurgie table -->
            <div class="table-container">
                <h2>Table des Opérations de Chirurgie</h2>
                <table>
                    <tr>
                        <th>Opération ID</th>
                        <th>Nom</th>
                        <th>Date d'Opération</th>
                        <th>Date de Fin</th>
                        <th>Heure de Début</th>
                        <th>Heure de Fin</th>
                        <th>Patient</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $sql_operations = "SELECT o.*, p.nom AS patient_nom, p.prenom AS patient_prenom
                                FROM op_chirgurie o
                                INNER JOIN patient p ON o.patient_id = p.patient_id
                                ORDER BY o.op_chirgurie_id DESC";

                    $result_operations = $conn->query($sql_operations);

                    if ($result_operations !== false && $result_operations->num_rows > 0) {
                        while ($row = $result_operations->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["op_chirgurie_id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["date_operation"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["date_fin"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["heure_debut"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["heure_fin"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["patient_nom"]) . " " . htmlspecialchars($row["patient_prenom"]) . "</td>";
                            echo '<td class="actions">';
                            echo '<a href="edit_add_operation.php?op_chirgurie_id=' . $row["op_chirgurie_id"] . '" class="edit-btn">Editer</a>';
                            echo '<a href="?delete_operation_id=' . $row["op_chirgurie_id"] . '" onclick="return confirmDelete()" class="delete-btn">Supprimer</a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Aucune opération de chirurgie trouvée.</td></tr>";
                    }
                    ?>
                </table>
            </div>

            <!-- Add a new operation form -->
            <div class="form-container">
                <h2>Ajouter une opération de chirurgie</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <label for="nom_operation">Nom de l'opération:</label>
                    <input type="text" id="nom_operation" name="nom_operation" required>

                    <label for="date_operation">Date d'opération:</label>
                    <input type="date" id="date_operation" name="date_operation" required>

                    <label for="date_fin">Date de fin:</label>
                    <input type="date" id="date_fin" name="date_fin" required>

                    <label for="heure_debut">Heure de début:</label>
                    <input type="time" id="heure_debut" name="heure_debut" required>

                    <label for="heure_fin">Heure de fin:</label>
                    <input type="time" id="heure_fin" name="heure_fin" required>

                    <label for="patient_id">Patient:</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Sélectionner un patient</option>
                        <?php
                        // Retrieve patient IDs and names from the "patient" table
                        $sql_patients_dropdown = "SELECT patient_id, CONCAT(nom, ' ', prenom) AS patient_name FROM patient";
                        $result_patients_dropdown = $conn->query($sql_patients_dropdown);

                        if ($result_patients_dropdown !== false && $result_patients_dropdown->num_rows > 0) {
                            while ($row = $result_patients_dropdown->fetch_assoc()) {
                                $patient_id = htmlspecialchars($row["patient_id"]);
                                $patient_name = htmlspecialchars($row["patient_name"]);
                                echo "<option value='$patient_id'>$patient_name</option>";
                            }
                        }
                        ?>
                    </select>

                    <input type="submit" name="ajouter_operation_submit" value="Ajouter" class="btn">
                </form>
            </div>

            <?php
            // Search patient by name
            if (isset($_GET['search_patient_nom']) && !empty($_GET['search_patient_nom'])) {
                $search_patient_nom = $_GET['search_patient_nom'];
                $sql_search_patient_nom = "SELECT * FROM patient WHERE nom LIKE '%$search_patient_nom%'";
                $result_search_patient_nom = $conn->query($sql_search_patient_nom);
                if ($result_search_patient_nom !== false && $result_search_patient_nom->num_rows > 0) {
                    echo "<div class='table-container'>";
                    echo "<h2>Résultat de recherche</h2>";
                    echo "<table>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Nom</th>";
                    echo "<th>Prénom</th>";
                    echo "<th>Age</th>";
                    echo "<th>Email</th>";
                    echo "<th>Adresse</th>";
                    echo "<th>Téléphone</th>";
                    echo "<th>Genre</th>";
                    echo "<th>Photo</th>";
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    while ($row = $result_search_patient_nom->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["patient_id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["prenom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["adresse"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["telephone"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["genre"]) . "</td>";
                        echo "<td><img src='" . htmlspecialchars($row["photo"]) . "' width='100' height='100'></td>";
                        echo '<td class="actions">';
                        echo '<a href="edit_add_client.php?patient_id=' . $row["patient_id"] . '" class="edit-btn">Editer</a>';
                        echo '<a href="?delete_patient_id=' . $row["patient_id"] . '" onclick="return confirmDelete()" class="delete-btn">Supprimer</a>';
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>Aucun patient trouvé avec ce nom.</p>";
                }
            }

            // Handle operation deletion
            if (isset($_GET['delete_operation_id'])) {
                $op_chirgurie_id = $_GET['delete_operation_id'];
                deleteOperation($conn, $op_chirgurie_id);
            }

            // Function to delete an operation from "op_chirgurie" table
            function deleteOperation($conn, $op_chirgurie_id)
            {
                $stmt_delete_operation = $conn->prepare("DELETE FROM op_chirgurie WHERE op_chirgurie_id = ?");
                $stmt_delete_operation->bind_param("i", $op_chirgurie_id);

                if ($stmt_delete_operation->execute()) {
                    echo "L'opération de chirurgie a été supprimée avec succès.";
                    // Redirect to the same page after successful deletion
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "Erreur lors de la suppression de l'opération de chirurgie: " . $conn->error;
                }
                $stmt_delete_operation->close();
            }

            // Handle patient deletion
            if (isset($_GET['delete_patient_id'])) {
                $patient_id = $_GET['delete_patient_id'];
                deletePatient($conn, $patient_id);
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>