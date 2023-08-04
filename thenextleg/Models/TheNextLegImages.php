<?php

namespace TheNextLeg\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheNextLegImages extends Model
{
    use HasFactory;

    public function butonData(){
        return $this->hasOne(TheNextLeg::class, 'id', 'thenextleg_id');
    }
}
