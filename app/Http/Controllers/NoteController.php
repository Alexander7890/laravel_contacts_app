<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function home(): RedirectResponse
    {
        return redirect()->route('notes_index');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $lang = $request->query('lang', 'uk');
        $translations = $this->translations($lang);

        $search = (string) $request->query('q', '');
        $sortBy = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, (int) $request->query('perPage', 6));

        $formTitle = '';
        $formText = '';
        $errors = [];

        if ($request->isMethod('post') && $request->has('create')) {
            $formTitle = trim((string) $request->input('title', ''));
            $formText = trim((string) $request->input('text', ''));

            if ($formTitle === '') {
                $errors['title'] = $translations['titleRequired'];
            }

            if (!$errors) {
                Note::create([
                    'title' => $formTitle,
                    'text' => $formText !== '' ? $formText : null,
                ]);

                return redirect()->route('notes_index', [
                    'lang' => $lang,
                    'q' => $search,
                    'sort' => $sortBy,
                    'dir' => $direction,
                    'page' => $page,
                    'perPage' => $perPage,
                ]);
            }
        }

        $query = Note::query();

        if ($search !== '') {
            $term = mb_strtolower($search);
            $query->where(function ($sub) use ($term) {
                $sub->whereRaw('LOWER(title) LIKE ?', ['%' . $term . '%'])
                    ->orWhereRaw('LOWER(text) LIKE ?', ['%' . $term . '%']);
            });
        }

        $allowedSorts = [
            'createdat' => 'created_at',
            'created_at' => 'created_at',
            'title' => 'title',
            'id' => 'id',
        ];
        $sortKey = strtolower($sortBy);
        $sortColumn = $allowedSorts[$sortKey] ?? 'created_at';

        $total = $query->count();
        $maxPage = max(1, (int) ceil($total / $perPage));
        if ($page > $maxPage) {
            $page = $maxPage;
        }

        $items = $query
            ->orderBy($sortColumn, $direction)
            ->forPage($page, $perPage)
            ->get();

        return view('notes.index', [
            'notes' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'sort' => $sortColumn,
            'dir' => $direction,
            'lang' => $lang,
            't' => $translations,
            'errors' => $errors,
            'form_title' => $formTitle,
            'form_text' => $formText,
        ]);
    }

    public function edit(int $id, Request $request): View|RedirectResponse
    {
        $lang = $request->query('lang', 'uk');
        $translations = $this->translations($lang);

        $note = Note::findOrFail($id);
        $errors = [];

        if ($request->isMethod('post')) {
            $title = trim((string) $request->input('title', ''));
            $text = trim((string) $request->input('text', ''));

            if ($title === '') {
                $errors['title'] = $translations['titleRequired'];
            }

            if (!$errors) {
                $note->title = $title;
                $note->text = $text !== '' ? $text : null;
                $note->save();

                return redirect()->route('notes_index', ['lang' => $lang]);
            }
        }

        return view('notes.edit', [
            'note' => $note,
            'lang' => $lang,
            't' => $translations,
            'errors' => $errors,
        ]);
    }

    public function delete(int $id, Request $request): RedirectResponse
    {
        $lang = $request->query('lang', 'uk');
        $note = Note::find($id);

        if ($note) {
            $note->delete();
        }

        return redirect()->route('notes_index', ['lang' => $lang]);
    }

    public function massDelete(Request $request): RedirectResponse
    {
        $lang = $request->query('lang', 'uk');
        $ids = $request->input('ids', []);

        if (is_array($ids) && $ids) {
            Note::whereIn('id', $ids)->delete();
        }

        return redirect()->route('notes_index', ['lang' => $lang]);
    }

    /**
     * @return array<string, string>
     */
    private function translations(string $lang): array
    {
        $common = [
            'deleteIcon' => '🗑',
            'editIcon' => '✏️',
        ];

        $uk = [
            'title' => 'Менеджер нотаток — CRUD (Laravel)',
            'searchPlaceholder' => 'Пошук по заголовку або тексту...',
            'find' => 'Знайти',
            'clear' => 'Очистити',
            'sortBy' => 'Сортувати за:',
            'date' => 'Дата',
            'addNote' => 'Додати',
            'titleRequired' => 'Заголовок обовʼязковий',
            'noteTitle' => 'Заголовок',
            'noteText' => 'Текст нотатки',
            'noteNotFound' => 'Нотаток не знайдено.',
            'edit' => '✏️ Редагувати',
            'delete' => '🗑',
            'editModal' => 'Редагувати нотатку',
            'deleteModal' => 'Видалити нотатку?',
            'massDelete' => 'Масове видалення',
            'confirmDelete' => 'Ви збираєтесь видалити нотатку',
            'confirmMassDelete' => 'Ви впевнені, що хочете видалити',
            'loading' => 'Завантаження…',
        ];

        $en = [
            'title' => 'Notes Manager — CRUD (Laravel)',
            'searchPlaceholder' => 'Search by title or text...',
            'find' => 'Find',
            'clear' => 'Clear',
            'sortBy' => 'Sort by:',
            'date' => 'Date',
            'addNote' => 'Add',
            'titleRequired' => 'Title is required',
            'noteTitle' => 'Title',
            'noteText' => 'Note text',
            'noteNotFound' => 'No notes found.',
            'edit' => '✏️ Edit',
            'delete' => '🗑',
            'editModal' => 'Edit note',
            'deleteModal' => 'Delete note?',
            'massDelete' => 'Mass delete',
            'confirmDelete' => 'You are about to delete note',
            'confirmMassDelete' => 'Are you sure you want to delete',
            'loading' => 'Loading…',
        ];

        return $lang === 'uk' ? array_merge($common, $uk) : array_merge($common, $en);
    }
}
