# light-ops

リモートアクセスできない保守用端末から死活監視結果などを受け取り、リモートからのモニタリングを可能とする Web アプリケーション。

## セットアップ

1. パッケージインストール
    ```
    $ composer install
    ```
1. 設定ファイルの作成
    ```
    $ cp .env.example .env
    ```
1. アプリケーションキー生成
    ```
    $ php artisan key:generate
    ```
1. DB マイグレーション
    ```
    $ php artisan migrate
    ```

### ローカル実行

1. サーバーを起動
    ```
    $ php artisan serve
    ```
2. ブラウザで `http://127.0.0.1:8000` を開く

## 監視状態記録 API

死活監視の結果を DB に記録する。

### Web API

GET: `/api/alive-log/{site}/add/{status}`

### Site

監視対象を識別するための文字列。

### Status

監視対象の状態を表す文字列。

-   `alive`: 正常の状態
-   `ok`: 正常の状態
-   `warning`: 注意が必要な状態
-   その他: 異常な状態
