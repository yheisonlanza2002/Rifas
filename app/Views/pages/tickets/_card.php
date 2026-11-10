<?php $t = $t ?? []; ?>
<div class="col-6 col-sm-4 col-md-3 col-xl-2">
	<div class="ticket-card ticket-<?= strtolower($t['status'] ?? 'available') ?>">
		<div class="ticket-top">
			<span class="ticket-code"><i class="bx bx-ticket me-1"></i><?= esc($t['code']) ?></span>
			<?php if (($t['status'] ?? '') === 'AVAILABLE'): ?>
				<span class="badge bg-success">Disponible</span>
			<?php elseif (($t['status'] ?? '') === 'RESERVED'): ?>
				<span class="badge bg-warning text-dark">Reservado</span>
			<?php elseif (($t['status'] ?? '') === 'SOLD'): ?>
				<span class="badge bg-primary">Vendido</span>
			<?php else: ?>
				<span class="badge bg-secondary">Cancelado</span>
			<?php endif; ?>
		</div>
		<div class="ticket-body">
			<div class="ticket-info"><i class="bx bx-dollar-circle"></i><span>$ <?= number_format((float) $t['price'], 2) ?></span></div>
			<div class="ticket-info"><i class="bx bx-list-ol"></i><span><?= (int) ($t['number_quantity'] ?? 0) ?> números</span></div>
		</div>
		<div class="ticket-actions">
			<a href="<?= base_url('boletas/' . $t['id'] . '/editar') ?>" class="btn btn-sm btn-outline-primary flex-fill">
				<i class="bx bx-edit-alt"></i> Editar
			</a>
			<?php if (($t['status'] ?? '') !== 'SOLD'): ?>
				<a href="<?= base_url('boletas/' . $t['id'] . '/eliminar') ?>"
				   class="btn btn-sm btn-outline-danger"
				   title="Eliminar"
				   data-confirm="¿Eliminar la boleta <?= esc($t['code']) ?>?"
				   data-confirm-text="Esta acción no se puede deshacer"
				   data-confirm-btn="Sí, eliminar">
					<i class="bx bx-trash"></i>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
