// public/js/dashboard.js

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    var mostSoldBookData = google.visualization.arrayToDataTable([
        ['Livre', 'Ventes'],
        // Ajoutez ici les données du livre le plus vendu
    ]);

    var mostSoldBookOptions = {
        title: 'Livre le Plus Vendu',
        hAxis: {title: 'Livres'},
        vAxis: {title: 'Ventes'}
    };

    var mostSoldBookChart = new google.visualization.ColumnChart(document.getElementById('most-sold-book-chart'));
    mostSoldBookChart.draw(mostSoldBookData, mostSoldBookOptions);

    var ordersCountData = google.visualization.arrayToDataTable([
        ['Indicateur', 'Valeur'],
        ['Nombre Total de Commandes', totalOrders]  // Remplacez totalOrders par la variable appropriée
    ]);

    var ordersCountOptions = {
        title: 'Nombre Total de Commandes',
        hAxis: {title: 'Indicateur'},
        vAxis: {title: 'Valeur'}
    };

    var ordersCountChart = new google.visualization.ColumnChart(document.getElementById('orders-count-chart'));
    ordersCountChart.draw(ordersCountData, ordersCountOptions);
}
