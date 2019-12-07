<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Models\OperationLog as OperationLogModel;
use Dcat\Admin\Repositories\EloquentRepository;

class OperationLog extends EloquentRepository
{
    protected $eloquentClass = OperationLogModel::class;
}
