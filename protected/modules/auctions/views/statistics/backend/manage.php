<?php
    $this->_activeMenu = 'statistics/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Statistics')),
    );

    // Register Morris files
    A::app()->getClientScript()->registerScriptFile('assets/vendors/morris/raphael-min.js', 1);
    A::app()->getClientScript()->registerCssFile('assets/vendors/morris/morris.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/morris/morris.js', 1);
?>

<h1><?= A::t('auctions', 'Statistics'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
        <form id="frmStatistics" action="statistics/manage" method="get">
        <fieldset>
            <label><?= A::t('auctions', 'Select Year'); ?>:</label>
            <select name="year">
                <?php
                    for($i= $currentYear; $i >= $currentYear-5; $i--){
                        echo '<option'.($selectedYear == $i ? ' selected="selected"' : '').' value="'.$i.'">'.$i.'</option>';
                    }
                ?>
            </select>
        </fieldset>
        </form>
        <br><br>

        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('auctions', 'Created Auctions'); ?></h4>
            <div id="graphCreatedAuctions"></div>
        </div>
        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('auctions', 'Closed Auctions'); ?></h4>
            <div id="graphClosedAuctions"></div>
        </div>
        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('auctions', 'Orders Count'); ?></h4>
            <div id="graphOrdersCount"></div>
        </div>

        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('auctions', 'Orders Income'); ?></h4>
            <div id="graphOrderIncome"></div>
        </div>

        <div style="clear:both"></div>
        <br><br>


    </div>


</div>


<script>
// Use Morris.Bar

Morris.Area({
    element: 'graphCreatedAuctions',
    data: [
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>", "created": <?= isset($createdAuctions[1]) ? $createdAuctions[1] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>", "created": <?= isset($createdAuctions[2]) ? $createdAuctions[2] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>", "created": <?= isset($createdAuctions[3]) ? $createdAuctions[3] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>", "created": <?= isset($createdAuctions[4]) ? $createdAuctions[4] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>", "created": <?= isset($createdAuctions[5]) ? $createdAuctions[5] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>", "created": <?= isset($createdAuctions[6]) ? $createdAuctions[6] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>", "created": <?= isset($createdAuctions[7]) ? $createdAuctions[7] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>", "created": <?= isset($createdAuctions[8]) ? $createdAuctions[8] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>", "created": <?= isset($createdAuctions[9]) ? $createdAuctions[9] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>", "created": <?= isset($createdAuctions[10]) ? $createdAuctions[10] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>", "created": <?= isset($createdAuctions[11]) ? $createdAuctions[11] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>", "created": <?= isset($createdAuctions[12]) ? $createdAuctions[12] : 0; ?>},
    ],
    xkey: 'elapsed',
    ykeys: ['created'],
    labels: ["<?= A::te('auctions', 'Created Auctions'); ?>", "<?= A::te('auctions', 'All Auctions'); ?>"],
    barColors: '#f00',
    parseTime: false
});

Morris.Area({
    element: 'graphClosedAuctions',
    data: [
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>", "closed": <?= isset($closedAuctions[1]) ? $closedAuctions[1] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>", "closed": <?= isset($closedAuctions[2]) ? $closedAuctions[2] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>", "closed": <?= isset($closedAuctions[3]) ? $closedAuctions[3] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>", "closed": <?= isset($closedAuctions[4]) ? $closedAuctions[4] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>", "closed": <?= isset($closedAuctions[5]) ? $closedAuctions[5] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>", "closed": <?= isset($closedAuctions[6]) ? $closedAuctions[6] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>", "closed": <?= isset($closedAuctions[7]) ? $closedAuctions[7] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>", "closed": <?= isset($closedAuctions[8]) ? $closedAuctions[8] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>", "closed": <?= isset($closedAuctions[9]) ? $closedAuctions[9] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>", "closed": <?= isset($closedAuctions[10]) ? $closedAuctions[10] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>", "closed": <?= isset($closedAuctions[11]) ? $closedAuctions[11] : 0; ?>},
        {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>", "closed": <?= isset($closedAuctions[12]) ? $closedAuctions[12] : 0; ?>},
    ],
    xkey: 'elapsed',
    ykeys: ['closed'],
    labels: ["<?= A::te('auctions', 'Closed Auctions'); ?>", "<?= A::te('auctions', 'All Auctions'); ?>"],
    barColors: '#f00',
    parseTime: false
});

Morris.Bar({
  element: 'graphOrdersCount',
  data: [
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>', y: <?= isset($ordersCount[1]) ? $ordersCount[1] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>', y: <?= isset($ordersCount[2]) ? $ordersCount[2] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>', y: <?= isset($ordersCount[3]) ? $ordersCount[3] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>', y: <?= isset($ordersCount[4]) ? $ordersCount[4] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>', y: <?= isset($ordersCount[5]) ? $ordersCount[5] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>', y: <?= isset($ordersCount[6]) ? $ordersCount[6] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>', y: <?= isset($ordersCount[7]) ? $ordersCount[7] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>', y: <?= isset($ordersCount[8]) ? $ordersCount[8] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>', y: <?= isset($ordersCount[9]) ? $ordersCount[9] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>', y: <?= isset($ordersCount[10]) ? $ordersCount[10] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>', y: <?= isset($ordersCount[11]) ? $ordersCount[11] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>', y: <?= isset($ordersCount[12]) ? $ordersCount[12] : 0; ?>},
  ],
  xkey: 'x',
  ykeys: ['y'],
  labels: ["<?= A::te('auctions', 'Orders Count'); ?>"]
}).on('click', function(i, row){
  console.log(i, row);
});

Morris.Area({
  element: 'graphOrderIncome',
  data: [
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>", "value": <?= isset($ordersIncome[1]) ? $ordersIncome[1] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>", "value": <?= isset($ordersIncome[2]) ? $ordersIncome[2] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>", "value": <?= isset($ordersIncome[3]) ? $ordersIncome[3] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>", "value": <?= isset($ordersIncome[4]) ? $ordersIncome[4] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>", "value": <?= isset($ordersIncome[5]) ? $ordersIncome[5] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>", "value": <?= isset($ordersIncome[6]) ? $ordersIncome[6] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>", "value": <?= isset($ordersIncome[7]) ? $ordersIncome[7] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>", "value": <?= isset($ordersIncome[8]) ? $ordersIncome[8] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>", "value": <?= isset($ordersIncome[9]) ? $ordersIncome[9] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>", "value": <?= isset($ordersIncome[10]) ? $ordersIncome[10] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>", "value": <?= isset($ordersIncome[11]) ? $ordersIncome[11] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>", "value": <?= isset($ordersIncome[12]) ? $ordersIncome[12] : 0; ?>},
  ],
  xkey: 'elapsed',
  ykeys: ['value'],
  labels: ["<?= A::te('auctions', 'Orders Income'); ?> $"],
  parseTime: false
});

</script>


<?php
A::app()->getClientScript()->registerScript(
    'statistics',
    'jQuery(document).ready(function(){
        var $ = jQuery;
        $(\'select[name="year"]\').change(function(){
            $("#frmStatistics").submit();
        });
    });
    ',
    2
);
