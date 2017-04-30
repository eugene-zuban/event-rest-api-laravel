<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'title', 'date', 'impact', 'instrument', 'actual', 'forecast',
    ];

    /**
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    /**
     * Gets date in ISO 8601 format.
     *
     * @return string
     */
    public function getIsoDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date, 'UTC')
            ->toIso8601String();
    }

    /**
     * Sets date.
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date =
            Carbon::parse($date)->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getImpact()
    {
        return (int) $this->impact;
    }

    /**
     * @param int $impact
     */
    public function setImpact($impact)
    {
        $this->impact = (int) $impact;
    }

    /**
     * @return string
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    /**
     * @param string $instrument
     */
    public function setInstrument($instrument)
    {
        $this->instrument = trim($instrument);
    }

    /**
     * @return float
     */
    public function getActual()
    {
        return (float) $this->actual;
    }

    /**
     * @param float|int $actual
     */
    public function setActual($actual)
    {
        $this->actual = (float) $actual;
    }

    /**
     * @return float
     */
    public function getForecast()
    {
        return (float) $this->forecast;
    }

    /**
     * @param float|int $forecast
     */
    public function setForecast($forecast)
    {
        $this->forecast = (float) $forecast;
    }
}
