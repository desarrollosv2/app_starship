<?php // Tarjeta "Filtros de busqueda" compartida por el listado de pallets y los reportes. $accion: funcion JS que consulta ?>
<form class="form_filtros panel mb-4" onsubmit="<?php echo $accion; ?>(); return false;" onreset="setTimeout(<?php echo $accion; ?>, 0);">
    <?php echo campo_csrf(); ?>
    <h2 class="panel-titulo"><i class="fa-solid fa-filter"></i> Filtros de búsqueda</h2>
    <div class="row g-3 align-items-end">
        <div class="col-6 col-md-3 col-xl-2">
            <label class="filtro-label" for="f-desde"><i class="fa-regular fa-calendar-days"></i> Desde</label>
            <input type="date" class="form-control" id="f-desde" name="desde">
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <label class="filtro-label" for="f-hasta"><i class="fa-regular fa-calendar-days"></i> Hasta</label>
            <input type="date" class="form-control" id="f-hasta" name="hasta">
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <label class="filtro-label" for="f-estado"><i class="fa-solid fa-circle-half-stroke"></i> Estado</label>
            <select class="form-select" id="f-estado" name="estado">
                <option value="">Todos</option>
                <option value="abierto">Abierto</option>
                <option value="cerrado">Cerrado</option>
            </select>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <label class="filtro-label" for="f-cliente"><i class="fa-solid fa-user-tie"></i> Cliente</label>
            <select class="form-select" id="f-cliente" name="cliente_id">
                <option value="">Todos</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?php echo (int) $c->id; ?>"><?php echo html_escape($c->nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-6 col-xl-2">
            <label class="filtro-label" for="f-tipo"><i class="fa-solid fa-tags"></i> Tipo de producto</label>
            <select class="form-select" id="f-tipo" name="tipo_producto_id">
                <option value="">Todos</option>
                <?php foreach ($tipos_producto as $t): ?>
                    <option value="<?php echo (int) $t->id; ?>"><?php echo html_escape($t->nombre_legible); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-6 col-xl-2 d-flex gap-2">
            <button type="submit" class="btn btn-naranja flex-grow-1"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
            <button type="reset" class="btn btn-light" title="Limpiar filtros"><i class="fa-solid fa-eraser"></i></button>
        </div>
    </div>
</form>
