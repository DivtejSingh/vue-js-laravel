@extends('layouts.userapp')

@section('content')
    <div class="pa-2">
        <!-- {{-- Pass the entire `business` object as a prop to the Vue component --}} -->
        <admin-businesslist-edit></admin-businesslist-edit>
    </div>
@endsection
