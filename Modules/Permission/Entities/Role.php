<?php

namespace Modules\Permission\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasFactory;

    protected $fillable = [];
    



    // protected static function newFactory()
    // {
    //     return \Modules\Permission\Database\factories\RoleFactory::new();
    // }
}
