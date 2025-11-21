from typing import Any

from django.db.models import Q
from django.urls import reverse_lazy
from django.views import generic

from .forms import NoteForm
from .models import Note


class NoteListView(generic.ListView):
    model = Note
    template_name = 'contacts/note_list.html'
    context_object_name = 'notes'
    paginate_by = 6

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
            queryset = queryset.filter(
                Q(title__icontains=search_term) | Q(text__icontains=search_term)
            )

        allowed_sorts = {
            'created_at': 'created_at',
            'title': 'title',
        }
        sort_field = allowed_sorts.get(sort, 'created_at')
        prefix = '' if direction == 'asc' else '-'
        return queryset.order_by(f"{prefix}{sort_field}")

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        context.update(
            {
                'search': self.request.GET.get('q', ''),
                'sort': self.request.GET.get('sort', 'created_at'),
                'dir': self.request.GET.get('dir', 'desc'),
                'per_page': self.get_paginate_by(self.get_queryset()),
            }
        )
        return context


class NoteDetailView(generic.DetailView):
    model = Note
    template_name = 'contacts/note_detail.html'
    context_object_name = 'note'


class NoteCreateView(generic.CreateView):
    model = Note
    form_class = NoteForm
    template_name = 'contacts/note_form.html'
    success_url = reverse_lazy('contacts:note_list')


class NoteUpdateView(generic.UpdateView):
    model = Note
    form_class = NoteForm
    template_name = 'contacts/note_form.html'
    success_url = reverse_lazy('contacts:note_list')


class NoteDeleteView(generic.DeleteView):
    model = Note
    template_name = 'contacts/note_confirm_delete.html'
    success_url = reverse_lazy('contacts:note_list')
