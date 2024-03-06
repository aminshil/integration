$(document).ready(function () {
    // ...

    // Ajouter un événement de clic au bouton de recherche
    $('#searchButton').on('click', function () {
        performSearch();
    });
});

function performSearch() {
    var query = $('#searchInput').val();

    // Effectuer une requête AJAX
    $.ajax({
        type: 'POST',
        url: '{{ path('app_search') }}',
        data: { query: query },
        success: function (data) {
            // Mettez à jour la zone des résultats avec les données reçues
            updateSearchResults(data);
        },
        error: function (error) {
            console.error('Erreur AJAX:', error);
        }
    });
}