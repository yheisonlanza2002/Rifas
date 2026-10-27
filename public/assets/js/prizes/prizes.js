document.addEventListener('DOMContentLoaded', function () {
    const prizeTypeSelect = document.getElementById('prize_type');
    const camposMoney = document.querySelectorAll('.campo-money');
    const nameInput = document.getElementById('name');
    const lotterySelect = document.getElementById('lottery_id');
    const raffleSelect = document.getElementById('raffle_id');
    const positionInput = document.getElementById('position');
    const valueInput = document.getElementById('value');
    const recurrenceSelect = document.getElementById('recurrence_days');
    const drawDateInput = document.getElementById('draw_date');
    const vistaPrevia = document.getElementById('vista-previa');

    function toggleCamposMoney() {
        const esMONEY = prizeTypeSelect?.value === 'MONEY';
        camposMoney.forEach(function (campo) {
            if (esMONEY) {
                campo.classList.add('visible');
            } else {
                campo.classList.remove('visible');
            }
        });
    }

    function actualizarVistaPrevia() {
        const nombre = nameInput?.value || 'Nombre del premio';
        const loteria = lotterySelect?.options[lotterySelect.selectedIndex]?.text || 'Lotería';
        const rifa = raffleSelect?.options[raffleSelect.selectedIndex]?.text || 'Rifa';
        const tipo = prizeTypeSelect?.value === 'MONEY' ? 'Premio en Dinero' : 'Premio Físico';
        const posicion = positionInput?.value || '—';
        const valor = valueInput?.value ? '$ ' + parseFloat(valueInput.value).toLocaleString() : '—';
        const recurrencia = recurrenceSelect?.value ? 'Cada ' + recurrenceSelect.value + ' días' : '—';
        const fechaSorteo = drawDateInput?.value || '—';

        if (!nameInput?.value && !lotterySelect?.value) {
            vistaPrevia.innerHTML = '<em class="text-muted">Completa los campos para ver la vista previa</em>';
            return;
        }

        let html = '<div class="prize-preview">';
        html += '<div class="prize-preview-header">';
        html += '<div class="prize-preview-icon"><i class="bx bx-gift"></i></div>';
        html += '<div class="prize-preview-info">';
        html += '<div class="prize-preview-name">' + nombre + '</div>';
        html += '<div class="prize-preview-lottery">' + loteria + ' • ' + rifa + '</div>';
        html += '</div>';
        html += '<div class="prize-preview-badge">' + tipo + '</div>';
        html += '</div>';
        html += '<div class="prize-preview-body">';
        html += '<div class="prize-preview-item"><span class="prize-preview-label">Posición</span><span class="prize-preview-value">#' + posicion + '</span></div>';
        html += '<div class="prize-preview-item"><span class="prize-preview-label">Valor</span><span class="prize-preview-value">' + valor + '</span></div>';
        if (prizeTypeSelect?.value === 'MONEY') {
            html += '<div class="prize-preview-item"><span class="prize-preview-label">Recurrencia</span><span class="prize-preview-value">' + recurrencia + '</span></div>';
            html += '<div class="prize-preview-item"><span class="prize-preview-label">Fecha Sorteo</span><span class="prize-preview-value">' + fechaSorteo + '</span></div>';
        }
        html += '</div>';
        html += '</div>';

        vistaPrevia.innerHTML = html;
    }

    if (prizeTypeSelect) {
        prizeTypeSelect.addEventListener('change', function () {
            toggleCamposMoney();
            actualizarVistaPrevia();
        });
    }

    [nameInput, lotterySelect, raffleSelect, positionInput, valueInput, recurrenceSelect, drawDateInput].forEach(function (el) {
        if (el) el.addEventListener('input', actualizarVistaPrevia);
    });

    const contenedorPremios = document.getElementById('contenedor-premios');
    const templatePremio = document.getElementById('template-premio');
    const btnAgregarPremio = document.getElementById('btn-agregar-premio');
    let premioIndex = 0;

    function agregarPremio() {
        if (!templatePremio || !contenedorPremios) return;
        const clone = templatePremio.content.cloneNode(true);
        const html = clone.innerHTML.split('INDEX').join(premioIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const prizeItem = wrapper.firstElementChild;
        prizeItem.querySelector('.prize-num').textContent = 'Premio #' + (premioIndex + 2);
        contenedorPremios.appendChild(prizeItem);

        const typeSelect = prizeItem.querySelector('select[name*="type"]');
        const moneyFields = prizeItem.querySelectorAll('.prize-money-fields');
        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                moneyFields.forEach(f => f.style.display = this.value === 'MONEY' ? 'block' : 'none');
            });
        }

        const btnEliminar = prizeItem.querySelector('.btn-eliminar-premio');
        if (btnEliminar) {
            btnEliminar.addEventListener('click', function () {
                prizeItem.remove();
                actualizarNumerosPremios();
            });
        }

        premioIndex++;
        actualizarNumerosPremios();
    }

    function actualizarNumerosPremios() {
        document.querySelectorAll('.prize-item').forEach(function (item, i) {
            const num = item.querySelector('.prize-num');
            if (num) num.textContent = 'Premio #' + (i + 2);
        });
    }

    if (btnAgregarPremio) {
        btnAgregarPremio.addEventListener('click', agregarPremio);
    }

    toggleCamposMoney();
    actualizarVistaPrevia();
});
