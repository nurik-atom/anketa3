@echo off
echo Starting Laravel Queue Worker...
echo Press Ctrl+C to stop
php artisan queue:work --timeout=300 --memory=512 