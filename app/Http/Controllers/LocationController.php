<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();

        if ($user->role->name == 'admin' || $user->role->name == 'root') {
            $location = new \App\Location();
            $data = $request->json()->get('data');
            $location->name = $data[0]['location'];
            $location->capacity = $data[0]['allowance'];
            $location->save();
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        $data = $request->json()->get('data');
        $locationOld = $data[0]['locationOld'];
        $location = $data[0]['location'];
        $allowance = $data[0]['allowance'];

        $locationRec = \App\Location::where('name', $locationOld);
        if ($allowance == '') {
            $locationRec->update(['name' => $location]);
        } else {
            $locationRec->update(['name' => $location, 'capacity' => $allowance]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
