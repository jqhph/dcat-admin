<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Models\OperationLog as OperationLogModel;

class OperationLog extends EloquentRepository
{
    protected $eloquentClass = OperationLogModel::class;
}
