<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
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
        $rp_arr = [];
        $user = \Auth::user();
        
        if ($user->role->name != 'guest') {
            $data = $request->json()->get('data');
            for ($i = 0; $i < count($data); $i++) {
                $order = new \App\Order();
                $from = Carbon::parse($data[$i]['from']);
                $to = Carbon::parse($data[$i]['to']);
                $order['from'] = $from;
                $order['to'] = $to;
                $order['location_id'] = \App\Location::where('name', $data[$i]['location'])->first()['id'];
                //$order['user_id'] = $user['id'];
                // count the similar records
                // if the num of them reaches upper limite then operation would be aborted
                $num_of_ordered = \App\Order::where('from', $from)->where('to', $to)->where('location_id', $order['location_id'])->count();
                $allowance = \App\Location::findOrFail($order['location_id'])->capacity;
                //dd($num_of_ordered);
                // $num_of_ordered can only be 0 or 1 in this case
                if ($num_of_ordered < $allowance) {
                    $user->orders()->save($order);
                    // Only one vacancy left before the saving statement.
                    if (($allowance - $num_of_ordered) <= 1)
                        array_push($rp_arr, ['from' => $order['from'], 'to' => $order['to'], 'status' => '0']);
                    else
                        array_push($rp_arr, ['from' => $order['from'], 'to' => $order['to'], 'status' => '1']);
                } /*else {
                    array_push($rp_arr, ['from' => $order['from'], 'to' => $order['to'], 'status' => '0']);
                }*/
            }

            /*if (\Auth::user()->orders()->save($order)) {

                // \Mail::send('emails.test', [], function ($m) {
                //     $m->from('soniczhangss@gmail.com', 'sonic');
                //     $m->to('soniczhangss@gmail.com', 'sonic')->subject('Your Reminder!');
                // });
                    return back()->withInput();

                // $headers = 'From: soniczhangss@gmail.com' . "\r\n" . 'Reply-To: soniczhangss@gmail.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
                // if (mail('soniczhangss@gmail.com', 'My Subject', 'blah blah', $headers)) {
                //     return response()->json(['response' => 'successful']);
                // } else {
                //     return response()->json(['response' => 'failed']);
                // }
                //dd(mail('soniczhangss@gmail.com', 'My Subject', 'blah blah', $headers));
                
                //return response()->json(['response' => 'successful']);
                
            }*/
        }

        //return back()->withInput();
        return response()->json(['data' => $rp_arr]);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $rp_arr = [];
        $user = \Auth::user();
        if ($user->role->name != ('admin' or 'root')) {
            // Permission denied
        } else {
            $data = $request->json()->get('data');
            $lookUpArr = [];
            for ($i = 0; $i < count($data); $i++) {
                /*$from = Carbon::parse($data[$i]['from']);
                $to = Carbon::parse($data[$i]['to']);
                $location_id = \App\Location::where('name', $data[$i]['location'])->first()['id'];

                \App\Order::where('from', $from)->where('to', $to)->where('location_id', $location_id)->delete();*/
                $id = $data[$i]['id'];
                $order = \App\Order::findOrFail($id);
                array_push($lookUpArr, ['from' => $order->from, 'to' => $order->to, 'location_id' => $order->location_id]);
                \App\Order::destroy($id);
            }

            $lookUpArr = array_unique($lookUpArr, SORT_REGULAR);

            foreach ($lookUpArr as $value) {
                $from = Carbon::parse($value['from']);
                $to = Carbon::parse($value['to']);
                $location_id = $value['location_id'];
                $num_of_ordered = \App\Order::where('from', $from)->where('to', $to)->where('location_id', $location_id)->count();
                $allowance = \App\Location::findOrFail($location_id)->capacity;

                if ($num_of_ordered < $allowance) {
                    if ($num_of_ordered == 0) {
                        array_push($rp_arr, ['from' => $value['from'], 'to' => $value['to'], 'status' => '-1']);
                    } else {
                        array_push($rp_arr, ['from' => $value['from'], 'to' => $value['to'], 'status' => '1']);
                    }
                } else {
                    array_push($rp_arr, ['from' => $value['from'], 'to' => $value['to'], 'status' => '0']);
                }
            }
            
        }
        return response()->json(['data' => $rp_arr]);
    }
}
