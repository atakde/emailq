<?php

namespace EmailQ\Services;

use EmailQ\Models\TemplateModel;

class TemplateService
{
    public function getByName(string $name)
    {
        return TemplateModel::where('name', $name)->first();
    }
}
