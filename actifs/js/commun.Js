// Add an event listener to the logo element
document.getElementById('logo').addEventListener('click', () => {
  // Redirect to the acceuil page
  window.location.href = 'acceuil.php'; // Replace 'acceuil.php' with the name of your homepage (the correct URL path)
});
/*the search bar */
// JavaScript to show/hide the search class on clicking the search icon
document.addEventListener("DOMContentLoaded", function () {
  const searchIcon = document.getElementById("search-icon");
  const searchBar = document.getElementById("search-bar");

  searchIcon.addEventListener("click", function () {
    searchBar.classList.toggle("search-visible");
    searchBar.focus();
  });
});

/*scrol the background color into white color   */

/*document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector(".header");
  
    window.addEventListener("scroll", function () {
      const scrollY = window.scrollY;
      const headerHeight = header.offsetHeight;
  
      if (scrollY > headerHeight) {
        header.style.backgroundColor = "#fff"; // Change to white when scrolled down
      } else {
        header.style.backgroundColor = "transparent"; // Keep transparent initially
      }
    });
  });*/
/*version 2 */
document.addEventListener("DOMContentLoaded", function () {
  // Get references to the elements
  const searchIcon = document.getElementById("search-icon");
  const searchBar = document.getElementById("search-bar");
  const rechercherButton = document.querySelector(".rechercher-button");
  const header = document.querySelector(".header");

  // Add a click event listener to the search icon
  searchIcon.addEventListener("click", function () {
    // Toggle the visibility of the search icon and search bar
    searchIcon.style.display = "none";
    searchBar.style.display = "inline-block";
    rechercherButton.style.display = "inline-block";

    // Focus on the search bar to make it easier for the user to start typing
    searchBar.focus();
  });

  // Add a click event listener to the "Rechercher" button
  rechercherButton.addEventListener("click", function () {
    // Toggle the visibility of the search icon and search bar
    searchIcon.style.display = "inline-block";
    searchBar.style.display = "none";
    rechercherButton.style.display = "none";
  });

  // Add a scroll event listener to change the header background
  document.addEventListener("scroll", function () {
    const scrollY = window.scrollY;
    if (scrollY > 0) {
      header.style.backgroundColor = "#fff";
    } else {
      header.style.backgroundColor = "transparent";
    }
  });
});
/*handle the page switch */

function goToAccueilPage() {
  // Replace "accueil-page.html" with the actual URL of your accueil page
  window.location.href = "acceuil.php";
}
// Function to handle the click event for "Espace Client"
function goToClientPage() {
  // Replace "client-page.html" with the actual URL of your client page
  window.location.href = "client.php";
}

// Function to handle the click event for "Espace Employé"
function goToEmployeePage() {
  // Replace "employee-page.html" with the actual URL of your employee page
  window.location.href = "employee.php";
}

// Function to handle the click event for "Directeur"
function goToDirecteurPage() {
  // Replace "directeur-page.html" with the actual URL of your Directeur page
  window.location.href = "utilisateur-directeur.php";
}

// Function to handle the click event for "Administrateur"
function goToAdministrateurPage() {
  // Replace "administrateur-page.html" with the actual URL of your Administrateur page
  window.location.href = "utilisateur-administrateur.php";
}
// Function to handle the click event for "À Propos"
function goToAProposPage() {
  // Replace "a-propos-page.html" with the actual URL of your À Propos page
  window.location.href = "propos.php";
}

// Function to handle the click event for "Contact"
function goToContactPage() {
  // Replace "contact-page.html" with the actual URL of your contact page
  window.location.href = "contact.php";
}
//Function to handle the click event for "rechercher"
// Function to handle the click event for "Contact"
function goToContactPage() {
  // Replace "contact-page.html" with the actual URL of your contact page
  window.location.href = "rechercher.php";
}

// Wait for the DOM content to load
document.addEventListener("DOMContentLoaded", function () {
  // Get references to the elements
  const espaceClientLink = document.querySelector('a[href="#espace-client"]');
  const espaceEmployeeLink = document.querySelector('a[href="#espace-employee"]');
  const accueilLink = document.querySelector('a[href="#accueil"]');
  const aProposLink = document.querySelector('a[href="#a-propos"]');
  const contactLink = document.querySelector('a[href="#contact"]');
  const directeurLink = document.querySelector('a[href="#directeur"]');
  const administrateurLink = document.querySelector('a[href="#administrateur"]');

  // Add click event listeners to handle the redirects
  espaceClientLink.addEventListener("click", goToClientPage);
  espaceEmployeeLink.addEventListener("click", goToEmployeePage);
  accueilLink.addEventListener("click", goToAccueilPage);
  aProposLink.addEventListener("click", goToAProposPage);
  contactLink.addEventListener("click", goToContactPage);
  directeurLink.addEventListener("click", goToDirecteurPage);
  administrateurLink.addEventListener("click", goToAdministrateurPage);
});

// Add the existing code for the search functionality here



