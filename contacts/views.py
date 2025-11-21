from typing import Any, Dict, List

from django.db import OperationalError
from django.db.models import Q
from django.http import HttpRequest, HttpResponse
from django.shortcuts import redirect
from django.urls import reverse_lazy
from django.utils.http import urlencode
from django.views import generic

from .forms import NoteForm
from .models import Note


def translations(lang: str) -> Dict[str, str]:
    common = {
        'deleteIcon': '🗑',
        'editIcon': '✏️',
        'notesTableMissing': 'The notes table is missing. Run python manage.py migrate before using the app.',
        'notesTableMissingUk': 'Таблиця notes відсутня. Запустіть python manage.py migrate перед використанням застосунку.',
    }

    uk = {
        'title': 'Менеджер нотаток — CRUD (Django)',
        'searchPlaceholder': 'Пошук по заголовку або тексту...',
        'find': 'Знайти',
        'clear': 'Очистити',
        'sortBy': 'Сортувати за:',
        'date': 'Дата',
        'addNote': 'Додати',
        'titleRequired': 'Заголовок обовʼязковий',
        'noteTitle': 'Заголовок',
        'noteText': 'Текст нотатки',
        'noteNotFound': 'Нотаток не знайдено.',
        'edit': '✏️ Редагувати',
        'delete': '🗑',
        'editModal': 'Редагувати нотатку',
        'deleteModal': 'Видалити нотатку?',
        'massDelete': 'Масове видалення',
        'confirmDelete': 'Ви збираєтесь видалити нотатку',
        'confirmMassDelete': 'Ви впевнені, що хочете видалити',
        'loading': 'Завантаження…',
        'perPage': 'На сторінці:',
        'page': 'Сторінка',
        'back': 'Назад',
        'save': 'Зберегти',
    }

    en = {
        'title': 'Notes Manager — CRUD (Django)',
        'searchPlaceholder': 'Search by title or text...',
        'find': 'Find',
        'clear': 'Clear',
        'sortBy': 'Sort by:',
        'date': 'Date',
        'addNote': 'Add',
        'titleRequired': 'Title is required',
        'noteTitle': 'Title',
        'noteText': 'Note text',
        'noteNotFound': 'No notes found.',
        'edit': '✏️ Edit',
        'delete': '🗑',
        'editModal': 'Edit note',
        'deleteModal': 'Delete note?',
        'massDelete': 'Mass delete',
        'confirmDelete': 'You are about to delete note',
        'confirmMassDelete': 'Are you sure you want to delete',
        'loading': 'Loading…',
        'perPage': 'Per page:',
        'page': 'Page',
        'back': 'Back',
        'save': 'Save',
    }

    return {**common, **(uk if lang == 'uk' else en)}


