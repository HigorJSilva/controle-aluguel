<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

final class Formatacao
{
    public static function documento($value): string
    {
        $CPF_LENGTH = 11;
        $cnpjCpf = preg_replace("/\D/", '', (string) $value);

        if (mb_strlen((string) $cnpjCpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', (string) $cnpjCpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', (string) $cnpjCpf);
    }

    public static function telefone($value): string
    {
        $telefone = preg_replace("/\D/", '', (string) $value);

        if (mb_strlen((string) $telefone) === 11) {
            return '(' . mb_substr((string) $telefone, 0, 2) . ') ' . mb_substr((string) $telefone, 2, 5) . '-' . mb_substr((string) $telefone, 7, 11);
        }

        return '(' . mb_substr((string) $telefone, 0, 2) . ') ' . mb_substr((string) $telefone, 2, 4) . '-' . mb_substr((string) $telefone, 6, 10);
    }

    public static function dinheiro($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    public static function retornarDigitos(array|string $value): array|string|null
    {
        if (is_array($value)) {
            array_walk($value, function (&$item): void {
                $item = empty($item) ? $item : preg_replace('/\D/', '', $item);
            });

            return $value;
        }

        return preg_replace('/\D/', '', $value);
    }

    public static function data(string $value): string
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }

    public static function dataMesAno(string $dataReferencia): string
    {
        return ucfirst(Carbon::parse($dataReferencia)->locale(app()->getLocale())->translatedFormat('F/Y'));
    }

    public static function dataAno(string $dataReferencia): string
    {
        return ucfirst(Carbon::parse($dataReferencia)->locale(app()->getLocale())->translatedFormat('F'));
    }
}
