<?php

namespace App\Enums;

enum MetodeBayar: string
{
    case TUNAI = 'TUNAI';
    case CASH = 'CASH';
    case TRANSFER = 'TRANSFER';
    case PAYMENT = 'PAYMENT';
}
