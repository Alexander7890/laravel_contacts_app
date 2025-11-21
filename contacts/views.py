from typing import Any

from django.db.models import Q
from django.urls import reverse_lazy
from django.views import generic

from .forms import ContactForm
from .models import Contact, ContactGroup


class ContactListView(generic.ListView):
    model = Contact
    template_name = 'contacts/contact_list.html'
    context_object_name = 'contacts'
    paginate_by = 10

    def get_queryset(self):
        queryset = Contact.objects.select_related('group').order_by('name')
        search_term = self.request.GET.get('q', '').strip()
        group_id = self.request.GET.get('group')

        if search_term:
            queryset = queryset.filter(
                Q(name__icontains=search_term)
                | Q(email__icontains=search_term)
                | Q(phone__icontains=search_term)
                | Q(note__icontains=search_term)
            )

        if group_id:
            queryset = queryset.filter(group_id=group_id)

        return queryset

    def get_context_data(self, **kwargs: Any):
        context = super().get_context_data(**kwargs)
        context['groups'] = ContactGroup.objects.all()
        context['active_group'] = self.request.GET.get('group', '')
        context['search_term'] = self.request.GET.get('q', '')
        return context


class ContactDetailView(generic.DetailView):
    model = Contact
    template_name = 'contacts/contact_detail.html'
    context_object_name = 'contact'


class ContactCreateView(generic.CreateView):
    model = Contact
    form_class = ContactForm
    template_name = 'contacts/contact_form.html'
    success_url = reverse_lazy('contacts:contact_list')


class ContactUpdateView(generic.UpdateView):
    model = Contact
    form_class = ContactForm
    template_name = 'contacts/contact_form.html'
    success_url = reverse_lazy('contacts:contact_list')


class ContactDeleteView(generic.DeleteView):
    model = Contact
    template_name = 'contacts/contact_confirm_delete.html'
    success_url = reverse_lazy('contacts:contact_list')
