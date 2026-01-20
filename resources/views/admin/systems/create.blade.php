@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nowy System</h1>
</div>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('admin.systems.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label class="form-label">Rodzina Systemu </label>
            <input type="text" name="name" class="input-standard" placeholder="np. Ubuntu" required><p>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="form-label">Wersja</label>
            <input type="text" name="version" class="input-standard" placeholder="np. 24.04 LTS" required>
        </div>

        <button type="submit" class="btn btn-primary">Dodaj System</button>
    </form>
</div>
@endsection