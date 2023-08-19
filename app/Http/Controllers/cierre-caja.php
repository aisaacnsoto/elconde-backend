<div class="row">
    <div class="col">

        <div id="seccion-imprimir">

            <div class="row col">
                <h3 class="m-auto">Cierre de Caja</h3>
            </div>

            <hr>
            <div class="form-inline">
                Fecha: <span class="ml-4"></span>
            </div>
            <hr>

            <div class="table-responsive">
                <table class="table table-borderless" width="100%" cellspacing="0">
                    <tbody>
                        <tr class="bg-primary text-light">
                            <td colspan="3"><strong>APERTURA DE CAJA</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL APERTURA DE CAJA</strong></td>
                            <td class="text-right">
                                <strong></strong>
                            </td>
                        </tr>

                        <!-- <tr><td colspan="3"><hr class="d-none d-print-block w-auto"></td></tr> -->

                        <tr class="bg-primary text-light">
                            <td colspan="3"><strong>VENTAS</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">VENTAS TARJETA</td>
                            <td class="text-right"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">VENTAS EFECTIVO</td>
                            <td class="text-right"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">PAGO PERSONAL</td>
                            <td class="text-right">
                                
                                
                                <hr class="d-none d-print-block w-auto"  style="height:50px;border:none;color:#333;background-color:#333;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL VENTAS</strong></td>
                            <td class="text-right"><strong></strong></td>
                        </tr>

                        <!-- <tr><td colspan="3"><hr class="d-none d-print-block w-auto"></td></tr> -->

                        <tr class="bg-primary text-light">
                            <td colspan="3"><strong>SERVICIOS</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">SERVICIOS TARJETA</td>
                            <td class="text-right"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">SERVICIOS EFECTIVO</td>
                            <td class="text-right"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">PAGO PERSONAL</td>
                            <td class="text-right">
                                
                                <hr class="d-none d-print-block w-auto">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL SERVICIOS</strong></td>
                            <td class="text-right"><strong></strong></td>
                        </tr>

                        <!-- <tr><td colspan="3"><hr class="d-none d-print-block w-auto"></td></tr> -->
<!-- 
                        <tr class="bg-primary text-light">
                            <td colspan="3"><strong>GASTOS</strong></td>
                        </tr>
                        <tr *ngFor="let gasto of data.gastos.detalle">
                            <td>{{ gasto.nro_comprobante }}</td>
                            <td>{{ gasto.get_gasto_tipo.nombre }} {{ gasto.descripcion != null ? '* '+gasto.descripcion : '' }}</td>
                            <td class="text-right">{{ gasto.total * -1 | currency:'S/ ' }}</td>
                        </tr>
                        <tr><td colspan="2"></td><td><hr class="d-none d-print-block w-auto"></td></tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL GASTOS</strong></td>
                            <td class="text-right"><strong>{{ data.gastos.total * -1 | currency:'S/ ' }}</strong></td>
                        </tr> -->

                        <!-- <tr><td colspan="3"><hr class="d-none d-print-block w-auto"></td></tr> -->

                        <tr class="bg-primary text-light">
                            <td colspan="3"><strong>EFECTIVO</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL EFECTIVO</strong></td>
                            <td class="text-right"><strong></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">CIERRE DE CAJA</td>
                            <td class="text-right">
                                
                                <hr class="d-none d-print-block w-auto">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"></td>
                            <td class="text-right"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        

    </div>
</div>