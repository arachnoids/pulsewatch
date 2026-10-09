#!/bin/bash

while [ true ]; do
    php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=256
    echo "Worker exited, restarting in 5s..."
    sleep 5
done