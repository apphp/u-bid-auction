Chosen (Select Box Enhancer) for jQuery and Prototype
-----

### Usage:

#### PHP code
```PHP
<!-- Chosen files -->
<?= CHtml::scriptFile('assets/vendors/chosen/chosen.jquery.min.js'); ?>
<?= CHtml::cssFile('assets/vendors/chosen/chosen.min.css'); ?>
```

#### JS code
```JS
<script>
    // INIT CHOSEN SELECTS
    // --------------------------------------
    $('select').addClass('chosen-select-filter');
    $('.chosen-select-filter').css({'padding': '10px'});
    $('.chosen-select-filter').chosen({disable_search_threshold: 7});	
    //$('.chosen-select').chosen({disable_search: true});
    $('.chosen-search input, .chosen-select-filter input').attr('maxlength', 255);
</script>
```