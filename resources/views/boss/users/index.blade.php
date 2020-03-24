@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Vimigo Team</div>

                <div class="card-body">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Roles</th>
                        <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- loop through users here -->
                        @foreach($users as $user)
                            <tr>
                                <th>{{ $user->name }}</th>
                                <th>{{ $user->email }}</th>
                                <th>{{ implode(', ', $user->roles()->get()->pluck('name')->toArray()) }}</th>
                                <th>
                                    <!-- so that every button is link to each id -->
                                    <a href="{{ route('boss.users.edit', $user->id) }}" class="float-left">
                                        <button type="button" class="btn btn-primary btn-sm">
                                            Edit
                                        </button>
                                    </a>
                                    <!-- delete button submit to destroy method -->
                                    <form action="{{ route('boss.users.destroy', $user->id) }}" method="POST" class="float-left">
                                        {{ method_field('DELETE') }}
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </form>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
