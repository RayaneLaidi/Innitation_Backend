// Configuration
const ITEMS_PER_PAGE = 20;
const API_LIMIT = 1300; // Augmenté pour couvrir tous les Pokémon existants
const SEARCH_DELAY = 300;

// État global
let allPokemons = [];
let filteredPokemons = [];
let currentPage = 1;
let searchTimeout = null;

// Éléments DOM
const pokemonContainer = document.getElementById('pokemon-container');
const searchBar = document.getElementById('search-bar');
const previousBtn = document.getElementById('previous');
const nextBtn = document.getElementById('next');

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    fetchPokemons();
    setupEventListeners();
});

// Récupère la liste complète des Pokémon
async function fetchPokemons() {
    try {
        pokemonContainer.innerHTML = '<p>Chargement en cours...</p>';
        
        // Récupération de la liste complète
        const response = await fetch(`https://pokeapi.co/api/v2/pokemon?limit=${API_LIMIT}`);
        const data = await response.json();
        
        // Chargement des détails en parallèle (optimisé)
        allPokemons = await Promise.all(
            data.results.map(async (pokemon, index) => {
                try {
                    const detailsResponse = await fetch(pokemon.url);
                    const details = await detailsResponse.json();
                    return {
                        id: index + 1,
                        name: pokemon.name,
                        image: details.sprites.other['official-artwork']?.front_default || 
                              details.sprites.front_default,
                        types: details.types.map(t => t.type.name)
                    };
                } catch (error) {
                    console.error(`Erreur sur ${pokemon.name}:`, error);
                    return null;
                }
            })
        );
        
        // Filtre les éventuels résultats null
        allPokemons = allPokemons.filter(p => p !== null);
        filteredPokemons = [...allPokemons];
        renderPokemons();
    } catch (error) {
        console.error("Erreur lors du chargement:", error);
        pokemonContainer.innerHTML = '<p class="error">Erreur de chargement des données</p>';
    }
}

// Gestion des événements
function setupEventListeners() {
    searchBar.addEventListener('input', handleSearch);
    previousBtn.addEventListener('click', goToPreviousPage);
    nextBtn.addEventListener('click', goToNextPage);
}

function handleSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchPokemons();
    }, SEARCH_DELAY);
}

function goToPreviousPage() {
    if (currentPage > 1) {
        currentPage--;
        renderPokemons();
    }
}

function goToNextPage() {
    const maxPage = Math.ceil(filteredPokemons.length / ITEMS_PER_PAGE);
    if (currentPage < maxPage) {
        currentPage++;
        renderPokemons();
    }
}

// Algorithme de recherche amélioré
function searchPokemons() {
    const searchTerm = searchBar.value.trim().toLowerCase();
    
    if (!searchTerm) {
        filteredPokemons = [...allPokemons];
    } else {
        filteredPokemons = allPokemons.filter(pokemon => {
            const name = pokemon.name.toLowerCase();
            
            // 1. Correspondance exacte
            if (name === searchTerm) return true;
            
            // 2. Commence par le terme
            if (name.startsWith(searchTerm)) return true;
            
            // 3. Terme présent dans le nom
            if (name.includes(searchTerm)) return true;
            
            // 4. Recherche par type
            if (pokemon.types.some(type => 
                type.toLowerCase().includes(searchTerm))) return true;
            
            return false;
        }).sort((a, b) => {
            // Tri par pertinence
            const aName = a.name.toLowerCase();
            const bName = b.name.toLowerCase();
            const aStarts = aName.startsWith(searchTerm);
            const bStarts = bName.startsWith(searchTerm);
            
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            
            // Si même pertinence, tri alphabétique
            return aName.localeCompare(bName);
        });
    }
    
    currentPage = 1;
    renderPokemons();
}

// Affiche les Pokémon
function renderPokemons() {
    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const end = start + ITEMS_PER_PAGE;
    const pokemonsToShow = filteredPokemons.slice(start, end);
    
    pokemonContainer.innerHTML = pokemonsToShow.length > 0 
        ? pokemonsToShow.map(createPokemonCard).join('')
        : '<p class="no-results">Aucun Pokémon correspond à votre recherche</p>';
    
    updatePaginationButtons();
}

// Crée une carte Pokémon (sans numéro)
function createPokemonCard(pokemon) {
    return `
        <div class="pokemon-card">
            <h2>${capitalize(pokemon.name)}</h2>
            <img src="${pokemon.image}" alt="${pokemon.name}" loading="lazy">
            <p>Type: ${pokemon.types.map(capitalize).join(', ')}</p>
        </div>
    `;
}

function updatePaginationButtons() {
    previousBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= Math.ceil(filteredPokemons.length / ITEMS_PER_PAGE);
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}