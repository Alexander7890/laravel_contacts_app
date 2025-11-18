@extends('layouts.app')

@section('title')
  {{ $isEdit ? 'Edit contact' : 'New contact' }}
@endsection

@section('content')
  <div class="row">
    <h1 class="space">{{ $isEdit ? 'Edit contact' : 'New contact' }}</h1>
    <a href="{{ route('contacts_index') }}"><button type="button">← Back</button></a>
  </div>

  <form method="post" class="row" style="gap:12px; align-items:stretch">
    @csrf
    <div style="flex:1; min-width:300px">
      <div class="row" style="gap:12px">
        <label style="flex:1">
          Name<br>
          <input type="text" name="name" required value="{{ old('name', $item->name) }}">
        </label>
        <label style="flex:1">
          Email<br>
          <input type="email" name="email" required value="{{ old('email', $item->email) }}">
        </label>
      </div>
      <div class="row" style="gap:12px; margin-top:8px">
        <label style="flex:1">
          Phone<br>
          <input type="text" name="phone" value="{{ old('phone', $item->phone) }}">
        </label>
        <label style="flex:1">
          Group<br>
          <select name="groupId">
            <option value="">No group</option>
            @foreach($groups as $g)
              <option value="{{ $g->id }}"
                @if(old('groupId', optional($item->group)->id) == $g->id) selected @endif>
                {{ $g->name }}
              </option>
            @endforeach
          </select>
        </label>
      </div>
      <div style="margin-top:8px">
        <label>
          Note<br>
          <textarea name="note" rows="4">{{ old('note', $item->note) }}</textarea>
        </label>
      </div>
      <div style="margin-top:12px">
        <button type="submit" class="primary">{{ $isEdit ? 'Save changes' : 'Create' }}</button>
      </div>
    </div>
  </form>
@endsection
