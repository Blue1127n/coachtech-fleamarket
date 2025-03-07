echo "設定開始..."

mkdir -p storage/framework/{sessions,views,cache,data}
mkdir -p bootstrap/cache

sudo chmod -R 777 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

echo "設定完了！"
