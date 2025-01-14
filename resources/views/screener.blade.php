<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screener</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        .arrow-up {
            color: green;
        }
        .arrow-down {
            color: red;
        }
    </style>
</head>
    
    <body>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Stock Screener</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Symbol</th>
                            <th>Price</th>
                            <th>Change (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($screenerData as $data)
                            <tr>
                                <td><strong>{{ $data['symbol'] }}</strong></td>
                                <td>
                                    {{ $data['price'] ?? 'N/A' }}
                                    @if (!is_null($data['change_pct']))
                                        @if ($data['change_pct'] > 0)
                                            <span class="arrow-up">↑</span>
                                        @elseif ($data['change_pct'] < 0)
                                            <span class="arrow-down">↓</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $data['change_pct'] !== null ? number_format($data['change_pct'], 2) : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Bootstrap JS (Optional for interactive components) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Auto-Refresh Script -->
        <script>
            // Auto-refresh every 60 seconds
            setInterval(() => {
                location.reload();
            }, 1000); // 60000ms = 60 seconds
        </script>
    </body>
    
</html>
