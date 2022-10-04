<?php

namespace App\Models;

use App\Traits\CustomCastEnums;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Traits\CastsEnums;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use HasFactory, CastsEnums, CustomCastEnums;

    public static ?string $crud_key = null;

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function getCrudKey(): string
    {
        if (!is_null(static::$crud_key)) {
            return static::$crud_key;
        }

        return lcfirst((string)Str::of(static::class)->afterLast('\\'));
    }

    public function getShowFields()
    {
        $fromFillable = collect($this->fillable)->filter(function ($element) {
            return !in_array($element, ['deleted_at', 'company_id']);
        });

        return [
            'id',
            ...$fromFillable->all()
        ];
    }

    public function propertyChanged(string $key): bool
    {
        $original = $this->getOriginal($key);
        $current  = $this->$key;

        if ($original instanceof Enum) {
            return $original->isNot($current);
        }

        if ($current instanceof Enum) {
            return $current->isNot($original);
        }

        return $this->getOriginal($key) != $this->$key;
    }

    public function debug($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
