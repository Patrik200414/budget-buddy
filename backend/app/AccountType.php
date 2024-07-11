<?php

namespace App;

enum AccountType: string
{
    case SAVINGS_ACCOUNT = 'Savings account';
    case DEBIT_ACCOUNT = 'Debit account';
    case HOLDINGS_ACCOUNT = 'Holdings account';
}
