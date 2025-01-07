<?php

namespace CountryValidations\Brazil;

class Helpers
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
    
    /**
     * Validates a Brazilian phone number.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is valid, false otherwise.
     */
    public function phone(string $phone): bool
    {
        // Validate mask format before cleaning
        if (!preg_match('/^\(?\d{2}\)? ?\d{4,5}-\d{4}$/', $phone) && !preg_match('/^\d{10,11}$/', $phone)) {
            return false;
        }
    
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
    
        // Check if the length is valid (10 or 11 digits)
        $length = strlen($phone);
        if ($length !== 10 && $length !== 11) {
            return false;
        }
    
        // Extract the DDD (first two digits)
        $ddd = substr($phone, 0, 2);
    
        // List of valid DDDs
        $validDDD = [
            '11', '12', '13', '14', '15', '16', '17', '18', '19',
            '21', '22', '24', '27', '28',
            '31', '32', '33', '34', '35', '37', '38',
            '41', '42', '43', '44', '45', '46',
            '47', '48', '49',
            '51', '53', '54', '55',
            '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '71', '73', '74', '75', '77', '79',
            '81', '82', '83', '84', '85', '86', '87', '88', '89',
            '91', '92', '93', '94', '95', '96', '97', '98', '99'
        ];
    
        // Check if the DDD is valid
        if (!in_array($ddd, $validDDD)) {
            return false;
        }
    
        // Validate the local number format after the DDD
        $localNumber = substr($phone, 2); // Part after the DDD
    
        if ($length === 10) {
            // Landline: 4 digits + 4 digits, starting with 2-8
            if (!preg_match('/^[2-8]\d{3}\d{4}$/', $localNumber)) {
                return false;
            }
        } elseif ($length === 11) {
            // Mobile: 5 digits + 4 digits, starting with 9
            if (!preg_match('/^9\d{4}\d{4}$/', $localNumber)) {
                return false;
            }
        }
    
        return true;
    }
    
    /**
     * Validates if a Brazilian phone number, without the DDD (area code), has a valid length and format.
     *
     * Input format: XXXXX-XXXX, XXXXXXXX, or XXXXXXXXX (digits only).
     * Non-numeric characters will be removed before validation.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is valid without DDD, false otherwise.
     */
    public function phoneWithoutDDD(string $phone): bool
    {
        // Validate the mask before cleaning
        if (!preg_match('/^\d{4,5}-\d{4}$/', $phone) && !preg_match('/^\d{8,9}$/', $phone)) {
            return false;
        }

        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check for valid length: 8 (landline) or 9 (mobile) digits without DDD
        if (!preg_match('/^\d{8,9}$/', $phone)) {
            return false;
        }

        // Validate the format: landline (8 digits, starting with 2-8) or mobile (9 digits, starting with 9)
        if (strlen($phone) === 8) {
            // Landline: 4 digits + 4 digits, starting with 2-8
            return preg_match('/^[2-8]\d{7}$/', $phone) === 1;
        } elseif (strlen($phone) === 9) {
            // Mobile: 5 digits + 4 digits, starting with 9
            return preg_match('/^9\d{8}$/', $phone) === 1;
        }

        return false;
    }

    /**
     * Calculates the remainder of a number when divided by a modulus.
     *
     * This function takes a number as a string and a modulus as a string,
     * converts the modulus to an integer, and then calculates the remainder
     * of the number when divided by the modulus using a manual division algorithm.
     *
     * @param string $number The number to be divided, represented as a string.
     * @param string $modulus The modulus to divide by, represented as a string.
     * @return int The remainder of the division.
     */
    public function genericBcmod(string $numericString): int
    {
        $remainder = 0;
        foreach (str_split($numericString, 9) as $chunk) {
            $remainder = (int)(($remainder . $chunk) % 97);
        }
        return $remainder;
    }

    /**
     * Retrieves the name of a financial institution based on the provided ISPB (Identificador de Sistema de Pagamentos Brasileiro).
     *
     * @return array An associative array where the key is the ISPB code and the value is the name of the financial institution.
     */
    public function getIspbList(): array
    {
        return [
            '00000000' => 'BCO DO BRASIL S.A.',
            '00000208' => 'BRB - BCO DE BRASILIA S.A.',
            '00122327' => 'SANTINVEST S.A. - CFI',
            '00204963' => 'CCR SEARA',
            '00250699' => 'AGK CC S.A.',
            '00315557' => 'UNICRED DO BRASIL',
            '00360305' => 'CAIXA ECONOMICA FEDERAL',
            '00416968' => 'BANCO INTER',
            '00460065' => 'COLUNA S.A. DTVM',
            '00517645' => 'BCO RIBEIRAO PRETO S.A.',
            '00556603' => 'BANCO BARI S.A.',
            '00714671' => 'EWALLY IP S.A.',
            '00795423' => 'BANCO SEMEAR',
            '00806535' => 'PLANNER CV S.A.',
            '00954288' => 'FDO GARANTIDOR CRÉDITOS',
            '00997185' => 'BCO B3 S.A.',
            '01023570' => 'BCO RABOBANK INTL BRASIL S.A.',
            '01027058' => 'CIELO IP S.A.',
            '01073966' => 'CCR DE ABELARDO LUZ',
            '01181521' => 'BCO COOPERATIVO SICREDI S.A.',
            '01235921' => 'SICRES',
            '01522368' => 'BCO BNP PARIBAS BRASIL S A',
            '01658426' => 'CECM COOPERFORTE',
            '01701201' => 'Kirton Bank',
            '01852137' => 'BCO BRASILEIRO DE CRÉDITO S.A.',
            '01858774' => 'BCO BV S.A.',
            '02038232' => 'BANCO SICOOB S.A.',
            '02276653' => 'TRINUS CAPITAL DTVM',
            '02318507' => 'BCO KEB HANA DO BRASIL S.A.',
            '02332886' => 'XP INVESTIMENTOS CCTVM S/A',
            '02398976' => 'SISPRIME DO BRASIL - COOP',
            '02671743' => 'BANVOX DTVM',
            '02682287' => 'PAN CFI',
            '02801938' => 'BCO MORGAN STANLEY S.A.',
            '02819125' => 'UBS BB CCTVM S.A.',
            '02992317' => 'TREVISO CC S.A.',
            '03012230' => 'HIPERCARD BM S.A.',
            '03017677' => 'BCO. J.SAFRA S.A.',
            '03046391' => 'UNIPRIME COOPCENTRAL LTDA.',
            '03215790' => 'BCO TOYOTA DO BRASIL S.A.',
            '03311443' => 'PARATI - CFI S.A.',
            '03323840' => 'BCO ALFA S.A.',
            '03532415' => 'BANCO ABN AMRO CLEARING S.A.',
            '03609817' => 'BCO CARGILL S.A.',
            '03751794' => 'TERRA INVESTIMENTOS DTVM',
            '03881423' => 'SOCINAL S.A. CFI',
            '03973814' => 'SERVICOOP',
            '04062902' => 'OZ CORRETORA DE CÂMBIO S.A.',
            '04184779' => 'BANCO BRADESCARD',
            '04257795' => 'NOVA FUTURA CTVM LTDA.',
            '04307598' => 'FIDUCIA SCMEPP LTDA',
            '04715685' => 'CCM DESP TRÂNS SC E RS',
            '04814563' => 'BCO AFINZ S.A. - BM',
            '04831810' => 'CECM SERV PUBL PINHÃO',
            '04849745' => 'HBI SCD',
            '04866275' => 'BANCO INBURSA',
            '04902979' => 'BCO DA AMAZONIA S.A.',
            '04913129' => 'CONFIDENCE CC S.A.',
            '04913711' => 'BCO DO EST. DO PA S.A.',
            '05351887' => 'ZEMA CFI S/A',
            '05442029' => 'CASA CREDITO S.A. SCM',
            '05463212' => 'COOPCENTRAL AILOS',
            '05491616' => 'COOP CREDITAG',
            '05676026' => 'CREDIARE CFI S.A.',
            '06249129' => 'RPW S.A. SCFI',
            '06271464' => 'BCO BBI S.A.',
            '07138049' => 'PICPAY INVEST',
            '07207996' => 'BCO BRADESCO FINANC. S.A.',
            '07237373' => 'BCO DO NORDESTE DO BRASIL S.A.',
            '07253654' => 'HEDGE INVESTMENTS DTVM LTDA.',
            '07450604' => 'BOC BRASIL',
            '07512441' => 'HS FINANCEIRA',
            '07652226' => 'LECCA CFI S.A.',
            '07656500' => 'BCO KDB BRASIL S.A.',
            '07679404' => 'BANCO TOPÁZIO S.A.',
            '07693858' => 'HSCM SCMEPP LTDA.',
            '07799277' => 'VALOR S/A SCFI',
            '07945233' => 'POLOCRED SCMEPP LTDA.',
            '08240446' => 'CCR DE IBIAM',
            '08253539' => 'COOP SULCREDI AMPLEA',
            '08357240' => 'BCO CSF S.A.',
            '08561701' => 'PAGSEGURO INTERNET IP S.A.',
            '08609934' => 'MONEYCORP BCO DE CÂMBIO S.A.',
            '08673569' => 'F D GOLD DTVM LTDA',
            '09089356' => 'EFÍ S.A. - IP',
            '09105360' => 'ICAP DO BRASIL CTVM LTDA.',
            '09210106' => 'SOCRED SA - SCMEPP',
            '09313766' => 'CARUANA SCFI',
            '09464032' => 'MIDWAY S.A. - SCFI',
            '09512542' => 'CODEPE CVC S.A.',
            '09526594' => 'MASTER BI S.A.',
            '09554480' => 'SUPERDIGITAL I.P. S.A.',
            '10264663' => 'BANCOSEGURO S.A.',
            '10371492' => 'BCO YAMAHA MOTOR S.A.',
            '10398952' => 'CRESOL CONFEDERAÇÃO',
            '10573521' => 'MERCADO PAGO IP LTDA.',
            '10663610' => 'AF DESENVOLVE SP S.A.',
            '10664513' => 'BCO AGIBANK S.A.',
            '10690848' => 'BCO DA CHINA BRASIL S.A.',
            '10853017' => 'GET MONEY CC LTDA',
            '10866788' => 'BCO BANDEPE S.A.',
            '11165756' => 'GLOBAL SCM LTDA',
            '11285104' => 'NEON FINANCEIRA - CFI S.A.',
            '11351086' => 'MERCADO BITCOIN IP LTDA',
            '11414839' => 'EAGLE IP LTDA.',
            '11476673' => 'BANCO RANDON S.A.',
            '11495073' => 'OM DTVM LTDA',
            '11581339' => 'BMP SCMEPP LTDA',
            '11703662' => 'BANCO TRAVELEX S.A.',
            '11758741' => 'BANCO FINAXIS',
            '11760553' => 'GAZINCRED S.A. SCFI',
            '11970623' => 'BCO SENFF S.A.',
            '12473687' => 'CONTA PRONTA IP',
            '13009717' => 'BCO DO EST. DE SE S.A.',
            '13059145' => 'EBURY BCO DE CÂMBIO S.A.',
            '13203354' => 'FITBANK IP',
            '13220493' => 'BR PARTNERS BI',
            '13293225' => 'ÓRAMA DTVM S.A.',
            '13370835' => 'DOCK IP S.A.',
            '13486793' => 'BRL TRUST DTVM SA',
            '13673855' => 'OSLO CAPITAL DTVM SA',
            '13720915' => 'BCO WESTERN UNION',
            '13884775' => 'HUB IP S.A.',
            '13935893' => 'CELCOIN IP S.A.',
            '14388334' => 'PARANA BCO S.A.',
            '14511781' => 'BARI CIA HIPOTECÁRIA',
            '15111975' => 'IUGU IP S.A.',
            '15114366' => 'BCO BOCOM BBM S.A.',
            '15124464' => 'BANCO BESA S.A.',
            '15173776' => 'SOCIAL BANK S/A',
            '15357060' => 'BCO WOORI BANK DO BRASIL S.A.',
            '15489568' => 'INTRA DTVM',
            '15581638' => 'FACTA S.A. CFI',
            '16501555' => 'STONE IP S.A.',
            '16695922' => 'ID CTVM',
            '16944141' => 'BROKER BRASIL CC LTDA.',
            '17079937' => 'PINBANK IP',
            '17157777' => 'BCO NACIONAL',
            '17184037' => 'BCO MERCANTIL DO BRASIL S.A.',
            '17351180' => 'BCO TRIANGULO S.A.',
            '17352220' => 'SENSO CCVM S.A.',
            '17453575' => 'ICBC DO BRASIL BM S.A.',
            '17772370' => 'VIPS CC LTDA.',
            '17826860' => 'BMS SCD S.A.',
            '18188384' => 'CREFAZ SCMEPP SA',
            '18189547' => 'CLOUDWALK IP LTDA',
            '18236120' => 'NU PAGAMENTOS - IP',
            '18394228' => 'CDC SCD S.A.',
            '18520834' => 'UBS BB BI S.A.',
            '18684408' => 'AZIMUT BRASIL DTVM LTDA',
            '19307785' => 'BRAZA BANK S.A. BCO DE CÂMBIO',
            '19324634' => 'LAMARA SCD S.A.',
            '19468242' => 'ZOOP MEIOS DE PAGAMENTO',
            '19540550' => 'ASAAS IP S.A.',
            '20018183' => 'STARK BANK S.A. - IP',
            '20155248' => 'UNIDA DTVM LTDA',
            '20251847' => 'SUDACRED SCD S.A.',
            '20757199' => 'PAY4FUN IP S.A.',
            '20855875' => 'NEON PAGAMENTOS S.A. IP',
            '21018182' => 'EBANX IP LTDA.',
            '21332862' => 'CARTOS SCD S.A.',
            '21995256' => 'MAG IP LTDA.',
            '22575466' => 'SRM BANK',
            '22610500' => 'VORTX DTVM LTDA.',
            '22896431' => 'PICPAY',
            '23114447' => 'FLAGSHIP IP LTDA',
            '23862762' => 'WILL FINANCEIRA S.A.CFI',
            '24074692' => 'GUITTA CC LTDA',
            '24537861' => 'FFA SCMEPP LTDA.',
            '25104230' => 'PAGARE IP S.A.',
            '26264220' => 'ZERO',
            '27084098' => 'TRANSFEERA IP S.A.',
            '27098060' => 'BANCO DIGIO',
            '27214112' => 'AL5 S.A. CFI',
            '27302181' => 'CRED-UFES',
            '27351731' => 'REALIZE CFI S.A.',
            '27652684' => 'GENIAL INVESTIMENTOS CVM S.A.',
            '27842177' => 'IB CCTVM S.A.',
            '27970567' => 'HINOVA PAY IP S.A.',
            '28127603' => 'BCO BANESTES S.A.',
            '28195667' => 'BCO ABC BRASIL S.A.',
            '28650236' => 'GALAPAGOS DTVM S.A.',
            '28811341' => 'STONEX BANCO DE CÂMBIO S.A.',
            '29030467' => 'Scotiabank Brasil',
            '29162769' => 'TORO CTVM S.A.',
            '30306294' => 'BANCO BTG PACTUAL S.A.',
            '30680829' => 'NU FINANCEIRA S.A. CFI',
            '30723886' => 'BCO MODAL S.A.',
            '30944783' => 'PAGPRIME IP',
            '31597552' => 'BCO CLASSICO S.A.',
            '31749596' => 'IDEAL CTVM S.A.',
            '31872495' => 'BCO C6 S.A.',
            '31880826' => 'BCO GUANABARA S.A.',
            '31895683' => 'BCO INDUSTRIAL DO BRASIL S.A.',
            '32062580' => 'BCO CREDIT SUISSE S.A.',
            '32074986' => 'BEETELLER',
            '32192325' => 'UZZIPAY IP S.A.',
            '32402502' => 'QI SCD S.A.',
            '32648370' => 'FAIR CC S.A.',
            '32708748' => 'WE PAY OUT IP LTDA.',
            '32997490' => 'CREDITAS SCD',
            '33040601' => 'MERCANTIL FINANCEIRA',
            '33042151' => 'BCO LA NACION ARGENTINA',
            '33042953' => 'CITIBANK N.A.',
            '33132044' => 'BCO CEDULA S.A.',
            '33147315' => 'BCO BRADESCO BERJ S.A.',
            '33172537' => 'BCO J.P. MORGAN S.A.',
            '33264668' => 'BCO XP S.A.',
            '33466988' => 'BCO CAIXA GERAL BRASIL S.A.',
            '33479023' => 'BCO CITIBANK S.A.',
            '33603457' => 'BCO RODOBENS S.A.',
            '33644196' => 'BCO FATOR S.A.',
            '33657248' => 'BNDES',
            '33737818' => 'CCC POUP INV DE MS, GO, DF E TO',
            '33775974' => 'ATIVA S.A. INVESTIMENTOS CCTVM',
            '33862244' => 'BGC LIQUIDEZ DTVM LTDA',
            '33884941' => 'BANCO MASTER MÚLTIPLO',
            '33885724' => 'BANCO ITAÚ CONSIGNADO S.A.',
            '33886862' => 'MASTER S/A CCTVM',
            '33923798' => 'BANCO MASTER',
            '34088029' => 'LISTO SCD S.A.',
            '34111187' => 'HAITONG BI DO BRASIL S.A.',
            '34265629' => 'INTERCAM CC LTDA',
            '34335592' => 'ÓTIMO SCD S.A.',
            '34337707' => 'BMP SCD S.A.',
            '34471744' => 'PAGME IP LTDA',
            '34747388' => 'ISSUER IP LTDA.',
            '34829992' => 'REAG TRUST DTVM',
            '35210410' => 'ONEKEY PAYMENTS IP S.A.',
            '35479592' => 'DUFRIO CFI S.A.',
            '35551187' => 'PLANTAE CFI',
            '35810871' => 'Z1 IP LTDA.',
            '35977097' => 'UP.P SEP S.A.',
            '36113876' => 'OLIVEIRA TRUST DTVM S.A.',
            '36266751' => 'FINVEST DTVM',
            '36321990' => 'AGORACRED S/A SCFI',
            '36583700' => 'QISTA S.A. CFI',
            '36586946' => 'BONUSPAGO SCD S.A.',
            '36864992' => 'MAF DTVM SA',
            '36947229' => 'COBUCCIO S.A. SCFI',
            '37229413' => 'SCFI EFÍ S.A.',
            '37241230' => 'SUMUP SCD S.A.',
            '37414009' => 'ZIPDIN SCD S.A.',
            '37470405' => 'SMART SOLUTIONS GROUP IP LTDA',
            '37526080' => 'LEND SCD S.A.',
            '37555231' => 'DM',
            '37678915' => 'FIDD DTVM LTDA.',
            '37679449' => 'MERCADO CRÉDITO SCFI S.A.',
            '37715993' => 'ACCREDITO SCD S.A.',
            '37880206' => 'CORA SCFI',
            '38129006' => 'NUMBRS SCD S.A.',
            '38224857' => 'DELCRED SCD S.A.',
            '38429045' => 'FÊNIX DTVM LTDA.',
            '38593706' => 'MULTICRED SCD S.A.',
            '39343350' => 'CC LAR CREDI',
            '39416705' => 'CREDIHOME SCD',
            '39519944' => 'OPEA SCD',
            '39587424' => 'UY3 SCD S/A',
            '39664698' => 'CREDSYSTEM SCD S.A.',
            '39669186' => 'HEMERA DTVM LTDA.',
            '39676772' => 'CREDIFIT SCD S.A.',
            '39738065' => 'FFCRED SCD S.A.',
            '39908427' => 'STARK SCD S.A.',
            '40083667' => 'CAPITAL CONSIG SCD S.A.',
            '40112555' => 'GIRO - SCD S/A',
            '40276692' => 'PROTEGE PAY CASH IP S.A.',
            '40303299' => 'PORTOPAR DTVM LTDA',
            '40333582' => 'SAYGO CÂMBIO',
            '40434681' => 'AZUMI DTVM',
            '40475846' => 'J17 - SCD S/A',
            '40654622' => 'TRINUS SCD S.A.',
            '40768766' => 'LIONS TRUST DTVM',
            '41592532' => 'MÉRITO DTVM LTDA.',
            '42047025' => 'UNAVANTI SCD S/A',
            '42066258' => 'RJI',
            '42259084' => 'SBCASH SCD',
            '42272526' => 'BNY MELLON BCO S.A.',
            '43180355' => 'PEFISA S.A. - C.F.I.',
            '43599047' => 'SUPERLÓGICA SCD S.A.',
            '44019481' => 'PEAK SEP S.A.',
            '44077014' => 'BR-CAPITAL DTVM S.A.',
            '44189447' => 'BCO LA PROVINCIA B AIRES BCE',
            '44292580' => 'HR DIGITAL SCD',
            '44478623' => 'ATICCA SCD S.A.',
            '44683140' => 'MAGNUM SCD',
            '44705774' => 'SOMAPAY SCD S.A.',
            '44728700' => 'ATF SCD S.A.',
            '44782130' => 'ACTUAL DTVM S.A.',
            '45246410' => 'BANCO GENIAL',
            '45331622' => 'BNK DIGITAL SCD S.A.',
            '45548763' => 'MAPS IP LTDA.',
            '45745537' => 'EAGLE SCD S.A.',
            '45756448' => 'MICROCASH SCMEPP LTDA.',
            '45854066' => 'WNT CAPITAL DTVM',
            '46026562' => 'MONETARIE SCD',
            '46518205' => 'JPMORGAN CHASE BANK',
            '46955383' => 'QI DTVM LTDA.',
            '47593544' => 'RED SCD S.A.',
            '47873449' => 'SER FINANCE SCD S.A.',
            '48707451' => 'PERCAPITAL SCD S.A.',
            '48795256' => 'BCO ANDBANK S.A.',
            '48967968' => 'VERT DTVM LTDA.',
            '49288113' => 'KANASTRA CFI',
            '49555647' => 'QUADRA SCD',
            '49931906' => 'TRIO IP LTDA.',
            '49933388' => 'BRCONDOS SCD S.A.',
            '50579044' => 'LEVYCAM CCV LTDA',
            '50585090' => 'BANCO BMG CONSIGNADO S.A.',
            '50946592' => 'SETHI SCD SA',
            '51212088' => 'G5 SCD SA',
            '51342763' => 'REVOLUT SCD S.A.',
            '51414521' => 'ALL IN CRED SCD S.A.',
            '52440987' => 'NITRO SCD S.A.',
            '52586293' => 'Z-ON SCD S.A.',
            '53518684' => 'BCO HSBC S.A.',
            '53842122' => 'URBANO S.A. SCFI',
            '54403563' => 'BCO ARBI S.A.',
            '54647259' => '321 SCD S.A.',
            '55230916' => 'INTESA SANPAOLO BRASIL S.A. BM',
            '55428859' => 'FREEX CC S.A.',
            '57839805' => 'BCO TRICURY S.A.',
            '58160789' => 'BCO SAFRA S.A.',
            '58497702' => 'BCO LETSBANK S.A.',
            '58616418' => 'BCO FIBRA S.A.',
            '59109165' => 'BCO VOLKSWAGEN S.A',
            '59118133' => 'BCO LUSO BRASILEIRO S.A.',
            '59274605' => 'BCO GM S.A.',
            '59285411' => 'BANCO PAN',
            '59588111' => 'BCO VOTORANTIM S.A.',
            '60394079' => 'BCO ITAUBANK S.A.',
            '60498557' => 'BCO MUFG BRASIL S.A.',
            '60701190' => 'ITAÚ UNIBANCO S.A.',
            '60746948' => 'BCO BRADESCO S.A.',
            '60814191' => 'BCO MERCEDES-BENZ S.A.',
            '60850229' => 'OMNI BANCO S.A.',
            '60889128' => 'BCO SOFISA S.A.',
            '61024352' => 'BANCO VOITER',
            '61033106' => 'BCO CREFISA S.A.',
            '61088183' => 'BCO MIZUHO S.A.',
            '61186680' => 'BCO BMG S.A.',
            '61348538' => 'BCO C6 CONSIG',
            '61384004' => 'AVENUE SECURITIES DTVM LTDA.',
            '61533584' => 'BCO SOCIETE GENERALE BRASIL',
            '61723847' => 'NEON CTVM S.A.',
            '61809182' => 'C.SUISSE HEDGING-GRIFFO CV S/A',
            '61820817' => 'BCO PAULISTA S.A.',
            '62073200' => 'BOFA MERRILL LYNCH BM S.A.',
            '62109566' => 'CREDISAN CC',
            '62144175' => 'BCO PINE S.A.',
            '62232889' => 'BCO DAYCOVAL S.A',
            '62237649' => 'CAROL DTVM LTDA.',
            '62285390' => 'SINGULARE CTVM S.A.',
            '62287735' => 'RENASCENCA DTVM LTDA',
            '62331228' => 'DEUTSCHE BANK S.A.BCO ALEMAO',
            '62421979' => 'BANCO CIFRA',
            '65913436' => 'GUIDE',
            '67030395' => 'TRUSTEE DTVM LTDA.',
            '68757681' => 'SIMPAUL',
            '68900810' => 'BCO RENDIMENTO S.A.',
            '70119680' => 'CENTRAL NORDESTE',
            '71027866' => 'BCO BS2 S.A.',
            '71590442' => 'LASTRO RDV DTVM LTDA',
            '71677850' => 'FRENTE CC S.A.',
            '73302408' => 'EXIM CC LTDA.',
            '73622748' => 'BT CC LTDA.',
            '74014747' => 'ÁGORA CTVM S.A.',
            '74828799' => 'NOVO BCO CONTINENTAL S.A. - BM',
            '75647891' => 'BCO CRÉDIT AGRICOLE BR S.A.',
            '76461557' => 'CCR COOPAVEL',
            '76543115' => 'BANCO SISTEMA',
            '78157146' => 'CREDIALIANÇA CCR',
            '78626983' => 'BCO VR S.A.',
            '78632767' => 'OURIBANK S.A.',
            '80271455' => 'BCO RNX S.A.',
            '81723108' => 'CREDICOAMO',
            '82096447' => 'CREDIBRF COOP',
            '89960090' => 'RB INVESTIMENTOS DTVM LTDA.',
            '90400888' => 'BCO SANTANDER (BRASIL) S.A.',
            '91669747' => 'DM SA CFI',
            '91884981' => 'BANCO JOHN DEERE S.A.',
            '92702067' => 'BCO DO ESTADO DO RS S.A.',
            '92825397' => 'COOPCRECE',
            '92856905' => 'ADVANCED CC LTDA',
            '92874270' => 'BCO DIGIMAIS S.A.',
            '92875780' => 'WARREN CVMC LTDA',
            '92894922' => 'BANCO ORIGINAL',
            '94968518' => 'EFX CC LTDA.',
        ];
    }

}