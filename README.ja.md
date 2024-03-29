# LoginAttempts plugin for CakePHP

<p align="center">
    <a href="LICENSE.txt" target="_blank">
        <img alt="Software License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square">
    </a>
    <a href="https://github.com/nojimage/cakephp-login-attempts/actions" target="_blank">
        <img alt="Build Status" src="https://img.shields.io/github/actions/workflow/status/nojimage/cakephp-login-attempts/ci.yml?style=flat-square">
    </a>
    <a href="https://codecov.io/gh/nojimage/cakephp-login-attempts" target="_blank">
        <img alt="Codecov" src="https://img.shields.io/codecov/c/github/nojimage/cakephp-login-attempts.svg?style=flat-square">
    </a>
    <a href="https://packagist.org/packages/nojimage/cakephp-login-attempts" target="_blank">
        <img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/nojimage/cakephp-login-attempts.svg?style=flat-square">
    </a>
</p>

このプラグインは、フォームログインの失敗を検知して一定期間内に同一IPから複数回のログイン失敗が発生した場合、以降のログイン処理をブロックします。

## 必要要件

- CakePHP >= 5.0
- [CakePHP Authentication plugin](https://github.com/cakephp/authentication)

## インストール

[composer](http://getcomposer.org) を使用してインストールできます。

以下のようにして、Composer経由でプラグインをCakePHPアプリケーションへ追加します:

```
composer require nojimage/cakephp-login-attempts:^3.0
```

### プラグインのロード

アプリケーションの `src/Application.php` に、次の行を追加してプラグインをロードします:

```
$this->addPlugin('LoginAttempts');
```

### テーブルの作成

マイグレーションを実行し、データベースへ必要なテーブルを作成します:

```
bin/cake migrations migrate -p LoginAttempts
```

### 使用方法

`Form` 認証機能の代わりに `LoginAttempts.Form` を使用してください。

Authentication プラグインを使う場合:

```
        $service->loadAuthenticator('LoginAttempts.Form', [
            'fields' => ['username' => 'email'],
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
        ]);
```

### LoginAttempts.Form のオプション

#### `attemptLimit`

`attemptDuration` の間にログイン失敗回数が `attemptLimit` を超えた場合、ログインを許可しません。

default: `5`

#### `attemptDuration`

`attemptDuration` の間にログイン失敗回数が `attemptLimit` を超えた場合、ログインを許可しません。

default: `'+5 minutes'`

#### `attemptAction`

試行回数はこのキー別に保存されます。ログインフォームが複数あり、それぞれ別個に制限を設けたい場合に使用します。

default: `'login'`

#### `attemptsStorageModel`

ログインの失敗回数を記録およびチェックするためのモデルを指定します。

default: `'LoginAttempts.Attempts'`
