sample-php7-laravel
---

### はじめに

[PHP7](https://secure.php.net/) と [Laravel 5.2](https://laravel.com/) を元にした DDD サンプル実装です。  
アーキテクチャは [sample-boot-hibernate](https://github.com/jkazama/sample-boot-hibernate) を参考にしています。  
フレームワークではないので、 Laravel を利用するプロジェクトを立ち上げる際に元テンプレートとして利用して下さい。

UI 側の実装サンプルについては [sample-ui-vue](https://github.com/jkazama/sample-ui-vue) / [sample-ui-react](https://github.com/jkazama/sample-ui-react) を参照してください。

*※ 参考実装レベルです。製品水準のコードが含まれているわけではありませんし、まだ運用を意識した十分な精査ができていません。*

#### レイヤリングの考え方

オーソドックスな三層モデルですが、横断的な解釈としてインフラ層を考えています。

| レイヤ          | 特徴                                                        |
| -------------- | ----------------------------------------------------------- |
| UI             | ユースケース処理を公開 ( 必要に応じてリモーティングや外部サイトを連携 ) |
| アプリケーション | ユースケース処理を集約 ( 外部リソースアクセスも含む )                 |
| ドメイン        | 純粋なドメイン処理 ( 外部リソースに依存しない )                      |
| インフラ        | DI コンテナや ORM 、各種ライブラリ、メッセージリソースの提供          |

UI 層の公開処理は通常テンプレートを用いて行いますが、本サンプルでは異なる種類のクライアント利用を想定して RESTfulAPI での API 提供のみをおこないます。 ( 利用クライアントは別途用意する必要があります )

#### Laravel の利用方針

Laravel は様々な利用方法が可能ですが、本サンプルでは以下のポリシーで利用します。

- API サーバでの運用を前提。
- ライブラリ化しないのでモジュールを vendor 等に切り出さない。
- 例外処理は終端 ( app/Exceptions/Handler ) で定義。
- DI は多用せずにインフラ層のコンポーネント束ねたヘルパークラスを利用。
- ORM 実装として Eloquent を利用。
- 認証方式はベーシック認証でなく、昔からよくある HttpSession で。
- パッケージ管理は [Composer](https://getcomposer.org/) で行う。
- 既にあるものはなるべく作らない。

#### PHP コーディング方針

PHP7 を前提としています。

- PSR 系の規約はなるべく守る。
- 型はなるべく明示する
- インターフェースの濫用をしない。
- ドメインの一部となる DTO などはクラスにせず連想配列で。

#### パッケージ構成

パッケージ/リソース構成については以下を参照してください。

```
[root]
  app                … psr-4 の自動ローディングルート
    Console          … コンソール関連
    Context          … インフラ層 [ Original ]
    Events           … イベント管理
    Exceptions       … 例外管理
    Http
      Controllers    … UI層
      Middleware     … ミドルウェアプラグイン
      Requests 
      - Kernel.php
      - routes.php     … URL ルーティング
    Jobs             … ジョブ関連
    Listeners
    Models           … ドメイン層 [ Original ]
    Policies
    Providers
      - AppServiceProvider.php … DI 定義
    Tests            … テスト関連 [ Original ]
    Usecases         … アプリケーション層 [ Original ]
    Utils            … ユーティリティ [ Original ]
    User.php
  bootstrap
  config             … 設定ファイル
  database           … マイグレーション定義 / 初期データ管理
  public             … HTTP 公開定義
  resources          … 国際化対応
  storage
  tests              … PHPUnit 関連リソース
  vendor             … Composer 経由で取得する依存ライブラリ
  - artisan          … コマンドラインインターフェース
  - composer.json    … プロジェクト構成定義
  - phpunit.xml      … 単体テスト定義
```

### サンプルユースケース

サンプルユースケースとしては以下のようなシンプルな流れを想定します。

- **口座残高 100 万円を持つ顧客**が出金依頼 ( 発生 T, 受渡 T + 3 ) をする。
- **システム**が営業日を進める。
- **システム**が出金依頼を確定する。(確定させるまでは依頼取消行為を許容)
- **システム**が受渡日を迎えた入出金キャッシュフローを口座残高へ反映する。

### 動作確認

本サンプルは PHP7 や Composer に依存しているため、事前に双方のインストールが必要となります。  
インストールの手順については公式のドキュメントを参照してください。

■ PHP

http://php.net/manual/ja/install.php

■ Composer

https://getcomposer.org/download/

#### ライブラリダウンロード

本プロジェクトには依存ライブラリが含まれていないため、以下のコマンドを実行してライブラリをダウンロードしてください。

```
cd [project_root]
composer update
```

> composer の呼び出し方は環境毎に異なるので必要に応じて読み変えてください。

*※ Composer は大量のライブラリを自動でダウンロードするため、容量制限の縛りが緩いネットワーク回線を利用して実行するようにしてください。*

#### DB準備

本サンプルでは DB に SQLite を用いています。  
以下の手順で DB ファイルを作成してください。

```
cd [project_root]
php artisan migrate:refresh --seed
```

> PHPUnit 実行時にスキーマが初期化されてしまうため、サーバ起動前は忘れずに実行してください

#### サーバ起動 （ Artisan ）

組み込みサーバで本サンプルを起動するにはコンソールから次のコマンドを実行してください。

```
cd [project_root]
php artisan serve --port=8080
```

> 実際の本番環境では Nginx + PHP-FPM などの組み合わせが良く利用されています。


#### クライアント検証

[sample-ui-vue](https://github.com/jkazama/sample-ui-vue) / [sample-ui-react](https://github.com/jkazama/sample-ui-react) どちらかのサンプルを起動して、ブラウザから「http://localhost:3000/」を呼び出してください。
※バックグラウンドで本サンプルの API を実行します。

> 認証を有効状態にしているため 「 sample / sample 」 でログインしてください。

---

ユニットテストの実施は以下のコマンドで

```
cd [project_root]
./vendor/bin/phpunit
```

個別に実行したい時は以下のような感じで

```
./vendor/bin/phpunit tests/Models/Asset/CashInOutTest.php
```

> 現状 DB 設定を流用しているのでテストを流した後に動作確認する際はマイグレーション処理を忘れずに

### 補足解説（インフラ層）

インフラ層の簡単な解説です。

*※プロジェクト構成の詳細な定義は `composer.json` を参照してください*

#### DB / トランザクション

Laravel の永続化機構 ( [Eloquent ORM](https://laravel.com/docs/5.2/eloquent) ) をそのまま利用しています。  
トランザクションを常にかけることはせず、必要な箇所に限定して明示的な指定を行っています。

#### 認証/認可

認証のみを実装しています。認証モデルは Laravel 標準の `User` を利用していますが、ドメインモデル上は `Login` 概念で置き換えています。  
`App\Http\Controllers\Auth\LoginApiController` は API モデルという事もあり、シンプルに実装を拡張しています。

#### 利用者監査

未実装

#### 例外

汎用概念としてフィールド単位にスタックした例外を持つ `ValidationException` を提供します。  
例外は末端の UI 層でまとめて処理します。具体的にはアプリケーション層、ドメイン層では用途別の実行時例外をそのまま上位に投げるだけとし、例外捕捉は `App\Exceptions\Handler` で行っています。

#### 日付/日時

`App\Context\Timestamper` を経由して取得します。休日等を考慮した営業日算出はドメイン概念が含まれるので `BusinessDayHandler` で考慮します。

#### キャッシング

未実装

#### テスト

PHPUnit を用いてモデルを中心としてテストを行っています。  
テストは以下のコマンドで実行してください。

```
cd [project_root]
vendor/bin/phpunit
```

### License

本サンプルのライセンスはコード含めて全て *MIT License* です。  
Laravel を用いたプロジェクト立ち上げ時のベース実装サンプルとして気軽にご利用ください。

