<?php

/**
 * Copyright (c) 2019 - PHARMACY APP AGENCIAMENTO E NEGÓCIOS S/A.
 */

$xml = <<<XML
<Document xmlns="urn:AcceptorCancellationRequestV02.1">
    <AccptrCxlReq>
        <Hdr>
            <MsgFctn>CCAQ</MsgFctn>
            <PrtcolVrsn>2.0</PrtcolVrsn>
        </Hdr>
        <CxlReq>
            <Envt>
                <Mrchnt>
                    <Id>
                        <Id>%s</Id>
                    </Id>
                </Mrchnt>
                <POI>
                    <Id>
                        <Id>2</Id>
                    </Id>
                </POI>
            </Envt>
            <Tx>
                <TxCaptr>true</TxCaptr>
                <TxId>
                    <TxDtTm>%s</TxDtTm>
                    <TxRef>%s</TxRef>
                </TxId>
                <TxDtls>
                    <Ccy>986</Ccy>
                    <TtlAmt>%s</TtlAmt>
                </TxDtls>
                <OrgnlTx>
                    <InitrTxId>%s</InitrTxId>
                    <RcptTxId>%s</RcptTxId>
                </OrgnlTx>
            </Tx>
        </CxlReq>
    </AccptrCxlReq>
</Document>
XML;

?>