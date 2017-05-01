<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;

class IndexEventRequest extends EventRequest
{
    /**
     * Return where filters from the request Input data
     *
     * @return array
     */
    public function getFiltersFromParameters()
    {
        $whereFilters = [];

        if ($this->has('impact')) {
            $whereFilters[] = $this->makeImpactFilter($this->input('impact'));
        }

        if ($this->has('instrument')) {
            $whereFilters[] = ['instrument', '=', $this->input('instrument')];
        }

        if ($this->has('from_date')) {
            $whereFilters[] = [
                'date',
                '>=',
                $this->getDateTimeFromString($this->input('from_date')),
            ];
        }

        if ($this->has('to_date')) {
            $whereFilters[] = [
                'date',
                '<=',
                $this->getDateTimeFromString($this->input('to_date')),
            ];
        }

        return $whereFilters;
    }

    /**
     * Return DateTime string in UTC format.
     *
     * @param $string
     * @return string
     */
    protected function getDateTimeFromString($string)
    {
        return Carbon::parse($string)->setTimezone('UTC')->toDateTimeString();
    }

    /**
     * Return array with Laravel where statement.
     *
     * Return ready to use where statement from the input string and
     * filtering out unsupported operators.
     *
     * @param string $input
     * @return array
     */
    protected function makeImpactFilter($input)
    {
        $supportedOperators = ['>', '>=', '<', '<=', '!=', '<>'];

        $operatorFromInput = preg_replace('/\d+/', '', $input);
        $number = preg_replace('/\D+/', '', $input);

        $operator = in_array($operatorFromInput, $supportedOperators) ?
            $operatorFromInput : '=';

        return ['impact', $operator, $number];
    }
}
