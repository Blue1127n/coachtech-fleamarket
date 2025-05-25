# coachtech-fleamarket(coachtechフリマ)  


## 環境構築  


**Dockerビルド**  

  1.GitHub からクローン  

    `git clone git@github.com:Blue1127n/coachtech-fleamarket.git`   

  2.プロジェクトディレクトリに移動  

    `cd coachtech-fleamarket`  

  3.権限設定  

    `chmod +x setup.sh`  
    `./setup.sh`  
    `code .`  

  4.DockerDesktopアプリを立ち上げる  

    `docker-compose up -d --build`  


**Laravel環境構築**  


  1.PHPコンテナ内にログイン  

    `docker-compose exec php bash`  

  2.インストール  

    `composer install`  

  3.「.env」ファイルを作成  

    `cp .env.example .env`  

  4..envに以下の環境変数に変更  

    ``` text  
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=laravel_user
    DB_PASSWORD=laravel_pass
    ```  

  5.アプリケーションキーの作成  

    `php artisan key:generate`  

  6.マイグレーションの実行  

    `php artisan migrate`  

  7.シーディングの実行  

    `php artisan db:seed`  


## MailHog  

- http://localhost:8025/


## Stripeについて
コンビニ支払いとカード支払いのオプションがありますが、決済画面にてコンビニ支払いを選択しますと、レシートを印刷する画面に遷移します。そのため、カード支払いを成功させた場合に意図する画面遷移が行える想定です。<br>

また、StripeのAPIキーは以下のように設定をお願いいたします。
```
STRIPE_PUBLIC_KEY="パブリックキー"
STRIPE_SECRET_KEY="シークレットキー"
```



## テーブル仕様

### usersテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  | ◯ |  |
| email | varchar(255) |  | ◯ | ◯ |  |
| password | varchar(255) |  |  | ◯ |  |
| postal code | varchar(8) |  |  | ◯ |  |
| address | varchar(255) |  |  | ◯ |  |
| building | varchar(255) |  |  | ◯ |  |
| profile_image | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### itemsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| status_id | bigint |  |  | ◯ | statuses(id) |
| condition_id | bigint |  |  |  | conditions(id) |
| name | varchar(255) |  |  | ◯ |  |
| description | text |  |  | ◯ |  |
| price | integer |  |  | ◯ |  |
| image | varchar(255) |  |  | ◯ |  |
| brand | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### categoriesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  | ◯ | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### item_categoryテーブル（中間テーブル）
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| category_id | bigint |  |  | ◯ | categories(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### transactionsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| item_id | bigint |  | ◯ | ◯ | items(id) |
| buyer_id | bigint |  |  |  | users(id) |
| status_id | bigint |  |  | ◯ | statuses(id) |
| payment_method | varchar(50) |  |  | ◯ |  |
| shipping_postal_code | varchar(8) |  |  | ◯ |  |
| shipping_address | text |  |  | ◯ |  |
| shipping_building | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### likesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  | ◯ | ◯ | users(id) |
| item_id | bigint |  | ◯ | ◯ | items(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### commentsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| user_id | bigint |  |  | ◯ | users(id) |
| item_id | bigint |  |  | ◯ | items(id) |
| content | text |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### statusesテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| name | varchar(255) |  |  | ◯ |  |
| description | text |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### conditionsテーブル
| カラム名 | 型 | primary key | unique key | not null | foreign key |
| --- | --- | --- | --- | --- | --- |
| id | bigint | ◯ |  | ◯ |  |
| condition | varchar(255) |  |  | ◯ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |


## 使用技術(実行環境)  

- PHP8.3.11  
- Laravel8.83.8  
- MySQL8.0.26  


## ER図  

![ER図](public/images/ER図.svg)  


## テストアカウント  

name: テストユーザー  
email: test@example.com  
password: password  
-------------------------
name: テストユーザー２  
email: test2@example.com  
password: password  
-------------------------


## PHPUnitを利用したテストの環境構築と確認

以下のコマンド:  
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p

//パスワードはrootと入力
CREATE DATABASE demo_test;
SHOW DATABASES;

//テストの実行
./vendor/bin/phpunit
```
※.env.testingにもStripeのAPIキーを設定してください。


## URL  

- 開発環境：http://localhost/  
- phpMyAdmin:：http://localhost:8080/  
