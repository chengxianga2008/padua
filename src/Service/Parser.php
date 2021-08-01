<?php

declare(strict_types=1);

namespace App\Service;

use App\Library\Log;
use App\Model\BankTransaction;
use DateTime;

/**
 * [Description Parser]
 */
class Parser
{
    public function __construct()
    {
    }
    
    /**
     * @param string $file_path
     * 
     * @return array
     */
    public function importCSV(string $file_path): array
    {
        $csv = array_map("str_getcsv", file($file_path, FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($csv);

        $keys = array_map(function ($ele) {
            return preg_replace('/\x{feff}/u', '', $ele);
        }, $keys);

        foreach ($csv as $i => $row) {
            $csv[$i] = array_combine($keys, $row);
        }

        return $csv;
    }
    

    /**
     * @param array $csv
     * 
     * @return string
     */
    public function renderTable(array $csv): string
    {

        $tbody = "";
        foreach ($csv as $row) {

            $date = DateTime::createFromFormat('Y-m-d g:iA', $row['Date']);
            $bankTransaction = new BankTransaction($date, $row['TransactionNumber'], (int)$row['CustomerNumber'], $row['Reference'], (int)$row['Amount']);
            $rowHtml = $bankTransaction->renderRow();
            $tbody .= "<tr>{$rowHtml}</tr>";
        }

        return "<table class=\"table\">
                    <thead><tr><th>Date</th><th>Transaction Code</th><th>Valid Transaction?</th><th>Customer Number</th><th>Reference</th><th>Amount</th></tr></thead>
                    <tbody>{$tbody}</tbody>
                </table>";
    }
}
