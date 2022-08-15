<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Customer_Mapping'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                 <form>
                   <div class="form-group">
                        <?= lang("Salesperson", "salesperson_id") ?>
                        <?php
                        $vehicleplate[''] = "";
                        foreach ($salespeople as $vehicle) {
                            $vehicleplate[$vehicle->id] = $vehicle->name;
                        }
                        echo form_dropdown('salesperson_id', $vehicleplate, (isset($_POST['salespeople']) ? $_POST['salespeople'] : ($vehicle ? $vehicle->id : '')), 'class="form-control select" id="salesperson_id" onchange="filterMarkers(this.value);" placeholder="' . lang("select") . " " . lang("salespeople") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    <label>Day</label>
                    <select class="form-control">
                        <option>Mon</option>
                        <option>Tue</option>
                        <option>Wed</option>
                        <option>Thur</option>
                        <option>Fri</option>
                        <option>Sat</option>
                        <option>Sun</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="box-content" style="height: 100%;">
        <div class="row" style="height: 100%;">
            <div class="col-lg-12" style="height: 1000px;" id="map">


            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
</script>


