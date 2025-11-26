<?php

declare(strict_types=1);

namespace App\Helpers;

final class Formatacao
{
    public static function documento($value): string
    {
        $CPF_LENGTH = 11;
        $cnpjCpf = preg_replace("/\D/", '', (string) $value);

        if (mb_strlen($cnpjCpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $cnpjCpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', $cnpjCpf);
    }

    public static function telefone($value): string
    {
        $telefone = preg_replace("/\D/", '', (string) $value);

        if (mb_strlen($telefone) === 11) {
            return '(' . mb_substr($telefone, 0, 2) . ') ' . mb_substr($telefone, 2, 5) . '-' . mb_substr($telefone, 7, 11);
        }

        return '(' . mb_substr($telefone, 0, 2) . ') ' . mb_substr($telefone, 2, 4) . '-' . mb_substr($telefone, 6, 10);
    }

    public static function retornarDigitos(array | string $value)
    {
        if (is_array($value)) {
            return array_walk($value, function (&$item) {
                $item = preg_replace('/\D/', '', $item);
            });
        }

        return preg_replace('/\D/', '', $value);
    }
}
