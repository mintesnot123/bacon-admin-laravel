@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Scheduling</h2>

            </div>

            <div class="pull-right">

                @can('scheduling-create')

                <a class="btn btn-success" href="{{ route('schedulings.create') }}"> Create New Scheduling</a>

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

            <th>Day Name</th>

            <th> Slot Name</th>

            <th> Start Time</th>

            <th>Duration</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($schedulings as $scheduling)

	    <tr>

	        <td>{{ ++$i }}</td>

            <td>{{ $scheduling->doctor->name }}</td>

	        <td>{{ App\Scheduling::dayofweek($scheduling->day_id) }}</td>

	        <td>{{ $scheduling->slot_name }}</td>

            <td>{{ $scheduling->start_time }}</td>

            <td>{{ $scheduling->slot_duration.' mins.' }}</td>

            <td>{{ $scheduling->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('schedulings.destroy',$scheduling->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('schedulings.show',$scheduling->id) }}">Show</a>

                    @can('scheduling-edit')

                    <a class="btn btn-primary" href="{{ route('schedulings.edit',$scheduling->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('scheduling-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $schedulings->links() !!}




@endsection