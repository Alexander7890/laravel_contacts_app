<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $groupId = $request->query('group');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(100, (int) $request->query('perPage', 10)));

        $query = Contact::query()->with('group');

        if ($q) {
            $qLower = mb_strtolower($q);
            $query->where(function ($sub) use ($qLower) {
                $sub->whereRaw('LOWER(name) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . $qLower . '%'])
                    ->orWhereRaw('LOWER(note) LIKE ?', ['%' . $qLower . '%']);
            });
        }

        if ($groupId) {
            $query->where('group_id', (int) $groupId);
        }

        $query->orderByDesc('id');

        $total = $query->count();
        $contacts = $query->forPage($page, $perPage)->get();

        $groups = Group::orderBy('name')->get();
        $totalPages = (int) ceil($total / $perPage);

        return view('contacts.index', [
            'items'      => $contacts,
            'total'      => $total,
            'groups'     => $groups,
            'q'          => $q,
            'groupId'    => $groupId ? (int) $groupId : null,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $this->validateForm($request);

            $contact = new Contact();
            $this->hydrate($contact, $validated);

            $contact->save();

            return redirect()->route('contacts_index');
        }

        $groups = Group::orderBy('name')->get();

        $item = new Contact();

        return view('contacts.form', [
            'item'   => $item,
            'groups' => $groups,
            'isEdit' => false,
        ]);
    }

    public function edit(int $id, Request $request)
    {
        $contact = Contact::findOrFail($id);

        if ($request->isMethod('post')) {
            $validated = $this->validateForm($request);

            $this->hydrate($contact, $validated);
            $contact->save();

            return redirect()->route('contacts_index');
        }

        $groups = Group::orderBy('name')->get();

        return view('contacts.form', [
            'item'   => $contact,
            'groups' => $groups,
            'isEdit' => true,
        ]);
    }

    public function delete(int $id, Request $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            $contact = Contact::find($id);
            if ($contact) {
                $contact->delete();
            }
        }

        return redirect()->route('contacts_index');
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    private function validateForm(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'string', 'email', 'max:180'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'note'    => ['nullable', 'string'],
            'groupId' => ['nullable', 'integer', 'exists:contact_groups,id'],
        ]);
    }

    /**
     * @param Contact               $contact
     * @param array<string, mixed>  $data
     * @return void
     */
    private function hydrate(Contact $contact, array $data): void
    {
        $contact->name = (string) ($data['name'] ?? '');
        $contact->email = (string) ($data['email'] ?? '');
        $contact->phone = $data['phone'] !== null && $data['phone'] !== '' ? (string) $data['phone'] : null;
        $contact->note = $data['note'] !== null && $data['note'] !== '' ? (string) $data['note'] : null;

        $gid = $data['groupId'] ?? null;
        $contact->group_id = $gid ? (int) $gid : null;
    }
}
