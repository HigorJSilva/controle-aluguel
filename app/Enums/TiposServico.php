<?php

declare(strict_types=1);

namespace App\Enums;

enum TiposServico: string
{
    case ENCANADOR = 'Encanador';
    case ELETRICISTA = 'Eletricista';
    case PINTOR = 'Pintor';
    case PEDREIRO = 'Pedreiro';
    case DIARISTA = 'Faxineiro/Diarista';
    case JARDINEIRO = 'Jardineiro';
    case GERAIS = 'Serviços Gerais';
    case OUTRO = 'Outro';

    public static function all(string $key = 'id', string $value = 'name'): array
    {
        return [
            [$key => self::ENCANADOR, $value => self::ENCANADOR->label()],
            [$key => self::ELETRICISTA, $value => self::ELETRICISTA->label()],
            [$key => self::PINTOR, $value => self::PINTOR->label()],
            [$key => self::PEDREIRO, $value => self::PEDREIRO->label()],
            [$key => self::DIARISTA, $value => self::DIARISTA->label()],
            [$key => self::JARDINEIRO, $value => self::JARDINEIRO->label()],
            [$key => self::GERAIS, $value => self::GERAIS->label()],
            [$key => self::OUTRO, $value => self::OUTRO->label()],
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ENCANADOR => 'Encanador',
            self::ELETRICISTA => 'Eletricista',
            self::PINTOR => 'Pintor',
            self::PEDREIRO => 'Pedreiro',
            self::DIARISTA => 'Faxineiro/Diarista',
            self::JARDINEIRO => 'Jardineiro',
            self::GERAIS => 'Serviços Gerais',
            self::OUTRO => 'Outro',
        };
    }
}
