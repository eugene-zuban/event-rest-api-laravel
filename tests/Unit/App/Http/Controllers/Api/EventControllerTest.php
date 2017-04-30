<?php

namespace tests\Unit\App\Http\Controllers\Api;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
     * Test that GET request returns all events from the DB.
     */
    public function testItGetsEventsUsingApiEndpoint()
    {
        // create two events and save them into the DB
        $firstEvent = factory(Event::class)->create();
        $secondEvent = factory(Event::class)->create();

        // Use API for getting all the events in JSON format
        $response = $this->json('GET', 'api/events');
        $response->assertStatus(200);

        if (($returnedEvents = count($response->json())) === 2) {
            // if events table was empty, we received only our created events
            // so we can check that we got them all
            $response->assertJson([
                ['title' => $firstEvent->title],
                ['title' => $secondEvent->title],
            ]);
        } else {
            // if events table was not empty before this test,
            // we just check that we received more than two elements
            $this->assertTrue($returnedEvents > 2);
        }
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

        // get this event via API
        $event = $response->json();
        $response = $this->json('GET', "api/events/{$event['id']}");
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
