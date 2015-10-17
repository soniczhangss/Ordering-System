<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'capacity'];

    /**
     * Get the orders for the location.
     */
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
