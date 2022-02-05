@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Permissions</h2>

            </div>

            <div class="pull-right">

                @can('role-permission-create')

                <a class="btn btn-success" href="{{ route('role-permissions.create') }}"> Create New Permission</a>

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

            <th>Name</th>

            <th>Guard Name</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($permissions as $permission)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $permission->name }}</td>

	        <td>{{ $permission->guard_name }}</td>

	        <td>

                <form action="{{ route('role-permissions.destroy',$permission->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('role-permissions.show',$permission->id) }}">Show</a>

                    @can('role-permission-edit')

                    <a class="btn btn-primary" href="{{ route('role-permissions.edit',$permission->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('role-permission-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $permissions->links() !!}




@endsection