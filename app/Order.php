<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'to'];

    protected $dates = ['from', 'to'];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the location that owns the order.
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function setFromAttribute($date)
    {

        // $this->attributes['published_at'] = Carbon::createFromFormat('d-m-Y', $date);
        $this->attributes['from'] = \Carbon\Carbon::parse($date);
    }

    public function setToAttribute($date)
    {

        // $this->attributes['published_at'] = Carbon::createFromFormat('d-m-Y', $date);
        $this->attributes['to'] = \Carbon\Carbon::parse($date);
    }

    /**
     * Get the from date.
     *
     * @param  string  $value
     * @return string
     */
    public function getFromAttribute($date)
    {
        return \Carbon\Carbon::parse($date)->format('d-m-Y');
    }

    /**
     * Get the to date.
     *
     * @param  string  $value
     * @return string
     */
    public function getToAttribute($date)
    {
        return \Carbon\Carbon::parse($date)->format('d-m-Y');
    }
}
