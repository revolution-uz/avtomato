<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class UniredMonitoringHumoRowDTO extends BaseUniredDTO
{
    public function __construct(
        public string $CARD_ACCT,
        public string $ACCOUNT_NO,
        public string $CL_ACCT_KEY,
        public string $CLIENT,
        public string $CARD,
        public string $EXP_DATE,
        public string $TRAN_TYPE,
        public string $TRAN_CCY,
        public string $TRAN_AMT,
        public string $CCY_EXP,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s')]
        public Carbon $TRAN_DATE_TIME,
        public string $REC_DATE,
        public string $POST_DATE,
        public string $ACCNT_CCY,
        public string $AMOUNT_NET,
        public string $ACQREF_NR,
        public string $APR_CODE,
        public string $APR_SCR,
        public string $STAN,
        public string $TERM_ID,
        public string $MERCHANT,
        public string $POINT_CODE,
        public string $MCC_CODE,
        public string $ABVR_NAME,
        public string $CITY,
        public string $COUNTRY,
        public string $DEAL_DESC,
        public string $COUNTERPARTY,
        public string $INTERNAL_NO,
        public string $FLD_104,
        public string $BANK_C,
        public string $GROUPC,
        public string $CTIME,
        public string $TR_CODE,
        public string $TR_FEE,
        public string $TR_CODE2,
        public string $TR_FEE2,
        public string $LOCKING_FLAG,
        public string $ADD_INFO,
        public string $REF_NUMBER,
    ) {}
}
