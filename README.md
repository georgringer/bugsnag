# Bugsnag Exception Handlers for TYPO3 CMS

**Requires TYPO3 13 or 14.**

Integrates [Bugsnag](https://www.bugsnag.com/) error monitoring into TYPO3 CMS by routing exceptions to the Bugsnag platform.

Originally created and maintained by [Michiel Roos](https://github.com/Tuurlijk) — thank you for the great work!

## Configuration

### General exceptions

Configure the [Bugsnag API key](https://app.bugsnag.com/) in the TYPO3 extension configuration screen, or provide it via environment variable:

```
BUGSNAG_API_KEY=your-api-key
```

Set the exception handlers either via the Install Tool or in `AdditionalConfiguration.php`:

```php
<?php
# AdditionalConfiguration.php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['debugExceptionHandler'] = \GeorgRinger\Bugsnag\Core\Error\DebugExceptionHandler::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['productionExceptionHandler'] = \GeorgRinger\Bugsnag\Core\Error\ProductionExceptionHandler::class;
```

### Exceptions thrown by content elements

The [content exception handler](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/Setup/Config/Index.html#contentobjectexceptionhandler) can be specified in TypoScript. Exceptions that occur during rendering of content objects (typically plugins) are caught by default in production context and a configurable error message is shown in place of the failing element — keeping the rest of the page intact.

```
# Use 1 for the default exception handler (enabled by default in production context)
config.contentObjectExceptionHandler = 1

# Use a class name for a custom exception handler
config.contentObjectExceptionHandler = GeorgRinger\Bugsnag\ContentObject\Exception\ProductionExceptionHandler
```

### Bugsnag Performance (Site Set)

This extension ships a Site Set (`GeorgRinger/bugsnag`) that enables [Bugsnag Performance Monitoring](https://docs.bugsnag.com/performance/integration-guides/js/) via a browser module script.

Add the set as a dependency in your site's Set configuration and provide the following settings — either directly or via environment variable:

| Setting | Description | Default |
|---|---|---|
| `bugsnag.performanceApiKey` | Bugsnag Performance API key | _(empty)_ |
| `bugsnag.path` | URL to the Bugsnag Performance JS bundle | `//d2wy8f7a9ursnm.cloudfront.net/v1/bugsnag-performance.min.js` |

In `config/sites/{site}/settings.yaml`:

```yaml
bugsnag:
    performanceApiKey: '%env(BUGSNAG_PERFORMANCE_API_KEY)%'
```

The script is only rendered when `bugsnag.performanceApiKey` is set.

## Issues

Please [report issues you find](https://github.com/georgringer/bugsnag/issues).
