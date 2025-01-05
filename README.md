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
composer require jeandepaula/country-validations
```

### Requirements
- PHP 7.4 or higher
- Composer

---

## Integration with Laravel

### Service Provider Registration (Optional)
If needed, you can create a service provider to encapsulate the library.

1. **Create a Service Provider**
   ```php
   php artisan make:provider CountryValidationsServiceProvider
   ```

2. **Register the Library in the Provider**
   In the generated `CountryValidationsServiceProvider`:
   ```php
   <?php

   namespace App\Providers;

   use Illuminate\Support\ServiceProvider;
   use CountryValidations\CountryValidator;

   class CountryValidationsServiceProvider extends ServiceProvider
   {
       public function register()
       {
           $this->app->singleton('country-validator', function () {
               return new CountryValidator();
           });
       }

       public function boot()
       {
           //
       }
   }
   ```

3. **Add the Provider in `config/app.php`**
   ```php
   'providers' => [
       // Other Service Providers...
       App\Providers\CountryValidationsServiceProvider::class,
   ],
   ```

4. **Using the Library in Controllers**
   ```php
   use Illuminate\Support\Facades\App;

   $validator = App::make('country-validator');

   // Validate CPF
   $isValidCpf = $validator->brazil()->personal()->cpf('123.456.789-09');
   ```

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

## Testing

Run the tests using PHPUnit:

```bash
php vendor/bin/phpunit
```

---

## License
This library is open-sourced software licensed under the [MIT license](LICENSE).
