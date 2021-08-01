<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Model\BankTransaction;
use DateTime;

/**
 * [Description BankTransactionTest]
 */
class BankTransactionTest extends TestCase
{

    /**
     * @return void
     */
    public function testVerifyKey(): void
    {
        $bankTransaction = new BankTransaction(new DateTime(), 'NUF5V6PT3U', 5156, 'Purchase at JB HiFi', -2498);
        $this->assertFalse($bankTransaction->verifyTransactionCode());

        $bankTransaction = new BankTransaction(new DateTime(), 'J82964EFPS', 5156, 'Purchase at JB HiFi', -2498);
        $this->assertTrue($bankTransaction->verifyTransactionCode());
    }
}
