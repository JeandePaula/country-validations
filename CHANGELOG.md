# Changelog

## Unreleased

### Added

- Address validators for Brazil (CEP/UF), Canada (postal code/province/territory)
  and USA (ZIP/ZIP+4/USPS region).
- Canadian banking institution, transit and routing format checks; US ABA routing
  prefix and checksum checks. Shared PAN, BIN and SWIFT checks in all countries.
- Numeric and alphanumeric CNPJ in the existing cnpj method.
- Canadian A123456BC passport format alongside legacy AA123456.
- country selection by ISO alpha-2/alpha-3 code, optional +55/+1 phone prefixes,
  8-digit BIN support and configurable ISPB map.
- VIN format-only check with optional check digit; chassis retains its checksum.
- Regression tests, Composer check scripts and multi-version GitHub Actions CI.

### Fixed

- Empty/all-zero/short PANs passing Luhn validation.
- Invalid characters being silently removed from identifiers.
- CNS suffix and weighted checksum, CNH second check digit and title-of-elector
  check digits (sequence and electoral state must be calculated separately).
- Boleto field and general check digits, including modulo 10/11 collection lines.
- Brazilian IBAN BBAN structure, check-digit range and printed representation.
- Lowercase vehicle plates, unmatched phone parentheses, trailing newline matches,
  punctuation-only names, and malformed dates.
- Ontario/Quebec licence lengths and overbroad US passport syntax.
- Zero exchange rates, non-finite monetary values and unsafe float conversion.
- Modulo 97 arithmetic on 32-bit PHP.
- Invalid positive fixtures in original CNS, electoral-title and boleto tests.

### Compatibility / migration

The fluent entry points, public method names and existing parameter names remain.
PHP 7.4 is still the minimum. Correctness fixes intentionally change results:

- Inputs containing letters/control bytes/arbitrary punctuation are no longer
  cleaned into valid identifiers. Supply compact strings or documented masks.
- Most validators do not trim outer whitespace. IBAN and boleto accept the
  documented ASCII separators.
- CNS/boleto/tituloEleitor now verify checksums; a matching length is insufficient.
- cnh uses the corrected second-digit adjustment.
- Ontario's short synthetic licence fixture is now invalid (15 characters needed).
- birthDate includes today, rejects year zero and never passes NUL to DateTime.
  Canada's existing minimum year of 1900 remains.
- brlFormat accepts ungrouped values such as 1234,56; R$ remains optional.
- convertToFloat throws InvalidArgumentException for malformed, negative,
  prefixed or overflowing values instead of silently returning a partial number/0.
- Helpers::genericBcmod throws InvalidArgumentException for empty/nondecimal input.
- country throws InvalidArgumentException for an unsupported code.
- Invalid non-array ispb configuration throws InvalidArgumentException when used.

Before publishing, select a version that reflects these observable behavior
changes, especially the new conversion exceptions. No package version/tag is
created by this refactor.
