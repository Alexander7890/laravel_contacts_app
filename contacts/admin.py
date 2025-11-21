from django.contrib import admin

from .models import Contact, ContactGroup


@admin.register(Contact)
class ContactAdmin(admin.ModelAdmin):
    list_display = ('name', 'email', 'phone', 'group')
    list_filter = ('group',)
    search_fields = ('name', 'email', 'phone', 'note')


@admin.register(ContactGroup)
class ContactGroupAdmin(admin.ModelAdmin):
    search_fields = ('name',)
