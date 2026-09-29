<?php

namespace Tests;

use CountryValidations\CountryValidator;
use CountryValidations\Brazil\Helpers;
use PHPUnit\Framework\TestCase;

class RegressionTest extends TestCase
{
    /** @dataProvider invalidInputProvider */
    public function testInvalidInputIsNotSilentlySanitized(string $country, string $domain, string $method, string $value): void
    {
        $validator = (new CountryValidator())->country($country)->$domain();
        $this->assertFalse($validator->$method($value));
    }

    public static function invalidInputProvider(): array
    {
        return [
            ['BR', 'personal', 'cpf', 'letters123.456.789-09'],
            ['BR', 'personal', 'cpf', "123.456.789-09\n"],
            ['BR', 'personal', 'cpf', '1.2345678909'],
            ['BR', 'company', 'cnpj', '12.345.678/0001-95!'],
            ['BR', 'personal', 'pisPasep', 'x639.22570.10-6'],
            ['BR', 'personal', 'tituloEleitor', 'x132509520302'],
            ['BR', 'personal', 'cnh', 'x02650306461'],
            ['BR', 'personal', 'passport', 'AB!123456'],
            ['BR', 'bank', 'bankCode', 'bank001'],
            ['BR', 'bank', 'branch', 'branch1234'],
            ['BR', 'bank', 'accountNumber', 'x123456-7'],
            ['BR', 'bank', 'checkCompensationCode', 'x12345678'],
            ['BR', 'bank', 'ispb', 'x00000000'],
            ['BR', 'bank', 'bin', 'x123456'],
            ['BR', 'vehicle', 'plate', 'ABC!1234'],
            ['BR', 'vehicle', 'renavam', 'x94473163410'],
            ['CA', 'personal', 'sin', 'abc046454286'],
            ['CA', 'personal', 'sin', '046-454 286'],
            ['CA', 'personal', 'phone', 'call4165551234'],
            ['CA', 'personal', 'passport', 'AB!123456'],
            ['CA', 'personal', 'birthDate', 'born1990-01-01'],
            ['US', 'personal', 'ssn', 'abc123456789'],
            ['US', 'personal', 'ssn', '12-345-6789'],
            ['US', 'personal', 'birthDate', 'born01/01/1990'],
            ['US', 'personal', 'passport', 'A!12345678'],
        ];
    }

    /** @dataProvider sharedCountryProvider */
    public function testSharedChecks(string $country, string $dateFormat): void
    {
        $validator = (new CountryValidator())->country($country)->personal();
        foreach (['José da Silva', "Anne-Marie O'Neill", 'Jean D’Angelo', "Jose\u{0301} Silva"] as $name) {
            $this->assertTrue($validator->fullName($name), $name);
        }
        foreach (["John\nDoe", "' -", '--- ---', 'John 123', 'John Doe!', "John Doe\0", 'John'] as $name) {
            $this->assertFalse($validator->fullName($name), $name);
        }
        $this->assertTrue($validator->birthDate((new \DateTimeImmutable('today'))->format($dateFormat)));
        $this->assertFalse($validator->birthDate((new \DateTimeImmutable('tomorrow'))->format($dateFormat)));
        foreach (['2023-02-29', '2024-02-30', '0000-01-01'] as $date) {
            if ($dateFormat === 'm/d/Y') {
                $date = substr($date, 5, 2) . '/' . substr($date, 8, 2) . '/' . substr($date, 0, 4);
            }
            $this->assertFalse($validator->birthDate($date));
        }
        $valid = $dateFormat === 'm/d/Y' ? '02/29/2024' : '2024-02-29';
        $this->assertTrue($validator->birthDate($valid));
        $this->assertFalse($validator->birthDate($valid . "\0"));
        $this->assertFalse($validator->birthDate($valid . "\n"));
        $this->assertTrue($validator->email('user@example.com'));
        $this->assertFalse($validator->email("user@example.com\n"));
    }

    public static function sharedCountryProvider(): array
    {
        return [['BR', 'Y-m-d'], ['CA', 'Y-m-d'], ['US', 'm/d/Y']];
    }

