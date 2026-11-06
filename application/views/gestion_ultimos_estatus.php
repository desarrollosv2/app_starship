<div class="container-fluid pl-0">

  <div class="row bg-secondary ml-0 p-0 vw-100 position-relative">
    <div class="col">

      <div class="filters" >
        <label>
          <select id="empresa_id" class="form-control form-control-sm" style="min-width:220px;">
            <option value="">Seleccione empresa...</option>
          </select>
        </label>
        <label> <input type="date" id="fecha_desde" class="form-control form-control-sm" style="padding:4px 6px;"></label>
        <label> <input type="date" id="fecha_hasta" class="form-control form-control-sm" style="padding:4px 6px;"></label>
        <button id="btnFiltrar" class="btn  btn-sm mt-3" style="background-color: #FE4F00; color:white" disabled>Filtrar</button>
        <button id="btnExport" class="btn btn-outline-success btn-sm mt-3" disabled>Exportar</button>
      </div>
    </div>
  </div>

  <div class="row pl-3">


    <div class="p-2">



      <table id="tabla-ultimos" class="display nowrap table table-striped" style="width:100%">
        <thead>
          <tr>
            <th>#</th>
            <th>Tracking</th>
            <th>ID Estatus</th>
            <th>Nombre Estatus</th>
            <th>Fecha Último Estatus</th>
              <th>Fecha Manifiesto</th>
            <th>Empresa</th> <!-- nombre de empresa -->
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

  </div>
</div>