<?php

namespace App\Http\Requests\Api;

class StoreUpdateEventRequest extends EventRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'date' => 'required|date',
            'impact' => 'integer',
            'instrument' => 'string',
            'actual' => 'numeric',
            'forecast' => 'numeric',
        ];
    }
}
