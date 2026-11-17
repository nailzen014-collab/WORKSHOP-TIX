

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('[data-attendee-form]').forEach((form) => {
    const rows = form.querySelector('[data-attendee-rows]');
    const addButton = form.querySelector('[data-add-attendee]');

    const updateRows = () => {
        rows.querySelectorAll('[data-attendee-row]').forEach((row, index) => {
            const label = row.querySelector('label');
            const input = row.querySelector('input');
            const removeButton = row.querySelector('button');

            label.htmlFor = `attendee-${index}`;
            label.textContent = `Nama lengkap peserta ${index + 1}`;
            input.id = `attendee-${index}`;
            removeButton.hidden = index === 0 && rows.children.length === 1;
        });

        addButton.hidden = rows.querySelectorAll('[data-attendee-row]').length >= 5;
    };

    const addRemoveButton = (row) => {
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Hapus peserta';
        removeButton.className = 'text-sm font-semibold text-forest-600 underline hover:text-forest-900';
        removeButton.addEventListener('click', () => {
            row.remove();
            updateRows();
        });
        row.append(removeButton);
    };

    rows.querySelectorAll('[data-attendee-row]').forEach(addRemoveButton);
    updateRows();
    addButton.addEventListener('click', () => {
        const index = rows.querySelectorAll('[data-attendee-row]').length;
        if (index >= 5) {
            return;
        }

        const row = document.createElement('div');
        row.dataset.attendeeRow = '';
        row.className = 'space-y-2';

        const label = document.createElement('label');
        label.htmlFor = `attendee-${index}`;
        label.className = 'text-xs font-semibold uppercase tracking-wide text-forest-600';
        label.textContent = `Nama lengkap peserta ${index + 1}`;

        const input = document.createElement('input');
        input.id = `attendee-${index}`;
        input.name = 'attendees[]';
        input.type = 'text';
        input.maxLength = 255;
        input.required = true;
        input.autocomplete = 'name';
        input.className = 'block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600';

        row.append(label, input);
        addRemoveButton(row);
        rows.append(row);
        updateRows();
    });
});
