<?php

namespace Dcat\Admin\Traits;

trait HasDateTimeFormatter
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }
}
