<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;

class PagesController extends Controller
{
    /**
     * Display all user accounts.
     *
     * @return Response
     */
    public function accountManagement()
    {
        return view('user_management');
    }

    /**
     * Display all user accounts.
     *
     * @return Response
     */
    public function accountManagementJson()
    {
        $users = \DB::table('users')->join('roles', 'users.role_id', '=', 'roles.id')->join('companies', 'companies.id', '=', 'users.company_id')->select('users.name AS username', 'companies.name AS company', 'users.email', 'roles.name')->get();
        //print_r($users);
        //$users = \App\User::all('name', 'role_id', 'email');
        //$users = \App\User::with('role')->get();
        //return view('home')->with('locations', $locations);
        return $users;
    }

    /**
     * Display the prefered type of calendar.
     *
     * @return Response
     */
    public function calendar($type, $date, $location = NULL)
    {
        if (isset($location)) {
            $location = str_replace("_", " ", $location);
        } else {
            $location = "Flinders Street Station";
        }
        
        $location_id = \App\Location::where('name', $location)->first()['id'];
        $location_capacity = \App\Location::where('name', $location)->first()['capacity'];
        $orders = NULL;
        $user = \Auth::user();
        $role = \App\Role::findOrFail($user['role_id'])['name'];
        /*$orders = new \Illuminate\Database\Eloquent\Collection;
        if ($role == 'staff') {
            // find orders of this company
            // select order from orders where user_id = (select id from user where company_id = $user[company_id])
            $colleagues = \App\User::where('company_id', $user['company_id'])->get();
            $colleagues->each(function ($colleague) use ($orders, $location_id) {
                // merge orders
                // $orders->add(\App\Order::where('user_id', $colleague->id)->where('location_id', $location_id)->get());
                $thierOrders = \App\Order::where('user_id', $colleague->id)->where('location_id', $location_id)->get();
                $thierOrders->each(function ($order) use ($orders) {
                    $orders->add($order);
                });
            });
        }*/
        if ($role != "guest") {
            $orders = \App\Order::where('location_id', $location_id)->get();
        }
        $date = new Carbon($date);
        switch ($type) {
            case 'year':
                return view('calendar_year')
                    ->with('date', $date)
                    ->with('orders', $orders)
                    ->with('capacity', $location_capacity);
                break;

            case 'month':
                return view('calendar_month')
                    ->with('date', $date)
                    ->with('orders', $orders)
                    ->with('capacity', $location_capacity);
                break;
            
            default:
                // sth went wrong
                break;
        }
    }

    /**
     * Get company create view.
     *
     * @return Response
     */
    public function companyCreate()
    {
        return view('reveal_modal_new_company');
    }

    /**
     * Get company view.
     *
     * @return Response
     */
    public function companyManagement()
    {
        return view('company_management');
    }

    /**
     * Get companies in Json format.
     *
     * @return Response
     */
    public function companyManagementJson()
    {
        $companies = \App\Company::all();
        return $companies;
    }

    /**
     * Update the information of a company.
     *
     * @return Response
     */
    public function companyUpdate()
    {
        
    }

    /**
     * Reset the email of an user.
     *
     * @return Response
     */
    public function emailReset(Request $request)
    {
        $email = $request->input('email');
        $user = \Auth::user();
        $user->update(['email' => $email]);
        return back()->withInput();
    }

    /**
     * Display the home page.
     *
     * @return Response
     */
    public function home()
    {
        $locations = \App\Location::all();
        return view('home')->with('locations', $locations);
    }

    /**
     * Get location capacity in json.
     *
     * @return Response
     */
    public function locationCapacity()
    {
        $location_capacity = \App\Location::where('name', $location)->first()['capacity'];
        return ;
    }

    /**
     * Get location view.
     *
     * @return Response
     */
    public function locationCreate()
    {
        return view('reveal_modal_new_location');
    }

    /**
     * Get location view.
     *
     * @return Response
     */
    public function locationManagement()
    {
        return view('location_management');
    }

    /**
     * Get locations in Json format.
     *
     * @return Response
     */
    public function locationManagementJson()
    {
        $locations = \App\Location::all();
        return $locations;
    }

    /**
     * Update the information of a location.
     *
     * @return Response
     */
    public function locationUpdate()
    {
        
    }

    /**
     * Get the role of the user.
     *
     * @param  array  $data
     * @return User
     */
    public function myRole() {
        $user = \Auth::user();
        $role = \App\Role::findOrFail($user['role_id']);
        return $role;
    }

    /**
     * Display the home page.
     *
     * @return Response
     */
    public function nav()
    {
        return view('calendar_nav');
    }


    /**
     * Display the reveal modal for order related actions.
     *
     * @return Response
     */
    public function order($action)
    {
        switch ($action) {
            case 'create':
                return view('reveal_modal');
                break;

            case 'delete':
                return view('reveal_modal_delete_orders');
                break;
            
            default:
                // sth went wrong
                break;
        }
    }

