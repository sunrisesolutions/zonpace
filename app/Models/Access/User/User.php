<?php

namespace App\Models\Access\User;

use App\Contracts\Presentable;
use App\Models\Access\User\Traits\UserAccess;
use App\Traits\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Access\User\Traits\Attribute\UserAttribute;
use App\Models\Access\User\Traits\Relationship\UserRelationship;

/**
 * Class User
 * @package App\Models\Access\User
 */
class User extends Authenticatable implements Presentable
{

    use SoftDeletes, UserAccess, UserAttribute, UserRelationship, PresentableTrait;

    /**
     * @var \App\Presenters\User
     */
    protected $presenter = \App\Presenters\User::class;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
}
