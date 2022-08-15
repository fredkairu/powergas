<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('group_permissions'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang("set_permissions"); ?></p>

                <?php if (!empty($p)) {
                    if ($p->group_id != 1) {

                        echo form_open("system_settings/permissions/" . $id); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">

                                <thead>
                                <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("group_permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center"><?= lang("module_name"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th class="text-center"><?= lang("view"); ?></th>
                                    <th class="text-center"><?= lang("Add/Import"); ?></th>
                                    <th class="text-center"><?= lang("edit"); ?></th>
                                    <th class="text-center"><?= lang("delete"); ?></th>
                                    <th class="text-center"><?= lang("misc"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= lang("products"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="products-index" <?php echo $p->{'products-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="products-add" <?php echo $p->{'products-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="products-edit" <?php echo $p->{'products-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="products-delete" <?php echo $p->{'products-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="products-cost" class="checkbox"
                                               name="products-cost" <?php echo $p->{'products-cost'} ? "checked" : ''; ?>><label
                                            for="products-cost" class="padding05"><?= lang('product_cost') ?></label>
                                        <input type="checkbox" value="1" id="products-discount" class="checkbox"
                                               name="products-discount" <?php echo $p->{'products-discount'} ? "checked" : ''; ?>><label
                                                for="products-discount" class="padding05"><?= lang('product_discount') ?></label>
                                        <input type="checkbox" value="1" id="products-price" class="checkbox"
                                               name="products-price" <?php echo $p->{'products-price'} ? "checked" : ''; ?>><label
                                            for="products-price" class="padding05"><?= lang('product_price') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("expenses"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="expenses-index" <?php echo $p->{'expenses-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="expenses-edit" <?php echo $p->{'expenses-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="expenses-delete" <?php echo $p->{'expenses-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="expenses-approve" <?php echo $p->{'expenses-approve'} ? "checked" : ''; ?>>
                                        <label for="expenses-approve" class="padding05"><?= lang('expenses approve') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("routes"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="routes-index" <?php echo $p->{'routes-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="routes-add" <?php echo $p->{'routes-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="routes-edit" <?php echo $p->{'routes-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="routes-delete" <?php echo $p->{'routes-delete'} ? "checked" : ''; ?>>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("vehicles"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-index" <?php echo $p->{'vehicles-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-add" <?php echo $p->{'vehicles-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-edit" <?php echo $p->{'vehicles-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-delete" <?php echo $p->{'vehicles-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-add-route" <?php echo $p->{'vehicles-add-route'} ? "checked" : ''; ?>>
                                        <label for="vehicles-add-route" class="padding05"><?= lang('add_route') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-view-route" <?php echo $p->{'vehicles-view-route'} ? "checked" : ''; ?>>
                                        <label for="vehicles-view-route" class="padding05"><?= lang('view_route') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-edit-route" <?php echo $p->{'vehicles-edit-route'} ? "checked" : ''; ?>>
                                        <label for="vehicles-edit-route" class="padding05"><?= lang('edit_route') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-delete-route" <?php echo $p->{'vehicles-delete-route'} ? "checked" : ''; ?>>
                                        <label for="vehicles-delete-route" class="padding05"><?= lang('delete_route') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-add-stock" <?php echo $p->{'vehicles-add-stock'} ? "checked" : ''; ?>>
                                        <label for="vehicles-add-stock" class="padding05"><?= lang('add_stock') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-view-stock" <?php echo $p->{'vehicles-view-stock'} ? "checked" : ''; ?>>
                                        <label for="vehicles-view-stock" class="padding05"><?= lang('view_stock') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="vehicles-edit-stock" <?php echo $p->{'vehicles-edit-stock'} ? "checked" : ''; ?>>
                                        <label for="vehicles-edit-stock" class="padding05"><?= lang('edit_stock') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("sales"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-index" <?php echo $p->{'sales-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-add" <?php echo $p->{'sales-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-edit" <?php echo $p->{'sales-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-delete" <?php echo $p->{'sales-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="sales-email" class="checkbox"
                                               name="sales-email" <?php echo $p->{'sales-email'} ? "checked" : ''; ?>><label
                                            for="sales-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="sales-pdf" class="checkbox"
                                               name="sales-pdf" <?php echo $p->{'sales-pdf'} ? "checked" : ''; ?>><label
                                            for="sales-pdf" class="padding05"><?= lang('pdf') ?></label>
                                        <?php if (POS) { ?>
                                            <input type="checkbox" value="1" id="pos-index" class="checkbox"
                                                   name="pos-index" <?php echo $p->{'pos-index'} ? "checked" : ''; ?>>
                                            <label for="pos-index" class="padding05"><?= lang('pos') ?></label>
                                        <?php } ?>
                                        <!--<input type="checkbox" value="1" id="sales-payments" class="checkbox"
                                               name="sales-payments" <?php echo $p->{'sales-payments'} ? "checked" : ''; ?>><label
                                            for="sales-payments" class="padding05"><?= lang('payments') ?></label>
                                        <input type="checkbox" value="1" id="sales-return_sales" class="checkbox"
                                               name="sales-return_sales" <?php echo $p->{'sales-return_sales'} ? "checked" : ''; ?>><label
                                            for="sales-return_sales"
                                            class="padding05"><?= lang('return_sales') ?></label>--->
                                    </td>
                                </tr>

                               <!-- <tr>
                                    <td><?= lang("deliveries"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-deliveries" <?php echo $p->{'sales-deliveries'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-add_delivery" <?php echo $p->{'sales-add_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-edit_delivery" <?php echo $p->{'sales-edit_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-delete_delivery" <?php echo $p->{'sales-delete_delivery'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <!--<input type="checkbox" value="1" id="sales-email" class="checkbox" name="sales-email_delivery" <?php echo $p->{'sales-email_delivery'} ? "checked" : ''; ?>><label for="sales-email_delivery" class="padding05"><?= lang('email') ?></label>-->
                                        <input type="checkbox" value="1" id="sales-pdf" class="checkbox"
                                               name="sales-pdf_delivery" <?php echo $p->{'sales-pdf_delivery'} ? "checked" : ''; ?>><label
                                            for="sales-pdf_delivery" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>-->
                                <!--<tr>
                                    <td><?= lang("gift_cards"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-gift_cards" <?php echo $p->{'sales-gift_cards'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-add_gift_card" <?php echo $p->{'sales-add_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-edit_gift_card" <?php echo $p->{'sales-edit_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="sales-delete_gift_card" <?php echo $p->{'sales-delete_gift_card'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>

                                    </td>
                                </tr>-->

                                <tr>
                                    <td>DashBoard</td>
                                    <td colspan="4">
                                       
                                    </td>
                                   
                                    <td>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="dashboard" <?php echo $p->{'dashboard'} ? "checked" : ''; ?>><label
                                            for="quotes-email" class="padding05"><?= lang('Dashboard') ?></label>
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="si" <?php echo $p->{'si'} ? "checked" : ''; ?>><label
                                            for="quotes-email" class="padding05"><?= lang('SI Analysis') ?></label>
                                            
                                            <input type="checkbox" value="1" class="checkbox"
                                               name="pso" <?php echo $p->{'pso'} ? "checked" : ''; ?>>
                                            <label
                                            for="quotes-email" class="padding05"><?= lang('PSO') ?></label>
                                            <input type="checkbox" value="1" class="checkbox"
                                               name="sso" <?php echo $p->{'sso'} ? "checked" : ''; ?>>
                                        <label
                                            for="quotes-email" class="padding05"><?= lang('SSO') ?></label>
                                        
                                        <input type="checkbox" value="1" id="quotes-email" class="checkbox"
                                               name="monthly_trend" <?php echo $p->{'monthly_trend'} ? "checked" : ''; ?>><label
                                            for="quotes-email" class="padding05"><?= lang('PSOSSOMonthly_Trend') ?></label>
                                        <input type="checkbox" value="1" id="quotes-pdf" class="checkbox"
                                               name="pso_sso_sit" <?php echo $p->{'pso_sso_sit'} ? "checked" : ''; ?>><label
                                            for="quotes-pdf" class="padding05"><?= lang('Pso_Sso_Sit') ?></label>
                                             <input type="checkbox" value="1" id="quotes-pdf" class="checkbox"
                                               name="distributor_sit" <?php echo $p->{'distributor_sit'} ? "checked" : ''; ?>><label
                                            for="quotes-pdf" class="padding05"><?= lang('Distributor_Sit') ?></label>
                                            
                                             <input type="checkbox" value="1" id="quotes-pdf" class="checkbox"
                                               name="msr_summary" <?php echo $p->{'msr_summary'} ? "checked" : ''; ?>><label
                                            for="quotes-pdf" class="padding05"><?= lang('Msr_Summary') ?></label>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td><?= lang("quotes"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="quotes-index" <?php echo $p->{'quotes-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="quotes-add" <?php echo $p->{'quotes-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="quotes-edit" <?php echo $p->{'quotes-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="quotes-delete" <?php echo $p->{'quotes-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="quotes-email" class="checkbox"
                                               name="quotes-email" <?php echo $p->{'quotes-email'} ? "checked" : ''; ?>><label
                                            for="quotes-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="quotes-pdf" class="checkbox"
                                               name="quotes-pdf" <?php echo $p->{'quotes-pdf'} ? "checked" : ''; ?>><label
                                            for="quotes-pdf" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>-->

                                <tr>
                                    <td><?= lang("purchases"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="purchases-index" <?php echo $p->{'purchases-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="purchases-add" <?php echo $p->{'purchases-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="purchases-edit" <?php echo $p->{'purchases-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="purchases-delete" <?php echo $p->{'purchases-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="purchases-email" class="checkbox"
                                               name="purchases-email" <?php echo $p->{'purchases-email'} ? "checked" : ''; ?>><label
                                            for="purchases-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="purchases-pdf" class="checkbox"
                                               name="purchases-pdf" <?php echo $p->{'purchases-pdf'} ? "checked" : ''; ?>><label
                                            for="purchases-pdf" class="padding05"><?= lang('pdf') ?></label>
                                        <!--<input type="checkbox" value="1" id="purchases-payments" class="checkbox"
                                               name="purchases-payments" <?php echo $p->{'purchases-payments'} ? "checked" : ''; ?>><label
                                            for="purchases-payments" class="padding05"><?= lang('payments') ?></label>
                                       <input type="checkbox" value="1" id="purchases-expenses" class="checkbox"
                                               name="purchases-expenses" <?php echo $p->{'purchases-expenses'} ? "checked" : ''; ?>><label
                                            for="purchases-expenses" class="padding05"><?= lang('expenses') ?></label>-->
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td><?= lang("Stock_Taking"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="stock-taking-index" <?php echo $p->{'stock-taking-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" value="1" class="checkbox"
                                               name="stock-taking-view" <?php echo $p->{'stock-taking-view'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="stock-taking-reverse" <?php echo $p->{'stock-taking-reverse'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="stock-taking-delete" <?php echo $p->{'stock-taking-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("Budget"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="budget-index" <?php echo $p->{'budget-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="budget-add" <?php echo $p->{'budget-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="budget-edit" <?php echo $p->{'budget-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="budget-delete" <?php echo $p->{'budget-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="budget-email" class="checkbox"
                                               name="budget-email" <?php echo $p->{'budget-email'} ? "checked" : ''; ?>><label
                                            for="budget-email" class="padding05"><?= lang('email') ?></label>
                                        <input type="checkbox" value="1" id="budget-pdf" class="checkbox"
                                               name="budget-pdf" <?php echo $p->{'budget-pdf'} ? "checked" : ''; ?>><label
                                            for="budget-pdf" class="padding05"><?= lang('pdf') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("Distributors"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="distributors-index" <?php echo $p->{'distributors-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="distributors-add" <?php echo $p->{'distributors-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="distributors-edit" <?php echo $p->{'distributors-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="distributors-delete" <?php echo $p->{'distributors-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="distributors-add-targets" class="checkbox"
                                               name="distributors-add-targets" <?php echo $p->{'distributors-add-targets'} ? "checked" : ''; ?>><label
                                                for="distributors-add-targets" class="padding05"><?= lang('add_targets') ?></label>
                                        <input type="checkbox" value="1" id="distributors-index-targets" class="checkbox"
                                               name="distributors-index-targets" <?php echo $p->{'distributors-index-targets'} ? "checked" : ''; ?>><label
                                                for="distributors-index-targets" class="padding05"><?= lang('view_targets') ?></label>
                                        <input type="checkbox" value="1" id="distributors-edit-targets" class="checkbox"
                                               name="distributors-edit-targets" <?php echo $p->{'distributors-edit-targets'} ? "checked" : ''; ?>><label
                                                for="distributors-edit-targets" class="padding05"><?= lang('edit_targets') ?></label>
                                        <input type="checkbox" value="1" id="distributors-delete-targets" class="checkbox"
                                               name="distributors-delete-targets" <?php echo $p->{'distributors-delete-targets'} ? "checked" : ''; ?>><label
                                                for="distributors-delete-targets" class="padding05"><?= lang('delete_targets') ?></label>
                                        <input type="checkbox" value="1" id="distributors-activate" class="checkbox"
                                               name="distributors-activate" <?php echo $p->{'distributors-activate'} ? "checked" : ''; ?>><label
                                                for="distributors-activate" class="padding05"><?= lang('activate') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("Salespeople"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="salespeople-index" <?php echo $p->{'salespeople-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="salespeople-add" <?php echo $p->{'salespeople-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="salespeople-edit" <?php echo $p->{'salespeople-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="salespeople-delete" <?php echo $p->{'salespeople-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="salespeople-add-targets" class="checkbox"
                                               name="salespeople-add-targets" <?php echo $p->{'salespeople-add-targets'} ? "checked" : ''; ?>><label
                                                for="salespeople-add-targets" class="padding05"><?= lang('add_targets') ?></label>
                                        <input type="checkbox" value="1" id="salespeople-index-targets" class="checkbox"
                                               name="salespeople-index-targets" <?php echo $p->{'salespeople-index-targets'} ? "checked" : ''; ?>><label
                                                for="salespeople-index-targets" class="padding05"><?= lang('view_targets') ?></label>
                                        <input type="checkbox" value="1" id="salespeople-edit-targets" class="checkbox"
                                               name="salespeople-edit-targets" <?php echo $p->{'salespeople-edit-targets'} ? "checked" : ''; ?>><label
                                                for="salespeople-edit-targets" class="padding05"><?= lang('edit_targets') ?></label>
                                        <input type="checkbox" value="1" id="salespeople-delete-targets" class="checkbox"
                                               name="salespeople-delete-targets" <?php echo $p->{'salespeople-delete-targets'} ? "checked" : ''; ?>><label
                                                for="salespeople-delete-targets" class="padding05"><?= lang('delete_targets') ?></label>
                                        <input type="checkbox" value="1" id="activate-salespeople" class="checkbox"
                                               name="salespeople-activate" <?php echo $p->{'salespeople-activate'} ? "checked" : ''; ?>><label
                                                for="salespeople-activate" class="padding05"><?= lang('activate') ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td><?= lang("Customers"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="customers-index" <?php echo $p->{'customers-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="customers-add" <?php echo $p->{'customers-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="customers-edit" <?php echo $p->{'customers-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="customers-delete" <?php echo $p->{'customers-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" value="1" id="customers-edit-shops" class="checkbox"
                                               name="customers-edit-shops" <?php echo $p->{'customers-edit-shops'} ? "checked" : ''; ?>><label
                                                for="customers-edit-shops" class="padding05"><?= lang('edit_shop') ?></label>
                                        <input type="checkbox" value="1" id="customers-delete-shops" class="checkbox"
                                               name="customers-delete-shops" <?php echo $p->{'customers-delete-shops'} ? "checked" : ''; ?>><label
                                                for="customers-delete-shops" class="padding05"><?= lang('delete_shop') ?></label>
                                        <input type="checkbox" value="1" id="customers-add-pm" class="checkbox"
                                               name="customers-add-pm" <?php echo $p->{'customers-add-pm'} ? "checked" : ''; ?>><label
                                                for="customers-add-pm" class="padding05"><?= lang('add_payment_method') ?></label>
                                        <input type="checkbox" value="1" id="customers-index-pm" class="checkbox"
                                               name="customers-index-pm" <?php echo $p->{'customers-index-pm'} ? "checked" : ''; ?>><label
                                                for="customers-index-pm" class="padding05"><?= lang('view_payment_method') ?></label>
                                        <input type="checkbox" value="1" id="customers-edit-pm" class="checkbox"
                                               name="customers-edit-pm" <?php echo $p->{'customers-edit-pm'} ? "checked" : ''; ?>><label
                                                for="customers-edit-pm" class="padding05"><?= lang('edit_payment_method') ?></label>
                                        <input type="checkbox" value="1" id="customers-delete-pm" class="checkbox"
                                               name="customers-delete-pm" <?php echo $p->{'customers-delete-pm'} ? "checked" : ''; ?>><label
                                                for="customers-delete-pm" class="padding05"><?= lang('delete_payment_method') ?></label>
                                        <input type="checkbox" value="1" id="customers-activate" class="checkbox"
                                               name="customers-activate" <?php echo $p->{'customers-activate'} ? "checked" : ''; ?>><label
                                                for="customers-activate" class="padding05"><?= lang('activate') ?></label>
                                        <input type="checkbox" value="1" id="customers-add-credit-limit" class="checkbox"
                                               name="customers-add-credit-limit" <?php echo $p->{'customers-add-credit-limit'} ? "checked" : ''; ?>><label
                                                for="customers-add-credit-limit" class="padding05"><?= lang('add-credit-limit') ?></label>
                                        <input type="checkbox" value="1" id="customers-edit-credit-limit" class="checkbox"
                                               name="customers-edit-credit-limit" <?php echo $p->{'customers-edit-credit-limit'} ? "checked" : ''; ?>><label
                                                for="customers-edit-credit-limit" class="padding05"><?= lang('edit-credit-limit') ?></label>
                                        <input type="checkbox" value="1" id="customers-delete-credit-limit" class="checkbox"
                                               name="customers-delete-credit-limit" <?php echo $p->{'customers-delete-credit-limit'} ? "checked" : ''; ?>><label
                                                for="customers-delete-credit-limit" class="padding05"><?= lang('delete-credit-limit') ?></label>
                                    </td>
                                </tr>

                               <!-- <tr>
                                    <td><?= lang("suppliers"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="suppliers-index" <?php echo $p->{'suppliers-index'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="suppliers-add" <?php echo $p->{'suppliers-add'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="suppliers-edit" <?php echo $p->{'suppliers-edit'} ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox"
                                               name="suppliers-delete" <?php echo $p->{'suppliers-delete'} ? "checked" : ''; ?>>
                                    </td>
                                    <td>
                                    </td>
                                </tr>-->

                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0"
                                   class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

                                <thead>
                                <tr>
                                    <th><?= lang("reports"); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                     <!--   <input type="checkbox" value="1" class="checkbox" id="product_quantity_alerts"
                                               name="reports-brand" <?php echo $p->{'reports-brand'} ? "checked" : ''; ?>><label
                                            for="product_quantity_alerts"
                                            class="padding05"><?= lang('Brand_Report') ?></label>-->
                                        <input type="checkbox" value="1" class="checkbox" id="reports-vehicles"
                                               name="reports-vehicles" <?php echo $p->{'reports-vehicles'} ? "checked" : ''; ?>><label
                                                for="reports-vehicles"
                                                class="padding05"><?= lang('Vehicles_Report') ?></label>
                                        <input type="checkbox" value="1" class="checkbox" id="reports-salespeople"
                                               name="reports-salespeople" <?php echo $p->{'reports-salespeople'} ? "checked" : ''; ?>><label
                                                for="reports-salespeople"
                                                class="padding05"><?= lang('SalesPeople_Report') ?></label>
                                        <input type="checkbox" value="1" class="checkbox" id="Product_expiry_alerts"
                                               name="reports-sales" <?php echo $p->{'reports-sales'} ? "checked" : ''; ?>><label
                                            for="Product_expiry_alerts"
                                            class="padding05"><?= lang('Sales_Report') ?></label>
                                        <input type="checkbox" value="1" class="checkbox" id="products"
                                               name="reports-products" <?php echo $p->{'reports-products'} ? "checked" : ''; ?>><label
                                            for="products" class="padding05"><?= lang('products') ?></label>
                                      <!--  <input type="checkbox" value="1" class="checkbox" id="daily_sales"
                                               name="reports-budget" <?php echo $p->{'reports-budget'} ? "checked" : ''; ?>><label
                                            for="daily_sales" class="padding05"><?= lang('Budget_Report') ?></label>-->
                                        <input type="checkbox" value="1" class="checkbox" id="monthly_sales"
                                               name="reports-monthly_sales" <?php echo $p->{'mashariki_report'} ? "checked" : ''; ?>><label
                                            for="monthly_sales" class="padding05"><?= lang('Mashariki_Report') ?></label>
                                       <!-- <input type="checkbox" value="1" class="checkbox" id="payments"
                                               name="reports-payments" <?php echo $p->{'reports-payments'} ? "checked" : ''; ?>><label
                                            for="payments" class="padding05"><?= lang('payments') ?></label>-->
                                        <input type="checkbox" value="1" class="checkbox" id="purchases"
                                               name="reports-purchases" <?php echo $p->{'reports-purchases'} ? "checked" : ''; ?>><label
                                            for="purchases" class="padding05"><?= lang('purchases') ?></label>
                                        <input type="checkbox" value="1" class="checkbox" id="customers"
                                               name="reports-customers" <?php echo $p->{'reports-customers'} ? "checked" : ''; ?>><label
                                            for="customers" class="padding05"><?= lang('customers') ?></label>
                                        <input type="checkbox" value="1" class="checkbox" id="suppliers"
                                               name="reports-suppliers" <?php echo $p->{'reports-suppliers'} ? "checked" : ''; ?>><label
                                            for="suppliers" class="padding05"><?= lang('suppliers') ?></label>
                                    </td>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();
                    } else {
                        echo $this->lang->line("group_x_allowed");
                    }
                } else {
                    echo $this->lang->line("group_x_allowed");
                } ?>


            </div>
        </div>
    </div>
</div>