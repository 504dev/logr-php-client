# logr-php-client

[Logr] client library for PHP.

[Logr]: https://github.com/504dev/logr

### Available `logger` methods

* `logger->emerg`
* `logger->alert`
* `logger->crit`
* `logger->error`
* `logger->warn`
* `logger->notice`
* `logger->info`
* `logger->debug`

### Example

```php
$logr = new Logr(
    'localhost:7776',
    'MCAwDQYJKoZIhvcNAQEBBQADDwAwDAIFAMg7IrMCAwEAAQ==',
    'MC0CAQACBQDIOyKzAgMBAAECBQCHaZwRAgMA0nkCAwDziwIDAL+xAgJMKwICGq0='
);
$logger = $logr->getLogger('hello.log');
$logger->debug('Hello!');
```