    /**
     * Find orders of a requird week and return in Json.
     *
     * @return Response
     */
    public function ordersDeleteJson(Request $request)
    {
        $rp_arr = [];
        // if admin
        $user = \Auth::user();
        if ($user->role->name != ('admin' or 'root')) {
            // Permission denied
        } else {
            $data = $request->json()->get('data');
            for ($i = 0; $i < count($data); $i++) {
                $from = Carbon::parse($data[$i]['from']);
                $to = Carbon::parse($data[$i]['to']);
                $location_id = \App\Location::where('name', $data[$i]['location'])->first()['id'];
                $response = \DB::table('orders')->where('from', '=', $from)->where('to', '=', $to)->where('location_id', '=', $location_id)->join('users', 'orders.user_id', '=', 'users.id')->join('companies', 'companies.id', '=', 'users.company_id')->join('locations', 'locations.id', '=', 'orders.location_id')->select('orders.id', 'users.name AS customer', 'companies.name AS company', 'from', 'to', 'locations.name As location', 'orders.created_at')->get();
                
                array_push($rp_arr, $response);
            }
        }
        return response()->json(['data' => $rp_arr]);
    }

    /**
     * Display the report.
     *
     * @return Response
     */
    public function report()
    {
        return view('report');
    }

    /**
     * Display all user accounts.
     *
     * @return Response
     */
    public function reportJson()
    {
        $report = \DB::table('orders')->join('users', 'orders.user_id', '=', 'users.id')->join('companies', 'companies.id', '=', 'users.company_id')->join('locations', 'locations.id', '=', 'orders.location_id')->select('orders.id', 'users.name AS customer', 'companies.name AS company', 'from', 'to', 'locations.name As location', 'orders.created_at')->get();
        return $report;
    }

    /**
     * Display the settings page.
     *
     * @return Response
     */
    public function settings()
    {
        return view('settings');
    }

    /**
     * Return the tooltips in Json.
     *
     * @return Response
     */
    public function tooltips(Request $request)
    {
        $rp_arr = [];
        $user = \Auth::user();
        $data = $request->json()->get('data');
        if ($user->role->name == ('admin' or 'root')) {
            for ($i = 0; $i < count($data); $i++) {
                $location_id = \App\Location::where('name', $data[$i]['location'])->first()['id'];
                $from = Carbon::parse($data[$i]['from']);
                $to = Carbon::parse($data[$i]['to']);
                $orders = \App\Order::where('from', $from)->where('to', $to)->where('location_id', $location_id)->get();
                $tooltip = '';
                for ($j = 0; $j < count($orders); $j++) {
                    $thisUser = \App\User::findOrFail($orders[$j]->user_id);
                    $company = \App\Company::findOrFail($thisUser->company_id);

                    if ($j != 0)
                        $tooltip .= "\r\n";
                    $tooltip .= "Ordered by " . $thisUser->name . " from " . $company->name . " at " . $orders[$j]->created_at;
                }
                array_push($rp_arr, ['from' => \Carbon\Carbon::parse($from)->format('d-m-Y'), 'to' => \Carbon\Carbon::parse($to)->format('d-m-Y'), 'location' => $data[$i]['location'], 'tooltip' => $tooltip]);
            }
        } elseif ($user->role->name == 'staff') {
            for ($i = 0; $i < count($data); $i++) {
                $location_id = \App\Location::where('name', $data[$i]['location'])->first()['id'];
                $from = Carbon::parse($data[$i]['from']);
                $to = Carbon::parse($data[$i]['to']);
                $orders = \App\Order::where('from', $from)->where('to', $to)->where('location_id', $location_id)->get();
                $tooltip = '';
                for ($j = 0; $j < count($orders); $j++) {
                    $thisUser = \App\User::findOrFail($orders[$j]->user_id);
                    if ($thisUser->company_id == $user->company_id) {
                        $company = \App\Company::findOrFail($thisUser->company_id);

                        if ($j != 0)
                            $tooltip .= "\r\n";
                        $tooltip .= "Ordered by " . $thisUser->name . " from " . $company->name . " at " . $orders[$j]->created_at;
                    }
                }
                array_push($rp_arr, ['from' => \Carbon\Carbon::parse($from)->format('d-m-Y'), 'to' => \Carbon\Carbon::parse($to)->format('d-m-Y'), 'location' => $data[$i]['location'], 'tooltip' => $tooltip]);
            }
        }

        return response()->json(['data' => $rp_arr]);
    }

    /**
     * Update the user info.
     *
     * @return Response
     */
    public function userUpdate(Request $request)
    {
        $role = \App\Role::where('name', $request->role)->first();
        $company = \App\Company::where('name', $request->company)->first();
        $user = \App\User::where('name', $request->name)
                          ->where('email', $request->email)
                          ->first();

        $user->role_id = $role->id;
        $user->company_id = $company->id;

        if ($user->save()) {
            return back()->withInput();
        }
        
    }

    /**
     * Reset password and email address.
     *
     * @return Response
     */
    public function userInfoUpdate(Request $request)
    {
        $user = \Auth::user();

        $user->password = $role->id;

        if ($user->save()) {
            return back()->withInput();
        }
        
    }
    
}
