<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentCategory: string implements HasLabel
{
    case PERJANJIAN_KERJASAMA = 'Surat Perjanjian Kerjasama';
    case PERMOHONAN = 'Surat Permohonan';
    case PENUNJUKAN_PENERIMA_KUASA = 'Surat Penunjukan Penerima Kuasa';
    case SK_PJ_PELAKSANA = 'SK PJ Pelaksana';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PERJANJIAN_KERJASAMA => 'Surat Perjanjian Kerjasama',
            self::PERMOHONAN => 'Surat Permohonan',
            self::PENUNJUKAN_PENERIMA_KUASA => 'Surat Penunjukan Penerima Kuasa',
            self::SK_PJ_PELAKSANA => 'SK PJ Pelaksana',
        };
    }
}
