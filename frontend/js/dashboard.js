// Chart.js - Harvest Trends
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Harvest Quantity (kg)',
            data: [100, 200, 150, 300, 250, 350],
            borderColor: 'rgba(52, 152, 219, 1)',
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});

// Chart.js - Storage Utilization
const storageCtx = document.getElementById('storageChart').getContext('2d');
new Chart(storageCtx, {
    type: 'doughnut',
    data: {
        labels: ['Used', 'Available'],
        datasets: [{
            data: [80, 20],
            backgroundColor: ['rgba(231, 76, 60, 1)', 'rgba(46, 204, 113, 1)']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        }
    }
});
