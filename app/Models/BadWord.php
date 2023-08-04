<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BadWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'words',
        'language',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getWordsAsCollection(): Collection
    {
        return str($this->words)->explode(',')
        ->map(fn ($word) => trim(strtolower($word)))
        ->filter(fn ($word) => strlen($word) > 0)
        ;
    }

    public function getWordsAsArray(): array
    {
        return $this->getWordsAsCollection()->toArray();
    }
}
