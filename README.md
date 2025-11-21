# Django Notes Manager

Приклад вебзастосунку нотаток на Django, який демонструє:

1. **Створення Django-проєкту та застосунку.**
2. **Роботу з моделями та ORM.**
3. **CRUD-операції для нотаток.**
4. **Маршрутизацію в `urls.py`.**
5. **Шаблони та контекст для HTML-інтерфейсу.**
6. **Пошук, сортування та пагінацію.**
7. **Підключення статичних файлів (JS).**
8. **Повноцінний прикладний вебзастосунок.**

## 1. Підготовка середовища

> Додаток працює з Python 3.11+ і SQLite за замовчуванням.

### Linux / macOS (bash/zsh)

```bash
# Створити та активувати віртуальне середовище (venv)
python -m venv .venv
source .venv/bin/activate

# Встановити залежності всередині активованого середовища
python -m pip install -r requirements.txt
```

### Windows PowerShell

```powershell
# Створити віртуальне середовище
python -m venv .venv

# Активувати середовище (PowerShell)
.\.venv\Scripts\Activate.ps1

# Якщо зʼявляється попередження про політику виконання, дозвольте скрипти лише для поточного користувача:
#   Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned

# Встановити залежності після активації
python -m pip install -r requirements.txt
```

> Використовуйте `python -m pip ...`, щоб ставити пакети саме у вибране середовище та уникати помилки `ModuleNotFoundError: No module named 'django'`.

## 2. Ініціалізація бази та запуск

```bash
# Застосувати міграції (створює таблицю notes)
python manage.py migrate

# Створити адміністратора для доступу до Django Admin (опційно)
python manage.py createsuperuser

# Запустити дев-сервер
python manage.py runserver 0.0.0.0:8000
```

Інтерфейс нотаток буде доступний на `http://localhost:8000/`.

## 3. Що всередині (короткий путівник)

### Створення проєкту та застосунку
- Проєкт ініціалізовано командою `django-admin startproject contacts_project .`.
- Застосунок створено командою `python manage.py startapp contacts` і підключено в `INSTALLED_APPS` у `contacts_project/settings.py`.

### Модель та ORM
- `contacts/models.py` містить модель `Note` з полями `title`, `text`, `created_at`, `updated_at`.
- Початкова міграція `contacts/migrations/0001_initial.py` створює таблицю `notes`.

### CRUD-операції
- `contacts/views.py` реалізує `NoteListView`, `NoteDetailView`, `NoteCreateView`, `NoteUpdateView`, `NoteDeleteView` на базі Django generic CBV.
- Форми визначені у `contacts/forms.py` для валідації полів `title` та `text`.

### Маршрути (`urls.py`)
- `contacts/urls.py` описує маршрути для списку, створення, деталізації, редагування та видалення нотаток.
- `contacts_project/urls.py` підключає маршрути застосунку та Django Admin.

### Шаблони та контекст
- Базовий шаблон `templates/base.html` підключає Bootstrap і статичний JS.
- Шаблони у `contacts/templates/contacts/` рендерять список нотаток із пошуком/сортуванням/пагінацією, форму створення/редагування й сторінку деталей.

### Пошук, сортування, пагінація
- `NoteListView` приймає query-параметри `q` (пошук за заголовком або текстом), `sort` (`created_at` чи `title`), `dir` (`asc`/`desc`) і `perPage` для розміру сторінки.
- Шаблон `note_list.html` містить форму фільтрації та пагінатор.

### Статичні файли (JS)
- Статичні файли розміщено в `contacts/static/contacts/` і підключено через `STATICFILES_DIRS` у `contacts_project/settings.py`.
- `filters.js` скидає параметри пошуку/сортування через кнопку «Очистити».

### Прикладний сценарій
1. Відкрийте `/` — бачите список нотаток із пошуком, сортуванням і пагінацією.
2. Створіть нотатку через кнопку «Додати нотатку» (`/notes/create/`).
3. Переглядайте, оновлюйте чи видаляйте нотатки за посиланнями зі списку або сторінки деталей.

## 4. Корисні команди для розробки

```bash
# Перевірити потенційні конфлікти міграцій
python manage.py makemigrations --check --dry-run

# Запустити вбудований сервер з авто-перезавантаженням
python manage.py runserver

# Створити нову міграцію після зміни моделей
python manage.py makemigrations
python manage.py migrate
```

## 5. Налаштування
- `DEBUG` увімкнено для локальної розробки. У продакшені встановіть `DEBUG=False` і налаштуйте `ALLOWED_HOSTS`.
- Статичні файли збираються командою `python manage.py collectstatic` (каталог `staticfiles`).
- За потреби підключіть іншу СУБД, змінивши секцію `DATABASES` у `contacts_project/settings.py` і додавши потрібні драйвери.
