document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productName = this.elements.product.value;
    const resultsDiv = document.getElementById('results');
    
    // Временное отображение результатов (mock data)
    resultsDiv.innerHTML = `
        <h3>Результаты поиска для "${productName}"</h3>
        <p>Идет разработка функционала поиска...</p>
    `;
}); 