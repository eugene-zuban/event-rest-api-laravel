<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllEvents()
    {
        return Event::all();
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function getEventById($id)
    {
        return Event::findOrFail($id);
    }

    /**
     * @param array $eventData
     * @return bool
     */
    public function createNewEventFromArray($eventData)
    {
        return $this->fillEventWithDataFromArray(
            $this->getEmptyEvent(),
            $eventData
        )->save();
    }

    /**
     * Update Event specified by id
     *
     * @param int $id
     * @param array $eventData
     * @return bool
     */
    public function updateEventFromArrayById($id, $eventData)
    {
        $event = Event::findOrFail($id);

        return $this->fillEventWithDataFromArray($event, $eventData)->save();
    }

    /**
     * Delete event by its id.
     *
     * @param $id
     */
    public function deleteEventById($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
    }

    /**
     * @param \App\Event $event
     * @param $eventData
     * @return \App\Event
     */
    protected function fillEventWithDataFromArray(Event $event, $eventData)
    {
        $event->setTitle($eventData->title);
        $event->setDate($eventData->date);
        $event->setImpact($eventData->impact);
        $event->setInstrument($eventData->instrument);
        $event->setActual($eventData->actual);
        $event->setForecast($eventData->forecast);

        return $event;
    }

    /**
     * @return \App\Event
     */
    protected function getEmptyEvent()
    {
        return new Event();
    }
}
