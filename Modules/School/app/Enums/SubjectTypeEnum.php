<?php

namespace Modules\School\Enums;

enum SubjectTypeEnum: string
{
    case Compulsory = 'compulsory';
    case Optional = 'optional';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
