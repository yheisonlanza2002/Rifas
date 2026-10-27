document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('name');
    const cantidadInput = document.getElementById('ticket_quantity');
    const numerosPorBoletaInput = document.getElementById('numbers_per_ticket');
    const longitudInput = document.getElementById('number_length');
    const inicioInput = document.getElementById('number_start');
    const finInput = document.getElementById('number_end');
    const tipoSelect = document.getElementById('tipo_generacion');
    const vistaPrevia = document.getElementById('vista-previa');
    const form = document.querySelector('form');

    function pad(n, len) {
        return String(n).padStart(len, '0');
    }

    function actualizarPlaceholders() {
        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;

        if (cantidad > 0 && numerosPorBoleta > 0) {
            const total = cantidad * numerosPorBoleta;
            const digitosNecesarios = String(total).length;
            if (longitudInput && !longitudInput.value) {
                longitudInput.placeholder = 'Se requieren ' + digitosNecesarios + ' dígitos';
            }
        } else if (longitudInput) {
            longitudInput.placeholder = 'Ej: 4';
        }

        if (longitud > 0 && numerosPorBoleta > 0) {
            let maxBoletas = Math.floor(Math.pow(10, longitud) / numerosPorBoleta);
            const maxConMismoDigitos = Math.pow(10, longitud) - 1;
            if (maxBoletas * numerosPorBoleta > maxConMismoDigitos) {
                maxBoletas = Math.floor(maxConMismoDigitos / numerosPorBoleta);
            }
            if (cantidadInput && !cantidadInput.value) {
                cantidadInput.placeholder = 'Máximo: ' + maxBoletas.toLocaleString() + ' boletas';
            }
        } else if (cantidadInput) {
            cantidadInput.placeholder = 'Ej: 1000';
        }

        if (cantidad > 0 && longitud > 0) {
            const maxOportunidades = Math.floor(Math.pow(10, longitud) / cantidad);
            if (numerosPorBoletaInput && !numerosPorBoletaInput.value) {
                numerosPorBoletaInput.placeholder = 'Máximo: ' + maxOportunidades + ' oportunidades';
            }
        } else if (numerosPorBoletaInput) {
            numerosPorBoletaInput.placeholder = 'Ej: 2';
        }
    }

    function calcular() {
        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;
        const nombre = nombreInput?.value || 'Rifa sin nombre';
        const tipo = tipoSelect?.value || 'RANDOM';

        if (!cantidad && !numerosPorBoleta && !longitud) {
            vistaPrevia.innerHTML = '<div class="preview-empty"><i class="bx bx-show"></i><span>Completa los campos para ver la vista previa</span></div>';
            return;
        }

        if (!cantidad || !numerosPorBoleta || !longitud) {
            let html = '<div class="preview-partial">';
            html += '<div class="preview-header"><i class="bx bx-show"></i> Vista Previa</div>';
            html += '<div class="preview-fields">';
            if (cantidad) html += '<span class="preview-tag">' + cantidad.toLocaleString() + ' boletas</span>';
            if (numerosPorBoleta) html += '<span class="preview-tag">' + numerosPorBoleta + ' oportunidades</span>';
            if (longitud) html += '<span class="preview-tag">' + longitud + ' dígitos</span>';
            html += '</div>';
            if (cantidad && numerosPorBoleta) {
                const total = cantidad * numerosPorBoleta;
                const digitosNecesarios = String(total).length;
                if (!longitud) {
                    html += '<div class="preview-hint">Se requieren <strong>' + digitosNecesarios + ' dígitos</strong> para esta configuración</div>';
                }
            }
            if (longitud && numerosPorBoleta && !cantidad) {
                const maxBoletas = Math.floor(Math.pow(10, longitud) / numerosPorBoleta);
                html += '<div class="preview-hint">Puedes crear hasta <strong>' + maxBoletas.toLocaleString() + ' boletas</strong></div>';
            }
            html += '</div>';
            vistaPrevia.innerHTML = html;
            return;
        }

        const totalNumeros = cantidad * numerosPorBoleta;
        const digitosMinimos = String(totalNumeros).length;
        const maxCombinaciones = Math.pow(10, longitud);

        const maxBoletasRedondeado = Math.floor((maxCombinaciones - 1) / numerosPorBoleta);

        if (longitud < digitosMinimos) {
            if (typeof showToast === 'function') {
                showToast('Con ' + cantidad.toLocaleString() + ' boletas y ' + numerosPorBoleta + ' oportunidades, se requieren al menos ' + digitosMinimos + ' dígitos. Máximo con ' + longitud + ' dígitos: ' + maxBoletasRedondeado.toLocaleString() + ' boletas.', 'warning', 5000);
            }
            vistaPrevia.innerHTML = '<div class="preview-error"><i class="bx bx-error-circle"></i><span>Se requieren al menos <strong>' + digitosMinimos + ' dígitos</strong> para ' + cantidad.toLocaleString() + ' boletas con ' + numerosPorBoleta + ' oportunidades</span></div>';
            if (form) form.dataset.valido = 'false';
            return;
        }

        if (totalNumeros > maxCombinaciones) {
            if (typeof showToast === 'function') {
                showToast('No hay suficientes combinaciones. Con ' + longitud + ' dígitos y ' + numerosPorBoleta + ' oportunidades, el máximo es: ' + maxBoletasRedondeado.toLocaleString() + ' boletas.', 'error', 5000);
            }
            vistaPrevia.innerHTML = '<div class="preview-error"><i class="bx bx-error-circle"></i><span>Sin combinaciones suficientes</span></div>';
            if (form) form.dataset.valido = 'false';
            return;
        }

        if (form) form.dataset.valido = 'true';

        const inicio = 0;
        const fin = totalNumeros - 1;

        if (inicioInput) inicioInput.value = pad(inicio, longitud);
        if (finInput) finInput.value = pad(fin, longitud);

        let numeros = [];
        if (tipo === 'SEQUENTIAL') {
            for (let i = 0; i < numerosPorBoleta; i++) {
                numeros.push(pad(inicio + i, longitud));
            }
        } else {
            const usados = new Set();
            let intentos = 0;
            while (numeros.length < numerosPorBoleta && intentos < 1000) {
                const n = Math.floor(Math.random() * maxCombinaciones);
                const nStr = pad(n, longitud);
                if (!usados.has(nStr)) {
                    usados.add(nStr);
                    numeros.push(nStr);
                }
                intentos++;
            }
        }

        let html = '<div class="ticket-preview">';
        html += '<div class="ticket-header">';
        html += '<div class="ticket-logo"><i class="bx bx-ticket"></i></div>';
        html += '<div class="ticket-info">';
        html += '<div class="ticket-number">' + nombre + '</div>';
        html += '<div class="ticket-total">Boleta #0001 de ' + cantidad.toLocaleString() + '</div>';
        html += '</div>';
        html += '<div class="ticket-badge">' + (tipo === 'SEQUENTIAL' ? 'Secuencial' : 'Aleatorio') + '</div>';
        html += '</div>';
        html += '<div class="ticket-numbers">';
        numeros.forEach((n, i) => {
            html += '<div class="number-chip">';
            html += '<span class="number-label">Número ' + (i + 1) + '</span>';
            html += '<span class="number-value">' + n + '</span>';
            html += '</div>';
        });
        html += '</div>';
        html += '<div class="ticket-footer">';
        html += '<div class="footer-item"><span class="footer-label">Total números</span><span class="footer-value">' + totalNumeros.toLocaleString() + '</span></div>';
        html += '<div class="footer-item"><span class="footer-label">Inicio</span><span class="footer-value">' + pad(inicio, longitud) + '</span></div>';
        html += '<div class="footer-item"><span class="footer-label">Final</span><span class="footer-value">' + pad(fin, longitud) + '</span></div>';
        html += '<div class="footer-item"><span class="footer-label">Dígitos</span><span class="footer-value">' + longitud + ' <small>(mín: ' + digitosMinimos + ')</small></span></div>';
        html += '</div>';
        html += '</div>';

        vistaPrevia.innerHTML = html;
    }

    function actualizarNombre() {
        const nombre = nombreInput?.value || '';
        const palabras = nombre.trim().split(/\s+/).filter(p => p.length > 0);
        const iniciales = palabras.map(p => p[0].toUpperCase()).join('').substring(0, 3);
        const codigoPreview = iniciales + '-001';

        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.value = codigoPreview;
        }
    }

    function validarAntesDeGuardar(e) {
        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;

        if (!cantidad || !numerosPorBoleta || !longitud) {
            e.preventDefault();
            if (typeof showToast === 'function') {
                showToast('Completa todos los campos de configuración', 'warning');
            }
            return false;
        }

        const totalNumeros = cantidad * numerosPorBoleta;
        const maxCombinaciones = Math.pow(10, longitud);

        if (totalNumeros > maxCombinaciones) {
            e.preventDefault();
            const maxBoletas = Math.floor(maxCombinaciones / numerosPorBoleta);
            if (typeof showToast === 'function') {
                showToast('Configuración inválida. Máximo de boletas: ' + maxBoletas.toLocaleString(), 'error');
            }
            return false;
        }

        return true;
    }

    function actualizarLabelDigitos() {
        const label = document.getElementById('label-digitos');
        const val = parseInt(longitudInput?.value) || 0;
        const lotterySelect = document.getElementById('lottery_id');
        const lotteryName = lotterySelect?.options[lotterySelect.selectedIndex]?.text || '';
        const lotteryLabel = lotteryName ? ' de la lotería <strong>' + lotteryName + '</strong>' : ' de la lotería';
        if (label) {
            if (val === 1) {
                label.innerHTML = 'Juega con la <strong>última</strong> cifra' + lotteryLabel + ' <span class="text-danger">*</span>';
            } else if (val > 1) {
                label.innerHTML = 'Juega con los <strong>últimos ' + val + '</strong> dígitos' + lotteryLabel + ' <span class="text-danger">*</span>';
            } else {
                label.innerHTML = 'Juega con los últimos dígitos' + lotteryLabel + ' <span class="text-danger">*</span>';
            }
        }
    }

    const lotterySelect = document.getElementById('lottery_id');

    if (cantidadInput) cantidadInput.addEventListener('input', function() { actualizarPlaceholders(); calcular(); });
    if (numerosPorBoletaInput) numerosPorBoletaInput.addEventListener('input', function() { actualizarPlaceholders(); calcular(); });
    if (longitudInput) longitudInput.addEventListener('input', function() { actualizarPlaceholders(); actualizarLabelDigitos(); calcular(); });
    if (tipoSelect) tipoSelect.addEventListener('change', calcular);
    if (nombreInput) nombreInput.addEventListener('input', function() { actualizarNombre(); calcular(); });
    if (lotterySelect) lotterySelect.addEventListener('change', function() { actualizarLabelDigitos(); calcular(); });

    if (form) {
        form.addEventListener('submit', validarAntesDeGuardar);
    }

    const btnNuevaLoteria = document.getElementById('btn-nueva-loteria');
    const modalLoteria = document.getElementById('modal-loteria');
    const guardarLoteriaBtn = document.getElementById('guardar-loteria');

    if (btnNuevaLoteria && modalLoteria) {
        btnNuevaLoteria.addEventListener('click', function () {
            const modal = new bootstrap.Modal(modalLoteria);
            modal.show();
        });
    }

    if (guardarLoteriaBtn && lotterySelect) {
        guardarLoteriaBtn.addEventListener('click', function () {
            const nombreInput = document.getElementById('nueva-loteria-nombre');
            const nombre = nombreInput?.value?.trim();

            if (!nombre) {
                if (typeof showToast === 'function') {
                    showToast('Ingresa el nombre de la lotería', 'warning');
                }
                return;
            }

            const palabras = nombre.split(/\s+/).filter(p => p.length > 0);
            const codigo = palabras.map(p => p[0].toUpperCase()).join('').substring(0, 3);

            fetch(RAFFLES_URL_GUARDAR_LOTERIA, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    name: nombre,
                    code: codigo,
                    csrf_test_name: document.querySelector('input[name="csrf_test_name"]')?.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const option = document.createElement('option');
                    option.value = data.lottery.id;
                    option.textContent = data.lottery.name;
                    lotterySelect.appendChild(option);
                    lotterySelect.value = data.lottery.id;

                    const modal = bootstrap.Modal.getInstance(modalLoteria);
                    if (modal) modal.hide();

                    nombreInput.value = '';

                    if (typeof showToast === 'function') {
                        showToast('Lotería guardada correctamente', 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Error al guardar', 'error');
                    }
                }
            })
            .catch(error => {
                if (typeof showToast === 'function') {
                    showToast('Error al guardar la lotería', 'error');
                }
            });
        });
    }

    const contenedorPremios = document.getElementById('contenedor-premios');
    const templatePremio = document.getElementById('template-premio');
    const btnAgregarPremio = document.getElementById('btn-agregar-premio');
    let premioIndex = 0;

    function agregarPremio() {
        if (!templatePremio || !contenedorPremios) return;
        const clone = templatePremio.content.cloneNode(true);
        const html = clone.innerHTML.replaceAll('INDEX', premioIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const prizeItem = wrapper.firstElementChild;
        prizeItem.querySelector('.prize-num').textContent = 'Premio #' + (premioIndex + 1);
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
            if (num) num.textContent = 'Premio #' + (i + 1);
        });
    }

    if (btnAgregarPremio) {
        btnAgregarPremio.addEventListener('click', agregarPremio);
    }

    actualizarPlaceholders();
    actualizarLabelDigitos();
    calcular();
    actualizarNombre();
});
