<?php

namespace App\Enums;

enum CarFuel: string
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Electric = 'electric';
    case Hybrid = 'hybrid';
    case Gas = 'gas';
}
