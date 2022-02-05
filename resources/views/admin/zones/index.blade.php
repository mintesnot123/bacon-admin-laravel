@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Zone/Thana</h2>

            </div>

            <div class="pull-right">

                @can('zone-create')

                <a class="btn btn-success" href="{{ route('zones.create') }}"> Create New Zone</a>

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

            <th>Zone Name</th>

            <th>Country Name</th>

            <th>City Name</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($zones as $zone)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $zone->name }}</td>

            <td>{{ $zone->country->name }}</td>

            <td>{{ $zone->city->name }}</td>

	         <td>{{ $zone->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('zones.destroy',$zone->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('zones.show',$zone->id) }}">Show</a>

                    @can('zone-edit')

                    <a class="btn btn-primary" href="{{ route('zones.edit',$zone->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('zone-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>

    {!! $zones->links() !!}
    
@endsection