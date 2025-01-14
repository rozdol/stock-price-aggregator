<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Stock Prices aggregator

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

The app is getting prices for a predifined list of stocks from Alpha Vantage and delivers to clients via REST API.

## Setup
1. Clone the repository.
2. Run `composer install`.
3. Configure `.env` with database and Alpha Vantage API key.
4. Run migrations: `php artisan migrate`.
5. Run seedet to add a list of stocks `php artisan db:seed --class=StocksTableSeeder`.
6. Schedule the command: `php artisan schedule:run`.

## API Endpoints
- `GET /api/stocks`: Fetch the list of available stocks.
- `GET /api/prices/{symbol}`: Fetch the latest stock price.
- `GET /api/history/{symbol}`: Fetch the 60 minutes history the stock prices.
- `GET /api/report`: Fetch a real-time stock report.

## Testing
Run tests with:
```bash
php artisan test
```

## MAC OS scheduled task

Create a .plist file com.laravel.updateprices.plist:

```bash
touch ~/Library/LaunchAgents/com.laravel.updateprices.plist
```

Content of `com.laravel.updateprices.plist`:

```bash
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>com.laravel.updateprices</string>
    <key>ProgramArguments</key>
    <array>
        <string>cd /{path_to_repo}/stock-price-aggregator/scripts/run_price_updates.sh</string>
    </array>
    <key>StartInterval</key>
    <integer>60</integer> <!-- Run every 60 seconds -->
    <key>RunAtLoad</key>
    <true/>
</dict>
</plist>
```

Load the task into launchd:

```bash
launchctl load ~/Library/LaunchAgents/com.laravel.updateprices.plist
```

Verify the task is loaded:

```bash
launchctl list | grep com.laravel.updateprices
```

To unload a task:

```bash
launchctl unload ~/Library/LaunchAgents/com.laravel.updateprices.plist
```

### Fallback
If you can not set the running updates, run the script:

```bash
/{path_to_repo}/stock-price-aggregator/scripts/run_infinite.sh
```

## Linux scheduled task
Add a cron task `crottab -e` to run the price updated every minute
```bash
 * * * * * cd /{path_to_repo}/stock-price-aggregator && php artisan schedule:run >> /dev/null 2>&1
 ```
