# Bolt

<p>
  <a href="https://packagist.org/packages/monooso/craft-bolt"><img src="https://poser.pugx.org/monooso/craft-bolt/v/stable.svg" alt="Latest Stable Version"/></a>
  <a href="https://packagist.org/packages/monooso/craft-bolt"><img src="https://poser.pugx.org/monooso/craft-bolt/license.svg" alt="License"/></a>
</p>

## About Bolt
Bolt speeds up your Craft plugin tests. It's opinionated, and a work-in-progress. You probably shouldn't use it.

Bolt consists of two parts:

1. A Codeception module, which is a thin wrapper around the official `craft\test\Craft` module.
2. A Codeception extension, which loads a SQL dump of a vanilla Craft install.

The extension relies on the module. The module is pointless unless you're using the extension.

## Requirements and installation
@todo: note regarding Codeception 4.

Bolt has been tested with with Craft 3.5, and PHP 7.4. Install Bolt using [Composer](https://getcomposer.org/):

```bash
composer require monooso/craft-bolt
```

## Usage
The basic steps are as follows:

1. Replace `\craft\test\Craft` in your `codeception.yml` file with `\Monooso\Bolt\BoltModule`.
2. Tell Craft not to set up the database.
3. Configure the extension.

### Configure the extension
The extension requires a single configuration setting, specifying the path to the SQL dump to load.

```yaml
extensions:
    enabled:
        - BoltExtension
    config:
        BoltExtension:
            dump: "tests/_data/dump.sql"
```
