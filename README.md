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

## Requirements

- CakePHP >= 5.0
- [CakePHP Authentication plugin](https://github.com/cakephp/authentication)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require nojimage/cakephp-login-attempts"^3.0
```

### Load plugin

Load the plugin by adding the following statement in your project's `src/Application.php`:

```
$this->addPlugin('LoginAttempts');
```

### Create table

run migration command:

```
bin/cake migrations migrate -p LoginAttempts
```

### Usage

Use `LoginAttempts.Form` authenticator instead of `Form`.

If use are using Authentication plugin:

```
        $service->loadAuthenticator('LoginAttempts.Form', [
            'fields' => ['username' => 'email'],
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
        ]);
```

### LoginAttempts.Form Options

#### `attemptLimit`

If the num of login failures of the user exceeds `attemptLimit` during `attemptDuration`,
the user's login is not permitted.

default: `5`

#### `attemptDuration`

If the num of login failures of the user exceeds `attemptLimit` during `attemptDuration`,
the user's login is not permitted.

default: `'+5 minutes'`

#### `attemptAction`

The number of attempts is stored separately for this key.
Use when there are multiple login forms and you want to set restrictions separately.

default: `'login'`

#### `attemptsStorageModel`

A model used for logging a failure login and check count of failure login.

default: `'LoginAttempts.Attempts'`
