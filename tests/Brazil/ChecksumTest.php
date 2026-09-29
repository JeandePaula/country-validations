<?php

namespace Tests\Brazil;

use CountryValidations\CountryValidator;
use PHPUnit\Framework\TestCase;

class ChecksumTest extends TestCase
{
    /** @dataProvider documents */
    public function testCheckDigits(string $domain, string $method, string $number): void
    {
        $validator = (new CountryValidator())->brazil()->$domain();
        $this->assertTrue($validator->$method($number), $number);
        for ($digit = 0; $digit <= 9; $digit++) {
            if ((string) $digit !== substr($number, -1)) {
                $this->assertFalse($validator->$method(substr($number, 0, -1) . $digit));
            }
        }
        $this->assertFalse($validator->$method($number . 'x'));
        $this->assertFalse($validator->$method($number . "\n"));
        $this->assertFalse($validator->$method($number . "\0"));
    }

    public static function documents(): array
    {
        return [
            ['company', 'cnpj', '12.ABC.345/01DE-35'], // Receita Federal manual
            ['company', 'cnpj', '12ABC34501DE35'],
            ['company', 'cnpj', '12345678000195'],
            ['personal', 'cpf', '12345678909'],
            ['personal', 'pisPasep', '63922570106'],
            ['personal', 'cns', '123456789010000'],
            ['personal', 'cns', '223456789010007'],
            ['personal', 'cns', '100000000060018'], // definitive, exceptional 0018 suffix
            ['personal', 'cns', '700123456789010'],
            ['personal', 'cns', '800123456789017'],
            ['personal', 'cns', '900123456789013'],
            ['personal', 'cnh', '02650306461'], // published validation example
            ['personal', 'cnh', '10000000108'], // both remainders 10: second digit becomes 8
            ['personal', 'cnh', '98765432109'], // adjusted remainder -2 wraps to 9
            ['personal', 'cnh', '10000003600'], // first remainder 10, second adjusted by 2
            ['personal', 'cnh', '00000000660'], // second remainder 10
            ['personal', 'tituloEleitor', '132509520302'], // published worked example
            ['personal', 'tituloEleitor', '558055510663'],
            ['personal', 'tituloEleitor', '280567082011'],
            ['vehicle', 'renavam', '94473163410'],
        ];
    }

    public function testAlphanumericCnpjRules(): void
    {
        $v = (new CountryValidator())->brazil()->company();
        $this->assertTrue($v->cnpj('12.abc.345/01de-35'));
        foreach (['12ABC34501DE3A', '12ABC34501DE3', '12ABC34501DE350', '12.ABC345/01DE-35', '00000000000000', '11111111111111'] as $number) {
            $this->assertFalse($v->cnpj($number));
        }
    }

    public function testCnsSuffixAndElectoralState(): void
    {
        $v = (new CountryValidator())->brazil()->personal();
        // Both checksum and the definitive suffix structure matter.
        $this->assertFalse($v->cns('123456789010019'));
        $this->assertFalse($v->cns('323456789010000'));
        $this->assertFalse($v->tituloEleitor('132509520002'));
        $this->assertFalse($v->tituloEleitor('132509522902'));
        $this->assertFalse($v->tituloEleitor('000000000000'));
        $this->assertTrue($v->tituloEleitor('1325 0952 0302'));
        $this->assertFalse($v->rg('12345678', 'ZZ'));
        $this->assertFalse($v->rg('12345X78', 'SP'));
        $this->assertFalse($v->cnh('02650306462'));
    }

    /** @dataProvider boletos */
    public function testBoletoChecksAllFields(string $line): void
    {
        $bank = (new CountryValidator())->brazil()->bank();
        $this->assertTrue($bank->boleto($line), $line);
        $positions = strlen($line) === 47 ? [9, 20, 31, 32] : [11, 23, 35, 47];
        foreach ($positions as $position) {
            $mutated = $line;
            $mutated[$position] = (string) (((int) $line[$position] + 1) % 10);
            $this->assertFalse($bank->boleto($mutated));
        }
        $this->assertFalse($bank->boleto($line . 'x'));
        $this->assertFalse($bank->boleto($line . "\n"));
    }

    public static function boletos(): array
    {
        // Collection fixtures were calculated independently from the FEBRABAN layout,
        // with references 6/7 (mod 10) and 8/9 (mod 11); they are synthetic, not payable.
        return [
            ['00190500954014481606906809350314337370000000100'],
            ['816700000028150482009746123220154090829010860593'],
            ['817500000028150482009746123220154090829010860593'],
            ['818100000022150482009740123220154096829010860591'],
            ['819000000029150482009740123220154096829010860591'],
        ];
    }

    public function testBoletoPrintedFormatAndOldFalsePositives(): void
    {
        $bank = (new CountryValidator())->brazil()->bank();
        $this->assertTrue($bank->boleto('00190.50095 40144.816069 06809.350314 3 37370000000100'));
        $this->assertFalse($bank->boleto(str_repeat('0', 47)));
        $this->assertFalse($bank->boleto('12345678901234567890123456789012345678901234567'));
        $this->assertFalse($bank->boleto('123456789012345678901234567890123456789012345678'));
        // Correct block digits must not hide an incorrect general check digit.
        $this->assertFalse($bank->boleto('816800000027150482009746123220154090829010860593'));
        $this->assertFalse($bank->boleto(''));
    }
}
