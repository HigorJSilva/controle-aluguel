<?php

declare(strict_types=1);

namespace App\Helpers;

class Formatacao
{

    public static function documento($value): string
    {
        $CPF_LENGTH = 11;
        $cnpjCpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpjCpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpjCpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpjCpf);
    }

    public static function telefone($value): string
    {
        $telefone = preg_replace("/\D/", '', $value);

        if (strlen($telefone) === 11) {
            return "(" . substr($telefone, 0, 2) . ") " . substr($telefone, 2, 5) . "-" . substr($telefone, 7, 11);
        }

        return  "(" . substr($telefone, 0, 2) . ") " . substr($telefone, 2, 4) . "-" . substr($telefone, 6, 10);
    }
}
