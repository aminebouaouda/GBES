<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Directeur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Your existing CSS styles */
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .login-container.success {
            transform: scale(1.05);
        }

        .logo {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .success-message {
            display: none;
            color: green;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        #back-to-acceuil {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        #back-to-acceuil i {
            margin-right: 5px;
        }

        #back-to-acceuil:hover {
            color: #0056b3;
        }

        /* Adjusted styles */
        .info-text {
            margin-top: -30px;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="chef/images/logo-black.png" alt="Logo" class="logo">
        <p class="info-text">Entrez vos informations de connexion ci-dessous</p>
        <a href="acceuil.php" id="back-to-acceuil">
            <i class="fas fa-arrow-left"></i> <!-- Font Awesome icon for back -->
            Retour 
        </a>
        <h1>Connexion chef</h1>
        <form id="login-form" action="" method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="login">Connecter</button>
        </form>
        <p id="success-message" class="success-message">Connexion réussie. Redirection en cours...</p>
        <p id="error-message" class="error-message">
            <?php
            if (isset($_POST['login'])) {
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

                $username = $_POST['username'];
                $password = $_POST['password'];

                // Query to retrieve the user
                $query = "SELECT * FROM admins WHERE role = 'chef' AND username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    
                    // Check if the password matches
                    if ($password === $row['password'] || password_verify($password, $row['password'])) {
                        echo '<script>
                            document.getElementById("login-form").style.display = "none";
                            document.getElementById("success-message").style.display = "block";
                            setTimeout(function(){
                                window.location.href = "chef/client_historique.php";
                            }, 2000);
                        </script>';
                    } else {
                        echo "Nom d'utilisateur ou mot de passe incorrect.";
                    }
                } else {
                    echo "Nom d'utilisateur ou mot de passe incorrect.";
                }
            }
            ?>
        </p>
    </div>
</body>

</html>
