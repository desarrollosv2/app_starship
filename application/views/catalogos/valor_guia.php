<div class="row">
    <div class="col">
        <label for="">DAI</label>
        <?php foreach ($valor_guiasxref as $row) : ?>
            <input type="text" class="form-control" id="dai" name="dai" step="0.01" value="<?php echo $row->dai; ?>">
        <?php endforeach ?>
    </div>

    <div class="col">
        <label for="">IVA</label>
        <?php foreach ($valor_guiasxref as $row) : ?>
            <input type="text" class="form-control" id="iva" name="iva" step="0.01" value="<?php echo $row->iva; ?>">
        <?php endforeach ?>

    </div>
</div>

<label for="dui">Cobro Final</label>
<?php foreach ($valor_guiasxref as $row) : ?>
    <input type="text" class="form-control" id="valor_total" name="valor_total" value="<?php echo $row->cobro_final; ?>" readonly>
<?php endforeach ?>

<script>
    $(document).ready(function() {
      return false;
        var iva = $("#iva").val();
        var dai = $("#dai").val();

        // Convertir las entradas a números decimales con dos decimales de precisión
        let dai_dp = dai.slice(0, -1) + dai.slice(-1).repeat(2);
        let iva_dp = iva.slice(0, -1) + iva.slice(-1).repeat(2);

        // Realizar el cálculo de cada parte
        let total_imp = parseFloat(dai_dp) + parseFloat(iva_dp);
        let manejo = 2.00;
        let iva_manejo = 0.26;
        let manejo_total = manejo + iva_manejo;
        let comision = ((parseFloat(total_imp) + manejo_total) * (5 / 100)) / 1.13;
        let iva_comision = ((parseFloat(total_imp) + manejo_total) * (5 / 100)) - comision;
        let total_iva = iva_manejo + iva_comision;
        let cobro_final = parseFloat(total_imp) + parseFloat(comision) + 2 + parseFloat(total_iva);

        // Redondear el resultado final a dos decimales
        cobro_final = cobro_final.toFixed(2);

        // Mostrar resultados intermedios (para depuración)
        console.log("total_imp:", total_imp);
        console.log("manejo_total:", manejo_total);
        console.log("comision:", comision);
        console.log("iva_comision:", iva_comision);
        console.log("total_iva:", total_iva);
        console.log("cobro_final:", cobro_final);



        document.getElementById("valor_total").value = cobro_final;
    });

    function calculo_total() {
        alert("Holaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbb");
        var iva = $("#iva").val();
        var dai = $("#dai").val();

        let dai_dp = dai.slice(0, -1) + dai.slice(-1).repeat(2);
        let iva_dp = iva.slice(0, -1) + iva.slice(-1).repeat(2);

        // Realizar el cálculo de cada parte
        let total_imp = parseFloat(dai_dp) + parseFloat(iva_dp);
        let manejo = 2.00;
        let iva_manejo = 0.26;
        let manejo_total = manejo + iva_manejo;
        let comision = ((parseFloat(total_imp) + manejo_total) * (5 / 100)) / 1.13;
        let iva_comision = ((parseFloat(total_imp) + manejo_total) * (5 / 100)) - comision;
        let total_iva = iva_manejo + iva_comision;
        let cobro_final = parseFloat(total_imp) + parseFloat(comision) + 2 + parseFloat(total_iva);

        // Redondear el resultado final a dos decimales
        cobro_final = cobro_final.toFixed(2);

        // Mostrar resultados intermedios (para depuración)
        console.log("total_imp:", total_imp);
        console.log("manejo_total:", manejo_total);
        console.log("comision:", comision);
        console.log("iva_comision:", iva_comision);
        console.log("total_iva:", total_iva);
        console.log("cobro_final:", cobro_final);


        document.getElementById("valor_total").value = cobro_final;
    }
</script>