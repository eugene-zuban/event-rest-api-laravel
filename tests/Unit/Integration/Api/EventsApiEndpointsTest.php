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
        $firstEvent = factory(Event::class)->create();
        $secondEvent = factory(Event::class)->create();

        // Use API for getting all events
        $response = $this->json('GET', 'api/events');

        // Check that our new events have been returned using API
        $returnedTitles = Collection::make($response->json())->pluck('title');
        $this->assertTrue($returnedTitles->contains($firstEvent->title));
        $this->assertTrue($returnedTitles->contains($secondEvent->title));
    }

    /**
     * Test that we receive only one event because or defaul equal operator
     */
    public function testEqualImpactParameter()
    {
        $event = [
            'title' => 'Event with impact: 3300',
            'date' => Carbon::now('UTC')->toIso8601String(),
            'impact' => 3300,
            'instrument' => 'direct',
            'actual' => 12.32,
            'forecast' => 23.12,
        ];

        // create new event using API
        $postResponse = $this->json('POST', 'api/events', $event);
        $postResponse->assertStatus(201);

        // get the event using filters
        $getResponse = $this->json('GET', 'api/events', ['impact' => 3300]);
        $getResponse->assertStatus(200);

        // check that we received correct event
        $returnedEvents = Collection::make($getResponse->json());
        $this->assertTrue($returnedEvents->count() === 1);

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($event['title'])
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
        $event = [
            'title' => 'Event with impact: 32',
            'date' => Carbon::now('UTC')->toIso8601String(),
            'impact' => 32,
            'instrument' => 'direct',
            'actual' => 12.32,
            'forecast' => 23.12,
        ];

        // create new event using API
        $postResponse = $this->json('POST', 'api/events', $event);
        $postResponse->assertStatus(201);

        // get the event using filters
        $getResponse = $this->json('GET', 'api/events', ['impact' => '>=32']);
        $getResponse->assertStatus(200);

        // check that we received correct event
        $returnedEvents = Collection::make($getResponse->json());
        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($event['title'])
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
        $firstEvent = [
            'title' => 'Event for from date testing 1',
            'date' => Carbon::now('UTC')->nextWeekday()->toIso8601String(),
            'impact' => 0,
            'instrument' => 'custom',
            'actual' => 1.32,
            'forecast' => 1.12,
        ];

        $secondEvent = [
            'title' => 'Event for from date testing 2',
            'date' => Carbon::now('UTC')->toIso8601String(),
            'impact' => 0,
            'instrument' => 'custom',
            'actual' => 1.32,
            'forecast' => 1.12,
        ];

        $thirdEvent = [
            'title' => 'Event for from date testing 3',
            'date' => Carbon::now('UTC')->previousWeekday()->toIso8601String(),
            'impact' => 0,
            'instrument' => 'custom',
            'actual' => 1.32,
            'forecast' => 1.12,
        ];

        // post (create) new events
        $events = Collection::make([$firstEvent, $secondEvent, $thirdEvent]);
        $events->each(function ($event) {
            $this->json('POST', 'api/events', $event);
        });

        // get events using
        $getResponse = $this->json(
            'GET',
            'api/events',
            ['from_date' => Carbon::now('UTC')->toDateTimeString()]
        );

        // check that we received correct events
        $returnedEvents = Collection::make($getResponse->json());

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($firstEvent['title'])
        );

        $this->assertTrue(
            $returnedEvents->pluck('title')->contains($secondEvent['title'])
        );

        $this->assertFalse(
            $returnedEvents->pluck('title')->contains($thirdEvent['title'])
        );
    }

    /**
     * Test that we can update request
     */
    public function testThatWeCanUpdateEvent()
    {
        $event = factory(Event::class)->create();

        $response = $this->json('PUT', "api/events{$event->id}");
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
