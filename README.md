# Django Contacts Application

Цей репозиторій містить приклад вебзастосунку контактів на Django, який демонструє:

1. **Створення Django-проєкту та застосунку.**
2. **Роботу з моделями та ORM.**
3. **CRUD-операції для контактів.**
4. **Маршрутизацію в `urls.py`.**
5. **Шаблони та контекст для HTML-інтерфейсу.**
6. **Пошук, фільтрацію та пагінацію.**
7. **Підключення та використання статичних файлів (JS).**
8. **Повноцінний прикладний вебзастосунок.**

## 1. Підготовка середовища

> Проєкт використовує Python 3.11+ і SQLite за замовчуванням.

### Linux / macOS (bash/zsh)

```bash
# Створити та активувати віртуальне середовище (приклад для venv)
python -m venv .venv
source .venv/bin/activate

# Встановити залежності *вже всередині активованого середовища*
python -m pip install -r requirements.txt
```

### Windows PowerShell

```powershell
# Створити віртуальне середовище
python -m venv .venv

# Активувати середовище (PowerShell)
.\.venv\Scripts\Activate.ps1

# Якщо бачите помилку про політику виконання, дозвольте скрипти лише для поточного користувача:
#   Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned

# Встановити залежності *після активації* (уникає помилок "ModuleNotFoundError: No module named 'django'")
python -m pip install -r requirements.txt
```

> Порада: використовуйте `python -m pip ...`, щоб упевнитися, що бібліотеки ставляться в обране середовище, а не в системний Python.

## 2. Ініціалізація бази та запуск

```bash
# Застосувати міграції (створює таблиці та додає початкові групи)
python manage.py migrate

# Створити адміністратора для доступу до Django Admin (опційно)
python manage.py createsuperuser

# Запустити дев-сервер
python manage.py runserver 0.0.0.0:8000
```

Тепер інтерфейс контактів доступний на `http://localhost:8000/`.

## 3. Що всередині (короткий путівник за вимогами)

### Створення проєкту та застосунку
- Проєкт ініціалізовано командою `django-admin startproject contacts_project .`.
- Застосунок створено командою `python manage.py startapp contacts` і підключено в `INSTALLED_APPS` у `contacts_project/settings.py`.

### Моделі та ORM
- `contacts/models.py` містить `Contact` і `ContactGroup` з полями, зв'язком `ForeignKey` та `get_absolute_url` для зручної навігації.
- Початкова міграція `contacts/migrations/0001_initial.py` створює таблиці та додає типові групи через `RunPython`.

### CRUD-операції
- `contacts/views.py` реалізує `ContactListView`, `ContactDetailView`, `ContactCreateView`, `ContactUpdateView`, `ContactDeleteView` на базі generic CBV, забезпечуючи створення, читання, оновлення й видалення.
- Форми визначені у `contacts/forms.py` для контролю полів і валідації.

### Маршрути (`urls.py`)
- `contacts/urls.py` описує маршрути для списку, деталей, створення, редагування та видалення контактів.
- `contacts_project/urls.py` підключає маршрути застосунку та Django Admin.

### Шаблони та контекст
- Базовий шаблон `templates/base.html` підключає Bootstrap і блоки контенту.
- Шаблони в `contacts/templates/contacts/` рендерять форми та сторінки списку/деталей, отримуючи контекст від CBV.

### Пошук, фільтрація, пагінація
- `ContactListView` у `contacts/views.py` приймає query-параметри `q` (пошук за ім'ям/email/телефоном) і `group` (фільтр за групою) та пагінує результати.
- У шаблоні `contact_list.html` є форма пошуку/фільтру та пагінатор.

### Статичні файли (JS)
- Статичні файли лежать у `contacts/static/contacts/` і підключені через `STATICFILES_DIRS` у `settings.py`.
- `filters.js` містить логіку для швидкого скидання полів пошуку/фільтру.

### Прикладний сценарій
1. Відкрийте `/` — бачите список контактів із пошуком, фільтрацією та пагінацією.
2. Створіть контакт через кнопку «Додати контакт» (`/contacts/create/`).
3. Переглядайте/оновлюйте/видаляйте контакти через відповідні кнопки на сторінці деталізації.

## 4. Корисні команди для розробки

```bash
# Перевірити потенційні конфлікти міграцій
python manage.py makemigrations --check --dry-run

# Запустити вбудований сервер із авто-перезавантаженням
python manage.py runserver

# Створити нову міграцію після зміни моделей
python manage.py makemigrations
python manage.py migrate
```

## 5. Нотатки по налаштуванню
- `DEBUG` увімкнено за замовчуванням для локальної розробки. Для продакшену змініть `DEBUG=False` і налаштуйте `ALLOWED_HOSTS`.
- Статичні файли збираються командою `python manage.py collectstatic` (папка `staticfiles`).
- За потреби можна підключити іншу СУБД, змінивши секцію `DATABASES` у `contacts_project/settings.py` та налаштувавши драйвери.

Готово! Цей гайд покриває повний цикл: від встановлення до використання CRUD із пошуком, фільтрацією, пагінацією та статикою в Django.
