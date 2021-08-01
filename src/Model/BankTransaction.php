<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use App\Library\Log;

/**
 * [Description BankTransaction]
 */
class BankTransaction
{
    private DateTime $date;
    private string $transaction_code;
    private int $customer_number;
    private string $reference;
    private int $amount;
    const VALID_CHARS = [
        '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C',
        'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    /**
     * @param DateTime $date
     * @param string $transaction_code
     * @param int $customer_number
     * @param string $reference
     * @param int $amount
     */
    public function __construct(DateTime $date, string $transaction_code, int $customer_number, string $reference, int $amount)
    {
        $this->date = $date;
        $this->transaction_code = $transaction_code;
        $this->customer_number = $customer_number;
        $this->reference = $reference;
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function isCredit(): bool
    {
        return $this->amount >= 0;
    }

    /**
     * @return string
     */
    public function renderRow(): string
    {
        $date_str = $this->date->format('d/m/Y g:iA');
        $amount_class = $this->isCredit() ? "credit" : "debit";

        $fmt = numfmt_create('en_US', \NumberFormatter::CURRENCY);
        $amount_str = numfmt_format_currency($fmt, $this->amount / 100, "USD");
        $valid_str = $this->verifyTransactionCode() ? "Yes" : "No";

        return " <td>{$date_str}</td>
        <td>{$this->transaction_code}</td>
        <td>{$valid_str}</td>
        <td>{$this->customer_number}</td>
        <td>{$this->reference}</td>
        <td class=\"{$amount_class}\">{$amount_str}</td>
        ";
    }
     
    /**
     * @return bool
     */
    public function verifyTransactionCode(): bool
    {
        $key = $this->transaction_code;
        if (strlen($key) != 10) {
            return false;
        }

        $check_digit = $this->generateCheckCharacter(substr(strtoupper($key), 0, 9));
        return $key[9] == $check_digit;
    }

    /**
     * @param string $input
     * 
     * @return string
     */
    private function generateCheckCharacter(string $input): string
    {
        $factor = 2;
        $sum = 0;
        $n = count(self::VALID_CHARS);
        // Starting from the right and working leftwards is easier since
        // the initial "factor" will always be "2"
        for ($i = strlen($input) - 1; $i >= 0; $i--) {
            $code_point = strpos(implode('', self::VALID_CHARS), $input[$i]);
            $addend = $factor * $code_point;
            // Alternate the "factor" that each "codePoint" is multiplied by
            $factor = ($factor == 2) ? 1 : 2;
            // Sum the digits of the "addend" as expressed in base "n"
            $addend = intdiv($addend, $n) + ($addend % $n);
            $sum += $addend;
        }

        // Calculate the number that must be added to the "sum"
        // to make it divisible by "n"

        $remainder = $sum % $n;
        $check_code_point = ($n - $remainder) % $n;
        return self::VALID_CHARS[$check_code_point];
    }
}
