@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Charges</h2>

            </div>

            <div class="pull-right">

                @can('Appointment_charge-create')

                <a class="btn btn-success" href="{{ route('appointment_charges.create') }}"> Create New Charge</a>

                @endcan

            </div>

        </div>

    </div>


    @if ($message = Session::get('success'))

        <div class="alert alert-success">

            <p>{{ $message }}</p>

        </div>

    @endif

    @if ($message = Session::get('error'))

        <div class="alert alert-danger">

            <p>{{ $message }}</p>

        </div>

    @endif


    <table class="table table-bordered">

        <tr>

            <th>No</th>

            <th>Doctor Name</th>

            <th>Amount</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($charges as $charge)

	    <tr>

	        <td>{{ ++$i }}</td>

            <td>{{ $charge->doctor->name }}</td>


	        <td>{{ $charge->amount }}</td>

	       

             <td>{{ $charge->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('appointment_charges.destroy',$charge->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('appointment_charges.show',$charge->id) }}">Show</a>

                    @can('Appointment_charge-edit')

                    <a class="btn btn-primary" href="{{ route('appointment_charges.edit',$charge->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('Appointment_charge-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $charges->links() !!}

@endsection