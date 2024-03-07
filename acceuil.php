<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Système Bancaire - Établissement de Santé gbes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
/*body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
   
}
header {
    background-color: #004f8b;
    color: #ffffff;
    padding: 10px;
    display: flex;
    align-items: center;
}*/
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-family: Sans, Century Gothic, CenturyGothic, AppleGothic, sans-serif;
}

body {
    margin: 0;
    padding: 0;
    /* font-family: Arial, sans-serif;*/

}

* {
    box-sizing: border-box;
}

header {

    position: fixed;
    /* Set the header to be fixed at the top */
    top: 0;
    /* Place the header at the top of the viewport */
    left: 0;
    /* Place the header at the left of the viewport */
    width: 100%;
    /* Set the width of the header to be 100% of the viewport width */
    background-color: transparent;
    transition: background-color 0.3s ease-in-out;
    color: #000;
    padding: 10px;
    display: flex;
    align-items: center;
    font-weight: 600;
    /* font-size: Inter,sans-serif;*/
    font-family: "HCA-Mark-Bold", "Arial", sans-serif;
    border-bottom: 1px solid rgba(56, 64, 65, .2);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    /*border-bottom: 1px solid #ccc;*/
    z-index: 100;
    /* Set the z-index to ensure the header appears above other elements */

}

/* Add margin to the body to push the content down below the fixed header */
body {
    margin-top: 70px;
    /* Adjust this value based on the height of your fixed header */
}


.logo img {
    width: 100px;
    height: 60px;
    padding-left: 22px;
    max-width: 100%;
    height: auto;
    vertical-align: middle;
    border-style: none;
    
}


nav {
    flex: 1;
    margin-left: 100px;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
}

nav li {
    padding: 10px 15px;
}

nav li a {
    color: #000;
    text-decoration: none;
}

nav li a:hover {
    text-decoration: underline;
    color: #008995;
    transition: all .1s ease-in;
}

.dropdown-content a:hover {
    text-decoration: none;
    color: #008995;
    transition: all .1s ease-in;
    font-size: .9rem;
    white-space: nowrap;
    margin-right: 1.5rem;
    font-weight: 400;

}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #fff;
}

.dropdown-content a {
    color: #000;
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    font-size: .9rem;
    font-weight: 400;
    white-space: nowrap;
}

.dropdown-content a:hover {
    /*  background-color: #003057;*/
    background-color: #c9eff0;

}

.dropdown:hover .dropdown-content {
    display: block;
}

/* Your existing CSS styles */


main {
    padding: 20px;
}

footer {
    background-color: #008995;
    color: #ffffff;
    text-align: center;
    padding: 10px;
}

/*search ico animation*/
#search-bar,
.rechercher-button {
    display: none;
}

#search-icon {
    margin-right: 20px;
    cursor: pointer;
    font-size: 24px;
    color: #333;
}
.cover-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
    }

    .container {
      background-color: white;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
      margin-top: 150px;
      max-width: 800px;
      margin: 150px auto 40px;
    }

    .welcome-message {
      font-size: 24px;
      color: #008995;
      margin-bottom: 10px;
    }

    .system-info {
      color: #555;
      font-size: 16px;
      margin-bottom: 20px;
    }

    .cta-buttons {
      display: flex;
      gap: 10px;
    }

    .cta-button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #008995;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease-in-out;
    }

    .cta-button:hover {
      background-color: #006c7a;
    }



  </style>
</head>

<body>
<header class="header">
  <div class="logo" id="logo">
    <img src="actifs/images/logo-no-background.png" alt="Logo">
  </div>
  <nav>
    <ul>
      <li><a href="acceuil.php">Accueil</a></li>
      <li><a href="directeur.php">Espace directeur</a></li>
      <li><a href="chef.php">Espace chef</a></li>
      <li><a href="administrateur.php">Espace admin</a></li>
    </ul>
  </nav>
  <style>


    /* Apply the animation to the arrow when the arrow-animate class is present */
  
  </style>
  
</header>


  <main>
    <div class="container">
      <h2 class="welcome-message">Bienvenue sur le Système Bancaire GBES</h2>
      <p class="system-info">Notre système de gestion bancaire est conçu spécialement pour les établissements de santé. Il offre un contrôle complet sur les transactions, les comptes et plus encore.</p>
      <div class="cta-buttons">
        <a href="directeur.php" class="cta-button">Espace Directeur</a>
        <a href="chef.php" class="cta-button">Espace Chef</a>
        <a href="administrateur.php" class="cta-button">Espace Administrateur</a>
      </div>
    </div>
  </main>
  <script>
    /*handle the page switch */

    function goToAccueilPage() {
      // Replace "accueil-page.html" with the actual URL of your accueil page
      window.location.href = "acceuil.php";
    }
    // Function to handle the click event for "Espace Client"
    function goToClientPage() {
      // Replace "client-page.html" with the actual URL of your client page
      window.location.href = "directeur.php";
    }

    // Function to handle the click event for "Espace Employé"
    function goToEmployeePage() {
      // Replace "employee-page.html" with the actual URL of your employee page
      window.location.href = "chef.php";
    }

    // Function to handle the click event for "Directeur"
    function goToDirecteurPage() {
      // Replace "directeur-page.html" with the actual URL of your Directeur page
      window.location.href = "administrateur.php";
    }


    // Wait for the DOM content to load
    document.addEventListener("DOMContentLoaded", function () {
      // Get references to the elements
      const espaceClientLink = document.querySelector('a[href="#espace-client"]');
      const espaceEmployeeLink = document.querySelector('a[href="#espace-employee"]');
      const accueilLink = document.querySelector('a[href="#accueil"]');
    
      const contactLink = document.querySelector('a[href="#contact"]');

      // Add click event listeners to handle the redirects
      espaceClientLink.addEventListener("click", goToClientPage);
      espaceEmployeeLink.addEventListener("click", goToEmployeePage);
      accueilLink.addEventListener("click", goToAccueilPage);
      contactLink.addEventListener("click", goToContactPage);
  
// Add the existing code for the search functionality here
  </script>
  <footer>
    <p>Tous droits réservés &copy;
        <?php echo date("Y"); ?> Établissement bancaire GBES
    </p>
</footer>
  <script src="actifs/js/commun.js"></script>
</body>

</html>