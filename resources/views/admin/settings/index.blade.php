@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Settings</h2>

            </div>

            <div class="pull-right">

                @can('site-setting-create')

                <a class="btn btn-success" href="{{ route('settings.create') }}"> Create New Setting</a>

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

            <th>Label Name</th>

             <th>Field Name</th> 

            <th>Field Value</th>

             <th>Status</th>

            <th width="280px">Action</th>

        </tr>

	    @foreach ($settings as $setting)

	    <tr>

	        <td>{{ ++$i }}</td>

	        <td>{{ $setting->label_name }}</td>

	         <td>{{ $setting->field_name }}</td> 

            <td>{{ $setting->field_value }}</td>

             <td>{{ $setting->status?'Active':'Inactive' }}</td>

	        <td>

                <form action="{{ route('settings.destroy',$setting->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('settings.show',$setting->id) }}">Show</a>

                    @can('site-setting-edit')

                    <a class="btn btn-primary" href="{{ route('settings.edit',$setting->id) }}">Edit</a>

                    @endcan


                    @csrf

                    @method('DELETE')

                    @can('site-setting-delete')

                    <button type="submit" class="btn btn-danger">Delete</button>

                    @endcan

                </form>

	        </td>

	    </tr>

	    @endforeach

    </table>


    {!! $settings->links() !!}




@endsection