from django.db import models
from django.urls import reverse


class ContactGroup(models.Model):
    name = models.CharField(max_length=150, unique=True)

    class Meta:
        ordering = ['name']

    def __str__(self) -> str:
        return self.name


class Contact(models.Model):
    name = models.CharField(max_length=150)
    email = models.EmailField(max_length=180)
    phone = models.CharField(max_length=50, blank=True, null=True)
    note = models.TextField(blank=True, null=True)
    group = models.ForeignKey(
        ContactGroup,
        related_name='contacts',
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
    )

    class Meta:
        ordering = ['name']

    def __str__(self) -> str:
        return self.name

    def get_absolute_url(self) -> str:
        return reverse('contacts:contact_detail', args=[self.pk])
