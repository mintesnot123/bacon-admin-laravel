@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Medicine</h2>

            </div>

            <div class="pull-right">

                

                @can('medicine-create')

                <a class="btn btn-success" href="{{ route('medicines.create') }}"> Create New Medicine</a>

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

            <!-- <th>Icon </th> -->

            <th>Medicine Name</th>

           <!--  <th>Medicine Detail</th> -->

             <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($medicines as $medicine)

	    <tr>

	        <td>{{ ++$i }}</td>

           <!--  <td><img src="{!! getenv('APP_URL').$medicine->icon !!}" width="80" ></td> -->

	        <td>{{ $medicine->name }}</td>

           <!--  <td>{{ $medicine->detail }}</td> -->

             <td>{{ $medicine->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('medicines.destroy',$medicine->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('medicines.show',$medicine->id) }}">Show</a>

                    @can('medicine-edit')

                    <a class="btn btn-primary" href="{{ route('medicines.edit',$medicine->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('medicine-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $medicines->links() !!}


@endsection