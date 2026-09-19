<?php

namespace App\Services;

/**
 * Service de conversion de montants numériques en lettres (Français).
 * Utilisé pour les bordereaux officiels d'assurance, factures et quittances.
 */
class NumberToWordsService
{
    private static array $unites = [
        0 => 'zéro',
        1 => 'un',
        2 => 'deux',
        3 => 'trois',
        4 => 'quatre',
        5 => 'cinq',
        6 => 'six',
        7 => 'sept',
        8 => 'huit',
        9 => 'neuf',
        10 => 'dix',
        11 => 'onze',
        12 => 'douze',
        13 => 'treize',
        14 => 'quatorze',
        15 => 'quinze',
        16 => 'seize',
    ];

    private static array $dizaines = [
        1 => 'dix',
        2 => 'vingt',
        3 => 'trente',
        4 => 'quarante',
        5 => 'cinquante',
        6 => 'soixante',
    ];

    /**
     * Convertit un nombre entier en texte français.
     */
    public static function convert(int|float $number): string
    {
        $number = (int) round($number);

        if ($number === 0) {
            return 'zéro';
        }

        if ($number < 0) {
            return 'moins '.self::convert(abs($number));
        }

        $parts = [];

        // Milliards
        if ($number >= 1000000000) {
            $milliards = (int) floor($number / 1000000000);
            $parts[] = self::convertCentaines($milliards).' '.($milliards > 1 ? 'milliards' : 'milliard');
            $number %= 1000000000;
        }

        // Millions
        if ($number >= 1000000) {
            $millions = (int) floor($number / 1000000);
            $parts[] = self::convertCentaines($millions).' '.($millions > 1 ? 'millions' : 'million');
            $number %= 1000000;
        }

        // Milliers
        if ($number >= 1000) {
            $mille = (int) floor($number / 1000);
            if ($mille === 1) {
                $parts[] = 'mille';
            } else {
                $parts[] = self::convertCentaines($mille).' mille';
            }
            $number %= 1000;
        }

        // Reste (< 1000)
        if ($number > 0) {
            $parts[] = self::convertCentaines($number);
        }

        return trim(implode(' ', $parts));
    }

    /**
     * Convertit un montant avec devise (ex: Francs CFA).
     */
    public static function toCurrency(int|float $number, string $devise = 'Francs CFA'): string
    {
        $texte = ucfirst(self::convert($number));

        return trim($texte.' '.$devise);
    }

    /**
     * Convertit un nombre de 0 à 999.
     */
    private static function convertCentaines(int $n): string
    {
        if ($n === 0) {
            return '';
        }

        $res = [];

        if ($n >= 100) {
            $c = (int) floor($n / 100);
            $resteCent = $n % 100;

            if ($c === 1) {
                $res[] = 'cent';
            } else {
                $res[] = self::$unites[$c].($resteCent === 0 ? ' cents' : ' cent');
            }
            $n = $resteCent;
        }

        if ($n > 0) {
            $res[] = self::convertDizaines($n);
        }

        return trim(implode(' ', $res));
    }

    /**
     * Convertit un nombre de 1 à 99.
     */
    private static function convertDizaines(int $n): string
    {
        if ($n <= 16) {
            return self::$unites[$n];
        }

        if ($n < 20) {
            return 'dix-'.self::$unites[$n - 10];
        }

        if ($n < 70) {
            $d = (int) floor($n / 10);
            $u = $n % 10;
            $dizaineNom = self::$dizaines[$d];

            if ($u === 0) {
                return $dizaineNom;
            }
            if ($u === 1) {
                return $dizaineNom.'-et-un';
            }

            return $dizaineNom.'-'.self::$unites[$u];
        }

        if ($n < 80) {
            $reste = $n - 60;
            if ($reste === 11) {
                return 'soixante-et-onze';
            }

            return 'soixante-'.self::convertDizaines($reste);
        }

        if ($n < 100) {
            $reste = $n - 80;
            if ($reste === 0) {
                return 'quatre-vingts';
            }
            if ($reste === 1) {
                return 'quatre-vingt-un';
            }
            if ($reste < 20) {
                return 'quatre-vingt-'.self::convertDizaines($reste);
            }
        }

        return (string) $n;
    }
}