    public function testBrazilianPhonesAndPlates(): void
    {
        $br = (new CountryValidator())->brazil();
        foreach ([$br->personal(), $br->company()] as $validator) {
            $this->assertTrue($validator->phone('+55 (11) 98765-4321'));
            $this->assertTrue($validator->phone('+5511987654321'));
            foreach (['(11 98765-4321', '11) 98765-4321', "(11) 98765-4321\n", '+1 11987654321'] as $value) {
                $this->assertFalse($validator->phone($value));
            }
            $this->assertFalse($validator->phoneWithoutDDD("98765-4321\n"));
        }
        $this->assertTrue($br->vehicle()->plate('abc-1234'));
        $this->assertTrue($br->vehicle()->plate('abc1d23'));
        $this->assertFalse($br->vehicle()->plate('ABC-1D23'));
        $this->assertTrue($br->vehicle()->vin('9BWZZZ377VT004251'));
        $this->assertFalse($br->vehicle()->vin('9BWZZZ377VT004251', true));
        $this->assertTrue($br->vehicle()->vin('1HGCM82633A004352', true));
        $this->assertFalse($br->vehicle()->vin('00000000000000000'));
    }

    public function testNanpAndPassports(): void
    {
        $all = new CountryValidator();
        foreach ([$all->usa()->personal(), $all->canada()->personal()] as $p) {
            $this->assertTrue($p->phone('+1 (416) 555-1234'));
            $this->assertTrue($p->phone('14165551234'));
            foreach (['(416 555-1234', '416)5551234', '2115551234', '4169111234', "+1 4165551234\n"] as $phone) {
                $this->assertFalse($p->phone($phone));
            }
            $this->assertFalse($p->passport("AB123456\n"));
        }
        $this->assertTrue($all->canada()->personal()->passport('A123456BC'));
        $this->assertTrue($all->usa()->personal()->passport('123456789'));
        $this->assertFalse($all->usa()->personal()->passport('ABC123456'));
        $this->assertFalse($all->usa()->personal()->driversLicense("A1234567\n", 'CA'));
        $this->assertFalse($all->canada()->personal()->driversLicense("1234567\n", 'AB'));
    }

    public function testCurrencyBoundaries(): void
    {
        $currency = (new CountryValidator())->brazil()->currency();
        $this->assertTrue($currency->brlFormat('1234,56'));
        $this->assertTrue($currency->brlFormat('R$ 1234,56'));
        $this->assertFalse($currency->brlFormat('R$ 12.34,56'));
        $this->assertFalse($currency->exchangeRate('0.0000'));
        $this->assertFalse($currency->exchangeRate(str_repeat('9', 400)));
        foreach ([INF, -INF, NAN] as $number) {
            $this->assertFalse($currency->positiveAmount($number));
            $this->assertFalse($currency->withinLimit($number, 100));
            $this->assertFalse($currency->withinLimit(100, $number));
            $this->assertFalse($currency->amountInRange(5, 0, $number));
            $this->assertFalse($currency->decimalPlaces($number));
            $this->assertFalse($currency->percentage($number));
        }
        $this->assertFalse($currency->amountInRange(5, 10, 0));
        $this->assertTrue($currency->decimalPlaces(100000000000000.0));
        $this->assertFalse($currency->decimalPlaces(0.001));
        $this->expectException(\InvalidArgumentException::class);
        $currency->convertToFloat('garbage');
    }

    public function testConversionOverflow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CountryValidator())->brazil()->currency()->convertToFloat(str_repeat('9', 400) . ',00');
    }

    public function testConfigurationAndCountrySelection(): void
    {
        $validator = new CountryValidator(['ispb' => ['12345678' => 'Test bank']]);
        $this->assertTrue($validator->country('br')->bank()->ispb('12345678'));
        $this->assertFalse($validator->brazil()->bank()->ispb('00000000'));
        $this->assertInstanceOf(\CountryValidations\Brazil\Validator::class, $validator->country('BRA'));
        $this->assertInstanceOf(\CountryValidations\Canada\Validator::class, $validator->country('CAN'));
        $this->assertInstanceOf(\CountryValidations\Usa\Validator::class, $validator->country('USA'));
        $this->expectException(\InvalidArgumentException::class);
        $validator->country('XX');
    }

    public function testInvalidIspbConfiguration(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CountryValidator(['ispb' => 'invalid']))->brazil()->bank()->ispb('12345678');
    }

    public function testModulo97WorksWithoutIntegerOverflow(): void
    {
        $helpers = new Helpers();
        $this->assertSame(90, $helpers->genericBcmod('9999999999999999999999999999999999999999'));
        $this->assertSame(0, $helpers->genericBcmod('0000000000000000000000000000000000000000'));
        $this->expectException(\InvalidArgumentException::class);
        $helpers->genericBcmod('123A');
    }
}
