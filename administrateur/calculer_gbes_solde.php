<?php
// Database connection parameters (same as before)
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

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Rapport des transactions</title>
    <style>
        /* Add your CSS styles for the report here */

        /* Common styles for tables */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 16px;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Header Styles */
        .header {
            
            text-align: left;
            margin-bottom: 20px;
        }

        .header img {
            margin-top: 30px;
            width: 100px; /* Adjust the width as needed */
            height: auto;
        }

        .header h1 {
            text-align: center; /* Center the header */
            margin-bottom: 15px;
            color: #000;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 400;
            text-transform: uppercase;
        }

        /* Total Styles */
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            border-top: 2px solid #000; /* Add a top border */
            padding-top: 10px; /* Add some space above the total */
        }

        .total span {
            font-size: 14px;
        }

        .total strong {
            font-size: 18px;
            color: #0066cc; +
        }
        .facture-buttons {
          
        text-align: center;
        margin-top: 20px;
    }
    .facture-buttons button {
        border-radius: 2px;
        background-color: #4CAF50; /* Green color, you can change it to any color you like */
        color: white; /* Text color */
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        margin-right: 10px;
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
            padding: 70px 50px; /* Add padding for spacing */
            max-width: calc(100% - 250px); /* Adjust as needed */
        }
    </style>
