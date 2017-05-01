<?php

namespace tests\Unit\Integration\Api;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EventsApiEndpointsTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    protected function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::createFromFormat('Y-m-d H:i:s', '2017-04-29 20:00:00')
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * Test that GET request returns correct events from the DB.
     */
    public function testGetEvents()
    {
        // create two events and save them into the DB
        $events = factory(Event::class, 2)->create();

        // Use API for getting all events
        $response = $this->json('GET', 'api/events');

        // Check that our new events have been returned using API
        $returnedTitles = Collection::make($response->json())->pluck('title');

        $events->each(function (Event $event) use ($returnedTitles) {
            $this->assertTrue($returnedTitles->contains($event->getTitle()));
        });
    }

    /**
     * Test that we receive only one event because or default equal operator
     */
    public function testEqualImpactParameter()
    {
        /** @var Event $event */
        $event = factory(Event::class)->create();

        // get this event using filters
        $response = $this->json(
            'GET', 'api/events', ['impact' => $event->getImpact()]
        );

        $response->assertStatus(200);

        // check that we received correct event
        $returnedEvents = Collection::make($response->json());
        $this->assertTrue($returnedEvents->count() === 1);

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($event->getTitle())
        );
    }

    /**
     * Test Greater or Equal impact operator.
     *
     * We can have more than one event with response,
     * so we need to make sure that our event is returned too.
     */
    public function testGreaterOrEqualImpactParameter()
    {
        $event = factory(Event::class)->create();

        // get the event using filters
        $getResponse = $this->json(
            'GET', 'api/events', ['impact' => ">={$event->getImpact()}"]
        );

        $getResponse->assertStatus(200);

        // check that we received correct event
        $returnedEvents = Collection::make($getResponse->json());
        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($event->getTitle())
        );
    }

    /**
     * Test from_date parameter.
     *
     * We create three events with different dates,
     * and need to be sure that we receive only two of them.
     */
    public function testDateFromParameter()
    {
        // make 3 events
        $events = factory(Event::class, 3)->make();

        // make 3 different date times
        $dateTimes = [
            Carbon::now('UTC')->nextWeekday()->toIso8601String(),
            Carbon::now('UTC')->toIso8601String(),
            Carbon::now('UTC')->previousWeekday()->toIso8601String(),
        ];

        // attach custom time to each event, and save it into the DB
        $events->each(function (Event $event, $key) use ($dateTimes) {
            $event->setDate($dateTimes[$key]);
            $event->save();
        });

        // get events using API with from_date parameter
        $response = $this->json(
            'GET',
            'api/events',
            ['from_date' => Carbon::now('UTC')->toDateTimeString()]
        );

        // check that we received correct events
        $returnedEvents = Collection::make($response->json());

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($events[0]['title'])
        );

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($events[1]['title'])
        );

        $this->assertFalse(
            $returnedEvents->pluck('title')->contains($events[2]['title'])
        );
    }

    /**
     * Test that we can update request
     */
    public function testThatWeCanUpdateEvent()
    {
        /** @var Event $event */
        $event = factory(Event::class)->create();

        // update the title and push updated event to the server
        $event->setTitle('New Updated Title');

        $response =
            $this->json('PUT', "api/events/{$event->id}", $event->toArray());
        $response->assertStatus(200);

        // get the updated event, and check that it has been updated
        $response = $this->json('GET', "api/events/{$event->id}");
        $response->assertStatus(200);
        $returnedEvent = $response->json();
        $this->assertEquals($event->getTitle(), $returnedEvent['title']);
    }

    /**
     * Test that we can create new event with POST request
     * and that we can get it back using GET request.
     *
     * @dataProvider createNewEventsDataProvider
     * @param $eventData
     */
    public function testItCanCreateAndGetNewEvent($eventData)
    {
        // create new event
        $response = $this->json('POST', 'api/events', $eventData);
        $response->assertStatus(201);
        $createdEventId = $response->json()['created_event_id'];

        // get this event via API
        $response = $this->json('GET', "api/events/{$createdEventId}");
        $response->assertStatus(200)->assertJson($eventData);
    }

    /**
     * Provide events for testItCanCreateAndGetNewEvent.
     *
     * @return array
     */
    public function createNewEventsDataProvider()
    {
        return [
            [
                [
                    'title' => 'New event for testing',
                    'date' => Carbon::now('UTC')->toIso8601String(),
                    'impact' => 20,
                    'instrument' => 'direct',
                    'actual' => 316529122.69859,
                    'forecast' => 1813.516529,
                ],
            ],

            [
                [
                    'title' => 'Other event for testing',
                    'date' => Carbon::now('UTC')->nextWeekday()->toIso8601String(),
                    'impact' => 0,
                    'instrument' => 'indirect',
                    'actual' => 23.69859,
                    'forecast' => 1813.0,
                ],

            ],
        ];
    }
}
