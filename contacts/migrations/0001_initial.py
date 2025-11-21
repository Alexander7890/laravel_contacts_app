from django.db import migrations, models
import django.db.models.deletion


def seed_groups(apps, schema_editor):
    ContactGroup = apps.get_model('contacts', 'ContactGroup')
    ContactGroup.objects.bulk_create(
        [
            ContactGroup(name='Work'),
            ContactGroup(name='Friends'),
            ContactGroup(name='Family'),
        ]
    )


def unseed_groups(apps, schema_editor):
    ContactGroup = apps.get_model('contacts', 'ContactGroup')
    ContactGroup.objects.filter(name__in=['Work', 'Friends', 'Family']).delete()


class Migration(migrations.Migration):

    initial = True

    dependencies = []

    operations = [
        migrations.CreateModel(
            name='ContactGroup',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=150, unique=True)),
            ],
            options={
                'ordering': ['name'],
            },
        ),
        migrations.CreateModel(
            name='Contact',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('name', models.CharField(max_length=150)),
                ('email', models.EmailField(max_length=180)),
                ('phone', models.CharField(blank=True, max_length=50, null=True)),
                ('note', models.TextField(blank=True, null=True)),
                (
                    'group',
                    models.ForeignKey(
                        blank=True,
                        null=True,
                        on_delete=django.db.models.deletion.SET_NULL,
                        related_name='contacts',
                        to='contacts.contactgroup',
                    ),
                ),
            ],
            options={
                'ordering': ['name'],
            },
        ),
        migrations.RunPython(seed_groups, unseed_groups),
    ]
