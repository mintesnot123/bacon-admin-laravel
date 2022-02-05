@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Advertisement</h2>

            </div>

            <div class="pull-right">

                @can('advertisement-create')

                <a class="btn btn-success" href="{{ route('advertisements.create') }}"> Create New Advertisement</a>

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

            <th>Image </th>

            <th>Advertisement Name</th>

            

             <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($advertisements as $advertisement)

	    <tr>

	        <td>{{ ++$i }}</td>

            <td><img src="{!! getenv('APP_URL').$advertisement->image !!}" width="80" ></td>

	        <td>{{ $advertisement->name }}</td>

             <td>{{ $advertisement->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('advertisements.destroy',$advertisement->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('advertisements.show',$advertisement->id) }}">Show</a>

                    @can('advertisement-edit')

                    <a class="btn btn-primary" href="{{ route('advertisements.edit',$advertisement->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('advertisement-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $advertisements->links() !!}


@endsection