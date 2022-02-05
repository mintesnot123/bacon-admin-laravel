@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2>Patients Management</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-success" href="{{ route('patients.create') }}"> Create New Patient</a>

        </div>

    </div>

</div>


@if ($message = Session::get('success'))

<div class="alert alert-success">

  <p>{{ $message }}</p>

</div>

@endif


<table class="table table-bordered">

 <tr>

   <th>No</th>

   <th>Name</th>

   <!-- <th>Email</th> -->

   <th>Mobile No</th>

   <th>Status</th>

   <th>Roles</th>

   <th width="280px">Action</th>

 </tr>

 @foreach ($data as $key => $user)

  <tr>

    <td>{{ ++$i }}</td>

    <td>{{ $user->name }}</td>

    <!-- <td>{{ $user->email }}</td> -->

    <td>{{ $user->mobile_no }}</td>

    <td>{{ $user->status?'Active':'Inactive' }}</td>

    <td>

      @if(!empty($user->getRoleNames()))

        @foreach($user->getRoleNames() as $v)

           <label class="badge badge-success">{{ $v }}</label>

        @endforeach

      @endif

    </td>

    <td>

       <a class="btn btn-info" href="{{ route('patients.show',$user->id) }}">Show</a>

        @can('patient-edit')

          <a class="btn btn-primary" href="{{ route('patients.edit',$user->id) }}">Edit</a>
        @endcan

        {!! Form::open(['method' => 'DELETE','route' => ['patients.destroy', $user->id],'style'=>'display:inline']) !!}

         @can('patient-delete')

            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
         @endcan

        {!! Form::close() !!}

    </td>

  </tr>

 @endforeach

</table>


{!! $data->render() !!}




@endsection