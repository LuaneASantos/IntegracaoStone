<?php

/**
 * Copyright (c) 2019 - PHARMACY APP AGENCIAMENTO E NEGÓCIOS S/A.
 */

$xml = <<<XML
<?xml version="1.0"?>
<Document xmlns="urn:AcceptorAuthorisationRequestV02.1">
    <AccptrAuthstnReq>
        <!-- Cabeçalho da requisição -->
        <Hdr>
            <!-- Identifica o tipo de processo em que a mensagem se propõe. -->
            <MsgFctn>AUTQ</MsgFctn>
            <!-- Versão do protocolo utilizado na mensagem. -->
            <PrtcolVrsn>2.0</PrtcolVrsn>
        </Hdr>
        <!-- Dados da requisição de autorização. -->
        <AuthstnReq>
            <!-- Ambiente da transação. -->
            <Envt>
                <!-- Dados do estabelecimento. -->
                <Mrchnt>
                    <!-- Identificação do estabelecimento. -->
                    <Id>
                        <!-- Identificação do estabelecimento comercial no adquirente.
                             Também conhecido internamente como “SaleAffiliationKey”. -->
                        <Id>%s</Id>
                        <!-- O nome que aparecerá na fatura.
                             - se a transação for mastercard, o limite é 22 caracteres;
                             - se a transação for visa, o limite é 25 caracteres;
                             - se for parcelado, a visa usa os 8 primeiros caracteres do
                               nome do lojista pra passar a informação de parcelamento,
                               sobrando 17 caracteres. -->
                        <ShortName>Farmacias APP</ShortName>
                    </Id>
                </Mrchnt>
                <!-- Dados do ponto de interação -->
                <POI>
                    <!-- Identificação do ponto de interação -->
                    <Id>
                        <!-- Código de identificação do ponto de interação
                             atribuído pelo estabelecimento. -->
                        <Id>1</Id>
                    </Id>
                </POI>
                <!-- Dados do cartão utilizado na transação. -->
                <Card>
                    <!-- Dados não criptografados do cartão utilizado na transação. -->
                    <PlainCardData>
                        <!-- Número do cartão. (Primary Account Number) -->
                        <PAN>%d</PAN>
                        <!-- Data de validade do cartão no formato “yyyy-MM”. -->
                        <XpryDt>%s</XpryDt>
                        <CardSctyCd>
                            <CSCVal>%d</CSCVal>
                        </CardSctyCd>

                    </PlainCardData>
                </Card>
            </Envt>
            <!-- Informações da transação a ser realizada. -->
            <Cntxt>
                <!-- Informações sobre o pagamento. -->
                <PmtCntxt>
                    <!-- Modo da entrada dos dados do cartão.
                         PHYS = Ecommerce ou Digitada; -->
                    <CardDataNtryMd>PHYS</CardDataNtryMd>
                    <!-- Tipo do canal de comunicação utilizado na transação.
                         ECOM = Ecommerce ou Digitada -->
                    <TxChanl>ECOM</TxChanl>
                </PmtCntxt>
            </Cntxt>
            <!-- Informações da transação. -->
            <Tx>
                <!-- Identificação da transação definida pelo sistema que se
                     comunica com o Host Stone. -->
                <InitrTxId>%s</InitrTxId>
                <!-- Indica se os dados da transação devem ser capturados (true)
                     ou não (false) imediatamente. -->
                <TxCaptr>true</TxCaptr>
                <!-- Dados de identificação da transação atribuída pelo POI. -->
                <TxId>
                    <!-- Data local e hora da transação atribuídas pelo POI. -->
                    <TxDtTm>%s</TxDtTm>
                    <!-- Identificação da transação definida pelo ponto de interação (POI,
                         estabelecimento, lojista, etc). O formato é livre contendo no
                         máximo 32 caracteres. -->
                    <TxRef>%s</TxRef>
                </TxId>
                <!-- Detalhes da transação. -->
                <TxDtls>
                    <!-- Moeda utilizada na transação em conformidade com a ISO 4217.-->
                    <Ccy>986</Ccy>
                    <!-- Valor total da transação em centavos. -->
                    <TtlAmt>%s</TtlAmt>
                    <!-- Modalidade do cartão utilizado na transação. -->
                    <AcctTp>CRDT</AcctTp>
                    <!-- Os dados relativos à(s) parcela(s) ou a uma transação recorrente. -->
                    <RcrngTx>
                        <!-- Tipo de parcelamento. -->
                        <InstlmtTp>%s</InstlmtTp>
                        <!-- Número do total de parcelas. -->
                        <TtlNbOfPmts>%d</TtlNbOfPmts>
                    </RcrngTx>
                </TxDtls>
            </Tx>
        </AuthstnReq>
    </AccptrAuthstnReq>
</Document>
XML;

?>