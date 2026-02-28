<?php

namespace App\Support;

class MontantEnLettres
{
    private const UNITS = [
        0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre',
        5 => 'cinq', 6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf',
        10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze',
        15 => 'quinze', 16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf',
    ];

    private const TENS = [
        2 => 'vingt', 3 => 'trente', 4 => 'quarante', 5 => 'cinquante', 6 => 'soixante',
        7 => 'soixante', 8 => 'quatre-vingt', 9 => 'quatre-vingt',
    ];

    /**
     * Convertit un montant numérique en lettres (français, dinars et centimes).
     * Ex: 1234.56 → "mille deux cent trente-quatre dinars et cinquante-six centimes"
     */
    public static function toWords(float $montant): string
    {
        $montant = round((float) $montant, 2);
        if ($montant < 0) {
            return 'zéro dinar';
        }

        $partieEntiere = (int) floor($montant);
        $centimes = (int) round(($montant - $partieEntiere) * 100);

        $lettres = self::nombreEnLettres($partieEntiere);

        if ($partieEntiere === 0 && $centimes === 0) {
            return 'zéro dinar';
        }

        $dinars = $partieEntiere === 1 ? 'dinar' : 'dinars';
        $str = trim($lettres) . ' ' . $dinars;

        if ($centimes > 0) {
            $centimesLettres = self::nombreEnLettres($centimes);
            $centime = $centimes === 1 ? 'centime' : 'centimes';
            $str .= ' et ' . trim($centimesLettres) . ' ' . $centime;
        }

        return $str;
    }

    private static function nombreEnLettres(int $n): string
    {
        if ($n === 0) {
            return '';
        }
        if ($n < 20) {
            return self::UNITS[$n];
        }
        if ($n < 100) {
            return self::dizainesEtUnites($n);
        }
        if ($n < 1000) {
            return self::centaines($n);
        }
        if ($n < 1_000_000) {
            return self::milliers($n);
        }
        if ($n < 1_000_000_000) {
            return self::millions($n);
        }

        return self::milliards($n);
    }

    private static function dizainesEtUnites(int $n): string
    {
        $dizaine = (int) floor($n / 10);
        $unite = $n % 10;

        $base = self::TENS[$dizaine];

        if ($dizaine === 7) {
            if ($unite === 0) {
                return 'soixante-dix';
            }
            return 'soixante-' . self::UNITS[10 + $unite];
        }
        if ($dizaine === 9) {
            if ($unite === 0) {
                return 'quatre-vingt-dix';
            }
            return 'quatre-vingt-' . self::UNITS[10 + $unite];
        }
        if ($dizaine === 8) {
            if ($unite === 0) {
                return 'quatre-vingts';
            }
            return 'quatre-vingt-' . ($unite === 1 ? 'un' : self::UNITS[$unite]);
        }
        if ($unite === 0) {
            return $base;
        }
        if ($dizaine === 2 || $dizaine === 3 || $dizaine === 4 || $dizaine === 5 || $dizaine === 6) {
            $liaison = ($dizaine === 2 || $dizaine === 3) && $unite === 1 ? ' et ' : '-';
            return $base . $liaison . ($unite === 1 ? 'un' : self::UNITS[$unite]);
        }

        return $base . '-' . ($unite === 1 ? 'un' : self::UNITS[$unite]);
    }

    private static function centaines(int $n): string
    {
        $c = (int) floor($n / 100);
        $reste = $n % 100;
        if ($c === 0) {
            return self::nombreEnLettres($reste);
        }
        // « cent » au singulier pour 100, « cents » (avec s) pour 200, 300, … 900 (ex. : mille quatre cents quarante)
        $cent = $c === 1 ? 'cent' : self::UNITS[$c] . ' cents';
        if ($reste === 0) {
            return $cent;
        }
        return $cent . ' ' . self::nombreEnLettres($reste);
    }

    private static function milliers(int $n): string
    {
        $m = (int) floor($n / 1000);
        $reste = $n % 1000;
        $mille = $m === 1 ? 'mille' : self::nombreEnLettres($m) . ' mille';
        if ($reste === 0) {
            return $mille;
        }
        return $mille . ' ' . self::centaines($reste);
    }

    private static function millions(int $n): string
    {
        $m = (int) floor($n / 1_000_000);
        $reste = $n % 1_000_000;
        $million = $m === 1 ? 'un million' : self::nombreEnLettres($m) . ' millions';
        if ($reste === 0) {
            return $million;
        }
        return $million . ' ' . self::milliers($reste);
    }

    private static function milliards(int $n): string
    {
        $m = (int) floor($n / 1_000_000_000);
        $reste = $n % 1_000_000_000;
        $milliard = $m === 1 ? 'un milliard' : self::nombreEnLettres($m) . ' milliards';
        if ($reste === 0) {
            return $milliard;
        }
        return $milliard . ' ' . self::millions($reste);
    }
}
