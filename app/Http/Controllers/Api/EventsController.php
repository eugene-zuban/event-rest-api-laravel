<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventRepository;
use App\Transformers\EventTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventsController extends Controller
{
    /**
     * @var \App\Transformers\EventTransformer
     */
    protected $transformer;

    /**
     * @var \App\EventRepository
     */
    protected $repository;

    /**
     * @param \App\Transformers\EventTransformer $transformer
     * @param \App\EventRepository $repository
     */
    public function __construct(
        EventTransformer $transformer,
        EventRepository $repository
    ) {
        $this->transformer = $transformer;

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allEvents =
            $this->repository->getAllEvents()->map(function (Event $event) {
                return $this->transformer->transform($event);
            });

        return response()->json($allEvents);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->repository->createNewEventFromArray($request->all());

        return response()->json(['status' => 'Event has been created.'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $event = $this->repository->getEventById($id);

        return response()->json($this->transformer->transform($event));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->repository->updateEventFromArrayById($id, $request->all());


        return response()->json(['status' => 'Event has been updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->deleteEventById($id);

        return response()->json(['status' => 'Event has been deleted.']);
    }
}
