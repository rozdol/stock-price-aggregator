<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Price Chart - {{ $symbol }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Price Chart for {{ $symbol }}</h1>
    <canvas id="priceChart" width="800" height="400"></canvas>

    <script>
        const labels = {!! json_encode($prices->pluck('retrieved_at')) !!};
        const data = {!! json_encode($prices->pluck('price')) !!};

        const ctx = document.getElementById('priceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Dates for the x-axis
                datasets: [{
                    label: 'Price',
                    data: data, // Prices for the y-axis
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time',
                        },
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Price (USD)',
                        },
                        beginAtZero: false,
                    }
                }
            }
        });
    </script>
</body>
</html>
