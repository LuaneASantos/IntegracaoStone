<?php

use DateTime;
use DOMDocument;

/**
 * Class StoneService
 */
class StoneService
{

    public const ENVIRONMENT_SANDBOX = 1;
    public const ENVIRONMENT_PRODUCTION = 2;

    private function processCreditCardPayment()
    {

        include 'StoneLayout.php';
        // Utilizado para identificar o ambiente de integração.
        $configEnvironment = 'Passar se vai ser produção(2) ou SandBox(1)'; // aqui será passado se sera em produção ou sendbox
        $curl = curl_init();

        if ($configEnvironment == self::ENVIRONMENT_SANDBOX) {
            // Caso estejamos utilizando o ambiente de testes, definimos
            // o endpoint de testes e credenciais adequadas.
            $apiEndpoint = 'https://sandbox-auth-integration.stone.com.br/Authorize';

            // Não precisamos fazer verificações do certificado digital
            // no ambiente de testes
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        } else {
            $apiEndpoint = 'https://e-commerce.stone.com.br/Authorize';
        }

        // Definimos o ambiente de integração
        curl_setopt($curl, CURLOPT_URL, $apiEndpoint);

        // Informamos que estamos enviando um XML
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));

        // Solicitamos a resposta da requisição para que possamos trabalhar com ela
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Definimos o método HTTP POST e informamos a chave de afiliação adequada para
        // o ambiente.
        curl_setopt($curl, CURLOPT_POST, true);
        $dateTime = new DateTime();
        curl_setopt($curl, CURLOPT_POSTFIELDS, sprintf(
            $xml,
            'MERCHANT TOKEN', //Mrchnt - ID
            'NUMERO CARTÃO', // PAN
            'VALIDADE CARTÃO', // XpryDt
            'CODIGO SEGURANÇA CARTÃO', // CardSctyCd - CSCVal
            'CODIGO DO PEDIDO OU IDENTIFICAÇÃO DA TRANSAÇÃO', // InitrTxId
            $dateTime->format('Y-m-d\TH:i:s'), // new \DateTime('now').datefmt_format(), // TxDtTm
            'CODIGO DO PEDIDO OU IDENTIFICAÇÃO DA TRANSAÇÃO', // TxRef
            str_replace('.', '', number_format('VALOR TRANSAÇÃO', 2, '.', '')), // TtlAmt
            'EM QUANTAS PARCELAS' == 1 ? 'NONE' : 'MCHT', // InstlmtTp,
            'EM QUANTAS PARCELAS' == 1 ? 0 : 'EM QUANTAS PARCELAS' // TtlNbOfPmts
        ));

        // Enviamos a requisição e aguardamos a resposta.
        $response = curl_exec($curl);

        $errno = curl_errno($curl);
        $error = curl_error($curl);

        curl_close($curl);

        // Verificamos se houve algum erro no envio da requisição
        if ($errno == 0) {

            // Manipulamos o XML de resposta e obtemos o status da aprovação ou
            // rejeição
            $dom = new DOMDocument();
            $dom->loadXML($response);

            $approved = $dom->getElementsByTagName('Rspn')->item(0);
            $authorizationCode = $dom->getElementsByTagName('RcptTxId')->item(0);

            if ($approved->nodeValue !== "DECL") {

                // O pagamento foi aprovado

            } else {
                $rejected = $dom->getElementsByTagName('RspnRsn')->item(0);

                if ($rejected->nodeValue !== null) {

                    //Pagamento recusado

                }
            }
        } else {
            // Erro ao processa o pagamento
        }

        return null;
    }


    public function cancelPayment()
    {
        try {
            include 'StoneLayoutCancellation.php';
            // Utilizado para identificar o ambiente de integração.
            $configEnvironment = 'Passar se vai ser produção(2) ou SandBox(1)';
            $curl = curl_init();

            if ($configEnvironment == self::ENVIRONMENT_SANDBOX) {
                // Caso estejamos utilizando o ambiente de testes, definimos
                // o endpoint de testes e credenciais adequadas.
                $apiEndpoint = 'https://sandbox-auth-integration.stone.com.br/Cancellation';

                // Não precisamos fazer verificações do certificado digital
                // no ambiente de testes
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            } else {
                $apiEndpoint = 'https://e-commerce.stone.com.br/Cancellation';
            }

            // Definimos o ambiente de integração
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint);

            // Informamos que estamos enviando um XML
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8'));

            // Solicitamos a resposta da requisição para que possamos trabalhar com ela
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Definimos o método HTTP POST e informamos a chave de afiliação adequada para
            // o ambiente.
            curl_setopt($curl, CURLOPT_POST, true);
            $dateTime = new DateTime();

            curl_setopt($curl, CURLOPT_POSTFIELDS, sprintf(
                $xml,
                'MERCHANT TOKEN', //Mrchnt - ID
                $dateTime->format('Y-m-d\TH:i:s'), // new \DateTime('now').datefmt_format(), // TxDtTm
                'CODIGO DO PEDIDO OU IDENTIFICAÇÃO DA TRANSAÇÃO', // TxRef
                str_replace('.', '', 'VALOR TRANSAÇÃO'), // TtlAmt
                'CODIGO DO PEDIDO OU IDENTIFICAÇÃO DA TRANSAÇÃO', // InitrTxId
                'CODIGO QUE FOI RETORNADO QUANDO A TRANSAÇÃO FOI ACEITA' // RcptTxId
            ));

            // Enviamos a requisição e aguardamos a resposta.
            $response = curl_exec($curl);
            $errno = curl_errno($curl);
            $error = curl_error($curl);

            curl_close($curl);

            // Verificamos se houve algum erro no envio da requisição
            if ($errno == 0) {

                // Manipulamos o XML de resposta e obtemos o status da aprovação ou
                // rejeição
                $dom = new DOMDocument();
                $dom->loadXML($response);

                $approved = $dom->getElementsByTagName('Rspn')->item(0);

                if ($approved->nodeValue !== "DECL") {

                    // Transação cancelada com sucesso

                } else {

                    $rejected = $dom->getElementsByTagName('RspnRsn')->item(0);

                    if ($rejected->nodeValue !== null) {
                        //Erro ao fazer cancelamento de transação
                    }
                }
            } else {

                //Erro ao fazer cancelamento de transação

            }
        } catch (\Exception $e) {

            //Erro ao fazer cancelamento de transação

        }
    }

    public function accreditation()
    {

        $curl = curl_init('https://affiliation-integration.stone.com.br/Merchant/MerchantService.svc/Merchant/Affiliate');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $body = array(
            "Credential" => array(
                "UserId" => 'USER_ID ENVIADO PELA STONE',
                "Signature" => hash_hmac('sha512',  'CNPJ DO CREDENCIADO' . '-' . 'NOME DO CREDENCIADO', 'SIGNATURE ENVIADA PELA STONE')
            ),
            "BypassCreditAnalysis" =>  false,
            "Merchant" => array(
                "BankAccountList" => [array(
                    "AccountNumber" => 'NUMERO CONTA BANCARIA',
                    "AccountVerificationCode" => 'CODIGO VERIFICADOR DA CONTA',
                    "BankBranchCode" => 'AGENCIA CONTA',
                    "BankIdentifier" => 'CODIGO DO BANCO'
                )],
                "CompanyName" =>  'NOME DO CREDENCIADO',
                "TradeName" => 'NOME FANTASIA DO CREDENCIADO',
                "DocumentNumber" => 'CNPJ DO CREDENCIADO',
                "AccountEmail" =>  'EMAIL DO CREDENCIADO',
                "DocumentType" => 1,
                "Mcc" => 5912,
                "CardBrandList" => array(
                    array(
                        "CardBrandId" => 1, // Visa
                        "CardBrandTypeId" => 1,
                        "IsEnabled" => 'SE A BANDEIRA É ATIVA (TRUE OU FALSE)'
                    ),

                    array(
                        "CardBrandId" => 2, // Mastercard
                        "CardBrandTypeId" => 1,
                        "IsEnabled" => 'SE A BANDEIRA É ATIVA (TRUE OU FALSE)'
                    ),

                    array(
                        "CardBrandId" => 3, // American Express
                        "CardBrandTypeId" => 1,
                        "IsEnabled" => 'SE A BANDEIRA É ATIVA (TRUE OU FALSE)'
                    ),

                    array(
                        "CardBrandId" => 9, // Hipercard
                        "CardBrandTypeId" => 1,
                        "IsEnabled" => 'SE A BANDEIRA É ATIVA (TRUE OU FALSE)'
                    ),

                    array(
                        "CardBrandId" => 171, //Elo
                        "CardBrandTypeId" => 1,
                        "IsEnabled" => 'SE A BANDEIRA É ATIVA (TRUE OU FALSE)'
                    )
                ),
                "MerchantAddress" =>  array(
                    "City" => 'CIDADE DO CREDENCIADO',
                    "Complement" => 'ENDEREÇO DO CREDENCIADO',
                    "Country" => "076",
                    "Neighborhood" => 'BAIRRO DO CREDENCIADO',
                    "PostalCode" => 'CEP DO CREDENCIADO',
                    "StateCode" => 'UF ESTADO DO CREDENCIADO',
                    "StreetName" => 'NOME RUA DO CREDENCIADO',
                    "StreetNumber" => 'NUMERO DO ENDEREÇO DO CREDENCIADO',
                ),
                "MerchantCaptureMethodList" => [array(
                    "TerminalTypeId" => 4,
                    "Url" => "URL DO SITE DO CREDENCIADO"
                )],
                "MerchantContactList" => [array(
                    "ContactName" => 'NOME DO CONTATO',
                    "Email" => 'EMAIL DO CONTATO',
                    "MobilePhoneNumber" => preg_replace("/[^0-9]/", "", 'CELULAR DO CONTATO'),
                    "PhoneNumber" => preg_replace("/[^0-9]/", "", 'TELEFONE DO CONTATO')
                )]
            )
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

        // Enviamos a requisição e aguardamos a resposta.
        $response = json_decode(curl_exec($curl), true);

        if ($response['Status']['Code'] == 'VALIDATION_ERRORS') {

            // Erro ao credenciar

            if (count($response['MessageList']) > 0) {
                $ErrorMessage = "";
                foreach ($response['MessageList'] as $mensagem) {
                    $ErrorMessage .= $mensagem['Message'] . ' <br /> ';
                }
                // $ErrorMessage mensagem de erro
            }
        } else if ($response['Status']['Code'] == 'OK') {

            //Credenciamento concluído com sucesso
            //$response['MerchantReturn']['AffiliationKey'] código de credenciamento

        }

        return null;
    }

    public function accreditationInfo()
    {

        $curl = curl_init('https://affiliation-integration.stone.com.br/Merchant/MerchantService.svc/Merchant/ListMerchants');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $body = array(
            "Credential" => array(
                "UserId" => 'USER_ID ENVIADO PELA STONE',
                "Signature" => hash_hmac('sha512',  'ListMerchants', 'SIGNATURE ENVIADA PELA STONE')
            ),

            "QueryExpression" => array(
                "ConditionList" => [array(
                    "__type" => "Condition",
                    "LogicalOperator" => "And",
                    "ComparisonOperator" => "Equals",
                    "Field" => "DocumentNumber",
                    "Value" => 'CNPJ DO CREDENCIADO'
                )],
                "OrderBy" => [array(
                    "Key" => "CompanyName",
                    "Value" => "Asc"
                )],
                "PageNumber" =>  1,
                "RowsPerPage" =>  10,
            )
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

        // Enviamos a requisição e aguardamos a resposta.
        $exec = curl_exec($curl);
        $response = json_decode($exec, true);

        if ($response['Status']['Code'] == 'VALIDATION_ERRORS') {
            // ocorreu um erro
            $ErrorMessage = "";
            foreach ($response['MessageList'] as $mensagem) {
                $ErrorMessage .= $mensagem['Message'] . ' <br /> ';
            }

            // $ErrorMessage mensagem de erro
        } else if ($response['Status']['Code'] == 'OK') {
            // processou com sucesso
            // informações em $exec
        }

        return null;
    }
}
