# event-rest-api-laravel
RESTFul API example using Laravel 5.4

Small example of RESTFul API for working with [Event](https://github.com/jack-zuban/event-rest-api-laravel/blob/master/app/Event.php) model (data object)
using [EventRepository](https://github.com/jack-zuban/event-rest-api-laravel/blob/master/app/EventRepository.php) for handling CRUD tasks and [EventTransformer](https://github.com/jack-zuban/event-rest-api-laravel/blob/master/app/Transformers/EventTransformer.php) for formattion JSON output.

### Supported requests:
- GET `api/events` return all events
- GET `api/events/{id}` return a specific event by its ID
- POST `api/events` post a new event
- PUT `api/events/{id}` update a specific event by ID
- DELETE `api/events/{id}` delete specified by ID event

### Tests
- [Event model test](https://github.com/jack-zuban/event-rest-api-laravel/blob/master/tests/Unit/App/EventTest.php) 
- [Integration test](https://github.com/jack-zuban/event-rest-api-laravel/blob/master/tests/Unit/Integration/Api/EventsApiEndpointsTest.php)
