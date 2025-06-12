<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case REPRESENTATIVE = 'representative';
    case DONOR = 'donor';
}
