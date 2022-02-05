@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Divisions</h2>

            </div>

            <div class="pull-right">

                @can('division-create')

                <a class="btn btn-success" href="{{ route('divisions.create') }}"> Create New Division</a>

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

            <th>Division Name</th>

            <th>Country Name</th>

            <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($divisions as $division)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $division->name }}</td>

	        <td>{{ $division->country->name }}</td>

             <td>{{ $division->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('divisions.destroy',$division->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('divisions.show',$division->id) }}">Show</a>

                    @can('division-edit')

                    <a class="btn btn-primary" href="{{ route('divisions.edit',$division->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('division-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $divisions->links() !!}

@endsection