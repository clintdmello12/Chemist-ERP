<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Gate;

use App\User;
use App\Vendor;
use App\Inventory;
use App\Sale;
use App\Purchase;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index');

        $months = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $d['current_user'] = auth()->user();
        $d['users']        = User::all();
        $d['vendors']      = Vendor::all();
        $d['sales']        = Sale::all();
        $d['purchases']    = Purchase::all();
        $d['inventories']  = Inventory::whereDate('expiry_date', '<', date('Y-m-d'))->where('quantity', '>', 0)->get();

        $today             = Carbon::now();

        $currentMonth      = $today->month;

        $monthsOld = array_where($months, function ($month, $id) use($currentMonth){
            return $id > $currentMonth;
        });
        $monthsNew = array_where($months, function ($month, $id) use($currentMonth){
            return $id <= $currentMonth;
        });

        $arrangeMonths = array_collapse([$monthsOld, $monthsNew]);

        $oneYearOld    = $today->subYear()->format('Y-m-d');

        $yearSales     = Sale::whereDate('created_at', '>', $oneYearOld)
                        ->get()
                        ->map(function($a){
                            $a->month = Carbon::parse($a->created_at)->format('M');
                            return $a;
                        });

        $monthlySale      = [];
        $monthlySaleCount = [];

        foreach ($arrangeMonths as $month) {
            $monthSale = $yearSales->where('month', $month);
            if($monthSale->isEmpty()){
                $monthlySale[]      = 0;
                $monthlySaleCount[] = 0;
            }
            else{
                $monthlySale[]      = $monthSale->sum('price');
                $monthlySaleCount[] = $monthSale->count();
            }
        }

        $d['months']           = $arrangeMonths;
        $d['monthlySales']     = $monthlySale;
        $d['monthlySaleCount'] = $monthlySaleCount;

        return view('home', $d);
    }
}
