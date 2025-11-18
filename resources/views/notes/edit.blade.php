@extends('layouts.app')

@section('title', $t['editModal'])

@section('content')
  <h1>{{ $t['editModal'] }}</h1>

  <form method="post">
    @csrf
    <div style="margin-bottom:8px;">
      <label>{{ $t['noteTitle'] }}</label>
      <input type="text" name="title" value="{{ old('title', $note->title) }}"/>
      @if(isset($errors['title']))
        <div class="error">{{ $errors['title'] }}</div>
      @endif
    </div>
    <div style="margin-bottom:8px;">
      <label>{{ $t['noteText'] }}</label>
      <textarea name="text" rows="6">{{ old('text', $note->text) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">{{ $lang === 'uk' ? 'Зберегти' : 'Save' }}</button>
    <a href="{{ route('notes_index', ['lang' => $lang]) }}" class="btn btn-secondary" style="text-decoration:none;">{{ $lang === 'uk' ? 'Назад' : 'Back' }}</a>
  </form>
@endsection
