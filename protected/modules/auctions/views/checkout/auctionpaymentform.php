<?php
$this->_pageTitle = A::t('auctions', 'Checkout Auction');

$breadCrumbs = array();
$breadCrumbs[] = array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage());
$breadCrumbs[] = array('label' => A::t('auctions', 'Auctions'), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')));
if(!empty($parentCategories) && is_array($parentCategories)){
    if(!empty($parentCategories['parent_category']) && is_array($parentCategories['parent_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['parent_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['parent_category']['id']), CHtml::encode($parentCategories['parent_category']['name'])));
    }
    if(!empty($parentCategories['current_category']) && is_array($parentCategories['current_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['current_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['current_category']['id']), CHtml::encode($parentCategories['current_category']['name'])));
    }
}
$breadCrumbs[] = array('label' => $auction->auction_name, 'url'=>Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction->id, $auction->auction_name));
$breadCrumbs[] = array('label' => A::t('auctions', 'Checkout Auction'));
$this->_breadCrumbs = $breadCrumbs;
$formName = 'newShippingAddress';
$onchange = "addChangeCountry(this.value,'')";
?>
<div class="row">
    <?= $actionMessage; ?>
    <div>
        <div class="v-heading-v2">
            <h3><?= A::t('auctions', 'Payment Info'); ?></h3>
        </div>
        <ul>
            <li><h5><strong><?= A::t('auctions', 'Name'); ?>: </strong><?= $providerSettings->name; ?></h5></li>
            <?php if($providerSettings->instructions != ''): ?>
                <li><h5><strong><?= A::t('app', 'Instructions'); ?>:</strong></h5><p><?= $providerSettings->instructions; ?>:</p></li>
            <?php endif; ?>
        </ul>

        <div class="v-heading-v2">
            <h3><?= A::t('auctions', 'Member Info'); ?></h3>
        </div>
        <ul>
            <?= $member->full_name ? '<li><h5><strong>'.A::t('app', 'Full Name').': </strong>'.$member->full_name.'</h5></li>' : ''; ?>
            <?= $member->email ? '<li><h5><strong>'.A::t('app', 'Email').': </strong>'.$member->email.'</h5></li>' : ''; ?>
            <?= $member->phone ? '<li><h5><strong>'.A::t('app', 'Phone').': </strong>'.$member->phone.'</h5></li>' : ''; ?>
        </ul>

        <div class="v-heading-v2">
            <h3><?= A::t('auctions', 'Auction Info'); ?></h3>
        </div>
        <ul>
            <?= $auction->auction_name ? '<li><h5><strong>'.A::t('auctions', 'Name').': </strong><a href="'.Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction->id, $auction->auction_name).'" class="link-to-post">'.CHtml::encode($auction->auction_name).'</a></h5></li>' : ''; ?>
        </ul>

        <div class="v-heading-v2">
            <h3><?= A::t('auctions', 'Auction Total Price'); ?></h3>
        </div>
        <ul>
            <li><h5><strong><?= A::t('auctions', 'Price'); ?>: </strong><?= CCurrency::format($price); ?></h5></li>
            <?php if(!empty($taxes) && $totalTax > 0): ?>
                <?php foreach($taxes as $tax): ?>
                    <li><h5><strong><?= CHtml::encode($tax['name'].' '.round($tax['percent'], 2).'%'); ?>: </strong><?= CCurrency::format($price * ($tax['percent'] * 0.01)); ?></h5></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(count($taxes) > 1): ?>
                <li><h5><strong><?= A::t('auctions', 'Taxes Total'); ?>: </strong><?= CCurrency::format($totalTax); ?></h5></li>
            <?php endif; ?>
            <li><h5><strong><?= A::t('auctions', 'Grand Total'); ?>: </strong><?= CCurrency::format($grandTotal); ?></h5></li>
        </ul>
        <div class="v-heading-v2">
            <h3><?= A::t('auctions', 'Shipping Address'); ?></h3>
        </div>
        <?php echo CHtml::openForm('checkout/checkout', 'post', array('id'=>$formName, 'class' => 'form-horizontal form-bordered'));
        echo CHtml::hiddenField('act', 'send');
        echo CHtml::hiddenField('order_number', $order->order_number ? $order->order_number : ''); ?>
        <div class="error alert alert-danger" id="shippingMessageError" style="display:none;"></div>
        <div>
            <label for="shipping-address-select"><?= A::t('auctions', 'Select a shipping address from your addresses or enter a new address.'); ?></label>
            <br/>
            <select name="<?= $formName; ?>_address_id" id="<?= $formName; ?>_address_id" class="form-control">
                <option value=""><?= A::t('auctions', '- select -'); ?></option>
                <option value="new_address"><?= A::t('auctions', 'New Address'); ?></option>
                <?php foreach($shippingAddress as $addressId => $address): ?>
                    <option value="<?= $addressId; ?>"<?= $shippingSelect == $addressId ? ' selected="selected"' : ''; ?><?= $addressId == 'session_address' ? ' class="is-clone"' : ''; ?>><?= $address; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="shipping-new-address-form" class="mv10" style="display: none;">
            <div class="v-heading-v2">
                <h3><?= A::t('auctions', 'New Address'); ?></h3>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <input type="text" id="<?= $formName; ?>_first_name" name="<?= $formName; ?>_first_name" value="" maxlength="32" title="<?= A::te('auctions', 'First Name'); ?>" placeholder="<?= A::t('auctions', 'First Name'); ?> * " data-required="1" class="form-control required-entry" />
                    <p class="error required" id="shippingErrorEmptyFirstName" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'First Name'))); ?></p>
                </div>
                <div class="visible-xs mb-md"></div>
                <div class="col-sm-6">
                    <input type="text" id="<?= $formName; ?>_last_name" name="<?= $formName; ?>_last_name" value="" maxlength="32" title="<?= A::t('auctions', 'Last Name'); ?>" placeholder="<?= A::t('auctions', 'Last Name'); ?> * " data-required="1" class="form-control required-entry" />
                    <p class="error required" id="shippingErrorEmptyLastName" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Last Name'))); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <input type="text" name="<?= $formName; ?>_phone" value="" title="<?= A::te('auctions', 'Phone'); ?>" placeholder="<?= A::t('auctions', 'Phone'); ?> * " maxlength="32" class="form-control required-entry" data-required="1" id="<?= $formName; ?>_phone" />
                    <p class="error required" id="shippingErrorEmptyPhone" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Phone'))); ?></p>
                </div>
                <div class="visible-xs mb-md"></div>
                <div class="col-sm-6">
                    <input type="text" name="<?= $formName; ?>_fax" value="" title="<?= A::te('auctions', 'Fax'); ?>" placeholder="<?= A::t('auctions', 'Fax'); ?>" maxlength="32" class="form-control" data-required="0" id="<?= $formName; ?>_fax">
                    <p class="error required" id="shippingErrorEmptyFax" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Fax'))); ?></p>
                </div>
            </div>
            <input type="text" id="<?= $formName; ?>_company" name="<?= $formName; ?>_company" maxlength="128" value="" title="<?= A::te('auctions', 'Company'); ?>" placeholder="<?= A::t('auctions', 'Company'); ?>" data-required="0" class="form-control" />
            <p class="error required" id="shippingErrorEmptyCompany" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Company'))); ?></p>
            <input type="text" title="<?= A::te('auctions', 'Address'); ?>" placeholder="<?= A::t('auctions', 'Address'); ?> * " maxlength="64" name="<?= $formName; ?>_address" id="<?= $formName; ?>_address" value="" data-required="1" class="form-control required-entry" />
            <p class="error required" id="shippingErrorEmptyAdderss" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Address'))); ?></p>
            <input type="text" title="<?= A::te('auctions', 'Address (line 2)'); ?>" placeholder="<?= A::t('auctions', 'Address (line 2)'); ?>" maxlength="64" name="<?= $formName; ?>_address_2" id="<?= $formName; ?>_address_2" value="" data-required="0" class="form-control" />
            <p class="error required" id="shippingErrorEmptyAdderss2" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Address (line 2)'))); ?></p>
            <div class="row">
                <div class="col-sm-6">
                    <input type="text" title="<?= A::t('auctions', 'City'); ?>" placeholder="<?= A::t('auctions', 'City'); ?> * " name="<?= $formName; ?>_city" maxlength="64" value="" class="form-control required-entry" data-required="1" id="<?= $formName; ?>_city" />
                    <p class="error required" id="shippingErrorEmptyCity" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'City'))); ?></p>
                </div>
                <div class="visible-xs mb-md"></div>
                <div class="col-sm-6">
                    <input type="text" title="<?= A::t('auctions', 'Zip Code'); ?>" placeholder="<?= A::t('auctions', 'Zip Code'); ?> * " name="<?= $formName; ?>_zip_code" id="<?= $formName; ?>_zip_code" maxlength="32" value="" data-required="1" class="form-control validate-zip-international required-entry" />
                    <p class="error required" id="shippingErrorEmptyZipCode" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Zip Code'))); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <select name="country_code" id="<?= $formName; ?>_country_code" class="form-control" data-required="1" title="<?= A::t('auctions', 'Country'); ?>" onchange="<?= $onchange; ?>">
                        <?php foreach($countries as $code => $country): ?>
                            <option value="<?= $code; ?>"><?= $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="error required" id="shippingErrorEmptyCountryCode" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Country'))); ?></p>
                </div>
                <div class="visible-xs mb-md"></div>
                <div class="col-sm-6">
                    <input type="text" name="state" value="" title="<?= A::te('auctions', 'State/Province'); ?>" class="form-control" maxlength="64" data-required="1" id="<?= $formName; ?>_state"/>
                    <p class="error required" id="shippingErrorEmptyState" style="display:none;"><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'State/Province'))); ?></p>
                </div>
            </div>
        </div>
        <div class="error required mv10" id="shippingErrorEmptyAddressId" style="display:none;">
            <p><?= A::t('auctions', 'You must specify a shipping address'); ?></p>
        </div>
        <div class="mv10">
            <button id="shipping-new-address-button" type="button" class="btn v-btn v-third-dark v-small-button" data-sending="<?= A::te('auctions', 'Sending...'); ?>" data-send="<?= A::te('auctions', 'Update'); ?>"><span><?= A::t('auctions', 'Update'); ?></span></button>
        </div>
        <?= CHtml::closeForm(); ?>
        <div id="payment-button" style="display: none;">
            <?php if(APPHP_MODE == 'demo'):
                echo CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')));
                echo CHtml::submitButton(A::t('auctions', 'Go To Payment'), array('class'=>'button'));
            else: ?>
                <?= $form; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'changeCountry',
    'addChangeCountry = function (country,stateCode){
        auctions_changeCountry("' . $formName . '",country,stateCode,"frontend");
    }

    jQuery(document).ready(function(){
        var country = "' . $countryCode . '";
        var stateCode = "' . $stateCode . '";

        auctions_changeCountry("' . $formName . '",country,stateCode,"frontend");
        
        $("#' . $formName . '_address_id").change(function(){
            $("#payment-button").slideUp();
            $(".error").hide();
            if($("#' . $formName . '_address_id option:selected").val() == "new_address"){
                $("#shipping-new-address-form").slideDown();
            }else{
                $("#shipping-new-address-form").slideUp();
                $("#' . $formName . '_first_name").val("");
                $("#' . $formName . '_last_name").val("");
                $("#' . $formName . '_company").val("");
                $("#' . $formName . '_address").val("");
                $("#' . $formName . '_address_2").val("");
                $("#' . $formName . '_city").val("");
                $("#' . $formName . '_country_code").val("");
                $("#' . $formName . '_state").val("");
                $("#' . $formName . '_zip_code").val("");
                $("#' . $formName . '_phone").val("");
                $("#' . $formName . '_fax").val("");
            }
        });
    });
    ',
    1
);