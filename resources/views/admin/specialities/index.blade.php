@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Specialty</h2>

            </div>

            <div class="pull-right">

                @can('speciality-create')

                <a class="btn btn-success" href="{{ route('specialities.create') }}"> Create New Specialty</a>

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

            <th>Icon </th>

            <th>Specialty Name</th>

            

             <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($specialities as $speciality)

	    <tr>

	        <td>{{ ++$i }}</td>

            <td><img src="{!! getenv('APP_URL').$speciality->icon !!}" width="80" ></td>

	        <td>{{ $speciality->name }}</td>

             <td>{{ $speciality->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('specialities.destroy',$speciality->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('specialities.show',$speciality->id) }}">Show</a>

                    @can('speciality-edit')

                    <a class="btn btn-primary" href="{{ route('specialities.edit',$speciality->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('speciality-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $specialities->links() !!}


@endsection