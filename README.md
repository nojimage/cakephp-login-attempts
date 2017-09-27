# LoginAttempts plugin for CakePHP

<p align="center">
    <a href="LICENSE.txt" target="_blank">
        <img alt="Software License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square">
    </a>
    <a href="https://travis-ci.org/nojimage/cakephp-login-attempts" target="_blank">
        <img alt="Build Status" src="https://img.shields.io/travis/nojimage/cakephp-login-attempts/master.svg?style=flat-square">
    </a>
    <a href="https://codecov.io/gh/nojimage/cakephp-login-attempts" target="_blank">
        <img alt="Codecov" src="https://img.shields.io/codecov/c/github/nojimage/cakephp-login-attempts.svg?style=flat-square">
    </a>
    <a href="https://packagist.org/packages/nojimage/cakephp-login-attempts" target="_blank">
        <img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/nojimage/cakephp-login-attempts.svg?style=flat-square">
    </a>
</p>

## Requirements

- CakePHP 3.x

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require nojimage/cakephp-login-attempts
```

in `config/bootstrap.php`

```
Plugin::load('LoginAttempts');
```

And apply migration file.

```
bin/cake migrations migrate -p LoginAttempts
```

### Usage

Load `LoginAttempts.Form` authenticate instead `Form`.

```
        $this->loadComponent('Auth', [
            'authenticate' => [
                'LoginAttempts.Form' => [
                    'fields' => ['username' => 'email'],
                    'attemptLimit' => 5,
                    'attemptDuration' => '+5 minutes',
                ],
            ],
        ]);
```
