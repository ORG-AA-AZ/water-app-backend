<?php

namespace App\Enums;

enum PlaceOfLocation: string
{
    case Home = 'home';
    case Work = 'work';
    case Other = 'other';
}
