<?php  

namespace App\Http\Controllers;   

use App\Payment;

use Illuminate\Http\Request;

use Session;
   

class PaymentController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:payment-list|payment-create|payment-edit|payment-delete', ['only' => ['index','show']]);

         $this->middleware('permission:payment-create', ['only' => ['create','store']]);

         $this->middleware('permission:payment-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:payment-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {


        $search = array_filter($request->all());
        unset($search['_token']);

        if($request->has('keyword')){
           Session::put("search", $search);
           $search = (object) Session::get('search'); 
        }else{
          $search = (object) Session::get('search'); 
        }



        $payments = new Payment;

        if(isset($search->keyword)){

           $search = $search->keyword;
           $payments = $payments->where(function($q) use ($search){
                  $q->orWhere('payments.invoice_no', 'like', '%'.$search.'%')
                 // ->orWhere('users.payments', 'like', '%'.$search.'%') 
                ->orWhere('payments.amount', 'like', '%'.$search.'%'); 
            });

        }

        $payments = $payments->latest()->paginate(20);


        return view('admin.payments.index',compact('payments'))

            ->with('i', (request()->input('page', 1) - 1) * 20);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.payments.create');

    }

    

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        request()->validate([

            'name' => 'required',

            'detail' => 'required',

        ]);

    

        Payment::create($request->all());

    

        return redirect()->route('payments.index')

                        ->with('success','Payment created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Payment  $payment

     * @return \Illuminate\Http\Response

     */

    public function show(Payment $payment)

    {

        return view('admin.payments.show',compact('payment'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Payment  $payment

     * @return \Illuminate\Http\Response

     */

    public function edit(Payment $payment)

    {

        return view('admin.payments.edit',compact('payment'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Payment  $payment

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, Payment $payment)

    {

         request()->validate([

            'name' => 'required',

            'detail' => 'required',

        ]);

    

        $payment->update($request->all());

    

        return redirect()->route('payments.index')

                        ->with('success','Payment updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Payment  $payment

     * @return \Illuminate\Http\Response

     */

    public function destroy(Payment $payment)

    {
 
        $payment->delete();
        return redirect()->route('payments.index')
        ->with('success','Payment deleted successfully');

    }

}
