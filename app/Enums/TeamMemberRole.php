<?php

namespace App\Enums;

enum TeamMemberRole: string
{
    case Lead = 'lead';
    case Developer = 'developer';
    case Designer = 'designer';
    case Manager = 'manager';
}
