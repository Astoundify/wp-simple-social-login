# Astoundify WC Simple Social Login

### Install

```
$ git clone --recursive git@github.com:Astoundify/wc-simple-social-login.git
```

### Setup

```
$ npm run setup
```

### Develop

#### Javascript

Validate your Javascript:

```
$ gulp js:hint
```

#### PHP

Setup [WordPress Coding
Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)
so you can run `phpcs`.

```
$ composer create-project wp-coding-standards/wpcs --no-dev
```

Add /path/to/wpcs/vendor/bin to `$PATH` so you can run `phpcs -i`

```
$ phpcs --config-set installed_paths /path/to/wpcs
$ phpcs --config-set default_standard WordPress
```

Validate your PHP:

```
$ gulp php
```

### Test

Install
[PHPUnit](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)

```
$ phpunit
```

### Build

Bump version in `package.json` and `astoundify-wc-simple-social-login.php`. 

```
$ npm run dist
```
