# Country Validations

PHP validators for Brazilian, Canadian and US identifiers, contact details, addresses,
banking data and vehicles. No runtime package dependencies or network calls.

## Installation

```bash
composer require jeandepaula/country-validations
```

Requires PHP 7.4+ and the standard ctype and filter extensions. Development checks
also require PHPUnit's extensions (including DOM, XML/XMLWriter and mbstring).
PHP 7.4 compatibility is retained for existing consumers; use a supported PHP
release for new deployments.

## Quick start

```php
require 'vendor/autoload.php';

use CountryValidations\CountryValidator;

$validator = new CountryValidator();

$validator->brazil()->personal()->cpf('123.456.789-09');       // true
$validator->brazil()->company()->cnpj('12.ABC.345/01DE-35');   // true
$validator->brazil()->address()->cep('01310-100');             // true
$validator->brazil()->address()->state('SP');                 // true
$validator->brazil()->personal()->phone('+55 (11) 98765-4321');// true

$validator->canada()->personal()->sin('046-454-286');         // true
$validator->canada()->address()->postalCode('K1A 0B1');        // true
$validator->canada()->address()->province('ON');              // true
$validator->canada()->bank()->routingNumber('000412345');     // true

$validator->usa()->personal()->ssn('123-45-6789');             // true
$validator->usa()->address()->zipCode('00501');               // true
$validator->usa()->bank()->routingNumber('021000021');        // true
$validator->country('US')->bank()->cardNumber('4111111111111111'); // true

```

Examples and test fixtures demonstrate formats/checksums, not real identities or
payable instructions. Pass identifiers as **strings** to preserve leading zeros.
Validation methods return booleans. PHP type errors still apply to incompatible
argument types.

`country()` accepts BR/BRA, CA/CAN and US/USA, case-insensitively.
Unknown country codes throw `InvalidArgumentException`.

## What validation means

A successful result checks the documented format, checksum or local policy.
It does **not** confirm issuance, identity, account ownership, available funds,
an active phone number, a registered boleto or address deliverability.

Input normalization is deliberately limited. CPF/CNPJ/PIS/SSN/SIN accept the
documented masks or their compact representation. Unexpected letters, control
characters and arbitrary punctuation are rejected rather than silently deleted.
Letter-based identifiers accept lowercase, except where a legacy method says
otherwise. Most methods do not trim outer whitespace. IBAN accepts ASCII spaces;
boleto accepts ASCII spaces, dots and hyphens, including surrounding spaces.

## Available validators

### Brazil

| Domain | Methods | Checks |
| --- | --- | --- |
| Personal | `cpf`, `cin` | 11 digits or CPF mask; both check digits; repeated digits rejected |
| Personal | `pisPasep` | 11 digits or XXX.XXXXX.XX-X; modulo 11 |
| Personal | `cns` | 15 digits; prefixes 1/2 with definitive suffix, or 7/8/9 with weighted checksum |
| Personal | `cnh` | 11 digits and both check digits, including remainder adjustment |
| Personal | `tituloEleitor` | 12 digits or XXXX XXXX XXXX; electoral UF 01–28 and two check digits |
| Personal | `rg($number, $state = 'SP')` | Legacy state-dependent length heuristic, **not** state-specific RG check digits |
| Personal | `passport` | Two letters and six digits |
| Personal | `phone`, `phoneWithoutDDD` | National masked/compact syntax; phone includes a known DDD and optional +55 |
| Personal | `birthDate` | Exact YYYY-MM-DD, valid calendar date, year >= 1, not after today |
| Personal | `email`, `fullName` | Shared checks described below |
| Company | `cnpj` | Numeric or alphanumeric CNPJ, compact or XX.XXX.XXX/XXXX-XX; both check digits |
| Company | `corporateName`, `email`, `phone`, `phoneWithoutDDD` | Name policy, email and national phone syntax |
| Company | `stateRegistration`, `nire` | Legacy length checks only (9–14 / 11 digits); **not** IE/NIRE check digits |
| Address | `postalCode`, `cep`, `state` | XXXXX-XXX or 8 digits (not all zero); 27 UF codes |
| Bank | `bankCode`, `branch`, `accountNumber`, `checkCompensationCode` | Legacy formats: 3 digits, 4 digits, 5–12 digits + hyphen + digit, 8 digits |
| Bank | `boleto` | 47-digit BRL bank line or 48-digit collection line; field and general check digits |
| Bank | `iban` | Brazilian 29-character IBAN: BBAN structure, check-digit range and modulo 97 |
| Bank | `ispb` | 8 digits and membership in a bundled historical or caller-supplied map |
| Bank | `cardNumber`, `bin`, `swift` | Shared banking checks below |
| Vehicle | `plate` | ABC1234 / ABC-1234 or Mercosul ABC1D23, case-insensitive |
| Vehicle | `renavam` | 11 digits and modulo 11 |
| Vehicle | `chassis`, `vin` | See VIN distinction below |
| Vehicle | `vehicleCategory` | Single A–E driving category; does not enumerate every licence combination |

RG and IE differ by issuing state and date. The legacy heuristic methods are not
exhaustive validators for all issued documents. Likewise, bank branch and account
formats differ by institution; these legacy methods represent only their stated
formats.

`phone` retains the legacy 8-digit subscriber range 2–8 and 9-digit mobile
prefix 9. It is not an exhaustive service-number validator (0800, short codes,
extensions and carrier-selection dialing are outside this API).

`chassis()` retains its checksum requirement. The ninth-character check digit
is not universal across all vehicle markets. Use `vin($number)` to check only
17 allowed characters (excluding I/O/Q and repeated placeholders), or
`vin($number, true)` to also require the North American check digit. These
methods do not check manufacturer allocation, model year or registration.

