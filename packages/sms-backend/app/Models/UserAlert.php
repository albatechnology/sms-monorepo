<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;

/**
 * @mixin IdeHelperUserAlert
 */
class UserAlert extends Model
{
    use Auditable, HasFactory;

    public $table = 'user_alerts';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'alert_text',
        'alert_link',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
