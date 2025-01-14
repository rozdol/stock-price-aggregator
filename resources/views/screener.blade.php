<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screener</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .arrow-up {
            color: green;
        }
        .arrow-down {
            color: red;
        }
    </style>
    <!-- Add jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Stock Screener</h1>
        <div class="table-responsive">
            <table id="screener-table" class="table table-bordered table-hover text-center">
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

    <!-- Script to Refresh Only the Table -->
    <script>
        function refreshTable() {
            $.ajax({
                url: '{{ route("screener.data") }}', // Endpoint for table data
                method: 'GET',
                success: function (response) {
                    // Replace table body with the updated data
                    $('#screener-table tbody').html(response);
                },
                error: function () {
                    console.error('Failed to fetch table data.');
                }
            });
        }

        // Auto-refresh every 60 seconds
        setInterval(refreshTable, 1000); // 60000ms = 60 seconds
    </script>
</body>
</html>
