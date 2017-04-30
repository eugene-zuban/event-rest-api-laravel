<?php

namespace App;

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
     * @return Event
     */
    public function getEventById($id)
    {
        return Event::findOrFail($id);
    }

    /**
     * Create and store new event using the specified data, and return it
     *
     * @param $eventData
     * @return Event
     */
    public function createNewEventFromArray($eventData)
    {
        $event = $this->fillEventWithDataFromArray(
            $this->getEmptyEvent(),
            $eventData
        );

        $event->save();

        return $event;
    }

    /**
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
     * @param int $id
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
        $event->setTitle($eventData['title']);
        $event->setDate($eventData['date']);
        $event->setImpact($eventData['impact'] ?? 0);
        $event->setInstrument($eventData['instrument'] ?? '');
        $event->setActual($eventData['actual'] ?? 0);
        $event->setForecast($eventData['forecast'] ?? 0);

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
