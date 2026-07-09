<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['kit_id', 'category_id', 'name', 'quantity', 'purchased'];

    protected $casts = ['purchased' => 'boolean'];

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
