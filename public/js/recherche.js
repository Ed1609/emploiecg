const searchInput = document.getElementById('search-input');
const jobList = document.getElementById('job-list');
const noResultsMessage = document.getElementById('no-results-message');

let initialJobList = null; // Store initial job list

// Function to fetch and display the initial job list
function fetchAndDisplayInitialJobs() {
    fetch('/abonne/voir-offre?offresParPage=10&page=1') // Replace with your actual route for paginated offers
        .then(response => response.text()) // Get HTML content
        .then(html => {
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = html;
          const initialList = tempDiv.querySelector('#job-list');

          if (initialList) {
            jobList.innerHTML = initialList.innerHTML;
            initialJobList = jobList.cloneNode(true); // store the initial list
          } else {
            console.error("Initial Job List not found in response HTML");
          }
        })
        .catch(error => console.error("Error fetching initial jobs:", error));
}


// Fetch initial jobs on page load
document.addEventListener('DOMContentLoaded', fetchAndDisplayInitialJobs);

searchInput.addEventListener('input', function () {
    const searchTerm = searchInput.value.trim();

    noResultsMessage.style.display = 'none';
    jobList.innerHTML = ""; // Clear existing list each time

    if (searchTerm === "") {
        if (initialJobList) {
            jobList.innerHTML = initialJobList.innerHTML; // Restore the initial list
        }
        return; // Exit early
    }

    if (searchTerm.length < 3) {
      return; // Do nothing if search term is less than 3 characters
    }

    fetch(`/search-offres?q=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                noResultsMessage.textContent = 'Aucun résultat trouvé pour votre recherche.';
                noResultsMessage.style.display = 'block';
            } else {
                data.forEach(item => {
                    const listItem = createJobListItem(item);
                    jobList.appendChild(listItem);
                });
            }
        })
        .catch(error => console.error("Error fetching data:", error));
});

function createJobListItem(item) {
    const listItem = document.createElement('li');
    listItem.classList.add('job-listing', 'd-block', 'd-sm-flex', 'pb-3', 'pb-sm-0', 'align-items-center');

    const link = document.createElement('a');
    link.href = `/offres/${item.slug}/${item.id}`; // Construct the URL dynamically

    const logoDiv = document.createElement('div');
    logoDiv.classList.add('job-listing-logo');
    const logoImg = document.createElement('img');
    logoImg.src = `/uploads/images/${item.entreprise.logo}`; // Construct the image URL dynamically
    logoImg.alt = item.entreprise.libele;
    logoImg.classList.add('img-fluid');
    logoDiv.appendChild(logoImg);

    const aboutDiv = document.createElement('div');
    aboutDiv.classList.add('job-listing-about', 'd-sm-flex', 'custom-width', 'w-100', 'justify-content-between', 'mx-4');

    const positionDiv = document.createElement('div');
    positionDiv.classList.add('job-listing-position', 'custom-width', 'w-50', 'mb-3', 'mb-sm-0');
    const titleH2 = document.createElement('h2');
    titleH2.textContent = item.titre;
    const entrepriseStrong = document.createElement('strong');
    entrepriseStrong.textContent = item.entreprise.libele;
    positionDiv.appendChild(titleH2);
    positionDiv.appendChild(entrepriseStrong);

    const lieuDiv = document.createElement('div');
    lieuDiv.classList.add('job-listing-lieu', 'mb-3', 'mb-sm-0', 'custom-width', 'w-25');
    const lieuSpan = document.createElement('span');
    lieuSpan.classList.add('icon-room');
    const lieuText = document.createTextNode(` ${item.lieu}`); // Add space before text
    lieuDiv.appendChild(lieuSpan);
    lieuDiv.appendChild(lieuText);

    const metaDiv = document.createElement('div');
    metaDiv.classList.add('job-listing-meta');
    const tempsTaffSpan = document.createElement('span');
    tempsTaffSpan.classList.add('badge');
    if (item.tempsTaff === 'Full Time') {
        tempsTaffSpan.classList.add('badge-success');
        tempsTaffSpan.textContent = 'Plein Temps';
    } else {
        tempsTaffSpan.classList.add('badge-danger');
        tempsTaffSpan.textContent = 'Mi-Temps';
    }
    metaDiv.appendChild(tempsTaffSpan);

    aboutDiv.appendChild(positionDiv);
    aboutDiv.appendChild(lieuDiv);
    aboutDiv.appendChild(metaDiv);

    listItem.appendChild(link); // Add the link as the first child
    listItem.appendChild(logoDiv);
    listItem.appendChild(aboutDiv);

    return listItem;
}