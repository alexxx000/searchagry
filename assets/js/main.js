document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productName = this.elements.product.value;
    const resultsDiv = document.getElementById('results');
    
    if (productName.length < 3) {
        resultsDiv.innerHTML = '<p class="error">Минимальная длина запроса - 3 символа</p>';
        return;
    }
    
    // Показываем индикатор загрузки
    resultsDiv.innerHTML = '<div class="loading">Поиск товаров...</div>';
    
    // Отправляем запрос на поиск
    fetch(`api/search.php?q=${encodeURIComponent(productName)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Сначала получаем текст ответа
        })
        .then(text => {
            console.log('Raw response:', text); // Отладочная информация
            try {
                return JSON.parse(text); // Пытаемся распарсить JSON
            } catch (e) {
                throw new Error('Ошибка парсинга JSON: ' + e.message);
            }
        })
        .then(data => {
            if (data.error) {
                resultsDiv.innerHTML = `<p class="error">${data.error}</p>`;
                return;
            }
            
            if (!data.length) {
                resultsDiv.innerHTML = '<p>Товары не найдены</p>';
                return;
            }
            
            // Группируем результаты по товарам
            const groupedResults = data.reduce((acc, item) => {
                if (!acc[item.name]) {
                    acc[item.name] = [];
                }
                if (item.marketplace) {
                    acc[item.name].push({
                        marketplace: item.marketplace,
                        price: item.price,
                        url: item.url
                    });
                }
                return acc;
            }, {});
            
            // Формируем HTML для отображения результатов
            let html = '<div class="search-results">';
            
            for (const [productName, prices] of Object.entries(groupedResults)) {
                html += `
                    <div class="product-card">
                        <h3>${productName}</h3>
                        <div class="prices-list">
                            ${prices.map(price => `
                                <div class="price-item">
                                    <span class="marketplace">${price.marketplace}</span>
                                    <span class="price">${price.price} ₽</span>
                                    ${price.url ? `<a href="${price.url}" target="_blank">Перейти</a>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            html += '</div>';
            resultsDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Ошибка:', error);
            resultsDiv.innerHTML = `<p class="error">Произошла ошибка при поиске: ${error.message}</p>`;
        });
}); 