class NoteListView(generic.ListView):
    model = Note
    template_name = 'contacts/note_list.html'
    context_object_name = 'notes'
    paginate_by = 6

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.table_missing: bool = False
        self.form_errors: Dict[str, str] = {}
        self.form_data: Dict[str, str] = {'title': '', 'text': ''}
        self.lang: str = 'uk'

    def dispatch(self, request: HttpRequest, *args: Any, **kwargs: Any) -> HttpResponse:
        self.lang = request.POST.get('lang') or request.GET.get('lang') or 'uk'
        return super().dispatch(request, *args, **kwargs)

    def get_paginate_by(self, queryset):
        per_page = self.request.GET.get('perPage')
        try:
            return max(1, int(per_page)) if per_page else self.paginate_by
        except (TypeError, ValueError):
            return self.paginate_by

    def get_queryset(self):
        queryset = Note.objects.all()
        search_term = self.request.GET.get('q', '').strip()
        sort = self.request.GET.get('sort', 'created_at').lower()
        direction = self.request.GET.get('dir', 'desc').lower()

        if search_term:
            queryset = queryset.filter(Q(title__icontains=search_term) | Q(text__icontains=search_term))

        allowed_sorts = {
            'created_at': 'created_at',
            'title': 'title',
        }
        sort_field = allowed_sorts.get(sort, 'created_at')
        prefix = '' if direction == 'asc' else '-'
        try:
            return queryset.order_by(f"{prefix}{sort_field}")
        except OperationalError:
            self.table_missing = True
            return Note.objects.none()

    def post(self, request: HttpRequest, *args: Any, **kwargs: Any) -> HttpResponse:
        action = request.POST.get('action')
        redirect_url = self._build_redirect_url(request)

        if action == 'create':
            form = NoteForm(request.POST)
            if form.is_valid():
                try:
                    form.save()
                    return redirect_url
                except OperationalError:
                    self.table_missing = True
            else:
                title_error = translations(self.lang)['titleRequired'] if 'title' in form.errors else ''
                self.form_errors = {field: ' '.join(err_list) for field, err_list in form.errors.items()}
                if title_error:
                    self.form_errors['title'] = title_error
                self.form_data = {
                    'title': form.data.get('title', ''),
                    'text': form.data.get('text', ''),
                }
                return self.get(request, *args, **kwargs)

        if action == 'mass_delete':
            ids: List[str] = request.POST.getlist('ids')
            if ids:
                try:
                    Note.objects.filter(id__in=ids).delete()
                except OperationalError:
                    self.table_missing = True
            return redirect_url

        return redirect_url

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        translations_dict = translations(self.lang)
        per_page = self.get_paginate_by(self.get_queryset())
        context.update(
            {
                'lang': self.lang,
                't': translations_dict,
                'search': self.request.GET.get('q', ''),
                'sort': self.request.GET.get('sort', 'created_at'),
                'dir': self.request.GET.get('dir', 'desc'),
                'per_page': per_page,
                'page_sizes': [3, 6, 10, 20],
                'form_title': self.form_data.get('title', ''),
                'form_text': self.form_data.get('text', ''),
                'errors': self.form_errors,
                'setup_error': (
                    translations_dict['notesTableMissingUk']
                    if self.table_missing and self.lang == 'uk'
                    else translations_dict['notesTableMissing'] if self.table_missing else ''
                ),
            }
        )
        return context

    def _build_redirect_url(self, request: HttpRequest) -> HttpResponse:
        params = {
            'lang': self.lang,
            'q': request.GET.get('q', ''),
            'sort': request.GET.get('sort', 'created_at'),
            'dir': request.GET.get('dir', 'desc'),
            'page': request.GET.get('page', '1'),
            'perPage': request.GET.get('perPage', str(self.paginate_by)),
        }
        query = urlencode(params)
        return redirect(f"{request.path}?{query}")


class NoteDetailView(generic.DetailView):
    model = Note
    template_name = 'contacts/note_detail.html'
    context_object_name = 'note'

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        lang = self.request.GET.get('lang', 'uk')
        context.update({'lang': lang, 't': translations(lang)})
        return context


class NoteCreateView(generic.CreateView):
    model = Note
    form_class = NoteForm
    template_name = 'contacts/note_form.html'
    success_url = reverse_lazy('contacts:note_list')

    def get_success_url(self):
        lang = self.request.GET.get('lang', 'uk')
        return f"{reverse_lazy('contacts:note_list')}?lang={lang}"

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        lang = self.request.GET.get('lang', 'uk')
        context.update({'lang': lang, 't': translations(lang)})
        return context


class NoteUpdateView(generic.UpdateView):
    model = Note
    form_class = NoteForm
    template_name = 'contacts/note_form.html'
    success_url = reverse_lazy('contacts:note_list')

    def get_success_url(self):
        lang = self.request.GET.get('lang', 'uk')
        return f"{reverse_lazy('contacts:note_list')}?lang={lang}"

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        lang = self.request.GET.get('lang', 'uk')
        context.update({'lang': lang, 't': translations(lang)})
        return context


class NoteDeleteView(generic.DeleteView):
    model = Note
    template_name = 'contacts/note_confirm_delete.html'
    success_url = reverse_lazy('contacts:note_list')

    def get_success_url(self):
        lang = self.request.GET.get('lang', 'uk')
        return f"{reverse_lazy('contacts:note_list')}?lang={lang}"

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        lang = self.request.GET.get('lang', 'uk')
        context.update({'lang': lang, 't': translations(lang)})
        return context