</head>
<body>
<div class="content-container">
        <div class="directeur_side_bar_content">
            <?php
  include 'admin_side_bar.php';
  ?>
        </div>
        <div class="main_content">
    <div class="header">
        <img src="images/logo-no-background.png" alt="Logo"> <!-- Replace with the actual path to your logo image -->
        <h1>Rapport des transactions</h1>
    </div>

    <!-- First Table: Transactions Intervenants -->
    <h2> transactions Intervenants</h2>
    <table>
        <!-- Table header row -->
        <tr>
            <th>Facture ID</th>
            <th>Date facture</th>
            <th>Référence</th>
            <th>Mode de paiement</th>
            <th>Numéro de chèque</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Montant (DH)</th>
        </tr>
        <!-- Table data rows - Replace with the actual PHP code for transactions intervenants table -->
        <?php
        // Retrieve transaction history data from the "facture_i," "cheque," and "produit" tables
        $search_i = isset($_GET['search_i']) ? $_GET['search_i'] : '';
        $sql_i = "SELECT f.facture_i_id, f.date_facture, f.mode_paiement, c.numero_cheque, i.nom AS intervenant_nom, i.prenom AS intervenant_prenom, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde
        FROM facture_i f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_i df ON f.facture_i_id = df.facture_i_id
        INNER JOIN produit p ON df.produit_id = p.produit_id
        LEFT JOIN intervenant i ON f.intervenant_id = i.intervenant_id
        WHERE p.ref LIKE '%$search_i%' OR f.facture_i_id = '$search_i'
        ORDER BY f.date_facture DESC, p.ref";
        $result_i = $conn->query($sql_i);

        // Display the data for transactions intervenants
        $total_intervenants = 0;
        if ($result_i->num_rows > 0) {
            while ($row = $result_i->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["facture_i_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_facture"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ref"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["mode_paiement"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["numero_cheque"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["libelle"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["prix_unitaire"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["solde"]) . "</td>";
                echo "</tr>";

                $total_intervenants += $row["solde"];
            }
        } else {
            echo "<tr><td colspan='9'>Aucune facture trouvée pour les intervenants.</td></tr>";
        }
        ?>
    </table>

    <!-- Second Table: Transactions Fournisseurs -->
    <h2> transactions Fournisseurs</h2>
    <table>
        <!-- Table header row -->
        <tr>
            <th>Facture ID</th>
            <th>Date facture</th>
            <th>Référence</th>
            <th>Mode de paiement</th>
            <th>Numéro de chèque</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Montant (DH)</th>
        </tr>
        <!-- Table data rows - Replace with the actual PHP code for transactions fournisseurs table -->
        <?php
        // Retrieve transaction history data from the "facture_f," "cheque," and "produit" tables
        $search_f = isset($_GET['search_f']) ? $_GET['search_f'] : '';
        $sql_f = "SELECT f.facture_f_id, f.date_facture, f.mode_paiement, c.numero_cheque, fo.nom AS fournisseur_nom, fo.prenom AS fournisseur_prenom, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde
        FROM facture_f f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_f df ON f.facture_f_id = df.facture_f_id
        INNER JOIN produit p ON df.produit_id = p.produit_id
        LEFT JOIN fournisseur fo ON f.fournisseur_id = fo.fournisseur_id
        WHERE p.ref LIKE '%$search_f%' OR f.facture_f_id = '$search_f'
        ORDER BY f.date_facture DESC, p.ref";
        $result_f = $conn->query($sql_f);

        // Display the data for transactions fournisseurs
        $total_fournisseurs = 0;
        if ($result_f->num_rows > 0) {
            while ($row = $result_f->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["facture_f_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_facture"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ref"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["mode_paiement"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["numero_cheque"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["libelle"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["prix_unitaire"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["solde"]) . "</td>";
                echo "</tr>";

                $total_fournisseurs += $row["solde"];
            }
        } else {
            echo "<tr><td colspan='9'>Aucune facture trouvée pour les fournisseurs.</td></tr>";
        }
        ?>
    </table>

    <!-- Third Table: Transactions Clients -->
    <h2> transactions Clients</h2>
    <table>
        <!-- Table header row -->
        <tr>
            <th>ID Facture</th>
            <th>Référence</th>
            <th>Date de transaction</th>
            <th>Mode de paiement</th>
            <th>Numéro de chèque</th>
            <th>Assurance</th>
            <th>Opération de chirurgie</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Montant (DH)</th>
        </tr>
        <!-- Table data rows - Replace with the actual PHP code for transactions clients table -->
        <?php
        // Retrieve transaction history data from the "facture_p," "cheque," and "produit" tables
        $search_p = isset($_GET['search_p']) ? $_GET['search_p'] : '';
        $sql_p = "SELECT f.facture_p_id, f.date_facture, f.mode_paiement, c.numero_cheque, p.ref, p.libelle, p.quantite, p.prix_unitaire, p.solde, pa.intitule AS assurance_intitule
        FROM facture_p f
        LEFT JOIN cheque c ON f.cheque_id = c.cheque_id
        INNER JOIN detail_p dp ON f.facture_p_id = dp.facture_p_id
        INNER JOIN produit p ON dp.produit_id = p.produit_id
        LEFT JOIN patient pt ON f.patient_id = pt.patient_id
        LEFT JOIN assurance pa ON pt.assurance_id = pa.assurance_id
        WHERE p.ref LIKE '%$search_p%' OR f.facture_p_id = '$search_p'
        ORDER BY f.date_facture DESC, p.ref";
        $result_p = $conn->query($sql_p);

        // Display the data for transactions clients
        $total_clients = 0;
        if ($result_p->num_rows > 0) {
            while ($row = $result_p->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["facture_p_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ref"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_facture"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["mode_paiement"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["numero_cheque"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["assurance_intitule"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["libelle"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantite"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["prix_unitaire"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["solde"]) . "</td>";
                echo "</tr>";

                $total_clients += $row["solde"];
            }
        } else {
            echo "<tr><td colspan='10'>Aucune facture trouvée pour les clients.</td></tr>";
        }
        ?>
    </table>

    <!-- Total Montant of the Society -->
    <?php
    $total_intervenants_fournisseurs = $total_intervenants + $total_fournisseurs;
    $total_society = $total_clients - $total_intervenants_fournisseurs;
    ?>
    <div class="total">
        <span>Montant total de la société:</span>
        <?php echo number_format($total_society, 2) . " DH"; ?>
    </div>
    <div class="facture-buttons">
        <button onclick="printFacture()"><i class="fas fa-print"></i> Imprimer</button>

    </div>
    <script>
        function printFacture() {
            window.print();
        }
    </script>
    </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
