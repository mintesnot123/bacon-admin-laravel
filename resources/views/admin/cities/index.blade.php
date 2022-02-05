@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Cities</h2>

            </div>

            <div class="pull-right">

                @can('city-create')

                <a class="btn btn-success" href="{{ route('cities.create') }}"> Create New City</a>

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

            <th>City Name</th>

            <th>Country Name</th>

            <th>Division Name</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($cities as $city)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $city->name }}</td>

	        <td>{{ $city->country->name }}</td>

            <td>{{ $city->division->name }}</td>

             <td>{{ $city->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('cities.destroy',$city->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('cities.show',$city->id) }}">Show</a>

                    @can('city-edit')

                    <a class="btn btn-primary" href="{{ route('cities.edit',$city->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('city-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>

    {!! $cities->links() !!}

@endsection