# Country Validations Library

The **Country Validations Library** is a powerful PHP package designed to validate various data formats for Brazil, Canada, and the USA. The library provides easy-to-use methods for personal, corporate, banking, and vehicle data validation.

---

## Features

### Supported Countries
- **Brazil**: Validates CPF, CNPJ, phone numbers, bank account details, vehicle plates, and more.
- **Canada**: Validates SIN, phone numbers, and driver's licenses.
- **USA**: Validates SSN, phone numbers, passports, and driver's licenses.

### Key Functionalities
- Personal data validation (e.g., CPF, SIN, SSN, email, name).
- Banking data validation (e.g., IBAN, SWIFT, bank codes).
- Vehicle data validation (e.g., plates, VIN, RENAVAM).
- Corporate data validation (e.g., CNPJ, state registration, NIRE).
- Easy configuration and modular structure.

---

## Installation

### Using Composer

```bash
composer require country-validations/country-validations
```

### Requirements
- PHP 7.4 or higher
- Composer

---

## Usage

### General Setup

```php
use CountryValidations\CountryValidator;

$validator = new CountryValidator();
```

### Examples

#### Brazil

```php
$brazilValidator = $validator->brazil();

// Validate CPF
var_dump($brazilValidator->personal()->cpf('123.456.789-09'));

// Validate CNPJ
var_dump($brazilValidator->company()->cnpj('12.345.678/0001-95'));

// Validate Brazilian phone number
var_dump($brazilValidator->personal()->phone('(11) 98765-4321'));

// Validate vehicle plate
var_dump($brazilValidator->vehicle()->plate('ABC1234'));
```

#### Canada

```php
$canadaValidator = $validator->canada();

// Validate SIN
var_dump($canadaValidator->personal()->sin('123-456-789'));

// Validate phone number
var_dump($canadaValidator->personal()->phone('(416) 555-2671'));
```

#### USA

```php
$usaValidator = $validator->usa();

// Validate SSN
var_dump($usaValidator->personal()->ssn('123-45-6789'));

// Validate driver's license
var_dump($usaValidator->personal()->driversLicense('A1234567', 'CA'));
```

---

## Validations Available

### Brazil
- **Personal**: CPF, RG, CNS, email, phone, birth date, full name, PIS/PASEP, CNH, voter registration.
- **Company**: CNPJ, corporate name, corporate phone, state registration, NIRE.
- **Bank**: Bank code, branch code, account number, boleto line, SWIFT, IBAN, BIN.
- **Vehicle**: Plate (standard and Mercosul), RENAVAM, VIN (chassis), vehicle category.
- **Currency**: Brazilian Real format, exchange rates, positive amounts, limits, numeric format, percentages.

### Canada
- **Personal**: SIN, email, phone, birth date, full name, passport, driver's license (by province).

### USA
- **Personal**: SSN, email, phone, birth date, full name, passport, driver's license (by state).

---

## Contributing

Feel free to fork this repository and submit pull requests for new features, bug fixes, or documentation improvements.

### Running Tests

```bash
php vendor/bin/phpunit
```

---

## License
This library is open-sourced software licensed under the [MIT license](LICENSE).

---

## Contact
For any questions or support, please reach out to [Your Name](mailto:your-email@example.com).

