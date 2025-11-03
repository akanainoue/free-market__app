# free-market__app

## 環境構築
### Dockerビルド
1. git clone git@github.com:akanainoue/free-market__app.git
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=laravel_user
    DB_PASSWORD=laravel_pass
5. アプリケーションキーの作成
    php artisan key:generate
6. マイグレーションの実行
    php artisan migrate
7. シーディングの実行
    php artisan db:seed
8. シンボリックリンク作成
    php artisan storage:link

Stripe を使って決済処理を実装。
.env 追加設定
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret


Laravel のメール通知（ユーザー認証・通知）に MailHog を使用。
.env 追加設定
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="${APP_NAME}"

    
### 新機能とセットアップ
 チャット機能（リアルタイム対応）
## 使用技術
+ Laravel Echo
+ Pusher（BROADCAST_DRIVER）
+ JavaScript + WebSocket

 .env に以下を追加：
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1

docker-compose exec php bash
composer install
php artisan migrate --seed
php artisan storage:link
php artisan key:generate
npm install && npm run dev
laravel-echo-server start


<!-- Laravel Echo Server の起動
npm install -g laravel-echo-server
laravel-echo-server init
laravel-echo-server start -->



### 使用技術
+ PHP8.3.0
+ Laravel8.83.27
+ MySQL8.0.26

## ER図
![alt text](image-2.png)

## URL
+ 開発環境：http://localhost/
+ phpMyAdmin:：http://localhost:8080/
+ Mailhog: http://localhost:8025



