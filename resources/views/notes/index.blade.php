@extends('layouts.app')

@section('title', $t['title'])

@section('content')
  <h1>{{ $t['title'] }}</h1>

  <form method="get" action="{{ route('notes_index') }}" style="display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; align-items:center;">
    <input
      type="text"
      name="q"
      placeholder="{{ $t['searchPlaceholder'] }}"
      value="{{ $search }}"
    />
    <input type="hidden" name="lang" value="{{ $lang }}"/>
    <button type="submit" class="btn btn-secondary">{{ $t['find'] }}</button>
    <a href="{{ route('notes_index', ['lang' => $lang]) }}" class="btn btn-secondary" style="text-decoration:none; display:inline-block; padding:6px 10px;">{{ $t['clear'] }}</a>

    <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
      <button type="button" class="btn" onclick="toggleTheme()">
        {{ $lang === 'uk' ? '🌗 Тема' : '🌗 Theme' }}
      </button>

      <select name="lang" onchange="this.form.submit()">
        <option value="uk" @if($lang === 'uk') selected @endif>🇺🇦 Укр</option>
        <option value="en" @if($lang === 'en') selected @endif>🇬🇧 Eng</option>
      </select>

      <label style="font-size:13px;">{{ $t['sortBy'] }}</label>
      <select name="sort" onchange="this.form.submit()">
        <option value="created_at" @if($sort === 'created_at') selected @endif>{{ $t['date'] }}</option>
        <option value="title" @if($sort === 'title') selected @endif>{{ $t['noteTitle'] }}</option>
      </select>
      <select name="dir" onchange="this.form.submit()">
        <option value="desc" @if($dir === 'desc') selected @endif>↓</option>
        <option value="asc" @if($dir === 'asc') selected @endif>↑</option>
      </select>
    </div>
  </form>

  <form method="post" action="{{ route('notes_index', ['lang' => $lang, 'q' => $search, 'sort' => $sort, 'dir' => $dir, 'page' => $page, 'perPage' => $perPage]) }}" style="display:grid; gap:8px; margin-bottom:16px;">
    @csrf
    <input type="hidden" name="lang" value="{{ $lang }}"/>
    <input type="hidden" name="create" value="1"/>
    <div>
      <input type="text" name="title" placeholder="{{ $t['noteTitle'] }} *" value="{{ $form_title }}"/>
      @if(isset($errors['title']))
        <div class="error">{{ $errors['title'] }}</div>
      @endif
    </div>
    <textarea name="text" rows="6" placeholder="{{ $t['noteText'] }}">{{ $form_text }}</textarea>
    <button type="submit" class="btn btn-primary">{{ $t['addNote'] }}</button>
  </form>

  @if(count($notes) > 0)
    <form id="mass-delete-form" method="post" action="{{ route('notes_mass_delete', ['lang' => $lang]) }}">
      @csrf
    </form>

    <table>
      <thead>
        <tr>
          <th></th>
          <th>{{ $t['noteTitle'] }}</th>
          <th>{{ $t['noteText'] }}</th>
          <th>{{ $t['date'] }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @foreach($notes as $note)
        <tr>
          <td>
            <input type="checkbox" name="ids[]" value="{{ $note->id }}" form="mass-delete-form"/>
          </td>
          <td><strong>{{ $note->title }}</strong></td>
          <td>
            @php $text = $note->text ?? '—'; @endphp
            @if(mb_strlen($text) > 180)
              {{ mb_substr($text, 0, 180) . '…' }}
            @else
              {{ $text }}
            @endif
          </td>
          <td>{{ optional($note->created_at)->format('Y-m-d H:i') }}</td>
          <td style="display:flex; gap:6px;">
            <a href="{{ route('notes_edit', ['id' => $note->id, 'lang' => $lang]) }}" class="btn btn-secondary" style="text-decoration:none;">{{ $t['edit'] }}</a>
            <form method="post" action="{{ route('notes_delete', ['id' => $note->id, 'lang' => $lang]) }}" onsubmit="return confirm('{{ $t['confirmDelete'] }} #{{ $note->id }}?');">
              @csrf
              <button type="submit" class="btn btn-danger">{{ $t['delete'] }}</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>

    <div style="margin-top:8px;">
      <button type="submit" class="btn btn-danger" form="mass-delete-form">{{ $t['massDelete'] }}</button>
    </div>
  @else
    <p>{{ $t['noteNotFound'] }}</p>
  @endif

  @if(isset($setupError))
    <div class="error" style="margin-top:12px;">{{ $setupError }}</div>
  @endif

  <div class="pagination">
    @php $maxPage = max(1, (int) ceil($total / $perPage)); @endphp
    <span>
      {{ $lang === 'uk' ? 'Сторінка' : 'Page' }} {{ $page }} / {{ $maxPage }}
    </span>
    @if($page > 1)
      <a href="{{ route('notes_index', ['lang' => $lang, 'q' => $search, 'sort' => $sort, 'dir' => $dir, 'page' => $page - 1, 'perPage' => $perPage]) }}" class="btn btn-secondary" style="text-decoration:none;">«</a>
    @endif
    @if($page < $maxPage)
      <a href="{{ route('notes_index', ['lang' => $lang, 'q' => $search, 'sort' => $sort, 'dir' => $dir, 'page' => $page + 1, 'perPage' => $perPage]) }}" class="btn btn-secondary" style="text-decoration:none;">»</a>
    @endif
    <form method="get" action="{{ route('notes_index') }}" style="margin-left:16px;">
      <input type="hidden" name="lang" value="{{ $lang }}"/>
      <input type="hidden" name="q" value="{{ $search }}"/>
      <input type="hidden" name="sort" value="{{ $sort }}"/>
      <input type="hidden" name="dir" value="{{ $dir }}"/>
      <label>
        {{ $lang === 'uk' ? 'На сторінці:' : 'Per page:' }}
        <select name="perPage" onchange="this.form.submit()">
          @foreach([3,6,10,20] as $size)
            <option value="{{ $size }}" @if($perPage == $size) selected @endif>{{ $size }}</option>
          @endforeach
        </select>
      </label>
    </form>
  </div>
@endsection
