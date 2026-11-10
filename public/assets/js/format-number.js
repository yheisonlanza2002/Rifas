document.addEventListener('DOMContentLoaded', function () {
    function formatear(valor, conDecimal) {
        if (!valor) return '';

        if (!conDecimal) {
            // Solo enteros: eliminar cualquier caracter que no sea dígito
            const limpio = valor.toString().replace(/\D/g, '');
            if (!limpio) return '';
            // Convertir a BigInt para soportar números grandes sin pérdida de precisión
            try {
                const num = BigInt(limpio);
                return num.toLocaleString('es-CO');
            } catch (e) {
                const n = parseInt(limpio, 10);
                return isNaN(n) ? '' : n.toLocaleString('es-CO');
            }
        }

        // Con decimales
        const estandar = valor.toString().replace(',', '.');
        const partes = estandar.split('.');
        const parteEntera = partes[0].replace(/\D/g, '');
        if (!parteEntera && partes.length <= 1) return '';

        let enteroFormateado = '0';
        if (parteEntera) {
            try {
                enteroFormateado = BigInt(parteEntera).toLocaleString('es-CO');
            } catch (e) {
                enteroFormateado = parseInt(parteEntera, 10).toLocaleString('es-CO');
            }
        }

        if (partes.length > 1) {
            const decimales = partes[1].replace(/\D/g, '').slice(0, 2);
            return enteroFormateado + '.' + decimales;
        }

        return enteroFormateado;
    }

    document.querySelectorAll('input[data-format-number]').forEach(function (input) {
        const conDecimal = input.dataset.formatNumber === 'decimal';

        input.addEventListener('input', function () {
            const cursor = input.selectionStart || 0;
            const original = input.value;
            const formateado = formatear(original, conDecimal);

            const digitosAntes = original.slice(0, cursor).replace(/\D/g, '').length;
            input.value = formateado;

            let posicion = 0;
            let contados = 0;
            while (posicion < formateado.length && contados < digitosAntes) {
                if (/\d/.test(formateado.charAt(posicion))) {
                    contados++;
                }
                posicion++;
            }

            try {
                input.setSelectionRange(posicion, posicion);
            } catch (e) {}

            input.dispatchEvent(new Event('formatted-input'));
        });

        if (input.value) {
            input.value = formatear(input.value, conDecimal);
        }
    });
});