### Canada

| Domain | Methods | Checks |
| --- | --- | --- |
| Personal | `sin` | 9 digits, XXX-XXX-XXX or XXX XXX XXX; Luhn; repeated digits rejected |
| Personal | `passport` | Legacy AA123456 or current A123456BC shape |
| Personal | `phone` | NANP syntax, optionally +1/1 |
| Personal | `birthDate` | Exact YYYY-MM-DD, year >= 1900, valid date not after today |
| Personal | `email`, `fullName` | Shared checks |
| Personal | `driversLicense($number, $province)` | Legacy provincial format heuristics; Ontario corrected to letter + 14 digits and Quebec to letter + 12 digits |
| Address | `postalCode`, `province` | A1A 1A1 / A1A1A1 with Canada Post's permitted letters; all 13 provinces/territories |
| Bank | `institutionNumber`, `transitNumber` | 3 digits (institution not 000), 5 digits |
| Bank | `routingNumber` | Electronic 0IIITTTTT or printed TTTTT-III, format only |
| Bank | `cardNumber`, `bin`, `swift` | Shared banking checks |

### USA

| Domain | Methods | Checks |
| --- | --- | --- |
| Personal | `ssn` | XXX-XX-XXXX / 9 digits; rejects area 000/666/9xx, group 00 and serial 0000 |
| Personal | `passport` | 9 digits or one letter and eight digits |
| Personal | `phone` | NANP syntax, optionally +1/1 |
| Personal | `birthDate` | Exact MM/DD/YYYY, year >= 1, valid date not after today |
| Personal | `email`, `fullName` | Shared checks |
| Personal | `driversLicense($number, $state)` | Legacy state format heuristics |
| Address | `postalCode`, `zipCode`, `state` | 5-digit ZIP / ZIP+4; rejects all-zero ZIP; USPS region codes, including possessions and military codes |
| Bank | `routingNumber` | ABA 9-digit format, routing prefix ranges and 3/7/1 checksum |
| Bank | `cardNumber`, `bin`, `swift` | Shared banking checks |

The Canadian and US `driversLicense` tables are retained legacy heuristics,
not a complete, freshly verified registry of all current and historical licence
formats. Unknown regions return false. Canadian NT/NU licence formats are not
implemented, although their address province codes are supported. Use the issuing
authority for definitive licence verification.

NANP is shared by several countries: `phone()` does not distinguish country
allocation. Area and exchange must start with 2–9 and cannot be N11.

### Shared personal and bank checks

- `email()`: PHP's FILTER_VALIDATE_EMAIL, without a DNS/mailbox lookup.
- `fullName()`: application policy requiring at least two letter-based words;
  Unicode accents, combining marks, internal apostrophes and hyphens are allowed.
  This is not a universal definition of a person's legal name.
- `cardNumber()`: 12–19 digit PAN and Luhn, allowing spaces/hyphens; rejects
  repeated placeholders. Does not enforce issuer-specific lengths/BIN allocation.
- `bin()`: exactly 6 or 8 digits, without issuer lookup.
- `swift()`: SwiftNet FIN shape with 4 bank letters, 2 country letters, 2 location characters and an
  optional 3-character branch; does not query assigned BIC or country codes.

### Currency

`brazil()->currency()` provides:

- `brlFormat()`: optional R$ prefix, optional thousands grouping, exactly two
  comma-separated decimals, e.g. R$ 1.234,56 or 1234,56.
- `brazilianNumericFormat()`: the same numeric syntax without R$.
- `exchangeRate()`: finite positive decimal using a dot, up to 4 decimal places.
- `positiveAmount()`, `withinLimit()`, `percentage()`, `decimalPlaces()`, `amountInRange()`:
  finite numeric checks. Limits/ranges are inclusive; percentage is 0–100;
  decimalPlaces allows nonnegative values with at most two decimal places.
  withinLimit tests only the upper bound, so a negative amount can pass it.
- `convertToFloat()`: converts a validated, unsigned Brazilian decimal string;
  malformed input or overflow throws `InvalidArgumentException`.

Floats are approximate. Use integer minor units or a decimal arithmetic package
for exact financial calculations.

## ISPB data

The default list is a historical snapshot inherited from the original package,
not a current official directory. Applications can replace it without network
calls inside a validator:

```php
$validator = new CountryValidator([
    'ispb' => [
        '00000000' => 'Banco do Brasil',
        '00360305' => 'Caixa Economica Federal',
    ],
]);
$validator->brazil()->bank()->ispb('00360305'); // true

```

The map replaces the bundled list. An empty map rejects all ISPBs.
Other configuration keys remain reserved and have no effect.

## Laravel

No service provider is required. Constructor injection can resolve
`CountryValidator` directly, or register a singleton in your application's
service provider when supplying configuration:

```php
$this->app->singleton(\CountryValidations\CountryValidator::class, function () {
    return new \CountryValidations\CountryValidator();
});

```

## Development

```bash
composer install
composer check                  # PHP syntax checks and PHPUnit
composer validate --strict
composer check-platform-reqs
composer audit
```

The GitHub Actions workflow covers PHP 7.4 and 8.0–8.5 on Linux, plus PHP 8.3 on
Windows. The Composer platform setting resolves development dependencies against
PHP 7.4.33 so the lowest supported runtime is not accidentally excluded.
A library lock file and vendor directory are intentionally not committed.

Tests include published examples, independently calculated synthetic fixtures,
malformed inputs, check-digit mutations and boundary cases. See
[validation references](docs/VALIDATION-SOURCES.md) for sources and limitations,
and [CHANGELOG](CHANGELOG.md) for migration notes.

## License

[MIT](LICENSE).
