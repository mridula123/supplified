<!-- Require the header -->
<?php require_once('tpl_header.php')?>

<!-- Require the navigation -->
<?php require_once('tpl_navigation.php')?>


<!-- Include content pages -->
<?php echo $content; ?>

<!-- Require the footer -->
<?php require_once('tpl_footer.php')?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".flash-success").fadeOut(5000);
        $(".flash-error").fadeOut(5000);
    });
</script>
