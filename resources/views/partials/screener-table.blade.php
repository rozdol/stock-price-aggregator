@foreach ($screenerData as $data)
    <tr>
        <td><strong>{{ $data['symbol'] }}</strong></td>
        <td>
            {{ $data['price'] }}
            @if (!is_null($data['change_pct']) && is_numeric($data['change_pct']))
                @if ($data['change_pct'] > 0)
                    <span class="arrow-up">↑</span>
                @elseif ($data['change_pct'] < 0)
                    <span class="arrow-down">↓</span>
                @endif
            @endif
        </td>
        <td>
            {{ is_numeric($data['change_pct']) ? number_format($data['change_pct'], 2) : 'N/A' }}
        </td>
    </tr>
@endforeach
