@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Country</h2>

            </div>

            <div class="pull-right">

                @can('country-create')

                <a class="btn btn-success" href="{{ route('countries.create') }}"> Create New Country</a>

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

            <th>Country Name</th>

            <th>Code Name</th>

            <th>Prefix Name</th>

             <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($countries as $country)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $country->name }}</td>

	        <td>{{ $country->code }}</td>

            <td>{{ $country->prefix }}</td>

             <td>{{ $country->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('countries.destroy',$country->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('countries.show',$country->id) }}">Show</a>

                    @can('country-edit')

                    <a class="btn btn-primary" href="{{ route('countries.edit',$country->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('country-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $countries->links() !!}




@endsection