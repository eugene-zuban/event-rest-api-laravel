<?php

namespace App\Transformers;

use App\Event;

class EventTransformer
{
    /**
     * Transforms Event model data into array.
     *
     * @param \App\Event $event
     * @return array
     */
    public function transform(Event $event)
    {
        return [
            'title' => $event->getTitle(),
            'date' => $event->getIsoDate(),
            'impact' => $event->getImpact(),
            'instrument' => $event->getInstrument(),
            'actual' => $event->getActual(),
            'forecast' => $event->getForecast(),
        ];
    }
}
