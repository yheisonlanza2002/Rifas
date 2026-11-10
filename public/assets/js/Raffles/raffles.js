document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('name');
    const codigoInput = document.getElementById('code');
    const cantidadInput = document.getElementById('ticket_quantity');
    const numerosPorBoletaInput = document.getElementById('numbers_per_ticket');
    const longitudInput = document.getElementById('number_length');
    const inicioInput = document.getElementById('number_start');
    const finInput = document.getElementById('number_end');
    const tipoSelect = document.getElementById('tipo_generacion');
    const lotterySelect = document.getElementById('lottery_id');
    const precioInput = document.getElementById('ticket_price');
    const monedaSelect = document.getElementById('currency');
    const fechaInicioInput = document.getElementById('start_at');
    const fechaFinInput = document.getElementById('end_at');
    const vistaPrevia = document.getElementById('vista-previa');
    const form = document.querySelector('form');

    function pad(n, len) {
        if (!len || len < 1) len = 4;
        return String(n).padStart(len, '0');
    }

    function formatFecha(isoStr) {
        if (!isoStr) return null;
        try {
            const d = new Date(isoStr);
            if (isNaN(d.getTime())) return null;
            const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const dia = String(d.getDate()).padStart(2, '0');
            const mes = meses[d.getMonth()];
            const anio = d.getFullYear();
            let horas = d.getHours();
            const minutos = String(d.getMinutes()).padStart(2, '0');
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12;
            horas = horas ? horas : 12;
            return `${dia} ${mes} ${anio}, ${String(horas).padStart(2, '0')}:${minutos} ${ampm}`;
        } catch (e) {
            return null;
        }
    }

    function getPrecioFormateado() {
        const val = precioInput?.value?.trim() || '';
        const moneda = monedaSelect?.value || 'COP';
        if (!val) {
            return '$ 0 ' + moneda;
        }
        return '$ ' + val + ' ' + moneda;
    }

    function getLoteriaNombre() {
        if (!lotterySelect) return 'Por definir';
        const opt = lotterySelect.options[lotterySelect.selectedIndex];
        if (!opt || !opt.value) return 'Por definir';
        return opt.text.trim();
    }

    function actualizarPlaceholders() {
        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;

        if (cantidad > 0 && numerosPorBoleta > 0) {
            const total = cantidad * numerosPorBoleta;
            const digitosNecesarios = String(total).length;
            if (longitudInput && !longitudInput.value) {
                longitudInput.placeholder = 'Se requieren mín. ' + digitosNecesarios + ' dígitos';
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
                cantidadInput.placeholder = 'Máximo: ' + maxBoletas.toLocaleString('es-CO') + ' boletas';
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
        if (!vistaPrevia) return;

        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;
        const nombre = nombreInput?.value?.trim() || 'Nombre de la Rifa';
        const codigo = codigoInput?.value?.trim() || 'RIF-001';
        const tipo = tipoSelect?.value || 'RANDOM';
        const loteria = getLoteriaNombre();
        const precio = getPrecioFormateado();
        const fechaInicio = formatFecha(fechaInicioInput?.value);
        const fechaFin = formatFecha(fechaFinInput?.value);

        let errorMsg = null;
        let warningMsg = null;
        let totalNumeros = 0;
        let digitosMinimos = 0;
        let maxCombinaciones = 0;

        if (cantidad > 0 && numerosPorBoleta > 0) {
            totalNumeros = cantidad * numerosPorBoleta;
            digitosMinimos = String(totalNumeros).length;
            maxCombinaciones = Math.pow(10, longitud || digitosMinimos);

            const maxBoletasRedondeado = Math.floor((maxCombinaciones - 1) / numerosPorBoleta);

            if (longitud > 0 && longitud < digitosMinimos) {
                errorMsg = `Con <strong>${cantidad.toLocaleString('es-CO')} boletas</strong> y <strong>${numerosPorBoleta} oportunidades</strong>, necesitas al menos <strong>${digitosMinimos} dígitos</strong>.`;
                if (form) form.dataset.valido = 'false';
            } else if (longitud > 0 && totalNumeros > maxCombinaciones) {
                errorMsg = `Combinaciones insuficientes. Con ${longitud} dígitos y ${numerosPorBoleta} oportunidades, el máximo permitido es de <strong>${maxBoletasRedondeado.toLocaleString('es-CO')} boletas</strong>.`;
                if (form) form.dataset.valido = 'false';
            } else {
                if (form) form.dataset.valido = 'true';
            }
        }

        // Rango de números inicial y final
        const sampleDigits = longitud > 0 ? longitud : 4;
        let inicio = 0;
        let fin = totalNumeros > 0 ? totalNumeros - 1 : Math.pow(10, sampleDigits) - 1;

        if (inicioInput) inicioInput.value = pad(inicio, sampleDigits);
        if (finInput && totalNumeros > 0) finInput.value = pad(fin, sampleDigits);

        // Generar números de muestra para la boleta
        const numOportunidades = numerosPorBoleta > 0 ? Math.min(numerosPorBoleta, 10) : 2;
        let numerosMuestra = [];

        if (tipo === 'SEQUENTIAL') {
            for (let i = 0; i < numOportunidades; i++) {
                numerosMuestra.push(pad(i, sampleDigits));
            }
        } else {
            // Ejemplo representativo fijo/aleatorio realista
            const baseSamples = [482, 7193, 3508, 9214, 1856, 6049, 8320, 2715, 5901, 4163];
            for (let i = 0; i < numOportunidades; i++) {
                const seed = baseSamples[i % baseSamples.length] % Math.pow(10, sampleDigits);
                numerosMuestra.push(pad(seed, sampleDigits));
            }
        }

        // Renderizar la Boleta Digital
        let html = '';

        if (errorMsg) {
            html += `<div class="preview-alert preview-alert-danger"><i class="bx bx-error-circle"></i><div>${errorMsg}</div></div>`;
        } else if (!cantidad || !numerosPorBoleta || !longitud) {
            let faltantes = [];
            if (!cantidad) faltantes.push('cantidad de boletas');
            if (!numerosPorBoleta) faltantes.push('oportunidades');
            if (!longitud) faltantes.push('dígitos');
            html += `<div class="preview-alert preview-alert-info"><i class="bx bx-info-circle"></i><div>Configura <strong>${faltantes.join(', ')}</strong> para calcular el rango exacto.</div></div>`;
        }

        html += `
        <div class="digital-ticket">
            <!-- Encabezado del ticket -->
            <div class="ticket-top">
                <div class="ticket-top-header">
                    <div class="ticket-brand">
                        <i class="bx bxs-coupon"></i> BOLETA OFICIAL
                        <span class="ticket-code-badge">${codigo}</span>
                    </div>
                    <div class="ticket-price-pill" id="preview-price-tag">
                        <i class="bx bx-tag-alt"></i> ${precio}
                    </div>
                </div>
                <div class="ticket-title">${nombre}</div>
                <div class="ticket-meta">
                    <span><i class="bx bx-receipt"></i> Boleta #0001</span>
                    <span>•</span>
                    <span>${cantidad > 0 ? cantidad.toLocaleString('es-CO') + ' boletas en juego' : 'Muestra preliminar'}</span>
                </div>
            </div>

            <!-- Separador perforado realista con muescas laterales -->
            <div class="ticket-perforated">
                <span class="ticket-notch-left"></span>
                <span class="ticket-notch-right"></span>
            </div>

            <!-- Cuerpo con números de la suerte -->
            <div class="ticket-body">
                <div class="ticket-body-title">
                    <i class="bx bxs-star text-warning"></i>
                    ${numerosPorBoleta > 1 ? numerosPorBoleta + ' OPORTUNIDADES DE GANAR' : 'NÚMERO DE LA SUERTE'}
                    <i class="bx bxs-star text-warning"></i>
                </div>

                <div class="numbers-grid">
                    ${numerosMuestra.map((num, idx) => `
                        <div class="number-chip">
                            <span class="chip-label">Oportunidad ${idx + 1}</span>
                            <span class="chip-value">${num}</span>
                        </div>
                    `).join('')}
                </div>
                ${numerosPorBoleta > 10 ? `<div class="text-center text-muted small mt-1">+ ${numerosPorBoleta - 10} oportunidades adicionales en la boleta</div>` : ''}

                <!-- Barra de Lotería -->
                <div class="ticket-lottery-bar">
                    <i class="bx bx-dice-5 fs-4"></i>
                    <div>
                        Juega con la <strong>${loteria}</strong>
                        <div class="small text-muted" style="font-size:0.75rem;">
                            ${longitud > 0 ? `(Con las últimas ${longitud} cifras)` : '(Cifras según configuración)'}
                        </div>
                    </div>
                </div>

                <!-- Barra de Fechas (Sorteo / Vigencia) -->
                <div class="ticket-dates-bar">
                    <div class="ticket-date-item">
                        <span class="ticket-date-label"><i class="bx bx-calendar-event text-primary"></i> Sorteo / Fin:</span>
                        <span class="ticket-date-value text-primary">${fechaFin || 'Por definir'}</span>
                    </div>
                    ${fechaInicio ? `
                    <div class="ticket-date-item">
                        <span class="ticket-date-label"><i class="bx bx-time text-secondary"></i> Inicio venta:</span>
                        <span class="ticket-date-value">${fechaInicio}</span>
                    </div>` : ''}
                </div>
            </div>

            <!-- Pie del Ticket -->
            <div class="ticket-footer">
                <div>
                    <strong>Rango:</strong> ${pad(inicio, sampleDigits)} al ${pad(fin, sampleDigits)}
                </div>
                <div>
                    <span class="ticket-footer-badge">
                        <i class="bx bx-shuffle"></i> Modo: ${tipo === 'SEQUENTIAL' ? 'Secuencial' : 'Aleatorio'}
                    </span>
                </div>
            </div>
        </div>`;

        vistaPrevia.innerHTML = html;
    }

    function actualizarNombre() {
        const nombre = nombreInput?.value || '';
        const palabras = nombre.trim().split(/\s+/).filter(p => p.length > 0);
        let iniciales = palabras.map(p => p[0].toUpperCase()).join('').substring(0, 3);
        if (!iniciales) iniciales = 'RIF';
        const codigoPreview = iniciales + '-001';

        if (codigoInput && (!codigoInput.value || codigoInput.dataset.auto === 'true' || codigoInput.readOnly)) {
            codigoInput.value = codigoPreview;
            codigoInput.dataset.auto = 'true';
        }
    }

    function validarAntesDeGuardar(e) {
        const cantidad = parseInt(cantidadInput?.value) || 0;
        const numerosPorBoleta = parseInt(numerosPorBoletaInput?.value) || 0;
        const longitud = parseInt(longitudInput?.value) || 0;

        if (!cantidad || !numerosPorBoleta || !longitud) {
            e.preventDefault();
            if (typeof showToast === 'function') {
                showToast('Completa todos los campos obligatorios de la boleta', 'warning');
            }
            return false;
        }

        const totalNumeros = cantidad * numerosPorBoleta;
        const maxCombinaciones = Math.pow(10, longitud);

        if (totalNumeros > maxCombinaciones) {
            e.preventDefault();
            const maxBoletas = Math.floor((maxCombinaciones - 1) / numerosPorBoleta);
            if (typeof showToast === 'function') {
                showToast('Configuración inválida. Máximo de boletas para ' + longitud + ' dígitos: ' + maxBoletas.toLocaleString('es-CO'), 'error');
            }
            return false;
        }

        return true;
    }

    function actualizarLabelDigitos() {
        const label = document.getElementById('label-digitos');
        const val = parseInt(longitudInput?.value) || 0;
        const lotteryName = getLoteriaNombre();
        const lotteryLabel = lotteryName && lotteryName !== 'Por definir' ? ' de la <strong>' + lotteryName + '</strong>' : ' de la lotería';

        if (label) {
            if (val === 1) {
                label.innerHTML = 'Juega con la <strong>última cifra</strong>' + lotteryLabel + ' <span class="text-danger">*</span>';
            } else if (val > 1) {
                label.innerHTML = 'Juega con los <strong>últimos ' + val + ' dígitos</strong>' + lotteryLabel + ' <span class="text-danger">*</span>';
            } else {
                label.innerHTML = 'Juega con las cifras' + lotteryLabel + ' <span class="text-danger">*</span>';
            }
        }
    }

    // Event listeners para cálculo en tiempo real
    if (cantidadInput) cantidadInput.addEventListener('input', function() { actualizarPlaceholders(); calcular(); });
    if (numerosPorBoletaInput) numerosPorBoletaInput.addEventListener('input', function() { actualizarPlaceholders(); calcular(); });
    if (longitudInput) longitudInput.addEventListener('input', function() { actualizarPlaceholders(); actualizarLabelDigitos(); calcular(); });
    if (tipoSelect) tipoSelect.addEventListener('change', calcular);
    if (nombreInput) nombreInput.addEventListener('input', function() { actualizarNombre(); calcular(); });
    if (codigoInput) codigoInput.addEventListener('input', calcular);
    if (lotterySelect) lotterySelect.addEventListener('change', function() { actualizarLabelDigitos(); calcular(); });

    // Precios y monedas en tiempo real
    if (precioInput) {
        precioInput.addEventListener('input', calcular);
        precioInput.addEventListener('formatted-input', calcular);
    }
    if (monedaSelect) monedaSelect.addEventListener('change', calcular);

    // Fechas en tiempo real
    if (fechaInicioInput) {
        fechaInicioInput.addEventListener('input', calcular);
        fechaInicioInput.addEventListener('change', calcular);
    }
    if (fechaFinInput) {
        fechaFinInput.addEventListener('input', calcular);
        fechaFinInput.addEventListener('change', calcular);
    }

    if (form) {
        form.addEventListener('submit', validarAntesDeGuardar);
    }

    // Modal de Nueva Lotería
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
            const csrfInput = document.querySelector('input[name="csrf_test_name"]');

            const postData = {
                name: nombre,
                code: codigo
            };
            if (csrfInput) {
                postData[csrfInput.name] = csrfInput.value;
            }

            fetch(RAFFLES_URL_GUARDAR_LOTERIA, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const option = document.createElement('option');
                    option.value = data.lottery.id;
                    option.textContent = data.lottery.name;
                    lotterySelect.appendChild(option);
                    lotterySelect.value = data.lottery.id;

                    const modalInstance = bootstrap.Modal.getInstance(modalLoteria);
                    if (modalInstance) modalInstance.hide();

                    nombreInput.value = '';
                    actualizarLabelDigitos();
                    calcular();

                    if (typeof showToast === 'function') {
                        showToast('Lotería guardada correctamente', 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Error al guardar la lotería', 'error');
                    }
                }
            })
            .catch(error => {
                if (typeof showToast === 'function') {
                    showToast('Error de conexión al guardar la lotería', 'error');
                }
            });
        });
    }

    // Inicialización al cargar la vista
    actualizarPlaceholders();
    actualizarLabelDigitos();
    if (!codigoInput?.value) actualizarNombre();
    calcular();
});