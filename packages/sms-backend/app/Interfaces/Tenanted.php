<?php


namespace App\Interfaces;


interface Tenanted
{
    public function scopeTenanted($query);

    public function scopeFindTenanted($query, int $id);
}
