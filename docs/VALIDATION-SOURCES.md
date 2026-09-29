# Validation references and scope

Reviewed 2026-09-28. The library is offline. Technical format/checksum verification
is distinct from registry confirmation.

| Area | Reference | Used for |
| --- | --- | --- |
| CNPJ | [Receita Federal / SERPRO DV manual](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/documentos-tecnicos/cnpj/manual-dv-cnpj.pdf) | ASCII minus 48 mapping, weights and published 12.ABC.345/01DE-35 vector |
| CNS | [ANS application algorithms](https://www.gov.br/ans/pt-br/centrais-de-conteudo/manuais-do-portal-operadoras/sib-manual-de-instalacao-historico-de-versao-e-outros-arquivos/manual/algoritmos-do-aplicativo-de-carga) | Definitive suffix and provisional weighted checksum |
| Electoral title | [TSE registration structure](https://www.tse.jus.br/legislacao/compilada/res/2003/resolucao-no-21-538-de-14-de-outubro-de-2003) | Historical document structure, separate sequence/UF check digits and UF 01–28 |
| Electoral title arithmetic | [validation-br implementation](https://github.com/klawdyo/validation-br/blob/master/src/tituloEleitor.ts) | Cross-check of the 2–9 / 7–9 weighted modulo 11 method; historical document-structure reference, not current enrollment rules |
| CNH | [Respect Validation reference](https://github.com/Respect/Validation/blob/2.3/library/Rules/Cnh.php) | Independent algorithm cross-check, including subtract-2 and negative-remainder handling |
| Collection boleto | [FEBRABAN layout v8](https://cmsarquivos.febraban.org.br/Arquivos/documentos/PDF/Layout%20-%20C%C3%B3digo%20de%20Barras%20-%20Vers%C3%A3o%208%20-%2011_05_2026.pdf) | References 6/7 use modulo 10; 8/9 use modulo 11; block and general digits |
| Bank boleto | [Banrisul FEBRABAN layout](https://banrisul.com.br/BOB/data/LeiauteBanrisulFebraban_pdr240_v103_23062023.pdf) | 47-digit line and barcode reordering; BRL currency 9 only |
| SWIFT | [BIC policy](https://www2.swift.com/knowledgecentre/rest/v1/publications/bic_policy/_latest/bic_policy.pdf) | SwiftNet FIN's four-letter prefix; broader ISO BIC alphanumeric prefixes are outside swift() |
| IBAN | [SWIFT IBAN registry](https://www.swift.com/standards/data-standards/iban-international-bank-account-number) | Brazil 29-character structure and BR1800360305000010009795493C1 vector |
| ABA routing | [American Bankers Association policy](https://www.aba.com/news-research/analysis-guides/routing-number-policy-procedures) | Prefix ranges and routing structure; no live institution lookup |
| Canadian routing | [Payments Canada FIF](https://www.payments.ca/systems-services/payment-services/financial-institutions-file) | Distinguishes format checks from a directory of actual branches |
| Canadian postal codes | [Canada Post format](https://www.canadapost-postescanada.ca/cpc/en/support/articles/addressing-guidelines/postal-codes.page), [permitted letters](https://www.canadapost-postescanada.ca/cpc/assets/cpc/uploads/files/marketing/2017-postal-code-conversion-file-reference-guide-en.pdf) | A1A 1A1 structure and letter restrictions |
| USPS regions | [USPS Publication 28, Appendix B](https://pe.usps.com/text/pub28/28apb.htm) | Postal region abbreviations, including military and possessions |
| Canadian passports | [IRCC document numbers](https://www.ircc.canada.ca/english/helpcentre/answer.asp?qnum=1446&top=14%23citizenship) | Legacy and current number shapes |
| Ontario licence | [MTO glossary](https://www.cdr.mto.gov.on.ca/assets/files/glossary/Business%20Glossary.pdf) | 15-character identifier |
| Quebec licence | [SAAQ validity checks](https://saaq.gouv.qc.ca/en/drivers-licences/checking-validity-licence), [licence specimens](https://saaq.gouv.qc.ca/blob/saaq/documents/publications/presentation-quebec-driver-licences.pdf) | 13-character identifier |

## Retained limitations

- Regional driver's-licence tables remain legacy heuristics except the specifically
  corrected Ontario/Quebec lengths. They have not all been verified against every
  current/historical issuing format.
- RG, IE, NIRE, branch/account numbers and compensation codes are legacy format
  policies, not full jurisdiction/institution-specific validation.
- Electoral titles use the documented 12-digit calculation; historical exceptional
  numbering variants are not exhaustively covered.
- The ISPB map is inherited historical data; an application can supply a fresh map.
- PAN length/Luhn and SWIFT shape do not establish issuer/BIC assignment.
- The VIN format method checks length and allowed characters, not manufacturer
  assignment; chassis requires the North American ninth-character checksum.
- Name and currency format checks are application policies, not registry rules.
- No personal data, live registries or paid APIs are needed to run the test suite.

## Fixture discipline

Published CNPJ/IBAN examples are cited above. CNS and collection-boleto fixtures
are synthetic constants calculated independently from the published arithmetic.
The tests never ask the implementation under test to generate its own expected
check digit. Negative cases include the original invalid positive fixtures and
mutated check digits. Passing the suite supports the tested behavior; it does not
certify every possible issued document.
