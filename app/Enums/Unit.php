<?php

namespace App\Enums;

enum Unit: string
{
    case Piece = 'piece';
    case Kg = 'kg';
    case Litre = 'litre';
    case Carton = 'carton';
    case Sachet = 'sachet';
    case Paquet = 'paquet';
    case Sac = 'sac';

    public function label(): string
    {
        return match ($this) {
            self::Piece => 'pièce',
            self::Kg => 'kg',
            self::Litre => 'litre',
            self::Carton => 'carton',
            self::Sachet => 'sachet',
            self::Paquet => 'paquet',
            self::Sac => 'sac',
        };
    }
}
