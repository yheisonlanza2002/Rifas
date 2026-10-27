document.addEventListener('DOMContentLoaded', function () {
    function formatNumber(value, allowDecimals) {
        const clean = value.replace(/[^\d.]/g, '');
        const parts = clean.split('.');
        const intPart = parts[0] ? parseInt(parts[0], 10) : 0;
        const formatted = intPart.toLocaleString('es-CO');
        if (allowDecimals && parts[1] !== undefined) {
            return formatted + '.' + parts[1];
        }
        return formatted;
    }

    document.querySelectorAll('input[data-format-number]').forEach(function (input) {
        const step = parseFloat(input.step) || 1;
        const allowDecimals = step < 1;

        input.addEventListener('input', function () {
            const cursor = input.selectionStart;
            const original = input.value;
            const digitsOnly = original.replace(/[^\d]/g, '');

            if (!digitsOnly) {
                input.value = '';
                return;
            }

            const formatted = formatNumber(digitsOnly, allowDecimals && original.includes('.'));

            const diff = formatted.length - original.length;
            input.value = formatted;

            try {
                const newPos = Math.max(0, cursor + diff);
                input.setSelectionRange(newPos, newPos);
            } catch (e) {
            }
        });

        if (input.value) {
            const digitsOnly = input.value.replace(/[^\d]/g, '');
            if (digitsOnly) {
                input.value = formatNumber(digitsOnly, allowDecimals);
            }
        }
    });
});
