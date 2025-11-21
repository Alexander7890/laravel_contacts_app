from django import forms

from .models import Note


class NoteForm(forms.ModelForm):
    class Meta:
        model = Note
        fields = ['title', 'text']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Заголовок нотатки'}),
            'text': forms.Textarea(attrs={'class': 'form-control', 'rows': 6, 'placeholder': 'Текст…'}),
        }
