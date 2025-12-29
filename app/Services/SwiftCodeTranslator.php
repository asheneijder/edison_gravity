<?php

namespace App\Services;

use Illuminate\Support\Str;

class SwiftCodeTranslator
{
    /**
     * Clean the code by removing any additional information following a slash.
     */
    private static function cleanCode(?string $code): ?string
    {
        if ($code == null) return null;
        if (str_contains($code, '/')) return Str::before($code, '/');
        return trim($code);
    }

    /**
     * Translate Message Type (MT and MX) to description.
     * Used for generating CSV filenames.
     */
    public static function translateMessageType(string $type): string
    {
        return match ($type) {
            // --- MT Categories ---
            '103' => 'Single Customer Credit Transfer',
            '202' => 'General Financial Institution Transfer',
            '210' => 'Notice to Receive',

            // Category 5 (Securities)
            '519' => 'Modification of Client Details',
            '527' => 'Triparty Collateral Instruction',
            '535' => 'Statement of Holdings',
            '536' => 'Statement of Transactions',
            '537' => 'Statement of Pending Transactions',
            '540' => 'Receive Free',
            '541' => 'Receive Against Payment',
            '542' => 'Deliver Free',
            '543' => 'Deliver Against Payment',
            '544' => 'Receive Free Confirmation',
            '545' => 'Receive Against Payment Confirmation',
            '546' => 'Deliver Free Confirmation',
            '547' => 'Deliver Against Payment Confirmation',
            '548' => 'Settlement Status and Processing Advice',
            '564' => 'Corporate Action Notification',
            '565' => 'Corporate Action Instruction', // Added
            '566' => 'Corporate Action Confirmation',
            '567' => 'Corporate Action Status and Processing Advice',

            // Reporting
            '940' => 'Customer Statement Message',
            '950' => 'Statement Message',

            // --- MX (ISO 20022) Categories ---
            // Payments (pacs)
            'pacs.008' => 'Customer Credit Transfer (MX)',
            'pacs.009' => 'Financial Institution Credit Transfer (MX)',
            'pacs.002' => 'Payment Status Report (MX)',
            'pacs.004' => 'Payment Return (MX)',

            // Cash Management (camt)
            'camt.052' => 'Bank to Customer Account Report (MX)',
            'camt.053' => 'Bank to Customer Statement (MX)',
            'camt.054' => 'Bank to Customer Debit/Credit Notification (MX)',
            'camt.060' => 'Account Reporting Request (MX)',

            // Securities (semt/seev) - Equivalents to MT5xx
            'semt.017' => 'Securities Settlement Transaction Instruction (MX)', // ~MT540
            'semt.018' => 'Securities Settlement Transaction Confirmation (MX)', // ~MT544
            'seev.031' => 'Corporate Action Notification (MX)', // ~MT564

            default => 'Unknown Message Type (' . $type . ')',
        };
    }

