# Bolt

<p>
  <a href="https://packagist.org/packages/monooso/craft-bolt"><img src="https://poser.pugx.org/monooso/craft-bolt/v/stable.svg" alt="Latest Stable Version"/></a>
  <a href="https://packagist.org/packages/monooso/craft-bolt"><img src="https://poser.pugx.org/monooso/craft-bolt/license.svg" alt="License"/></a>
</p>

## About Bolt
Bolt speeds up your Craft plugin tests. It's opinionated, and a work-in-progress. You probably shouldn't use it.

Bolt consists of two parts:

1. A Codeception module, which is a thin wrapper around the official `craft\test\Craft` module.
2. A Codeception extension, which loads a SQL dump of your choosing.

The extension relies on the module. You can use the module without the extension, but there's no reason to do so.

## Requirements
Bolt assumes that you're using Codeception 4. It has been tested with Craft 3.5 and PHP 7.4.

## Installation
Install Bolt using [Composer](https://getcomposer.org/), as a development dependency:

```bash
composer require --dev monooso/craft-bolt
```

## Configuration

### 1. Replace the Craft Codeception module 
The Bolt Codeception module is a drop-in replacement for the Craft Codeception module. To use it, simply replace any references to `\craft\test\Craft` in your Codeception configuration files with `\Monooso\Bolt\BoltModule`.

For example:

```yaml
modules:
    config:
        \Monooso\Bolt\BoltModule:
            configFile: "tests/_craft/config/test.php"
            # Other config...
```

Don't forget to update your `*.suite.yml` files as well.

### Step 2: Disable the database setup
The Bolt extension is responsible for loading a SQL dump, which means we don't need the Craft module to set up the database. Set the configuration options accordingly:

```yaml
modules:
    config:
        \Monooso\Bolt\BoltModule:
            cleanup: true
            transaction: true
            dbSetup: { clean: false, setupCraft: false }
            fullMock: false
            # Other config...
```

### Step 3: Enable and configure the extension
The extension requires a single configuration setting, specifying the SQL dump(s) to load.

For the sake of convenience, Bolt includes a SQL dump of a vanilla Craft 3.5 site. Use it as follows:

```yaml
extensions:
    enabled:
        - \Monooso\Bolt\BoltExtension
    config:
        \Monooso\Bolt\BoltExtension:
            dump: "bolt:3.5"
```

You can also choose to load your own SQL dump, by specifying a path relative to the project root:

```yaml
extensions:
    enabled:
        - \Monooso\Bolt\BoltExtension
    config:
        \Monooso\Bolt\BoltExtension:
            dump: "tests/_data/dump.sql"
```

Finally, you can specify multiple dump files, which will be loaded in the order specified. For example:

```yaml
extensions:
    enabled:
        - \Monooso\Bolt\BoltExtension
    config:
        \Monooso\Bolt\BoltExtension:
            dump:
                - "bolt:3.5"
                - "tests/_data/extras.sql"
```
