@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Manage {{ $user->name }}</div>

                <div class="card-body">
                    <form action="{{ route('boss.users.update', ['user'=>$user->id]) }}" method="POST">
                        @csrf
                        <!-- update method use PUT request -->
                        {{ method_field('PUT') }}
                        
                        <!-- make a checkbox to select roles -->
                        @foreach($roles as $role)
                            <div class="form-check">
                                <!-- check the box if user already has the role -->
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ $user->hasAnyRole($role->name)? 'checked':'' }}>
                                <label>{{ $role->name }}</label>
                                </input>
                            </div>
                        @endforeach
                        <!-- Button to submit data to update method -->
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
