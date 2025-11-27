<?php

declare(strict_types=1);

namespace App\Rules;

use App\Helpers\Formatacao;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class CnpjCpfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $documento = Formatacao::retornarDigitos($value);

        if (mb_strlen($documento) === 11) {
            $this->validarCpf($documento) ? null : $fail($this->message());

            return;
        }

        if (mb_strlen($documento) === 14) {
            $this->validarCnpj($documento) ? null : $fail($this->message());

            return;
        }

        $fail($this->message());
    }

    public function validarCpf(string $documento): bool
    {

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $documento[$i] * (10 - $i);
        }

        $resto = $sum % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;

        if ((int) $documento[9] !== $digito) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $documento[$i] * (11 - $i);
        }

        $resto = $sum % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;

        return (int) $documento[10] === $digito;
    }

    public function validarCnpj(string $documento): bool
    {

        $soma = 0;
        $weight = 5;

        for ($i = 0; $i < 12; $i++) {
            $soma += (int) $documento[$i] * $weight;
            $weight = ($weight === 2) ? 9 : $weight - 1;
        }

        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;

        if ((int) $documento[12] !== $digito) {
            return false;
        }

        $soma = 0;
        $weight = 6;

        for ($i = 0; $i < 13; $i++) {
            $soma += (int) $documento[$i] * $weight;
            $weight = ($weight === 2) ? 9 : $weight - 1;
        }

        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;

        return (int) $documento[13] === $digito;
    }

    public function message(): string
    {
        return __('validation.not_regex');
    }
}
