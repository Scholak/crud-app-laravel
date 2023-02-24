<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Contact\StoreRequest;
use App\Http\Requests\Contact\UpdateRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return response(Contact::whereUserId($request->user()->id)->paginate(10), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): Response
    {
        return Contact::create([...$request->validated(), 'user_id' => $request->user()->id]) ?
            response(['message' => 'Contact created successfully'], Response::HTTP_CREATED) :
            response(['message' => 'Contact could not created'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $contact = Contact::whereUserId($request->user()->id)->whereId($id)->first();

        return $contact ?
            response(['message' => 'Contact fetched successfully', 'contact' => $contact], Response::HTTP_OK) :
            response(['message' => 'Contact could not fetched'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        return Contact::whereUserId($request->user()->id)->whereId($id)->update($request->validated()) ?
            response(['message' => 'Contact updated successfully'], Response::HTTP_ACCEPTED) :
            response(['message' => 'Contact could not updated'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        return Contact::whereUserId($request->user()->id)->whereId($id)->delete() ?
            response([], Response::HTTP_NO_CONTENT) :
            response(['message' => 'Contact could not deleted'], Response::HTTP_BAD_REQUEST);
    }
}
