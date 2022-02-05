@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Payments Management</h2>

            </div>

           

        </div>

    </div>


    @if ($message = Session::get('success'))

        <div class="alert alert-success">

            <p>{{ $message }}</p>

        </div>

    @endif


    <table class="table table-bordered">

        <tr>

            <th>No</th>

            <th>Created Date</th>

            <th>Invoice #</th>

            <th>Appointment #</th>

            <th>Payment Status</th>

            <th>Amount</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($payments as $payment)

	    <tr>

	        <td>{{ ++$i }}</td>

            <td>{{ $payment->created_at }}</td>

	        <td>{{ $payment->invoice_no }}</td>

            <td> <a target="_blank" href="{{ route('appointments.show',$payment->appointment->id) }}">{{$payment->appointment->appoint_no}}</a></td>

	        <td>
                @if($payment->payment_type==1) {!!'Payment Pending'!!}@endif
                @if($payment->payment_type==2) {!!'Payment Completed'!!}@endif
                @if($payment->payment_type==3) {!!'Payment Cancelled'!!}@endif
            </td>

            <td>{{ $payment->amount }}</td>

            <td>{{ $payment->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('payments.destroy',$payment->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('payments.show',$payment->id) }}">Show</a>

                    <!-- @can('payment-edit')

                    <a class="btn btn-primary" href="{{ route('payments.edit',$payment->id) }}">Edit</a>

                    @endcan -->


                    @csrf

                    @method('DELETE')

                    @can('payment-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $payments->links() !!}




@endsection