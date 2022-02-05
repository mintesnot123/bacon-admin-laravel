@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Patient Appointments Management</h2>

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

            <th>Appointment #</th>

            <th>Appointment Date</th>

            <th>Doctor Name</th>

            <th>Patient Name</th>

            <th>Status Date</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($appointments as $appointment)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $appointment->appoint_no }}</td>

	        <td>@if($appointment->schedule){{ App\Appointment::schedule_date($appointment->appointment_date,$appointment->schedule->start_time)}} @endif</td>

            <td> <a target="_blank" href="{{ route('doctors.show',$appointment->doctor->id) }}">{{ $appointment->doctor->name }}</a></td>

            <td> <a target="_blank" href="{{ route('patients.show',$appointment->patient->id) }}">{{ $appointment->patient->name }}</a></td>

            <td>{{ $appointment->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('appointments.destroy',$appointment->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('appointments.show',$appointment->id) }}">Show</a>

                    <!-- @can('appointment-edit')

                    <a class="btn btn-primary" href="{{ route('appointments.edit',$appointment->id) }}">Edit</a>

                    @endcan -->


                    @csrf

                    @method('DELETE')

                    @can('appointment-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $appointments->links() !!}

@endsection