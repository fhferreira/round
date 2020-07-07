# Fhferreira/Round

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Round numbers with normatives like ABNT NBR 5891.

## Structure

```
src/
tests/
```


## Install

Via Composer

``` bash
$ composer require fhferreira/round
```

## Usage

``` php
$rounder = new Fhferreira\Round();
echo $rounder->ABNT_NBR_5891(12,744623);
// should echo 12,74
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email flaviometalvale@gmail.com instead of using the issue tracker.

## Credits

- [Flávio H. Ferreira][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/fhferreira/round.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/fhferreira/round/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/fhferreira/round.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/fhferreira/round.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/fhferreira/round.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/fhferreira/round
[link-travis]: https://travis-ci.org/fhferreira/round
[link-scrutinizer]: https://scrutinizer-ci.com/g/fhferreira/round/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/fhferreira/round
[link-downloads]: https://packagist.org/packages/fhferreira/round
[link-author]: https://github.com/fhferreira
[link-contributors]: ../../contributors