    /**
     * Translate Payment Category Purpose Codes (MX field: CtgyPurp).
     */
    public static function translatePurposeCode(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'SALA' => 'Salary Payment',
            'PENS' => 'Pension Payment',
            'TAXEp' => 'Tax Payment',
            'CORT' => 'Trade Settlement Payment',
            'DIVI' => 'Dividend',
            'INTC' => 'Intra-Company Payment',
            'SUPP' => 'Supplier Payment',
            'HEDG' => 'Hedging',
            'TREA' => 'Treasury Payment',
            'VATX' => 'Value Added Tax Payment',
            'WHLD' => 'Withholding Tax',
            default => $code,
        };
    }

    public static function translateFunction(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'NEWM' => 'New Message',
            'CANC' => 'Cancellation Request',
            'PREA' => 'Preadvice',
            'REPL' => 'Replacement',
            'DUPL' => 'Duplicate',
            'RECO' => 'Reconciliation Only',
            'CODU' => 'Copy Duplicate',
            'COPY' => 'Copy',
            'RMDR' => 'Reminder',
            default => $code,
        };
    }

    public static function translatePaymentStatus(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'APMT' => 'Against Payment',
            'FREE' => 'Free of Payment',
            default => $code,
        };
    }

    public static function translateSettlementType(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'TRAD' => 'Trade',
            'TRAN' => 'Transfer',
            'REDE' => 'Redemption',
            'SUBS' => 'Subscription',
            'SECL' => 'Securities Lending',
            'SECB' => 'Securities Borrowing',
            'REPU' => 'Repo',
            'RVPO' => 'Reverse Repo',
            'TRPO' => 'Triparty Repo',
            'TRVO' => 'Triparty Reverse Repo',
            'SBBK' => 'Sell Buy Back',
            'BSBK' => 'Buy Sell Back',
            'COLL' => 'Collateral',
            'CORP' => 'Corporate Action',
            'CLAI' => 'Market Claim',
            'CNCB' => 'Central Bank Collateral Operation',
            'ISSU' => 'Issuance',
            'REAL' => 'Realignment',
            'NETT' => 'Netting',
            'PORT' => 'Portfolio Move',
            'OWNI' => 'Internal Account Transfer',
            'OWNE' => 'External Account Transfer',
            'PAIR' => 'Pair-Off',
            'TURN' => 'Turnaround',
            'INSP' => 'Move of Stock',
            'CONV' => 'DR Conversion',
            'RELE' => 'DR Release/Cancellation',
            'BYIY' => 'Buy In',
            'SLIY' => 'Sell In',
            'PLAC' => 'Placement',
            'SYND' => 'Syndicate',
            'RODE' => 'Return of Delivery Without Matching',
            default => $code,
        };
    }

    /**
     * Translate Settlement Condition (Tag 22F::STCO).
     * Added for MT540/MT542 Samples.
     */
    public static function translateSettlementCondition(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'PART' => 'Partial Settlement',
            'NPAR' => 'No Partial Settlement',
            'PARS' => 'Partial Settlement Allowed',
            'DROP' => 'Drop',
            'GAMB' => 'Gambling',
            'CLEN' => 'Clean',
            'DIRT' => 'Dirty',
            'PHYS' => 'Physical',
            'SPDL' => 'Special Delivery',
            default => $code,
        };
    }

    /**
     * Translate Corporate Action Event (Tag 22F::CAEV).
     * Added for MT565 Sample.
     */
    public static function translateCorporateActionEvent(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'DRIP' => 'Dividend Reinvestment',
            'DVCA' => 'Cash Dividend',
            'DVSE' => 'Stock Dividend',
            'INTR' => 'Interest Payment',
            'REDM' => 'Redemption',
            'RHTS' => 'Rights Issue',
            'BONU' => 'Bonus Issue',
            'SPLF' => 'Stock Split',
            'MEET' => 'Meeting',
            'TEND' => 'Tender/Acquisition/Takeover/Purchase Offer',
            'XMET' => 'Extraordinary Meeting',
            'CHAN' => 'Change',
            'MRGR' => 'Merger',
            default => $code,
        };
    }

    /**
     * Translate Corporate Action Option (Tag 22F::CAOP).
     * Added for MT565 Sample.
     */
    public static function translateCorporateActionOption(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'CASH' => 'Receive Cash',
            'SECU' => 'Receive Securities',
            'OTHR' => 'Other',
            'NOAC' => 'No Action',
            'LAPS' => 'Lapse',
            'BOBD' => 'Bond Holder',
            'QINV' => 'Qualified Investor',
            'SLLE' => 'Sell Entitlement',
            default => $code,
        };
    }

    public static function translatePlaceOfTrade(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'EXCH' => 'Stock Exchange',
            'OTCO' => 'Over The Counter',
            'PRIM' => 'Primary Market',
            'SECM' => 'Secondary Market',
            'VARI' => 'Various',
            'BLOC' => 'Block Trade',
            default => $code,
        };
    }

    public static function translateBankOpCode(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'CRED' => 'Creditor Payment',
            'CRTS' => 'Test/Credit Transfer System',
            'SPAY' => 'SWIFT Payment',
            'SPRI' => 'Priority',
            'SSTD' => 'Standard',
            'TREA' => 'Treasury Payment',
            default => $code,
        };
    }

    public static function translateCharges(?string $code): ?string
    {
        $code = self::cleanCode($code);
        return match ($code) {
            'BEN' => 'Beneficiary',
            'OUR' => 'Our',
            'SHA' => 'Shared',
            default => $code,
        };
    }
}
