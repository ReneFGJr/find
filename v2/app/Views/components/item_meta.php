<?php
/**
 * Visualização de array de dados complexos (Work, Expression, Manifestation)
 * Espera um array como $dataArr
 */
?>
<div class="row g-3">
<?php foreach ($meta as $entity => $entityArr): ?>
	<div class="col-md-4">
		<div class="card mb-3 shadow-sm">
			<div class="card-header bg-primary text-white">
				<?= ucfirst($entity) ?>
			</div>
			<div class="card-body small">
				<?php if (!empty($entityArr['concept'])): ?>
					<div class="mb-2">
						<strong>Conceito:</strong><br>
						ID: <?= esc($entityArr['concept']['id'] ?? '') ?><br>
						Nome: <?= esc($entityArr['concept']['name'] ?? '') ?><br>
						Classe: <?= esc($entityArr['concept']['Class'] ?? '') ?><br>
						Tipo: <?= esc($entityArr['concept']['type'] ?? '') ?><br>
						Idioma: <?= esc($entityArr['concept']['lang'] ?? '') ?><br>
					</div>
				<?php endif; ?>
				<?php if (!empty($entityArr['data'])): ?>
					<div class="mb-2">
						<strong>Dados:</strong>
						<ul class="list-unstyled ms-2">
						<?php foreach ($entityArr['data'] as $d): ?>
							<li>
								<span class="badge bg-light text-dark border">Prop: <?= esc($d['Property'] ?? '') ?></span>
								<?php if (!empty($d['Caption'])): ?>
									<span class="text-secondary ms-1">"<?= esc($d['Caption']) ?>"</span>
								<?php endif; ?>
								<?php if (!empty($d['Lang'])): ?>
									<span class="badge bg-info bg-opacity-10 text-info ms-1">[<?= esc($d['Lang']) ?>]</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>
XXX