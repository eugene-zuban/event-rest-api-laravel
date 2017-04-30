<?php

namespace tests\Unit\App\Http\Controllers\Api;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    protected function setUp()
    {
        parent::setUp();

        // set testing date
        Carbon::setTestNow(
            Carbon::createFromFormat('Y-m-d H:i:s', '2017-04-29 20:00:00')
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow(); // restore Carbon
    }

    /**
     * Test that GET request returns events from the DB.
     */
    public function testItGetsEventsUsingApiEndpoint()
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

    public function testThatImpactFilterWorksForGetEventsApiEndpoint()
    {
        $event = [
            'title' => 'Event with impact: 2200',
            'date' => Carbon::now('UTC')->toIso8601String(),
            'impact' => 2200,
            'instrument' => 'direct',
            'actual' => 316529122.69859,
            'forecast' => 1813.516529,
        ];

        $postResponse = $this->json('POST', 'api/events', $event);
        $postResponse->assertStatus(201);

        $getResponse = $this->json('GET', 'api/events', ['impact' => '2200']);


        $getResponse->assertStatus(200);

        $returnedEvents = Collection::make($getResponse->json());
        $this->assertTrue($returnedEvents->count() === 1);
    }

    /**
     * Test that we can create new event with POST request
     * and that we can get it back using GET request.
     */
    public function testItCanCreateAndGetNewEventUsingApi()
    {
        $eventData = $this->getDataForNewEvent();

        // create new event
        $response = $this->json('POST', 'api/events', $eventData);
        $response->assertStatus(201);
        $createdEventId = $response->json()['created_event_id'];

        // get this event via API
        $response = $this->json('GET', "api/events/{$createdEventId}");
        $response->assertStatus(200)->assertJson($eventData);
    }

    /**
     * @return array
     */
    protected function getDataForNewEvent()
    {
        return [
            'title' => 'New event for testing',
            'date' => Carbon::now('UTC')->toIso8601String(),
            'impact' => 20,
            'instrument' => 'direct',
            'actual' => 316529122.69859,
            'forecast' => 1813.516529,
        ];
    }
}
