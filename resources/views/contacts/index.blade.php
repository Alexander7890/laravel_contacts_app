@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
  <div class="row">
    <h1 class="space">Contacts</h1>
    <a href="{{ route('contacts_new') }}"><button class="primary">+ New contact</button></a>
  </div>

  <form method="get" class="toolbar">
    <input type="text" name="q" placeholder="Search..." value="{{ $q }}">
    <select name="group">
      <option value="">All groups</option>
      @foreach($groups as $g)
        <option value="{{ $g->id }}" @if($groupId == $g->id) selected @endif>{{ $g->name }}</option>
      @endforeach
    </select>

    <select name="perPage" onchange="this.form.submit()">
      @foreach([5,10,20,50] as $n)
        <option value="{{ $n }}" @if($perPage == $n) selected @endif>{{ $n }}</option>
      @endforeach
    </select>

    <button type="submit">Apply</button>
  </form>

  @if($total === 0)
    <p>No contacts found.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Group</th>
          <th width="140"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $c)
          <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->name }}</td>
            <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
            <td>{{ $c->phone ?: '—' }}</td>
            <td>
              @if($c->group)
                <span class="badge">{{ $c->group->name }}</span>
              @else
                —
              @endif
            </td>
            <td>
              <a href="{{ route('contacts_edit', ['id' => $c->id]) }}"><button type="button">Edit</button></a>
              <form method="post" action="{{ route('contacts_delete', ['id' => $c->id]) }}" style="display:inline"
                    onsubmit="return Modal.confirm('Delete contact &lt;strong&gt;{{ e($c->name) }}&lt;/strong&gt;? This action cannot be undone.', this);">
                @csrf
                <button type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="pagination">
      <div class="space"></div>
      <div>
        Page {{ $page }} of {{ $totalPages }}
        @if($page > 1)
          <a href="{{ route('contacts_index', array_merge(request()->query(), ['page' => $page - 1])) }}">Prev</a>
        @endif
        @if($page < $totalPages)
          <a href="{{ route('contacts_index', array_merge(request()->query(), ['page' => $page + 1])) }}">Next</a>
        @endif
      </div>
    </div>
  @endif
@endsection
